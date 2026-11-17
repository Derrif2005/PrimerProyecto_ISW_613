
<?php

//Comprueba si existe el token
if(empty($_SESSION['csrf_token'])){

    //Genera el token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<nav class="menu">

    <div class="menu-contenido">

        <div>
            <a href="/pages/home.php">Inicio</a>

            <a href="/pages/home.php">Mi Perfil</a>
        </div>

        <form action="/actions/logout.php" method="POST">

            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

            <button class="boton boton-eliminar" type="submit">
                Cerrar Sesión
            </button>

        </form>

    </div>

</nav>
