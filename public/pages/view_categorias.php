<?php
 require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/buscar_categorias.php';
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/cookies.php';

    // Verificar que el usuario haya iniciado sesion
    requireLogin();

    // Comprobar que sea administrador
    validarAdmin($conn);
?>

<?php require_once 'common/header.php'; ?>

<body>

    <?php
        // Mostrar la barra de navegacion
        require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">

        <h1>Categorías</h1>
        <p>Lista de categorías</p>
        <hr>

        <!-- Seleccionar cantidad de categorias -->
        <form action="/actions/act_buscar_categoria.php" method="GET">

            <div class="grupo-formulario">
                <label for="cantidad_categorias">Cantidad de categorías a escoger</label>

                <select name="cantidad_categorias" id="cantidad_categorias" style="width: 90px;">

                    <?php foreach (cantidadespermitidas as $opcion): ?>

                    <option value="<?= (int)$opcion ?>" <?= (int)$limite === (int)$opcion ? 'selected' : '' ?>>
                        <?= (int)$opcion ?>
                    </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <button type="submit" class="boton">
                Seleccionar
            </button>

        </form>

        <br>

        <!-- Boton para crear categoria -->
        <a href="/actions/act_buscar_categoria.php?nuevo=1" class="boton">
            Nueva categoría
        </a>

        <!-- Tabla de categorias -->
        <table class="tabla">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Días máximos de préstamo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($Categorias as $categoria): ?>

                <tr>

                    <td><?php echo (int)$categoria['id']; ?></td>

                    <td>
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </td>

                    <td>
                        <?php echo (int)$categoria['dias_max_prestamo']; ?>
                    </td>

                    <td>
                        <?php
                                if ((int)$categoria['active'] === 1) {
                                    echo 'Activo';
                                } else {
                                    echo 'Inactivo';
                                }
                            ?>
                    </td>

                    <td>

                        <!-- Editar categoria -->
                        <a href="/actions/act_buscar_categoria.php?buscar_categoria=<?php echo urlencode($categoria['nombre']); ?>"
                            class="boton boton-editar">
                            Editar
                        </a>

                        <!-- Eliminar categoria -->
                        <form action="/actions/act_categoria.php" method="POST"
                            onsubmit="return confirm('Desea eliminar la categoria puede producir un desastre en la base de datos')">

                            <input type="hidden" name="accion" value="eliminar">

                            <input type="hidden" name="id" value="<?php echo (int)$categoria['id']; ?>">

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                            <button type="submit" class="boton boton-eliminar">
                                Eliminar
                            </button>

                        </form>

                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

        <!-- Paginacion -->
        <div class="paginacion">

            <?php /* Comprobar que no estamos en la primera pagina */ ?>
            <?php if ($pagina > 1): ?>

            <a href="/actions/act_buscar_categoria.php?pagina=<?= $pagina - 1 ?>" class="boton">
                Anterior
            </a>

            <?php endif; ?>

            <span>
                Página <?= $pagina ?> de <?= $totalPaginas ?>
            </span>

            <?php /* Comprobar si existen mas paginas */ ?>
            <?php if ($pagina < $totalPaginas): ?>

            <a href="/actions/act_buscar_categoria.php?pagina=<?= $pagina + 1 ?>" class="boton">
                Siguiente
            </a>

            <?php endif; ?>

        </div>

    </div>


    <!-- FORMULARIOS DE CATEGORIAS -->

    <div class="contenedor">
        <div class="tarjeta">

            <h2>Buscar categoría</h2>

            <!-- Buscar categoria por nombre -->
            <form action="/actions/act_buscar_categoria.php" method="GET">

                <div class="grupo-formulario">
                    <label for="buscar_categoria">Nombre de la categoría</label>

                    <input type="text" id="buscar_categoria" name="buscar_categoria" required>
                </div>

                <button type="submit" class="boton boton-completo">
                    Buscar
                </button>

            </form>


            <!-- FORMULARIO PARA ACTUALIZAR CATEGORIA -->

            <?php if ($encontrarCategoria !== false): ?>

            <h2>Actualizar categoría</h2>

            <form action="/actions/act_categoria.php" method="POST">

                <input type="hidden" name="accion" value="actualizar">

                <input type="hidden" id="id" name="id"
                    value="<?php echo htmlspecialchars($encontrarCategoria['id'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>

                    <input type="text" id="nombre" name="nombre"
                        value="<?php echo htmlspecialchars($encontrarCategoria['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                        maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="dias_max_prestamo">Días máximos de préstamo</label>

                    <input type="number" id="dias_max_prestamo" name="dias_max_prestamo" min="1" step="1"
                        value="<?php echo htmlspecialchars($encontrarCategoria['dias_max_prestamo'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="grupo-formulario">
                    <label for="active">Estado</label>

                    <select id="active" name="active">

                        <option value="1" <?php if ((int)$encontrarCategoria['active'] === 1) echo 'selected'; ?>>
                            Activo
                        </option>

                        <option value="0" <?php if ((int)$encontrarCategoria['active'] === 0) echo 'selected'; ?>>
                            Inactivo
                        </option>

                    </select>
                </div>

                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Actualizar categoría
                </button>

            </form>

            <?php endif; ?>


            <!-- FORMULARIO PARA CREAR CATEGORIA -->

            <?php if ($mostrar): ?>

            <h2>Crear categoría</h2>

            <form action="/actions/act_categoria.php" method="POST">

                <input type="hidden" name="accion" value="crear">

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>

                    <input type="text" id="nombre" name="nombre" maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="dias_max_prestamo">Días máximos de préstamo</label>

                    <input type="number" id="dias_max_prestamo" name="dias_max_prestamo" min="1" step="1" required>
                </div>

                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Crear categoría
                </button>

            </form>

            <?php endif; ?>

        </div>
    </div>
    <?php require_once 'common/footer.php'; ?>