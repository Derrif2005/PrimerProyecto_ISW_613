<?php
session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/buscar_categorias.php';
require_once __DIR__ . '/../../config/auth.php';


requireLogin();

// comprueba si el todavia tiene token 
if (empty($_SESSION['csrf_token'])) {

    // geenra el token y lo guarda
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//Busca usuarios por medio del correo 
 //Si no exisite tendra un valor falso
  $encontrarCategoria = false;
    //Comprueba que la categoria existe y si es texto
    if(isset($_GET['buscar_categoria']) && is_string($_GET['buscar_categoria'])){
        //Busca la categoria en MYSQL
        $encontrarCategoria = findcategorias($conn,trim($_GET['buscar_categoria']));
    }

//Variable para guardar todo los usuarios
$Categorias = getCategorias($conn); 
//carga pagina para mostrar los datos del usuario
require __DIR__ . '/../pages/view_categorias.php';