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

//Busca categorias por medio del nombre 
 //Si no exisite tendra un valor falso
  $encontrarCategoria = false;
    //Comprueba que la categoria existe y si es texto
    if(isset($_GET['buscar_categoria']) && is_string($_GET['buscar_categoria'])){
        //Busca la categoria en MYSQL
        $encontrarCategoria = findcategorias($conn,trim($_GET['buscar_categoria']));
    }

    //Se inicia vacia para luego ser llenada 
    $ultimaID = 0;
    if(isset($_GET['Cargar_id']) && is_string($_GET['Cargar_id'])){
        //Busca la ultima id en MYSQL
        $encontrarCategoria = findcategorias($conn,trim($_GET['Cargar_id']) + 1);
    }


//Variable para guardar todo los usuarios
$Categorias = getCategorias($conn); 
//Variable para mostrar el formulario de crear
$mostrar = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
//carga pagina para mostrar los datos del usuario
require __DIR__ . '/../pages/view_categorias.php';