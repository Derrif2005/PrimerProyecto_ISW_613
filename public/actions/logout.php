<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/auth.php';

//Comprueba que la solicitud sea por medio de POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit('Metodo no permitido');
}

requireLogin();


//Comprueba el token CSRF
$token = $_POST['csrf_token'] ?? '';

if(
    !is_string($token) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
){
    http_response_code(403);
    exit('Token invalido');
}


try{

    //Elimina el token y la cookie
    eliminarToken($conn);

}catch(PDOException $error){
    error_log('Error al eliminar token: ' . $error->getMessage());
    logoutUser();
    http_response_code(500);
    exit('No se pudo revocar el token');
}


//Cierra la sesion
logoutUser();

header('Location: /?success=logged_out');
exit;
