<?php 

//COOKIES DE USUARIOS//
//AQUI EMPIEZA
//funcion para guardar los usuarios que se mostraran en la lista de usuarios
function guardarCantidadUsuarios(int $cantidad){
    //si la cantidad tiene que no puede ser menor a 1 o mayor a 100, esto es modificable
    if ($cantidad < 1 || $cantidad > 100) {
        //si hace alguna de las dos retorna el false
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
    if (!is_string($cantidad) || !ctype_digit($cantidad) ||
        (int)$cantidad < 1 || (int)$cantidad > 100
    ) {
        //si no existe o es incorrecta utiliza 10 como defecto
        return 10;
    }

    //devuelve la cantidad guardada como numero entero
    return (int)$cantidad;
}
//AQUI TERMINA





?>