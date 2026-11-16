<?php

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_categorias.php';
require_once __DIR__ . '/../../includes/auth.php';

//carga las funciones de cookies
require_once __DIR__ . '/../../includes/cookies.php';


requireLogin();

// Comprueba que sea administrador
validarAdmin($conn);


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

    //obtiene el nombre que se desea buscar en la tabla
    $filtro = $_GET['filtro'] ?? '';

    //comprueba que el filtro sea texto
    if (!is_string($filtro)) {
        $filtro = '';
    }

    //elimina los espacios y limita la longitud
    $filtro = trim($filtro);
    $filtro = substr($filtro, 0, 100);



    //obtiene el numero de pagina desde la URL
    $pagina = $_GET['pagina'] ?? '1';

//valida que la pagina sea un numero entero positivo
    if (!is_string($pagina) || !ctype_digit($pagina) || (int)$pagina < 1) {
        $pagina = 1;
    } else {
        $pagina = (int)$pagina;
    }

    //obtiene la cantidad solicitada desde el formulario
    $cantidad = $_GET['cantidad_categorias'] ?? null;

    //obtiene la cantidad que se guardo en la cookie (10 si no hay una valida)
    $limite = obtenerCantidadCategorias();

    //comprueba que el valor recibido sea un numero valido
    if (is_string($cantidad) && ctype_digit($cantidad)) {
        // Convierte la cantidad a un numero entero
        $cantidad = (int)$cantidad;

        //solo permite las cantidades declaradas en cookies.php
        if (in_array($cantidad, cantidadespermitidas, true)) {
            //utiliza la cantidad seleccionada
            $limite = $cantidad;

            //guarda la preferencia en la cookie
            guardarCantidadCategorias($limite);

            //vuelve a la primera pagina cuando se cambia la cantidad
            $pagina = 1;
        }
    }

    // Obtiene el numero total de categorias activas
    $totalCategorias = ContarCategorias($conn, $filtro);

    // Calcula el total de paginas y garantiza que exista por lo menos la pagina 1
    $totalPaginas = max(1, (int)ceil($totalCategorias / $limite));

    // Evita que la pagina solicitada supere la cantidad disponible
    if ($pagina > $totalPaginas) {
        $pagina = $totalPaginas;
    }

    // Calcula la cantidad de categorias que debemos saltar
    $offset = ($pagina - 1) * $limite;


    //se llena con las categorias de la pagina seleccionada
    $Categorias = getCategorias($conn, $limite, $offset, $filtro);
    //Variable para mostrar el formulario de crear
    $mostrar = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
//carga pagina para mostrar los datos de la categoria
require __DIR__ . '/../pages/view_categorias.php'; 