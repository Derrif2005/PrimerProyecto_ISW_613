<?php
require_once __DIR__ . '/primerproyecto_isw_613/public/actions/conexion.php';


$rol = $_POST['id_rol'] ?? null;
$username = $_POST['username'] ?? null;
$correo = $_POST['correo'] ?? null;
$password = $_POST['password'] ?? null;
$confirmPassword = $_POST['confirm_password'] ?? null;
$active = $_POST['active'] ?? null;
$intentos = $_POST['intentos'] ?? null;

//Validaciones de entrada
if (
    !ctype_digit((string) $rol)
    || !is_string($username)
    || !is_string($correo)
    || !is_string($password)    
    || !is_string($confirmPassword)
    || !ctype_digit((string) $active)



    //tengo que continuar por aqui despues de terminar register.php
    ) {
    header('Location: /public/pages/register.php?error=invalid_input');
    exit;
}

if (strlen($username) >= 50) {
    header('Location: /public/pages/register.php?error=username_too_long');
    exit;
}



if (strlen($password) < 8) {
    header('Location: /public/pages/register.php?error=password_too_short');
    exit;
}

if ($password !== $confirmPassword) {
    header('Location: /public/pages/register.php?error=password_mismatch');
    exit;
}


/*
try {
    if (findProvince($conn, (int) $provinceId) === false) {
        header('Location: /public/pages/register.php?error=invalid_province');
        exit;
    }
*/
    $statement = $conn->prepare(
        'INSERT INTO usuarios(id, id_rol, nombre, correo, password_hash, active, intentos) VALUES (NULL, :id_rol, :nombre, :correo, :password_hash, :active, :intentos)'

        );
    $statement->execute([
        'id_rol' => (int) $rol,
        'nombre' => trim($username),
        'correo' => trim($correo),
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'active' => (int) $active,
        'intentos' => (int) $intentos
        
    ]);
} catch (PDOException $exception) {
    // 23000 = integrity constraint violation (the username unique key)
    if ($exception->getCode() === '23000') {
        header('Location: /public/pages/register.php?error=username_taken');
        exit;
    }

    error_log('Registration database error: ' . $exception->getMessage());
    header('Location: /public/pages/register.php?error=server_error');
    exit;
}

header('Location: /public/index.php?success=registered');
