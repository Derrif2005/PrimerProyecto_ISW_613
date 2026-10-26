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
    header('Location: /workshop03/pages/register.php?error=invalid_input');
    exit;
}

if (strlen($password) < 8) {
    header('Location: /workshop03/pages/register.php?error=password_too_short');
    exit;
}

if ($password !== $confirmPassword) {
    header('Location: /workshop03/pages/register.php?error=password_mismatch');
    exit;
}

try {
    if (findProvince($conn, (int) $provinceId) === false) {
        header('Location: /workshop03/pages/register.php?error=invalid_province');
        exit;
    }

    $statement = $conn->prepare(
        'INSERT INTO users (username, password_hash, province_id) VALUES (:username, :password_hash, :province_id)'
    );
    $statement->execute([
        'username' => trim($username),
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'province_id' => (int) $provinceId,
    ]);
} catch (PDOException $exception) {
    // 23000 = integrity constraint violation (the username unique key)
    if ($exception->getCode() === '23000') {
        header('Location: /workshop03/pages/register.php?error=username_taken');
        exit;
    }

    error_log('Registration database error: ' . $exception->getMessage());
    header('Location: /workshop03/pages/register.php?error=server_error');
    exit;
}

header('Location: /workshop03/index.php?success=registered');
