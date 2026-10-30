<?php


require_once __DIR__ . '/../../config/conexion.php';
/*require_once __DIR__ . '/../../config/auth.php';*/

$correo = $_POST['correo'] ?? null;
$password = $_POST['password'] ?? null;


if (
    !is_string($correo)
    || !is_string($password)
    || trim($correo) === ''
    || strlen($correo) > 50
    || $password === ''
) {
    //redirect to login page with error message
    header('Location: /index.php?error=invalid_input');
    exit;
}
