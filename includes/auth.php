<?php

require_once __DIR__ . '/../config/constantes.php';

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
function requireLogin(){
    startSession();

    //Si no hay un usuario autenticado, vuelve al login
    if (!isset($_SESSION['user_id'])) {
        header('Location: /?error=login_required');
        exit;
    }

    //Comprueba cuanto tiempo lleva inactivo
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT_SECONDS) {
        header('Location: /?error=session_expired');
        exit;
    }

    //Actualiza la ultima actividad
    $_SESSION['last_activity'] = time();
}

//Genera el token para recordar la sesion
function crearToken(PDO $conn, int $usuario_id){

    //Genera el selector y el validador
    $selector = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(32));

    //Genera el hash del validador
    $validator_hash = hash('sha256', $validator);

    //Guarda el token en la base de datos
    $consulta = $conn->prepare('INSERT INTO auth_tokens
    (usuario_id, selector, validator_hash, expira_en)
    VALUES(:usuario_id, :selector, :validator_hash, DATE_ADD(NOW(), INTERVAL 14 DAY))');

    $consulta->execute([
        'usuario_id' => $usuario_id,
        'selector' => $selector,
        'validator_hash' => $validator_hash
    ]);

    //Guarda el token en la cookie
    setcookie(REMEMBER_TOKEN_COOKIE, "$selector:$validator", [
        'expires' => time() + 60 * 60 * 24 * 14,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}


//Valida el token para recuperar la sesion
function validarToken(PDO $conn){

    //Obtiene la cookie
    $token = $_COOKIE[REMEMBER_TOKEN_COOKIE] ?? '';

    //Comprueba que la cookie sea valida
    if(!is_string($token) || $token === ''){
        return false;
    }

    //Separa el selector y el validador
    $datos = explode(':', $token, 2);

    if(count($datos) !== 2){
        return false;
    }

    $selector = $datos[0];
    $validator = $datos[1];

    //Comprueba el formato del token
    if(strlen($selector) !== 18 || strlen($validator) !== 64 ||
        !ctype_xdigit($selector) || !ctype_xdigit($validator)){
        return false;
    }

    //Busca el token y los datos del usuario
    $consulta = $conn->prepare('SELECT u.id, u.nombre, u.correo,
    u.id_rol, u.active, t.validator_hash
    FROM auth_tokens t
    INNER JOIN usuarios u ON t.usuario_id = u.id
    WHERE t.selector = :selector
    AND t.expira_en > NOW()
    AND u.active = 1 LIMIT 1');

    $consulta->execute(['selector' => $selector]);
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    //Comprueba que el token exista
    if($usuario === false){
        return false;
    }

    //Comprueba que el validador sea correcto
    if(!hash_equals($usuario['validator_hash'], hash('sha256', $validator))){
        return false;
    }

    //Genera un nuevo selector y validador
    $nuevo_selector = bin2hex(random_bytes(9));
    $nuevo_validator = bin2hex(random_bytes(32));

    //Actualiza el token anterior
    $consulta = $conn->prepare('UPDATE auth_tokens
    SET selector = :nuevo_selector,
    validator_hash = :nuevo_hash,
    expira_en = DATE_ADD(NOW(), INTERVAL 14 DAY)
    WHERE selector = :selector
    AND validator_hash = :validator_hash
    AND expira_en > NOW()');

    $consulta->execute([
        'nuevo_selector' => $nuevo_selector,
        'nuevo_hash' => hash('sha256', $nuevo_validator),
        'selector' => $selector,
        'validator_hash' => $usuario['validator_hash']
    ]);

    //Comprueba que el token se actualizo
    if($consulta->rowCount() !== 1){
        return false;
    }

    //Guarda el nuevo token en la cookie
    setcookie(REMEMBER_TOKEN_COOKIE, "$nuevo_selector:$nuevo_validator", [
        'expires' => time() + 60 * 60 * 24 * 14,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    //Recupera la sesion del usuario
    loginUser($usuario);

    return true;
}


//Elimina el token para olvidar la sesion
function eliminarToken(PDO $conn){

    //Obtiene la cookie
    $token = $_COOKIE[REMEMBER_TOKEN_COOKIE] ?? '';

    //Elimina la cookie
    setcookie(REMEMBER_TOKEN_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    unset($_COOKIE[REMEMBER_TOKEN_COOKIE]);

    //Comprueba que la cookie sea valida
    if(!is_string($token) || $token === ''){
        return;
    }

    //Separa el selector y el validador
    $datos = explode(':', $token, 2);

    if(count($datos) === 2){

        $selector = $datos[0];
        $validator_hash = hash('sha256', $datos[1]);

        //Elimina el token de la base de datos
        $consulta = $conn->prepare('DELETE FROM auth_tokens
        WHERE selector = :selector
        AND validator_hash = :validator_hash');

        $consulta->execute([
            'selector' => $selector,
            'validator_hash' => $validator_hash
        ]);
    }
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