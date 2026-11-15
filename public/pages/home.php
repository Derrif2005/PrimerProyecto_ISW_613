<?php   
 
 session_start();


require_once __DIR__ . '/../../includes/auth.php';

    requireLogin();

 require_once __DIR__ . '/common/header.php';


?>

<body>
    <?php
    // Mostrar la barra de navegación.
    require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">
        <div class="cuadricula">
            <!-- div de usuarios -->
            <div class="tarjeta-inicio">
                <h2>usuarios</h2>
                <p>Consultar los usuarios</p>
                <a href="/actions/act_buscar_usuarios.php" class="boton boton-completo">Abrir</a>
            </div>


            <!-- div de categorias -->
            <div class="tarjeta-inicio ">
                <h2>Categorias</h2>
                <p>Consultar las categorias</p>
                <a href="/actions/act_buscar_categoria.php" class="boton boton-completo">Abrir</a>
            </div>



            <!-- div de equipos -->
            <div class="tarjeta-inicio ">
                <h2>equipos</h2>
                <p>Consultar los equipos</p>
                <a href="/actions/act_buscar_equipos.php" class="boton boton-completo">Abrir</a>
            </div>


            <!-- div de reportes -->
            <div class="tarjeta-inicio">
                <h2>reportes</h2>
                <p>Consultar los reportes</p>
                <a href="/pages/view_reportes.php" class="boton boton-completo">Abrir</a>
            </div>

            <!-- div de prestamos -->
            <div class="tarjeta-inicio">
                <h2>Prestamos</h2>
                <p>Consultar los Prestamos</p>
                <a href="/actions/act_buscar_prestamos.php" class="boton boton-completo">Abrir</a>
            </div>


        </div>
    </div>
    </div>

    <?php

    require_once __DIR__ . '/common/footer.php';

?>