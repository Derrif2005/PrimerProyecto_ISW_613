<?php
session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_usuarios.php';
require_once __DIR__ . '/../../includes/auth.php';


requireLogin();

validarAdmin($conn);

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
//Se implementara modificacion para mostrar una cantidad n de ususario por pagina $users = getUsers($conn);

    // Obtiene el numero de pagina desde la URL
    $pagina = $_GET['pagina'] ?? '1';

    // Valida que la pagina sea un numero entero positivo
    if (!is_string($pagina) || !ctype_digit($pagina) || (int)$pagina < 1) {
        $pagina = 1;
    } else {
        $pagina = (int)$pagina;
    }

    // Obtiene la cantidad solicitada por el usuario
    $cantidad = $_GET['cantidad_usuarios'] ?? null;

    // Cantidad por defecto
    $limite = 10;

    // Comprueba que la cantidad sea un numero valido
    if (is_string($cantidad) && ctype_digit($cantidad)) {

        // Convierte la cantidad a un numero entero
        $cantidad = (int)$cantidad;

        // Comprueba que la cantidad este entre 1 y 100
        if ($cantidad >= 1 && $cantidad <= 100) {

            // Guarda la cantidad seleccionada en la variable limite
            $limite = $cantidad;
        }
    }

    // Calcula cuantos usuarios debemos saltar
    $offset = ($pagina - 1) * $limite;

    // Obtiene los usuarios de la pagina seleccionada
    $users = getUsers($conn, $limite, $offset);

    // Obtiene los usuarios de la pagina seleccionada
    $users = getUsers($conn, $limite, $offset); 



//Variable para mostrar el formulario de crear
$mostrarU = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
//carga pagina para mostrar los datos del usuario
require __DIR__ . '/../pages/view_usuarios.php';