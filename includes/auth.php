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
function rememberUsername($user)
{
    setcookie(REMEMBER_COOKIE, $user, [
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



//se agrega la funcion de verificar el rol de usuario ya que durante el desarollo del programa se repite mucho ese codigo

    function validarAdmin(PDO $conn){

    // Revisa que el usuario este logueado 
    requireLogin();
        
    try{
    //Validad que el usuario administrador este activo
    $consulta = $conn->prepare('SELECT id_rol, active FROM usuarios WHERE id =:id LIMIT 1');
    //Ejecuta la consulta
    $consulta -> execute(['id' => $_SESSION['user_id']]);
    // Obitne los datos del usurio encontrado
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);
    // Si el usuario es false entonces cancela proceso o si es diferente a 1 lo cancela y si es no esta activo tambie lo canceala
    if($usuario === false || (int)$usuario['id_rol'] !== 1 || (int)$usuario['active'] !== 1)
    {
     http_response_code(403);
    exit('No tiene permisos para actualizar datos');
    }
    } catch (PDOException $error) {
        error_log('Error al comprobar permisos: ' . $error->getMessage());
        http_response_code(500);
        exit('No se pudieron comprobar los permisos');
    }
    }

   
    
    
//Valida si el rol existe
function validarRol(PDO $conn, $id_rol) {
    //Busca por id de rol y comprueba que existe  
    $consulta = $conn->prepare('SELECT id FROM roles WHERE id=:id_rol LIMIT 1');
    $consulta->execute(['id_rol' => $id_rol]);    
    $rol_existente = $consulta->fetch(PDO::FETCH_ASSOC);
    //Valida que $rol_existente es true porque si da false no existe
    if($rol_existente === false){
        header('Location: /actions/act_buscar_usuarios.php?error=1');
        exit;
    }
}




//Comprueba si el usuario tiene permiso para realizar una accion
function validarPermiso(PDO $conn, $permiso) {

    //Comprueba que el usuario tenga una sesion valida
    requireLogin();

    //Obtiene el ID del usuario que inicio sesion
    $usuario_id = $_SESSION['user_id'] ?? null;

    //Comprueba si existe una sesion valida
    if (!is_numeric($usuario_id) || (int)$usuario_id < 1) {
        http_response_code(403);
        exit('Acceso denegado');
    }

    //Busca si el rol del usuario tiene el permiso solicitado
    $consulta = $conn->prepare(
        'SELECT p.id FROM usuarios u
         INNER JOIN rol_permiso rp ON rp.rol_id = u.id_rol
         INNER JOIN permisos p ON p.id = rp.permiso_id
         WHERE u.id = :usuario_id AND u.active = 1 AND p.clave = :permiso LIMIT 1'
    );

    $consulta->execute(['usuario_id' => $usuario_id,'permiso' => $permiso]);

    //Obtiene el permiso encontrado
    $permiso_existente = $consulta->fetch(PDO::FETCH_ASSOC);

    //Si no tiene el permiso, detiene la operacion
    if ($permiso_existente === false) {
        http_response_code(403);
        exit('No tiene permisos para realizar esta accion');
    }
}


?>