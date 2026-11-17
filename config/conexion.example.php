<?php

//Guarda los errores sin mostrarlos en pantalla
error_reporting(E_ALL);//Funciona para reportar todos los tipos de erroes
ini_set('display_errors', '0'); //Controla si los errores se muestran en la pantalla
ini_set('log_errors', '1');//Registra los errores en el archivo del servidor

//Copia este archivo como conexion.php y completa tus datos de MySQL.
$servidor = "127.0.0.1";//Direccion del servidor
$usuario = "TU_USUARIO_MYSQL";//Usuario de la BD
$contrasena = "TU_CONTRASENA_MYSQL";//Contraseña de la BD
$baseDatos = "prestamoutn";//Nombre de la DB

// Cadena de conexión.
$dsn = "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4";

try {

    // Crear conexión PDO.
    $conn = new PDO($dsn, $usuario, $contrasena, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//Capturara los errores por medio de Try, catch
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//Funciona para obtener datos con el nombre de la columna
        PDO::ATTR_EMULATE_PREPARES => false //Se utiliza para funciones nativas del servidor
    ]);

} catch (PDOException $error) {

    error_log("Error de conexión: " . $error->getMessage());

    http_response_code(500);

    die("No se pudo conectar a la base de datos.");
}

