<?php

session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/buscar_usuarios.php';

//Comprobara si tiene una sesion iniciada y ahora tambien si es administrador
require_once __DIR__ . '/../../config/auth.php';


//Mensajes de error
require_once __DIR__ . '/../../config/mensajes.php';


requireLogin();


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
    validarRol($conn, $id_rol);
    

    //Busca el id del usuario para comprobar que existe
    $consulta = $conn->prepare('SELECT id FROM usuarios WHERE id=:id LIMIT 1');
    $consulta->execute(['id' => $id]);
    $usuario_existente = $consulta->fetch(PDO::FETCH_ASSOC);
    //Valida que $Usuario_existente es verdero
    if($usuario_existente === false){
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }    
    

    //Actualiza el codigo 
    $consulta = $conn->prepare('UPDATE usuarios SET nombre = :nombre, correo = :correo, id_rol = :id_rol, active = :active WHERE id = :id');
    $consulta->execute(['nombre' => trim($nombre), 'correo' => trim($correo), 'id_rol' => $id_rol, 'active' => $active, 'id' =>$id ]); 
    header('Location: /actions/act_buscar_usuarios.php?success=1');
    exit;

     //Al terminar borra el codigo para generar uno nuevo al recargar
    unset($_SESSION['csrf_token']);

    //Vuelve al formulario mostrando un mesaje de exito
    header('Location: /actions/act_buscar_usuarios.php?ok=1');
    exit;

     //Al terminar borra el codigo para generar uno nuevo al recargar
    unset($_SESSION['csrf_token']);

    //Vuelve al formulario mostrando un mesaje de exito
    header('Location: /actions/act_buscar_usuarios.php?ok=1');
    exit;

    } catch(PDOException $error){
    // Registrara el error sin mostralos en la vista
    error_log('Error de actualizacion'.$error->getMessage());
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
        validarRol($conn, $id_rol);

        //Guarda el usuario con su rol asignado
        $consulta = $conn->prepare('INSERT INTO usuarios (id_rol, nombre, correo, password_hash) VALUES(:rol, :nombre, :correo, :hash)'
        );
        $consulta->execute(['rol' => (int)$id_rol,
        'nombre' => trim($nombre),
        'correo' => trim($correo),
        'hash' => password_hash($password,PASSWORD_DEFAULT)
        ]);

        //Al terminar borra el codigo para generar uno nuevo al recargar
        unset($_SESSION['csrf_token']);

        //Vuelve al formulario mostrando un mesaje de exito
        header('Location: /actions/act_buscar_usuarios.php?ok=1');
        exit;
    } catch(PDOException $error){
        // Registrara el error son mostralos en la vista
        error_log('Error de registro'.$error->getMessage());
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }

    } elseif($accion === 'eliminar'){

      if( !is_string($id) || !ctype_digit($id) || (int)$id < 1) 
        {
            header('Location: /actions/act_buscar_usuarios.php?error=1');
            exit;
        }

        $consulta = $conn->prepare('DELETE FROM usuarios WHERE id = :id');
        $consulta->execute(['id' => $id ]);

        //Al terminar borra el codigo para generar uno nuevo al recargar
        unset($_SESSION['csrf_token']);
    //Vuelve al formulario mostrando un mesaje de exito
        header('Location: /actions/act_buscar_usuarios.php?ok=1');
        exit;


    } else {

        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;

    }




















     


   

    


    