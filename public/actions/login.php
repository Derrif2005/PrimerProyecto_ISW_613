<?php

require_once __DIR__ . '/../../config/conexion.php';
/*require_once __DIR__ . '/../../config/auth.php';*/

$correo = $_POST['correo'] ?? null;
$password = $_POST['password'] ?? null;


// validates the input to ensure that the username and password are strings, not empty, and within reasonable length limits
if (
    !is_string($correo)
    || !is_string($password) 
    || !filter_var(trim($correo), FILTER_VALIDATE_EMAIL) //Filtro de php
    || $password === ''
) {
    //redirect to login page with error message
    header('Location: /PrimerProyecto_ISW_613/index.php?error=invalid_credentials');
    exit;
}


try {
    // connect to the database and fetch the user record along with its province
    $statement = $conn->prepare(
        'SELECT
         FROM usuarios u
         
         WHERE u.correo = :correo
         LIMIT 1'
    );
    $statement->execute(['correo' => trim($correo)]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('Authentication database error: ' . $exception->getMessage());
}


/*




try {
    // connect to the database and fetch the user record along with its province
    $statement = $conn->prepare(
        'SELECT id, id_rol, nombre, 
        correo, password_hash, 
        active, intentos
         FROM usuarios
         WHERE correo = :correo
         LIMIT 1'
    );
    $statement->execute(['correo' => trim($correo)]);//Obtener el registro de usuario como un array asociativo
    $user = $statement->fetch(PDO::FETCH_ASSOC); 
} catch (PDOException $exception) {
    error_log('Authentication database error: ' . $exception->getMessage());
}

// check if the user exists and verify the password
if ($user === false || !password_verify($password, $user['password_hash'])) {
    // Invalid credentials redirect to login page with error message
    header('Location: /PrimerProyecto_ISW_613/index.php?error=invalid_credentials');
    exit;
}

//comprueba si el usuario esta activo
if ((int) $user['active'] !== 1) {
    header('Location: /PrimerProyecto_ISW_613/index.php?error=inactive_user');
    exit;
}




statement = $conn->prepare(
    'UPDATE usuarios SET intentos = 0 WHERE id = :id'
);

// llena el array de parametros con los valores del usuario
$statement->execute(['id' => (int) $user['id']]);


//crea la sesion
loginUser($user);
header('Location: /PrimerProyecto_ISW_613/pages/home.php');
exit;








// Set session variables and redirect to dashboard
loginUser($user);

// "Remember me": store the username in a cookie for 30 days, or clear it if unchecked
if (isset($_POST['remember_me'])) {
    rememberUsername($user['username']);
} else {
    forgetUsername();
}

header('Location: /PrimerProyecto_ISW_613/pages/home.php');
exit;
*/




