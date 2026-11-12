<?php 

//COOKIES DE USUARIOS//
//AQUI EMPIEZA

// Cantidades permitidas para mostrar registros por pagina
const cantidadespermitidas = [5, 10, 20];


//funcion para guardar los usuarios que se mostraran en la lista de usuarios
function guardarCantidadUsuarios(int $cantidad){
    
    //comprueba que la cantidad este en la lista permitida
    if (!in_array($cantidad, cantidadespermitidas, true)) {

        //si la cantidad no esta permitida retorna false
        return false;
    }

    //guarda la cantidad seleccionada durante 30 dias
    return setcookie('cantidad_usuarios', (string)$cantidad, [
        'expires' => time() + 60 * 60 * 24 * 30,//tiempo de la cookie
        'path' => '/', //se puede usar en rutas 
        'httponly' => true, // evita que javascritp lo lea
        'samesite' => 'Lax', // no lo puede visitar otros sitios
    ]);
    }

// obtiene la cantidad de usuarios guardada en la cookie
function obtenerCantidadUsuarios()
{
    //busca la cantidad guardada en el navegador
    $cantidad = $_COOKIE['cantidad_usuarios'] ?? null;

    //comprueba que la cantidad sea un numero valido
    if (!is_string($cantidad) || !ctype_digit($cantidad)){
        //si no existe o es incorrecta utiliza 10 como defecto
        return 10;
    }

     //convierte la cantidad a numero entero
        $cantidad = (int)$cantidad;

        //comprueba que la cantidad este en la lista permitida
        if (!in_array($cantidad, cantidadespermitidas, true)) {

            // Si no esta permitida utiliza 10 por defecto
            return 10;
        }



    //devuelve la cantidad guardada como numero entero
    return (int)$cantidad;
}
//AQUI TERMINA




 //COOKIES DE CATEGORIAS//
    //AQUI EMPIEZA

    //funcion para guardar la cantidad de categorias que se mostraran
    function guardarCantidadCategorias(int $cantidad){

        //comprueba que la cantidad este en la lista permitida
        if (!in_array($cantidad, cantidadespermitidas, true)) {
            return false;
        }

        //guarda la cantidad seleccionada durante 30 dias
        return setcookie('cantidad_categorias', (string)$cantidad, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    //obtiene la cantidad de categorias guardada en la cookie
    function obtenerCantidadCategorias()
    {
        //busca la cantidad guardada en el navegador
        $cantidad = $_COOKIE['cantidad_categorias'] ?? null;

        //comprueba que la cantidad sea un numero valido
        if (!is_string($cantidad) || !ctype_digit($cantidad)) {
            return 10;
        }

        //convierte la cantidad a numero entero
        $cantidad = (int)$cantidad;

        //comprueba que la cantidad este en la lista permitida
        if (!in_array($cantidad, cantidadespermitidas, true)) {
            return 10;
        }

        //devuelve la cantidad guardada
        return $cantidad;
    }

    //AQUI TERMINA


     //COOKIES DE EQUIPOS//
    //AQUI EMPIEZA

    //funcion para guardar la cantidad de equipos que se mostraran
    function guardarCantidadEquipos(int $cantidad){

        //comprueba que la cantidad este en la lista permitida
        if (!in_array($cantidad, cantidadespermitidas, true)) {

            //si la cantidad no esta permitida retorna false
            return false;
        }

        //guarda la cantidad seleccionada durante 30 dias
        return setcookie('cantidad_equipos', (string)$cantidad, [
            'expires' => time() + 60 * 60 * 24 * 30, //tiempo de la cookie
            'path' => '/', //se puede usar en todas las rutas
            'httponly' => true, //evita que javascript la lea
            'samesite' => 'Lax', //restringe el envio desde otros sitios
        ]);
    }


    //obtiene la cantidad de equipos guardada en la cookie
    function obtenerCantidadEquipos()
    {
        //busca la cantidad guardada en el navegador
        $cantidad = $_COOKIE['cantidad_equipos'] ?? null;

        //comprueba que la cantidad sea un numero valido
        if (!is_string($cantidad) || !ctype_digit($cantidad)) {

            //si no existe o es incorrecta utiliza 10 por defecto
            return 10;
        }

        //convierte la cantidad a numero entero
        $cantidad = (int)$cantidad;

        //comprueba que la cantidad este en la lista permitida
        if (!in_array($cantidad, cantidadespermitidas, true)) {

            //si no esta permitida utiliza 10 por defecto
            return 10;
        }

        //devuelve la cantidad guardada como numero entero
        return $cantidad;
    }

    //AQUI TERMINA

