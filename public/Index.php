<?php   
    require_once __DIR__ . '/../config/conexion.php';
    require_once __DIR__ . '/../includes/auth.php';
    require_once __DIR__ . '/../includes/mensajes.php';

    startSession();

    //Comprueba si existe una sesion
    if(isset($_SESSION['user_id'])){

        //Comprueba si la sesion ha vencido
        if(time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT_SECONDS){

            try{
                eliminarToken($conn);
            }catch(PDOException $error){
                error_log('Error al eliminar token: ' . $error->getMessage());
                logoutUser();
                http_response_code(500);
                exit('No se pudo revocar el token');
            }

            logoutUser();

            header('Location: /?error=session_expired');
            exit;

        }else{
            header('Location: /pages/home.php');
            exit;
        }
    }

    //Comprueba si existe una cookie para recuperar la sesion
    if(isset($_COOKIE[REMEMBER_TOKEN_COOKIE])){

        try{

            //Valida el token
            if(validarToken($conn)){
                header('Location: /pages/home.php');
                exit;
            }else{
                eliminarToken($conn);
            }

        }catch(PDOException $error){
            error_log('Error al recuperar sesion: ' . $error->getMessage());
        }
    }
    //Genera el codigo de seguridad del formulario
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    require_once __DIR__ . '/pages/common/header.php';

?>

<body>
    <div class="contenedor">
        <h1>Prestamos UTN</h1>
        <p>Esta pagina es para uso de los funcinarios de la UTN para que puedan prestar equipos</p>
    </div>

    <div class="contenedor">
        <div class="tarjeta">

            <h2>Iniciar Sesion</h2>



            <?php renderAlert(); ?>
            <form action="actions/login.php" method="POST">
                <input type="hidden"name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'],ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8') ?>">
                <div class="grupo-formulario">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="grupo-formulario">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <?php /* Pendiente de revisar */ ?>
                <div class="grupo-recordar">
                   <input type="checkbox" id="remember_me" name="remember_me">
                    <label for="remember_me">Recuérdame</label>
                </div>

                <button type="submit" class="boton boton-completo">Iniciar sesión</button>
            </form>


            <p class="texto-registro">
                ¿No tienes una cuenta?
                <a href="/pages/register.php">Regístrate</a>
            </p>
        </div>
    </div>


    <?php
    require_once __DIR__ . '/pages/common/footer.php';
    ?>