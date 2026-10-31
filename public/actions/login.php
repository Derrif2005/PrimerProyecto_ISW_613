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
    $statement = $conn->prepare('SELECT *, (bloqueado > NOW()) AS sigue_bloqueado FROM usuarios WHERE correo = :correo LIMIT 1');

    $statement->execute(['correo' => trim($correo)]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('Authentication database error: ' . $exception->getMessage());
}


//Revisa si el usuario esta activo 
if ($user !== false && $user['bloqueado'] !== null){
    // no permite entrar hasta despues de ds minutos
  if ((int) $user['sigue_bloqueado'] === 1){
        header('Location: /index.php?error=invalid_credentials');
        exit;
    }

    error_log('antes del update');
    //si pasaron 2 minutos se reinicia
    $statement = $conn->prepare('UPDATE usuarios SET intentos = 0, bloqueado = NULL WHERE id = :id');
    $statement->execute(['id' => (int) $user['id']]);
    error_log('despues del update');
    $user['intentos'] = 0;
    }

// Revisara la contraseña para ver si esta buena
$contraseñaCorrecta = $user !== false && password_verify($password, $user['password_hash']);

// Si es incorrecta debera sumar los intentos al usuario
if(!$contraseñaCorrecta){
    //Sumara los intentos si existe el id
    if($user !== false){
        try{
        //Si lleva 2 errores el tercero lo bloquea
        if((int) $user['intentos'] >=2 ){
            $statement = $conn->prepare('UPDATE usuarios SET intentos = intentos + 1, bloqueado = DATE_ADD(NOW(), INTERVAL 2 MINUTE) WHERE id = :id');            
        
            }else{
                // Primer y segundo intento equivocado
                $statement = $conn->prepare(
                'UPDATE usuarios SET intentos = intentos + 1 WHERE id = :id');
            }
            $statement->execute(['id' => (int) $user['id']]);
        } catch(PDOException $error){
            error_log($error->getMessage());
        }
    }
    header('Location: /index.php?error=invalid_credentials');
    exit;
}


// Verifica que la contraseña sea correcta y que este activo
if ($user === false || !password_verify($password, $user['password_hash'])
    || (int) $user['active']!== 1
    ) {// Invalid credentials redirect to login page with error message
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
