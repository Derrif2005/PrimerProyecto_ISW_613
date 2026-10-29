<?php
// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';

/*
$rol = $_POST['id_rol'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$correo = $_POST['correo'] ?? null;
$password_hash = $_POST['password_hash'] ?? null;
$confirmPassword = $_POST['confirm_password'] ?? null;
$active = $_POST['active'] ?? null;
$intentos = $_POST['intentos'] ?? null;
*/
//Validaciones de entrada
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $rol = $_POST['id_rol'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password_hash = $_POST['password_hash'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $active = $_POST['active'] ?? '';

    if (!is_string($rol) || trim($rol) === '' || srtlen($rol) > 2  
    || !is_string($nombre) || trim($nombre) === '' || srtlen($nombre) > 50
    || !is_string($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL) 
    || strlen($correo) > 100 
    || !is_string($active) || trim($active) === ''
    || !is_string($password_hash) || strlen($clave) < 8 || $clave !== $confirmPassword) { 
        $mensaje = 'La contraseña tiene que ser de minimo 8 caracteres'
    } else {
        try {
            $consulta = $conn->prepare('INSERT INTO usuarios (id_rol, nombre,correo ,password_hash, active) value (id_rol, nombre, correo, password_hash, active)');
            $consulta->execute(['nombre' => trim($nombre), 'correo' => password_hash($clave, PASSWORD_DEFAULT)]);
            $mensaje = 'Usuario registrado';
        } catch (PDOException $e){
            error_log($e->getMessage());
            $mensaje = "el usuario no se pude registrar"
        }
    }
    
}
?>
    
 