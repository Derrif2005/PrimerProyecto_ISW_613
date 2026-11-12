<?php
session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_equipos.php';
require_once __DIR__ . '/../../includes/auth.php';

//Llama a la funcion para llegar la categoria
require_once __DIR__ . '/../../includes/buscar_categorias.php';

//carga las funciones de cookies
require_once __DIR__ . '/../../includes/cookies.php';

requireLogin();

validarPermiso($conn, 'equipos_ver');

// comprueba si el todavia tiene token 
if (empty($_SESSION['csrf_token'])) {

    // geenra el token y lo guarda
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//Busca equipos por medio del nombre 
 //Si no exisite tendra un valor falso
  $encontrarEquipo = false;
    //Comprueba que la categoria existe y si es texto
    if(isset($_GET['buscar_equipo']) && is_string($_GET['buscar_equipo'])){
     //busca el equipo en MYSQL
        $encontrarEquipo = findNombresEquipos($conn,trim($_GET['buscar_equipo']));
    }

//obtiene el numero de pagina desde la URL
$pagina = $_GET['pagina'] ?? '1';

//valida que la pagina sea un numero entero positivo
if (!is_string($pagina) || !ctype_digit($pagina) || (int)$pagina < 1) {

    $pagina = 1;

} else {

    $pagina = (int)$pagina;
}


//obtiene la cantidad solicitada desde el formulario
$cantidad = $_GET['cantidad_equipos'] ?? null;

//obtiene la cantidad guardada en la cookie
$limite = obtenerCantidadEquipos();


//comprueba que el valor recibido sea un numero valido
if (is_string($cantidad) && ctype_digit($cantidad)) {

    //convierte la cantidad a numero entero
    $cantidad = (int)$cantidad;

    //comprueba que la cantidad este en la lista permitida
    if (in_array($cantidad, cantidadespermitidas, true)) {

        //utiliza la cantidad seleccionada
        $limite = $cantidad;

        //guarda la cantidad en la cookie
        guardarCantidadEquipos($limite);

        //regresa a la primera pagina
        $pagina = 1;
    }
}


        //cuenta la cantidad total de equipos activos
        $totalEquipos = ContarEquipos($conn);

        //calcula el numero total de paginas
        $totalPaginas = max(1, (int)ceil($totalEquipos / $limite));

        //comprueba que la pagina no supere el total
        if ($pagina > $totalPaginas) {

            $pagina = $totalPaginas;
        }

        //calcula cuantos equipos debemos saltar
        $offset = ($pagina - 1) * $limite;

        //obtiene los equipos de la pagina seleccionada
        $Equipos = getEquipos($conn, $limite, $offset);

            

//Variable para cargar todas las categorias activas para el select
$Categorias = getCategoriasParaSelect($conn);


//Variable para mostrar el formulario de crear
$mostrarE = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
//carga pagina para mostrar los datos del usuario
require __DIR__ . '/../pages/view_equipos.php';