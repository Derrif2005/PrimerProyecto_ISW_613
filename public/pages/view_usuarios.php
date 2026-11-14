<?php
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/buscar_usuarios.php';
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/buscar_categorias.php';

    // Verificar que el usuario tenga permiso
    requireLogin();

    validarAdmin($conn);

    validarPermiso($conn, 'usuarios_ver');
?>

<?php require_once 'common/header.php'; ?>

<body>

    <?php
        // Mostrar la barra de navegacion
        require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">

        <h1>Usuarios</h1>
        <p>Lista de usuarios</p>
        <hr>

         <form action="/actions/act_buscar_usuarios.php" method="GET">

            <div class="grupo-formulario">
                <label for="filtro">Buscar usuarios por nombre</label>

                <input type="text" id="filtro" name="filtro" maxlength="100"
                    value="<?= htmlspecialchars($filtro, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>

            <button type="submit" class="boton">Buscar en la tabla</button>

            <a href="/actions/act_buscar_usuarios.php" class="boton">Mostrar todos</a>

        </form>


        <!-- Seleccionar cantidad de usuarios -->
        <form action="/actions/act_buscar_usuarios.php" method="GET">

            <div class="grupo-formulario">
                <label for="cantidad_usuarios">Cantidad de usuarios a escoger</label>

                <select name="cantidad_usuarios" id="cantidad_usuarios">

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

        <!-- Boton para mostrar el formulario de crear -->
        <a href="/actions/act_buscar_usuarios.php?nuevo=1" class="boton">
            Nuevo usuario
        </a>

        <!-- Tabla de usuarios -->
        <table class="tabla">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($users as $user): ?>

                <tr>
                    <td><?php echo (int)$user['id']; ?></td>

                    <td><?php echo htmlspecialchars($user['nombre']); ?></td>

                    <td><?php echo htmlspecialchars($user['correo']); ?></td>

                    <td><?php echo htmlspecialchars($user['rol']); ?></td>

                    <td>
                        <?php
                                if ((int)$user['active'] === 1) {
                                    echo 'Activo';
                                } else {
                                    echo 'Inactivo';
                                }
                            ?>
                    </td>

                    <td>

                        <!-- Editar usuario -->
                        <a href="/actions/act_buscar_usuarios.php?buscar_correo=<?php echo urlencode($user['correo']); ?>"
                            class="boton boton-editar">
                            Editar
                        </a>

                        <!-- Eliminar usuario -->
                        <form action="/actions/act_usuarios.php" method="POST"
                            onsubmit="return confirm('Desea eliminar el usuario puede producir un desastre en la base de datos')">

                            <input type="hidden" name="accion" value="eliminar">

                            <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">

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

            <a href="/actions/act_buscar_usuarios.php?pagina=<?= $pagina - 1 ?>" class="boton">
                Anterior
            </a>

            <?php endif; ?>

            <span>
                Página <?= $pagina ?> de <?= $totalPaginas ?>
            </span>

            <?php /* Comprobar si existen mas paginas */ ?>
            <?php if ($pagina < $totalPaginas): ?>

            <a href="/actions/act_buscar_usuarios.php?pagina=<?= $pagina + 1 ?>" class="boton">
                Siguiente
            </a>

            <?php endif; ?>

        </div>

    </div>


    <!-- FORMULARIOS DE USUARIOS -->

    <div class="contenedor">
        <div class="tarjeta">

            <h2>Buscar usuario</h2>

            <!-- Buscar usuario por correo -->
            <form action="/actions/act_buscar_usuarios.php" method="GET">

                <div class="grupo-formulario">
                    <label for="buscar_correo">Correo</label>

                    <input type="text" id="buscar_correo" name="buscar_correo" required>
                </div>

                <button type="submit" class="boton boton-completo">
                    Buscar
                </button>

            </form>


            <!-- FORMULARIO DE ACTUALIZAR -->

            <?php if ($encontrarUsuario !== false): ?>

            <h2>Actualizar usuario</h2>

            <form action="/actions/act_usuarios.php" method="POST">

                <input type="hidden" name="accion" value="actualizar">

                <div class="grupo-formulario">
                    <label for="correo">Correo</label>

                    <input type="email" id="correo" name="correo"
                        value="<?php echo htmlspecialchars($encontrarUsuario['correo'], ENT_QUOTES, 'UTF-8'); ?>"
                        required>
                </div>

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>

                    <input type="text" id="nombre" name="nombre"
                        value="<?php echo htmlspecialchars($encontrarUsuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                        maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="id_rol">Tipo de rol</label>

                    <select id="id_rol" name="id_rol">

                        <option value="2" <?php if ((int)$encontrarUsuario['id_rol'] === 2) echo 'selected'; ?>>
                            Operador
                        </option>

                        <option value="1" <?php if ((int)$encontrarUsuario['id_rol'] === 1) echo 'selected'; ?>>
                            Administrador
                        </option>

                        <option value="3" <?php if ((int)$encontrarUsuario['id_rol'] === 3) echo 'selected'; ?>>
                            Profesor
                        </option>

                    </select>
                </div>

                <div class="grupo-formulario">
                    <label for="active">Estado</label>

                    <select id="active" name="active">

                        <option value="1" <?php if ((int)$encontrarUsuario['active'] === 1) echo 'selected'; ?>>
                            Activo
                        </option>

                        <option value="0" <?php if ((int)$encontrarUsuario['active'] === 0) echo 'selected'; ?>>
                            Inactivo
                        </option>

                    </select>
                </div>

                <input type="hidden" id="id" name="id"
                    value="<?php echo htmlspecialchars($encontrarUsuario['id'], ENT_QUOTES, 'UTF-8'); ?>">

                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Actualizar cuenta
                </button>

            </form>

            <?php endif; ?>


            <!-- FORMULARIO PARA CREAR USUARIO -->

            <?php if ($mostrarU): ?>

            <h2>Crear usuario</h2>

            <form action="/actions/act_usuarios.php" method="POST">

                <input type="hidden" name="accion" value="crear">

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

                <div class="grupo-formulario">
                    <label for="id_rol">Tipo de rol</label>

                    <select id="id_rol" name="id_rol">

                        <option value="2">Operador</option>
                        <option value="1">Administrador</option>
                        <option value="3">Profesor</option>

                    </select>
                </div>

                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Crear usuario
                </button>

            </form>

            <?php endif; ?>

        </div>
    </div>

    <?php require_once 'common/footer.php'; ?>