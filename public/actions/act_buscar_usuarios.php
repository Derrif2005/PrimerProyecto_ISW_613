<?php
session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_usuarios.php';
require_once __DIR__ . '/../../includes/auth.php';


requireLogin();

// comprueba si el todavia tiene token 
if (empty($_SESSION['csrf_token'])) {

    // geenra el token y lo guarda
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


//Busca usuarios por medio del correo 
 //Si no exisite tendra un valor falso
  $encontrarUsuario = false;
    //Comprueba que el correo existe y si es texto
    if(isset($_GET['buscar_correo']) && is_string($_GET['buscar_correo'])){
        
        //Busca el correo en MYSQL
        $encontrarUsuario = findcorreo($conn,trim($_GET['buscar_correo']));
    }

//Variable para guardar todo los usuarios
$users = getUsers($conn); 
//Variable para mostrar el formulario de crear
$mostrarU = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
//carga pagina para mostrar los datos del usuario
require __DIR__ . '/../pages/view_usuarios.php';