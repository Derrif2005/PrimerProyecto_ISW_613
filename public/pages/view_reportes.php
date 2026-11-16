
<?php

    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/mensajes.php';

    //Verifica que el usuario tenga una sesion iniciada
    requireLogin();

    //Comprueba que tenga permiso para ver reportes
    validarPermiso($conn, 'reportes_ver');

    //Si ingresa directamente a la vista, carga la accion
    if(!isset($Reportes)){
        header('Location: /actions/act_buscar_reporte.php');
        exit;
    }

?>

<?php require_once 'common/header.php'; ?>

<body>

    <?php
    //Mostrar la barra de navegacion
    require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">
        <h1>Reportes</h1>
        <?php renderAlert(); ?>
        <p>Reporte de equipos mas prestados</p>
        <hr>
        <br>

        <!-- Formulario para buscar por rango de fechas -->
        <div class="tarjeta">
            <h2>Buscar reporte</h2>

            <form action="/actions/act_buscar_reporte.php" method="GET">

                <div class="grupo-formulario">
                    <label for="fecha_inicio">Fecha de inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio"
                        value="<?= htmlspecialchars($fecha_inicio, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
                </div>

                <div class="grupo-formulario">
                    <label for="fecha_fin">Fecha de finalizacion</label>
                    <input type="date" id="fecha_fin" name="fecha_fin"
                        value="<?= htmlspecialchars($fecha_fin, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
                </div>

                <button type="submit" class="boton boton-completo">
                    Buscar reporte
                </button>

            </form>
        </div>

        <!-- Tabla de reportes -->
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Codigo</th>
                    <th>Cantidad de prestamos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Reportes as $reporte): ?>
                <tr>
                    <td><?= (int)$reporte['id'] ?></td>
                    <td><?= htmlspecialchars($reporte['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($reporte['codigo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= (int)$reporte['cantidad'] ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($Reportes)): ?>
                <tr>
                    <td colspan="4">No se encontraron resultados</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Formulario para exportar el reporte -->
        <?php if (!empty($Reportes)): ?>
        <form action="/actions/act_reportes.php" method="GET">

            <input type="hidden" name="fecha_inicio"
                value="<?= htmlspecialchars($fecha_inicio, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

            <input type="hidden" name="fecha_fin"
                value="<?= htmlspecialchars($fecha_fin, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

            <button type="submit" class="boton">
                Exportar CSV
            </button>

        </form>
        <?php endif; ?>

    </div>

    <?php require_once 'common/footer.php'; ?>
</body>
