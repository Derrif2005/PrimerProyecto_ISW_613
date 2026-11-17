<?php
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/buscar_equipos.php';
    require_once __DIR__ . '/../../includes/auth.php';
    require_once __DIR__ . '/../../includes/mensajes.php';

    //Verificar que el usuario haya iniciado sesion
    requireLogin();

    //Comprobar el permiso para consultar equipos
    validarPermiso($conn, 'equipos_ver');
 

    if(!isset($Equipos)){
        header('Location: /actions/act_buscar_equipos.php');
        exit;
    }
?>

<?php require_once __DIR__ . '/common/header.php'; ?>

<body>

    <?php
        //Mostrar la barra de navegacion
        require_once __DIR__ . '/common/navbar.php';
    ?>

    <div class="contenedor">

        <h1>Equipos</h1>
        <?php renderAlert(); ?>
        <p>Lista de equipos</p>
        <hr>

        <!-- Seleccionar cantidad de equipos -->
        <form action="/actions/act_buscar_equipos.php" method="GET">
            <input type="hidden" name="filtro"
                value="<?= htmlspecialchars($filtro, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

            <div class="grupo-formulario">
                <label for="cantidad_equipos">Cantidad de equipos a escoger</label>

                <select name="cantidad_equipos" id="cantidad_equipos" style="width: 90px;">

                    <?php foreach(cantidadespermitidas as $opcion): ?>

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


        <!-- Buscar registros dentro de la tabla -->
        <form action="/actions/act_buscar_equipos.php" method="GET">

            <div class="grupo-formulario">
                <label for="filtro">Buscar equipos por nombre</label>
                <input type="text" id="filtro" name="filtro"
                    value="<?= htmlspecialchars($filtro, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            </div>

            <button type="submit" class="boton">Buscar en la tabla</button>
            <a href="/actions/act_buscar_equipos.php" class="boton">Mostrar todos</a>

        </form>

        <br>


        <!-- Boton para crear equipo -->
        <?php if($esAdmin): ?>

        <a href="/actions/act_buscar_equipos.php?nuevo=1" class="boton">
            Nuevo equipo
        </a>

        <?php endif; ?>


        <!-- Tabla de equipos -->
        <table class="tabla">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Categoría</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Activo</th>
                    <th>Imagen</th>
                    <th>Versión</th>
                    <th>Eliminado en</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach($Equipos as $equipo): ?>

                <tr>

                    <td><?php echo (int)$equipo['id']; ?></td>
                    <td><?php echo htmlspecialchars($equipo['categoria'] ?? 'Sin categoría', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                    </td>
                    <td><?php echo htmlspecialchars($equipo['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($equipo['codigo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>

                    <td>
                        <?php
                            if((int)$equipo['active'] === 1){
                                echo 'Activo';
                            }else{
                                echo 'Inactivo';
                            }
                        ?>
                    </td>


                    <!-- Imagenes del equipo -->
                    <td>

                        <?php
                            //Buscar las imagenes del equipo
                            $imagen = getImagenesEquipo($conn, (int)$equipo['id']);
                        ?>

                        <?php if(empty($imagen)): ?>

                        Sin imagen

                        <?php else: ?>

                        <?php foreach($imagen as $imagenEquipo): ?>

                        <img src="/actions/ver_imagen_equipo.php?id=<?php echo (int)$imagenEquipo['id']; ?>"
                            alt="Imagen del equipo" class="imagen-equipo">

                        <?php endforeach; ?>

                        <?php endif; ?>

                    </td>

                    <td><?php echo htmlspecialchars($equipo['versionn'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>

                    <td><?php echo htmlspecialchars($equipo['eliminado_en'] ?? 'No eliminado', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></td>

                    <td><?php if($esAdmin): ?>

                        <!-- Editar equipo -->
                        <a href="/actions/act_buscar_equipos.php?buscar_equipo=<?php echo urlencode($equipo['nombre']); ?>#actualizar_equipo"
                            class="boton boton-editar">
                            Editar
                        </a>


                        <!-- Eliminar equipo -->
                        <form action="/actions/act_equipos.php" method="POST"
                            onsubmit="return confirm('Desea eliminar el equipo puede producir un desastre en la base de datos')">

                            <input type="hidden" name="accion" value="eliminar">

                            <input type="hidden" name="id" value="<?php echo (int)$equipo['id']; ?>">

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'],  ENT_QUOTES | ENT_SUBSTITUTE  , 'UTF-8'); ?>">
                            <button type="submit" class="boton boton-eliminar">
                                Eliminar
                            </button>

                        </form>

                        <?php elseif($esOperador): ?>

                        <!-- Consultar equipo -->
                        <a href="/actions/act_buscar_equipos.php?buscar_id=<?= (int)$equipo['id'] ?>#equipo_consultado"
                            class="boton">
                            Consultar
                        </a>

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>


        <!-- Paginacion -->
        <div class="paginacion">

            <?php if($pagina > 1): ?>

            <a href="/actions/act_buscar_equipos.php?pagina=<?= $pagina - 1 ?>&amp;filtro=<?= urlencode($filtro) ?>"
                class="boton">Anterior</a>

            <?php endif; ?>

            <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>

            <?php if($pagina < $totalPaginas): ?>

            <a href="/actions/act_buscar_equipos.php?pagina=<?= $pagina + 1 ?>&amp;filtro=<?= urlencode($filtro) ?>"
                class="boton">Siguiente</a>

            <?php endif; ?>

        </div>

    </div>


    <!-- FORMULARIOS DE EQUIPOS -->

    <div class="contenedor">
        <div class="tarjeta">

            <h2>Buscar equipo</h2>

            <?php if($esOperador): ?>

            <h3>Equipos recientes</h3>

            <?php foreach($EquiposRecientes as $reciente): ?>

            <p>
                <a href="/actions/act_buscar_equipos.php?buscar_id=<?= (int)$reciente['id'] ?>#equipo_consultado">
                    <?= htmlspecialchars($reciente['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </a>
            </p>

            <?php endforeach; ?>

            <?php endif; ?>


            <!-- Buscar equipo por nombre -->
            <form action="/actions/act_buscar_equipos.php" method="GET">

                <div class="grupo-formulario">
                    <label for="buscar_equipo">Nombre del equipo</label>

                    <input type="text" id="buscar_equipo" name="buscar_equipo" required>
                </div>

                <button type="submit" class="boton boton-completo">
                    Buscar
                </button>

            </form>


            <!-- CONSULTAR EQUIPO -->

            <?php if($esOperador && $encontrarEquipo !== false): ?>

            <h3 id="equipo_consultado">Equipo consultado</h3>

            <p>Nombre: <?= htmlspecialchars($encontrarEquipo['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>

            <p>Codigo: <?= htmlspecialchars($encontrarEquipo['codigo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>

            <?php endif; ?>


            <!-- FORMULARIO DE ACTUALIZAR EQUIPO -->

            <?php if($esAdmin && $encontrarEquipo !== false): ?>

            <h2 id="actualizar_equipo">Actualizar equipo</h2>

            <form action="/actions/act_equipos.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="accion" value="actualizar">

                <input type="hidden" id="id" name="id"
                    value="<?php echo htmlspecialchars($encontrarEquipo['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>

                    <input type="text" id="nombre" name="nombre"
                        value="<?php echo htmlspecialchars($encontrarEquipo['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
                        maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="categoria_id">Categoría</label>

                    <select id="categoria_id" name="categoria_id" required>

                        <?php foreach($Categorias as $categoria): ?>

                        <option value="<?php echo (int)$categoria['id']; ?>"
                            <?php if((int)$categoria['id'] === (int)$encontrarEquipo['categoria_id']) echo 'selected'; ?>>

                            <?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>

                        </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="grupo-formulario">
                    <label for="codigo">Código</label>

                    <input type="text" id="codigo" name="codigo"
                        value="<?php echo htmlspecialchars($encontrarEquipo['codigo'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
                        maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="active">Estado</label>

                    <select id="active" name="active">

                        <option value="1" <?php if((int)$encontrarEquipo['active'] === 1) echo 'selected'; ?>>
                            Activo
                        </option>

                        <option value="0" <?php if((int)$encontrarEquipo['active'] === 0) echo 'selected'; ?>>
                            Inactivo
                        </option>

                    </select>
                </div>

                <div class="grupo-formulario">
                    <label for="image">Imagen</label>

                    <input type="file" id="image" name="image[]" accept="image/jpeg,image/png,image/webp" multiple>
                </div>

                <div class="grupo-formulario">
                    <label for="versionn">Version de Windows</label>

                    <input type="text" id="versionn" name="versionn" maxlength="50"
                    placeholder="Ejemplo: Windows 11 Pro" value="<?= htmlspecialchars($encontrarEquipo['versionn'] ?? '',ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8') ?>">
                </div>

                <div class="grupo-formulario">
                    <label for="eliminado_en">Eliminado en</label>

                    <input type="text" id="eliminado_en" name="eliminado_en"
                        value="<?php echo htmlspecialchars($encontrarEquipo['eliminado_en'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                </div>

                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Actualizar equipo
                </button>

            </form>

            <?php endif; ?>


            <!-- FORMULARIO PARA CREAR EQUIPO -->

            <?php if($esAdmin && $mostrarE): ?>

            <h2>Crear equipo</h2>

            <form action="/actions/act_equipos.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="accion" value="crear">

                <div class="grupo-formulario">
                    <label for="nombre">Nombre</label>

                    <input type="text" id="nombre" name="nombre" maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="categoria_id">Categoría</label>

                    <select id="categoria_id" name="categoria_id" required>

                        <?php foreach($Categorias as $categoria): ?>

                        <option value="<?php echo (int)$categoria['id']; ?>">
                            <?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
                        </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="grupo-formulario">
                    <label for="codigo">Código</label>

                    <input type="text" id="codigo" name="codigo" maxlength="50" required>
                </div>

                <div class="grupo-formulario">
                    <label for="image">Imágenes</label>

                    <input type="file" id="image" name="image[]" accept="image/jpeg,image/png,image/webp" multiple
                        required>
                </div>

                <div class="grupo-formulario">
                    <label for="versionn_nuevo">Version</label>
                    <input type="text" id="versionn_nuevo" name="versionn" maxlength="50" placeholder="Ejemplo: Windows 11 Pro">
                </div>

                <div class="grupo-formulario">
                    <label for="eliminado_en">Eliminado en</label>
                    <input type="text" id="eliminado_en" readonly value="<?= htmlspecialchars($encontrarEquipo['eliminado_en'] ?? 'No eliminado',ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8') ?>"></div>

                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

                <button type="submit" class="boton boton-completo">
                    Crear equipo
                </button>

            </form>

            <?php endif; ?>

        </div>
    </div>

    <?php require_once __DIR__ . '/common/footer.php'; ?>