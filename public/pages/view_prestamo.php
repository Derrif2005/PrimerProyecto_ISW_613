
<?php

    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/buscar_prestamos.php';
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/mensajes.php';

    //Verifica que el usuario tenga una sesion iniciada
    requireLogin();

    //Comprueba que tenga permiso para ver prestamos
    validarPermiso($conn, 'prestamos_ver');

    //Si ingresa directamente a la vista, carga la accion
    if (!isset($Prestamos)) {
        header('Location: /actions/act_buscar_prestamos.php');
        exit;
    }

    //Estados de los prestamos
    $estados = [
        1 => 'Solicitado',
        2 => 'Aprobado',
        3 => 'Entregado',
        4 => 'Devuelto',
        5 => 'Cancelado'
    ];

?>

<?php require_once 'common/header.php'; ?>

<body>

    <?php
    //Mostrar la barra de navegacion
    require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">
        <h1>Prestamos</h1>
        <?php renderAlert(); ?>
        <p>Lista de prestamos</p>
        <hr>
        <br>

        <!-- Boton para mostrar el formulario de crear -->
        <a href="/actions/act_buscar_prestamos.php?nuevo=1" class="boton">
            Nuevo prestamo
        </a>

        <!-- Tabla de prestamos -->
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Operador</th>
                    <th>Estado</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Prorrogas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Prestamos as $prestamo): ?>
                <tr>
                    <td><?= (int)$prestamo['id'] ?></td>
                    <td><?= htmlspecialchars($prestamo['usuario'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($prestamo['operador'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= $estados[(int)$prestamo['estado']] ?? 'Desconocido' ?></td>
                    <td><?= htmlspecialchars($prestamo['fecha_inicio'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($prestamo['fecha_fin'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= (int)$prestamo['prorrogas'] ?></td>
                    <td>
                        <a href="/actions/act_buscar_prestamos.php?buscar_id=<?= (int)$prestamo['id'] ?>" class="boton">
                            Actualizar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($Prestamos)): ?>
                <tr>
                    <td colspan="8">No se encontraron prestamos</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- FORMULARIOS DE PRESTAMOS -->
    <div class="contenedor">
        <div class="tarjeta">
            <h2>Buscar prestamo</h2>
            <!-- Buscar prestamo por id -->
            <form action="/actions/act_buscar_prestamos.php" method="GET">
                <div class="grupo-formulario">
                    <label for="buscar_id">ID del prestamo</label>
                    <input type="number" id="buscar_id" name="buscar_id" min="1" required>
                </div>
                <button type="submit" class="boton boton-completo">
                    Buscar
                </button>
            </form>

            <!-- FORMULARIO PARA ACTUALIZAR PRESTAMOS -->
            <?php if ($mostrarA): ?>
            <h2>Actualizar prestamo</h2>
            <form action="/actions/act_prestamos.php" method="POST">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id" value="<?= (int)$Prestamo['id'] ?>">

                <!-- ID del prestamo -->
                <div class="grupo-formulario">
                    <label>ID del prestamo</label>
                    <input type="number" value="<?= (int)$Prestamo['id'] ?>" readonly>
                </div>

                <!-- Usuario del prestamo -->
                <div class="grupo-formulario">
                    <label>Usuario</label>
                    <input type="text" value="<?= htmlspecialchars($Prestamo['usuario'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" readonly>
                </div>

                <!-- Operador del prestamo -->
                <div class="grupo-formulario">
                    <label>Operador</label>
                    <input type="text" value="<?= htmlspecialchars($Prestamo['operador'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" readonly>
                </div>

                <!-- Fecha de inicio -->
                <div class="grupo-formulario">
                    <label>Fecha de inicio</label>
                    <input type="text" value="<?= htmlspecialchars($Prestamo['fecha_inicio'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" readonly>
                </div>

                <!-- Fecha de finalizacion -->
                <div class="grupo-formulario">
                    <label>Fecha de finalizacion</label>
                    <input type="text" value="<?= htmlspecialchars($Prestamo['fecha_fin'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" readonly>
                </div>

                <!-- Estado actual del prestamo -->
                <div class="grupo-formulario">
                    <label>Estado actual</label>
                    <input type="text" value="<?= $estados[(int)$Prestamo['estado']] ?? 'Desconocido' ?>" readonly>
                </div>

                <!-- Cantidad de prorrogas utilizadas -->
                <div class="grupo-formulario">
                    <label>Prorrogas utilizadas</label>
                    <input type="number" value="<?= (int)$Prestamo['prorrogas'] ?>" readonly>
                </div>

                <!-- Seleccionar el nuevo estado -->
                <?php if (in_array((int)$Prestamo['estado'], [1, 2, 3], true)): ?>
                <div class="grupo-formulario">
                    <label for="estado">Nuevo estado</label>
                    <select id="estado" name="estado" required>
                        <option value="">Seleccione un estado</option>

                        <?php if ((int)$Prestamo['estado'] === 1): ?>
                        <option value="2">Aprobado</option>
                        <option value="5">Cancelado</option>

                        <?php elseif ((int)$Prestamo['estado'] === 2): ?>
                        <option value="3">Entregado</option>
                        <option value="5">Cancelado</option>

                        <?php elseif ((int)$Prestamo['estado'] === 3): ?>
                        <option value="4">Devuelto</option>
                        <?php endif; ?>

                    </select>
                </div>
                <?php endif; ?>

                <!-- Equipos asociados al prestamo -->
                <div class="grupo-formulario">
                    <h3>Equipos del prestamo</h3>

                    <!-- Devuelve todos los equipos pendientes -->
                    <?php if ((int)$Prestamo['estado'] === 3): ?>
                    <div>
                        <input type="checkbox" name="devolver_todo" id="devolver_todo" value="1">
                        <label for="devolver_todo">Devolver todos los equipos</label>
                    </div>
                    <hr>
                    <?php endif; ?>

                    <!-- Recorre los equipos del prestamo -->
                    <?php foreach ($Detalles as $detalle): ?>
                    <div>

                        <!-- Comprueba si el equipo esta pendiente de devolver -->
                        <?php if ((int)$Prestamo['estado'] === 3 && $detalle['fecha_devolucion'] === null): ?>
                        <input type="checkbox" name="detalle_id[]" id="detalle_<?= (int)$detalle['detalle_id'] ?>"
                            value="<?= (int)$detalle['detalle_id'] ?>">
                        <?php endif; ?>

                        <label>
                            <?= htmlspecialchars($detalle['equipo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>

                            <?= htmlspecialchars($detalle['codigo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </label>

                        <!-- Muestra la fecha de devolucion del equipo -->
                        <p>Fecha de devolucion:
                            <?= htmlspecialchars($detalle['fecha_devolucion'] ?? 'Sin devolucion', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </p>
                    </div>
                    <hr>
                    <?php endforeach; ?>
                </div>

                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                <!-- Comprueba si se puede actualizar el prestamo -->
                <?php if (in_array((int)$Prestamo['estado'], [1, 2, 3], true)): ?>
                <button type="submit" class="boton boton-completo">
                    Actualizar prestamo
                </button>
                <?php endif; ?>

            </form>

            <!-- FORMULARIO PARA PRORROGAR PRESTAMOS -->
            <?php if ((int)$Prestamo['estado'] === 3 && (int)$Prestamo['prorrogas'] === 0): ?>

            <hr>
            <h2>Prorrogar prestamo</h2>

            <form action="/actions/act_prestamos.php" method="POST">
                <input type="hidden" name="accion" value="prorrogar">
                <input type="hidden" name="id" value="<?= (int)$Prestamo['id'] ?>">

                <!-- Fecha de finalizacion actual -->
                <div class="grupo-formulario">
                    <label>Fecha de finalizacion actual</label>
                    <input type="text" value="<?= htmlspecialchars($Prestamo['fecha_fin'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" readonly>
                </div>

                <!-- Nueva fecha de finalizacion -->
                <div class="grupo-formulario">
                    <label for="nueva_fecha_fin">Nueva fecha de finalizacion</label>
                    <input type="datetime-local" id="nueva_fecha_fin" name="nueva_fecha_fin" required>
                </div>

                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                <button type="submit" class="boton boton-completo">
                    Prorrogar prestamo
                </button>

            </form>

            <?php elseif ((int)$Prestamo['prorrogas'] > 0): ?>

            <p>Este prestamo ya utilizo su prorroga.</p>

            <?php endif; ?>

            <?php endif; ?>

            <!-- FORMULARIO PARA CREAR PRESTAMOS -->
            <?php if ($mostrarP): ?>
            <h2>Crear prestamo</h2>
            <form action="/actions/act_prestamos.php" method="POST">
                <input type="hidden" name="accion" value="crear">

                <!-- Seleccionar el usuario -->
                <div class="grupo-formulario">
                    <label for="usuario_id">Usuario</label>
                    <select id="usuario_id" name="usuario_id" required>
                        <option value="">Seleccione un usuario</option>
                        <?php foreach ($Usuarios as $usuario): ?>
                        <option value="<?= (int)$usuario['id'] ?>">
                            <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fecha de inicio del prestamo -->
                <div class="grupo-formulario">
                    <label for="fecha_inicio">Fecha de inicio</label>
                    <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" required>
                </div>

                <!-- Fecha de finalizacion del prestamo -->
                <div class="grupo-formulario">
                    <label for="fecha_fin">Fecha de finalizacion</label>
                    <input type="datetime-local" id="fecha_fin" name="fecha_fin" required>
                </div>

                <!-- Seleccionar multiples equipos -->
                <div class="grupo-formulario">
                    <h3>Seleccione los equipos</h3>
                    <?php foreach ($Equipos as $equipo): ?>
                    <div>
                        <input type="checkbox" name="equipo_id[]" id="equipo_<?= (int)$equipo['id'] ?>"
                            value="<?= (int)$equipo['id'] ?>">
                        <label for="equipo_<?= (int)$equipo['id'] ?>">
                            <?= htmlspecialchars($equipo['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>

                            <?= htmlspecialchars($equipo['codigo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                <button type="submit" class="boton boton-completo">
                    Crear prestamo
                </button>

            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once 'common/footer.php'; ?>
