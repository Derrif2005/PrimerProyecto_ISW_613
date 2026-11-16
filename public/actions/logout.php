<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/auth.php';

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