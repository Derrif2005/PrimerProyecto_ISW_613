<?php

session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/buscar_usuarios.php';

//Comprobara si tiene una sesion iniciada
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


//comprueba que los datos para no subir solo del html

if(
    !is_string($id) || !ctype_digit($id) || (int)$id < 1 ||
    !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
    !is_string($correo) || !filter_var(trim($correo), FILTER_VALIDATE_EMAIL) || strlen($correo) > 50 ||
    !is_string($id_rol) || !ctype_digit($id_rol) || (int)$id_rol < 1 ||
    !is_string($active) || !in_array($active, ['0', '1'], true)
  
) {
    header('Location: /actions/act_buscar_usuarios.php?error=1');
    exit;
}

  

    try{
    //Validad que el usuario administrador este activo
    $consulta = $conn->prepare('SELECT id_rol, active FROM usuarios WHERE id =:id LIMIT 1');
    $consulta -> execute(['id' => $_SESSION['user_id']]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
    // Si el usuario es false entonces cancela proceso o si es diferente a 1 lo cancela y si es no esta activo tambie lo canceala
    if($usuario === false || (int)$usuario['id_rol'] !== 1 || (int)$usuario['active'] !== 1)
    {
     http_response_code(403);
    exit('No tiene permisos para actualizar usuarios');


    
    } 


    //Busca por id de rol y comprueba que existe  
    $consulta = $conn->prepare('SELECT id FROM roles WHERE id=:id_rol LIMIT 1');
    $consulta->execute(['id_rol' => $id_rol]);    
    $rol_existente = $consulta->fetch(PDO::FETCH_ASSOC);
    //Valida que $rol_existente es true porque si da false no existe
    if($rol_existente === false){
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }

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

    } catch(PDOException $error){
    // Registrara el error sin mostralos en la vista
    error_log('Error de actualizacion'.$error->getMessage());
    header('Location: /actions/act_buscar_usuarios.php?error=1');
    exit;
    }


    