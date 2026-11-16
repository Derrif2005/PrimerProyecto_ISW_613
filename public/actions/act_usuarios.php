<?php


// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_usuarios.php';
require_once __DIR__ . '/../../includes/bitacora.php';

//Comprobara si tiene una sesion iniciada y ahora tambien si es administrador
require_once __DIR__ . '/../../includes/auth.php';


//Mensajes de error
require_once __DIR__ . '/../../includes/mensajes.php';


requireLogin();

//Solo permite solicitudes mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo no permitido');
}

//Comprueba que el codigo es del CSRF
$token = $_POST['csrf_token'] ?? '';
if(
    !is_string($token) || 
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token) 
    ){
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }


//recibe los datos del formulario   
$id = $_POST['id'] ?? null; 
$nombre = $_POST['nombre'] ?? null;
$correo = $_POST['correo'] ?? null;
$id_rol = $_POST['id_rol'] ?? null;
$active = $_POST['active'] ?? null;
$password = $_POST['password'] ?? null;
$confirmPassword = $_POST['confirm_password'] ?? null;

$accion = $_POST['accion'] ?? null;





validarAdmin($conn);

    if($accion === 'actualizar'){

//comprueba que los datos para no subir solo del html

    if( !is_string($id) || !ctype_digit($id) || (int)$id < 1 ||
        !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
        !is_string($correo) || !filter_var(trim($correo), FILTER_VALIDATE_EMAIL) || strlen($correo) > 50 ||
        !is_string($id_rol) || !ctype_digit($id_rol) || (int)$id_rol < 1 ||
        !is_string($active) || !in_array($active, ['0', '1'], true)
    ) {
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }


    try{
    validarRol($conn, $id_rol); $conn->beginTransaction();
    

    //Busca el id del usuario para comprobar que existe
    $consulta = $conn->prepare('SELECT id, nombre, correo, id_rol, active FROM usuarios WHERE id=:id LIMIT 1 FOR UPDATE');
    $consulta->execute(['id' => $id]);
    $usuario_existente = $consulta->fetch(PDO::FETCH_ASSOC);
    //Valida que $Usuario_existente es verdero
    if($usuario_existente === false){ $conn->rollBack();
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }    
    

    //Actualiza el codigo 
    $consulta = $conn->prepare('UPDATE usuarios SET nombre = :nombre, correo = :correo, id_rol = :id_rol, active = :active WHERE id = :id');
    $consulta->execute(['nombre' => trim($nombre), 'correo' => trim($correo), 'id_rol' => $id_rol, 'active' => $active, 'id' =>$id ]); 
    $datosNuevos = ['id' => $id, 'nombre' => trim($nombre), 'correo' => trim($correo), 'id_rol' => $id_rol, 'active' => $active];
    registrarBitacora($conn, 'actualizar', $id, $usuario_existente, $datosNuevos);
    $conn->commit();

     //Al terminar borra el codigo para generar uno nuevo al recargar
    unset($_SESSION['csrf_token']);

    //Vuelve al formulario mostrando un mesaje de exito
    header('Location: /actions/act_buscar_usuarios.php?ok=1');
    exit;


    } catch(PDOException $error){ if($conn->inTransaction()){ $conn->rollBack(); }

    //comprueba si existe un problema de integridad
    if ($error->getCode() === '23000') {
        header('Location: /actions/act_buscar_usuarios.php?error=correo_duplicado');
        exit;
    }

    //registra el error sin mostrarlo al usuario
    error_log('Error de actualizacion: ' . $error->getMessage());

    header('Location: /actions/act_buscar_usuarios.php?error=1');
    exit;
    }

    
 //metodo para crear
   } elseif($accion === 'crear'){
    
    if( !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
        !is_string($correo) || !filter_var(trim($correo), FILTER_VALIDATE_EMAIL) || strlen($correo) > 50 ||
        !is_string($password) || strlen($password) < 8 ||
        !is_string($confirmPassword) || $password !== $confirmPassword ||
        !is_string($id_rol) || !in_array($id_rol, ['1', '2', '3'], true)   
    ) {
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }

    try{
        validarRol($conn, $id_rol); $conn->beginTransaction();

        //Guarda el usuario con su rol asignado
        $consulta = $conn->prepare('INSERT INTO usuarios (id_rol, nombre, correo, password_hash) VALUES(:rol, :nombre, :correo, :hash)'
        );
        $consulta->execute(['rol' => (int)$id_rol,
        'nombre' => trim($nombre),
        'correo' => trim($correo),
        'hash' => password_hash($password,PASSWORD_DEFAULT)
        ]);

        $usuario_id = $conn->lastInsertId();
        $consulta = $conn->prepare('SELECT id, nombre, correo, id_rol, active FROM usuarios WHERE id = :id');
        $consulta->execute(['id' => $usuario_id]);
        $datosNuevos = $consulta->fetch(PDO::FETCH_ASSOC);
        registrarBitacora($conn, 'crear', $usuario_id, null, $datosNuevos);
        $conn->commit();
        //Al terminar borra el codigo para generar uno nuevo al recargar
        unset($_SESSION['csrf_token']);

        //Vuelve al formulario mostrando un mesaje de exito
        header('Location: /actions/act_buscar_usuarios.php?ok=1');
        exit;
    } catch(PDOException $error){ if($conn->inTransaction()){ $conn->rollBack(); }

    //comprueba si existe un problema de integridad
    if ($error->getCode() === '23000') {
        header('Location: /actions/act_buscar_usuarios.php?error=correo_duplicado');
        exit;
    }

    //registra el error sin mostrarlo al usuario
    error_log('Error de registro: ' . $error->getMessage());

    header('Location: /actions/act_buscar_usuarios.php?error=1');
    exit;
    }

    } elseif($accion === 'eliminar'){

      if( !is_string($id) || !ctype_digit($id) || (int)$id < 1) 
        {
            header('Location: /actions/act_buscar_usuarios.php?error=1');
            exit;
        }

        // Comprueba que el administrador no se desactive a si mismo
        if ((int)$_SESSION['user_id'] === (int)$id) {
            header('Location: /actions/act_buscar_usuarios.php?error=1');
            exit;
        }

        try{
            $conn->beginTransaction();
            //Lee los datos antes de desactivar al usuario
            $consulta = $conn->prepare('SELECT id, nombre, correo, id_rol, active FROM usuarios WHERE id = :id AND active = 1 FOR UPDATE');
            $consulta->execute(['id' => $id]);
            $datosAntes = $consulta->fetch(PDO::FETCH_ASSOC);
            if($datosAntes === false){
                $conn->rollBack();
                header('Location: /actions/act_buscar_usuarios.php?error=1');
                exit;
            }
            $consulta = $conn->prepare('UPDATE usuarios SET active = 0 WHERE id = :id');
            $consulta->execute(['id' => $id]);
            $datosNuevos = $datosAntes;
            $datosNuevos['active'] = 0;
            registrarBitacora($conn, 'eliminar', $id, $datosAntes, $datosNuevos);
            $conn->commit();
            unset($_SESSION['csrf_token']);
            header('Location: /actions/act_buscar_usuarios.php?ok=1');
            exit;
        } catch(PDOException $error){
            if($conn->inTransaction()){
                $conn->rollBack();
            }
            error_log('Error al desactivar usuario: ' . $error->getMessage());
            header('Location: /actions/act_buscar_usuarios.php?error=1');
            exit;
        }
    } else {

        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;

    }
