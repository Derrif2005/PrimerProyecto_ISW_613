<?php

session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_usuarios.php';

//Comprobara si tiene una sesion iniciada
require_once __DIR__ . '/../../includes/auth.php';


//Mensajes de error
require_once __DIR__ . '/../../includes/mensajes.php';


requireLogin();


//Comprueba que el codigo es del CSRF
$token = $_POST['csrf_token'] ?? '';
if(
    !is_string($token) || 
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token) 
    ){
        header('Location: /actions/act_buscar_categoria.php?error=1');
        exit;
    }


//recibe los datos del formulario   
$id = $_POST['id'] ?? null; 
$nombre = $_POST['nombre'] ?? null;
$dias_max_prestamo = $_POST['dias_max_prestamo'] ?? null;
$active = $_POST['active'] ?? null;

//determina si la accion es para crear o actualizar
$accion = $_POST['accion'] ?? '';


//Validad que el usuario administrador este activo
    $consulta = $conn->prepare('SELECT id_rol, active FROM usuarios WHERE id =:id LIMIT 1');
    $consulta -> execute(['id' => $_SESSION['user_id']]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
    // Si el usuario es false entonces cancela proceso o si es diferente a 1 lo cancela y si es no esta activo tambie lo canceala
    if($usuario === false || (int)$usuario['id_rol'] !== 1 || (int)$usuario['active'] !== 1)
    {
     http_response_code(403);
    exit('No tiene permisos para actualizar categorias');
    } 



    //aqui empieza el metodo de actualizar

    if($accion === 'actualizar'){
        //comprueba que los datos para no subir solo del html para actualizar

        if(
            !is_string($id) || !ctype_digit($id) || (int)$id < 1 ||
            !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
            !is_string($dias_max_prestamo) || !ctype_digit($dias_max_prestamo) || (int)$dias_max_prestamo < 1 ||
            !is_string($active) || !in_array($active, ['0', '1'], true)
        
        ) {
            header('Location: /actions/act_buscar_categoria.php?error=1');
            exit;
        }

      try{
    //Busca por id de categoria y comprueba que existe  
    $consulta = $conn->prepare('SELECT id FROM categorias WHERE id=:id LIMIT 1');
    $consulta->execute(['id' => $id]);    
    $id_existente = $consulta->fetch(PDO::FETCH_ASSOC);
    //Valida que $rol_existente es true porque si da false no existe
    if($id_existente === false){
        header('Location: /actions/act_buscar_categoria.php?error=1');
        exit;
    }


    //Actualiza el codigo 
    $consulta = $conn->prepare('UPDATE categorias SET nombre = :nombre, dias_max_prestamo = :dias_max_prestamo,  active = :active WHERE id = :id');
    $consulta->execute(['nombre' => trim($nombre), 'dias_max_prestamo' => $dias_max_prestamo, 'active' => $active, 'id' =>$id ]); 
    header('Location: /actions/act_buscar_categoria.php?success=1');
    exit;
    } catch(PDOException $error){
    // Registrara el error sin mostralos en la vista
    error_log('Error de actualizacion'.$error->getMessage());
    header('Location: /actions/act_buscar_categoria.php?error=1');
    exit;
    }


//Aqui empieza el insert

    } elseif($accion === 'crear'){

        if( !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
            !is_string($dias_max_prestamo) || !ctype_digit($dias_max_prestamo) || (int)$dias_max_prestamo < 1
        ) {
            header('Location: /actions/act_buscar_categoria.php?error=1');
            exit;
        }

      try{
    //Guarda la categoria 
    $consulta = $conn->prepare('INSERT INTO categorias (nombre, dias_max_prestamo) VALUES(:nombre, :dias_max_prestamo)'
    );

    $consulta->execute([ 'nombre' => trim($nombre),
    'dias_max_prestamo' => $dias_max_prestamo
    ]);

    //Al terminar borra el codigo para generar uno nuevo al recargar
    unset($_SESSION['csrf_token']);

    //Vuelve al formulario mostrando un mesaje de exito
    header('Location: /actions/act_buscar_categoria.php?ok=1');
    exit;
} catch(PDOException $error){
    // Registrara el error son mostralos en la vista
    error_log('Error de registro'.$error->getMessage());
    header('Location: /actions/act_buscar_categoria.php?error=1');
    exit;
    }


//Aqui empiza el delete
    } elseif($accion === 'eliminar'){
        
    if( !is_string($id) || !ctype_digit($id) || (int)$id < 1) 
        {
            header('Location: /actions/act_buscar_categoria.php?error=1');
            exit;
        }

    $consulta = $conn->prepare('DELETE FROM categorias WHERE id = :id');
    $consulta->execute(['id' => $id ]);

    //Al terminar borra el codigo para generar uno nuevo al recargar
    unset($_SESSION['csrf_token']);
//Vuelve al formulario mostrando un mesaje de exito
    header('Location: /actions/act_buscar_categoria.php?ok=1');
    exit;

    }
    
    else{
        header('Location: /actions/act_buscar_categoria.php?error=1');
        exit;
    }
  




    
   