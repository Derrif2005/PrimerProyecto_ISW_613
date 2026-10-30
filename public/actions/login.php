<?php


require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/auth.php';

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



$user = false;
try {
    // connect to the database and fetch the user record along with its province
    $statement = $conn->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');

    $statement->execute(['correo' => trim($correo)]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('Authentication database error: ' . $exception->getMessage());
}

// check if the user exists and verify the password
if ($user === false || !password_verify($password, $user['password_hash'])) {
    // Invalid credentials redirect to login page with error message
    header('Location: /index.php?error=invalid_credentials');
    exit;
}

// Set session variables and redirect to dashboard
loginUser($user);

// "Remember me": store the username in a cookie for 30 days, or clear it if unchecked
if (isset($_POST['remember_me'])) {
    rememberUsername($user['correo']);
} else {
    forgetUsername();
}

header('Location: /pages/home.php');
