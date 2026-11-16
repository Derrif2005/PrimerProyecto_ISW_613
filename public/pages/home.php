
<?php

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/buscar_usuarios.php';

requireLogin();

//obtiene el rol actual del usuario
$rolActual = obtenerRolActual($conn, (int)$_SESSION['user_id']);

//comprueba que el usuario este activo
if($rolActual === false){
    http_response_code(403);
    exit('Acceso denegado');
}

require_once __DIR__ . '/common/header.php';

?>

<body>
    <?php
    //Mostrar la barra de navegacion
    require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">
        <div class="cuadricula">

            <?php if($rolActual === 1): ?>

            <!-- div de usuarios -->
            <div class="tarjeta-inicio">
                <h2>usuarios</h2>
                <p>Consultar los usuarios</p>
                <a href="/actions/act_buscar_usuarios.php" class="boton boton-completo">Abrir</a>
            </div>


            <!-- div de categorias -->
            <div class="tarjeta-inicio">
                <h2>Categorias</h2>
                <p>Consultar las categorias</p>
                <a href="/actions/act_buscar_categoria.php" class="boton boton-completo">Abrir</a>
            </div>

            <?php endif; ?>


            <?php if($rolActual === 1 || $rolActual === 2): ?>

            <!-- div de equipos -->
            <div class="tarjeta-inicio">
                <h2>equipos</h2>
                <p>Consultar los equipos</p>
                <a href="/actions/act_buscar_equipos.php" class="boton boton-completo">Abrir</a>
            </div>

            <?php endif; ?>


            <?php if($rolActual === 1): ?>

            <!-- div de reportes -->
            <div class="tarjeta-inicio">
                <h2>reportes</h2>
                <p>Consultar los reportes</p>
                <a href="/actions/act_buscar_reporte.php" class="boton boton-completo">Abrir</a>
            </div>

            <?php endif; ?>


            <?php if($rolActual === 2): ?>

            <!-- div de prestamos -->
            <div class="tarjeta-inicio">
                <h2>Prestamos</h2>
                <p>Consultar los Prestamos</p>
                <a href="/actions/act_buscar_prestamos.php" class="boton boton-completo">Abrir</a>
            </div>

            <?php endif; ?>


            <?php if($rolActual !== 1 && $rolActual !== 2): ?>

            <p>No tiene permisos asignados</p>

            <?php endif; ?>

        </div>
    </div>

    <?php

    require_once __DIR__ . '/common/footer.php';

    ?>
