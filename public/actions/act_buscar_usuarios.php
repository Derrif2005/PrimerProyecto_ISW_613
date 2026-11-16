<?php

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';

require_once __DIR__ . '/../../includes/buscar_usuarios.php';

//lo llama para utilizar la funciones de validar permisos
require_once __DIR__ . '/../../includes/auth.php';
//llama a cookie para la lista de usuarios
require_once __DIR__ . '/../../includes/cookies.php';


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


    //obtiene el nombre que se desea buscar en la tabla
    $filtro = $_GET['filtro'] ?? '';

        //comprueba que el filtro sea texto
        if (!is_string($filtro)) {
            $filtro = '';
        }

        //elimina espacios y limita la longitud
        $filtro = trim($filtro);
        $filtro = substr($filtro, 0, 100);

//Se implementara modificacion para mostrar una cantidad n de ususario por pagina



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
    $limite = obtenerCantidadUsuarios();

    // Comprueba que la cantidad sea un numero valido
    if (is_string($cantidad) && ctype_digit($cantidad)) {

        // Convierte la cantidad a un numero entero
        $cantidad = (int)$cantidad;

        //comprueba que la cantidad este en la lista permitida
        if (in_array($cantidad, cantidadespermitidas, true)) {

            // Guarda la cantidad seleccionada en la variable limite
            $limite = $cantidad;

             // Guarda la cantidad seleccionada en la cookie
        guardarCantidadUsuarios($limite);

         //regresa a la primera pagina al cambiar la cantidad
        $pagina = 1;
        }
    }

    //obtiene la cantidad total de usuarios activos
    $totalUsuarios = ContarUsuarios($conn, $filtro);

    //calcula la cantidad de paginas necesarias
    $totalPaginas = (int)ceil($totalUsuarios / $limite);

    // Comprueba que exista por lo menos la pagina 1
    if ($totalPaginas < 1) {
        $totalPaginas = 1;
    }

    // Comprueba que la pagina solicitada no supere las disponibles
    if ($pagina > $totalPaginas) {
        $pagina = $totalPaginas;
    }

    // Calcula cuantos usuarios debemos saltar
    $offset = ($pagina - 1) * $limite;

    // Obtiene los usuarios de la pagina seleccionada
    $users = getUsers($conn, $limite, $offset, $filtro);




//Variable para mostrar el formulario de crear
$mostrarU = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
//carga pagina para mostrar los datos del usuario
require __DIR__ . '/../pages/view_usuarios.php';