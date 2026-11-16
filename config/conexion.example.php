<?php

//Guarda los errores sin mostrarlos en pantalla
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

//Copia este archivo como conexion.php y completa tus datos de MySQL.
$servidor = "127.0.0.1";
$usuario = "TU_USUARIO_MYSQL";
$contrasena = "TU_CONTRASENA_MYSQL";
$baseDatos = "prestamoutn";

// Cadena de conexión.
$dsn = "mysql:host=$servidor;dbname=$baseDatos;charset=utf8mb4";

try {

    // Crear conexión PDO.
    $conn = new PDO($dsn, $usuario, $contrasena, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

} catch (PDOException $error) {

    error_log("Error de conexión: " . $error->getMessage());

    http_response_code(500);

    die("No se pudo conectar a la base de datos.");
}

