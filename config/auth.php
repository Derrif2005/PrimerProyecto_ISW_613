<?php
// Vence la sesion despues de 30 minutos
const SESSION_TIMEOUT_SECONDS = 30 * 60;


const REMEMBER_COOKIE = 'remembered_username';

//inicia la sesion si no esta iniciada
function startSession()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

//Se llama despues de que la contraseña en login.php es correcta
function loginUser(array $user)
{
    startSession();
   //Crea un id de sesion nuevo despues del login
    session_regenerate_id(true);

    //Guarda informacion del usuario
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['correo'] = $user['correo'];
    $_SESSION['id_rol'] = (int)$user['id_rol'];
    $_SESSION['active'] = (int) $user['active'];
    $_SESSION['last_activity'] = time();
}
//Cierra la sesion 
function logoutUser()
{
    startSession();
    $_SESSION = [];
//elimina la cookie que identifica la sesion
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

// Pretege la paginas que requieren iniciar sesion
function requireLogin()
{
    startSession();
//si no hay un usuario auntetificado, vuelve al login
    if (!isset($_SESSION['user_id'])) {
        header('Location: /?error=login_required');
        exit;
    }
//comprueba cuanto tiempo lleva inactivo
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT_SECONDS) {
        logoutUser();
        header('Location: /?error=session_expired');
        exit;
    }
//Actualiza la ultima actividad
    $_SESSION['last_activity'] = time();
}


//Recuerda el usuario
function rememberUsername($username)
{
    setcookie(REMEMBER_COOKIE, $username, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

//olvida el usuario
function forgetUsername()
{
    setcookie(REMEMBER_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

?>