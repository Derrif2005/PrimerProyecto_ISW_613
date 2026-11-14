<?php


?>

<?php require_once 'common/header.php'; ?>
<body>
     <?php
    // Mostrar la barra de navegación.
    require_once __DIR__ . '/common/navbar.php';
    ?>
    <div class="contenedor">

        <h1>Prestamos</h1>
        <?php/* renderAlert(); */?>
        <p>Lista de prestamos</p>
        <hr>

         <br>

        <!-- Boton para mostrar el formulario de crear -->
        <a href="/actions/act_buscar_usuarios.php?nuevo=1" class="boton">
            Nuevo usuario
        </a>

        <!-- Tabla de usuarios -->
        <table class="tabla">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>id</th>  
                    <th>id de usurio</th> 
                    <th>id de operardro</th>  
                    <th>estado</th>  
                    <th>fecha_inicio</th> 
                    <th>fecha_fin</th> 
                    <th>active</th> 
                </tr>
            </thead>

            <tbody>

</body>

  
<?php require_once 'common/footer.php'; ?>
