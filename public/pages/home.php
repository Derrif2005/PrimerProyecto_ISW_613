<?php   
 
 session_start();
 require_once __DIR__ . '/common/header.php';
?>

<body>
     <?php
    // Mostrar la barra de navegación.
    require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="container mt-5">
        <div class="row">
        <!-- div de usuarios -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    usuarios
                </div>
                <div class="card-body">
                    <p>Consultar los usuarios</p>
                      <a href="/pages/view_usuarios.php" class="btn btn-primary btn-block">Abrir</a>
                </div>
            </div>
        </div>   

 <!-- div de categorias -->
        <div class="col-md-6 mb-3 ">
            <div class="card">
                <div class="card-header">
                    categorias
                </div>
                <div class="card-body">
                    <p>Consultar las categorias</p>
                      <a href="/pages/view_categorias.php" class="btn btn-primary btn-block">Abrir</a>
                </div>
            </div>
        </div>   

        
 <!-- div de equipos -->
        <div class="col-md-6 mb-3 ms-auto">
            <div class="card">
                <div class="card-header ">
                    equipos
                </div>
                <div class="card-body">
                    <p>Consultar los equipos</p>
                      <a href="/pages/view_equipos.php" class="btn btn-primary btn-block">Abrir</a>
                </div>
            </div>
        </div>   

        <!-- div de reportes -->
        <div class="col-md-6 mb-3 ms-auto">
            <div class="card">
                <div class="card-header">
                    reportes
                </div>
                <div class="card-body">
                    <p>Consultar los reportes</p>
                      <a href="/pages/view_reportes.php" class="btn btn-primary btn-block">Abrir</a>
                </div>
            </div>
        </div>   
    </div>
    </div>

 <?php
    require_once __DIR__ . '/common/footer.php';
    ?>