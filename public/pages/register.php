
<?php
// Se inicia la sesión
session_start();

require_once __DIR__ . '/../../includes/mensajes.php';

// Es un código de seguridad para el formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<?php require_once 'common/header.php'; ?>

<body>

    <div class="contenedor">
        <div class="tarjeta">

            <h2>Crear Cuenta</h2>
            <?php renderAlert(); ?>

            <?php /* Formulario de registro */ ?>
            <form action="/actions/act_register.php" method="POST">

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="correo">Correo</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="grupo-formulario">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" minlength="8" required>
                </div>

                <div class="grupo-formulario">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="8" required>
                </div>

                <!-- Token de seguridad CSRF -->
                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Crear Cuenta
                </button>

            </form>

            <p class="texto-registro">
                ¿Ya tienes una cuenta?
                <a href="/">Iniciar sesión</a>
            </p>

        </div>
    </div>

<?php require_once 'common/footer.php'; ?>
