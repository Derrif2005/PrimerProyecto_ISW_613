<?php

session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';

//Codigo para que solo se pueda mandar el formulario por medio de POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: /pages/register.php');
    exit;
}

//Comprueba que el codigo es del CSRF
$token = $_POST['csrf_token'] ?? '';
if(
    !is_string($token) || 
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token) 
    ){
        header('Location: /pages/register.php?error=1');
        exit;
    }


//recibe los datos del formulario    
$nombre = $_POST['nombre'] ?? null;
$correo = $_POST['correo'] ?? null;
$password = $_POST['password'] ?? null;
$confirmPassword = $_POST['confirm_password'] ?? null;

//comprueba que los datos para no subir solo del html

if(
    !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
    !is_string($correo) || !filter_var(trim($correo), FILTER_VALIDATE_EMAIL) || strlen($correo) > 50 ||
    !is_string($password) || strlen($password) < 8 ||
    !is_string($confirmPassword) || $password !== $confirmPassword    
) {
    header('Location: /pages/register.php?error=1');
    exit;
}

try{
    //Busca por id 
    $consulta = $conn->prepare('SELECT id FROM roles WHERE nombre =:nombre LIMIT 1');
    $consulta->execute(['nombre' => 'operador']);/*cambiar esto en el futuro*/ 
    $rol = $consulta->fetch(PDO::FETCH_ASSOC);

    // Si el rol no existe no se puede registar
    if($rol === false){
        error_log('rol no encontrado');
        header('Location: /pages/register.php?error=1');
        exit;
    }

    //Guarda el usuario con su rol asignado
    $consulta = $conn->prepare('INSERT INTO usuarios (id_rol, nombre, correo, password_hash) VALUES(:rol, :nombre, :correo, :hash)'
    );

    $consulta->execute(['rol' => (int) $rol['id'],
    'nombre' => trim($nombre),
    'correo' => trim($correo),
    'hash' => password_hash($password,PASSWORD_DEFAULT)
    ]);

    //Al terminar borra el codigo para generar uno nuevo al recargar
    unset($_SESSION['csrf_token']);

    //Vuelve al formulario mostrando un mesaje de exito
    header('Location: /pages/register.php?ok=1');
    exit;
} catch(PDOException $error){
    // Registrara el error son mostralos en la vista
    error_log('Error de registro'.$error->getMessage());
    header('Location: /pages/register.php?error=1');
    exit;
}


 