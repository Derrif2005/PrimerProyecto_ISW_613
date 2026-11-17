<?php
//Carga los archivos requeridos
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/auth.php';

startSession();

//Solo permite enviar el formulario mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {//si el método que utilizó el navegador para realizar la petición es diferente de POST, entonces entra al if
    //Devuleve el metodo codigo 405 significa método no permitido
    http_response_code(405);
    exit('Metodo no permitido');
}

//Comprueba el codigo de seguridad
$token = $_POST['csrf_token'] ?? '';
//Valida que sea tipo texto
if (!is_string($token) ||
    empty($_SESSION['csrf_token']) ||//Valida que no venga vacio
    !hash_equals($_SESSION['csrf_token'], $token)// Recibe el token de la $_SESSION y lo compara con el $token, si son iguales TRUE si son diferentes, FALSE
) {
    header('Location: /?error=invalid_input');
    exit;
}

$correo = $_POST['correo'] ?? null;//Variable que recibe datos
$password = $_POST['password'] ?? null;//Variable que recibe datos

if (!is_string($correo)//Verifica que sea tipo texto
    || !is_string($password)//Verifica que sea tipo texto
    || trim($correo) === ''//Quita los espacions
    || strlen($correo) > 50//Cuenta la cantidad de carateres
    || $password === '' //Tambien cuenta los espacios en blanco
) {
    //si no lo cumple muestra el error
    header('Location: /index.php?error=invalid_input');
    exit;
}


$user = false;//Indica que aun no se ha encontrado al usuario
try {
   //prepara la consulta SQL para buscar al usuario por su correo, obtener todos sus datos y comprobar si sigue bloqueado
    $statement = $conn->prepare('SELECT *, (bloqueado > NOW()) AS sigue_bloqueado FROM usuarios WHERE correo = :correo LIMIT 1');
    //lo asigna al parametro :correo y ejecuta la consulta SQL
    $statement->execute(['correo' => trim($correo)]);
    //pasa los datos del usuario como un arreglo y los guarda en $user y si no encuentra al usuario devuelve false
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    //Por si pasa un error lo guarda
    } catch (PDOException $exception) {
    error_log('Authentication database error: ' . $exception->getMessage());
    }


//Comprueba si el usuario tiene un bloqueo registrado
    if ($user !== false && $user['bloqueado'] !== null) {

        //Si el bloqueo sigue vigente, rechaza el ingreso
        if ((int)$user['sigue_bloqueado'] === 1) {
            header('Location: /?error=invalid_credentials');
            exit;
        }

        try {
            //Si el bloqueo vencio, reinicia los intentos
            $statement = $conn->prepare('UPDATE usuarios SET intentos = 0, bloqueado = NULL WHERE id = :id');
            $statement->execute(['id' => (int)$user['id']]);

            $user['intentos'] = 0;
            $user['bloqueado'] = null;

        } catch (PDOException $error) {
            error_log('Error al restablecer intentos: ' . $error->getMessage());

            header('Location: /?error=1');
            exit;
        }
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

try {
    //Un ingreso correcto reinicia los intentos anteriores
    $statement = $conn->prepare(
        'UPDATE usuarios
         SET intentos = 0, bloqueado = NULL
         WHERE id = :id'
    );

    $statement->execute(['id' => (int)$user['id']]);

} catch (PDOException $error) {
    error_log('Error al reiniciar intentos: ' . $error->getMessage());

    header('Location: /?error=1');
    exit;
}

// Verifica que la contraseña sea correcta y que este activo
if ($user === false || !password_verify($password, $user['password_hash'])
    || (int) $user['active']!== 1
    ) {// Invalid credentials redirect to login page with error message
    header('Location: /index.php?error=invalid_credentials');
    exit;
} 


//Inicia la sesion
    loginUser($user);
    try{

        //Elimina el token anterior
        eliminarToken($conn);

        //Comprueba si el usuario selecciono recordarme
        if(isset($_POST['remember_me'])){
            crearToken($conn, (int)$user['id']);
        }

    }catch(PDOException $error){
        error_log('Error al guardar token: ' . $error->getMessage());
    }

    header('Location: /pages/home.php');
    exit;

