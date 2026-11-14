<?php   
    require_once __DIR__ . '/pages/common/header.php';
    require_once __DIR__ . '/../includes/auth.php';
    // Obtiene el correo guardado en la cookie

    require_once __DIR__ . '/../includes/mensajes.php';
    $correoRecordado = $_COOKIE['remembered_username'] ?? '';
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
                <div class="grupo-formulario">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo"
                        value="<?= htmlspecialchars($correoRecordado, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="grupo-formulario">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <?php /* Pendiente de revisar */ ?>
                <div class="grupo-recordar">
                    <input type="checkbox" id="remember_me" name="remember_me"
                        <?php echo $correoRecordado !== '' ? 'checked' : ''; ?>>
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