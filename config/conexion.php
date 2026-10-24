<?php 

$dns = 'mysql:Host=127.0.0.1;dbname=prestamoutn;charset=utf8mb4';
$dbUser = 'root';
$dbPassword = 'root';

try {
    $conn = new PDO($dns, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $exception) {
    error_log('Database connection error: ' . $exception->getMessage());
    http_response_code(500);
    exit('The service is temporarily unavailable. Please try again later.prueba: si hay conexion');
}