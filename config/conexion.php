<?php

// Datos de la base de datos.
$servidor = "127.0.0.1";
$usuario = "root";
$contrasena = "";
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
