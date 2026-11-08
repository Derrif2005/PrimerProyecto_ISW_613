<?php
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../config/buscar_equipos.php';
    require_once __DIR__ . '/../../config/auth.php';


  // The users list is only reachable with a valid, active session
  requireLogin();

 
?>
<?php require_once 'common/header.php'; ?>

<body>
    <?php
    // Mostrar la barra de navegación.
    require_once __DIR__ . '/common/navbar.php';
    ?>
    <div class="container-fluid">
        <div class="jumbotron">
            <h1 class="display-4">Equipos</h1>
            <p class="lead">Lista de equipos</p>
            <hr class="my-4">
        </div>

        <?php /*btn para mostrar el formulario de crear*/ ?>
        <a href="/actions/act_buscar_equipos.php?nuevo=1" class="btn btn-primary mb-3">Nuevo equipo</a>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Categoria</th>
                    <th>Nombre</th>
                    <th>Codigo</th>
                    <th>Activo</th>
                    <th>Imagen</th>
                    <th>version</th>
                    <th>Eliminado_en</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                <tr>
                    <td scope="row"><?php echo (int) $equipo['id']; ?></td>
                    <td><?php echo htmlspecialchars($equipo['Categoria']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['Codigo']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['Activo']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['Imagen']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['version']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['Eliminado_en']); ?></td>

                    ?>
                    </td>
                    <td>
                        <?php /*este codigo se puede utilizar*/ ?>
                        <a href="/actions/act_buscar_equipos.php?buscar_equipo=<?php echo urlencode($equipo['equipo']); ?>"
                            class="btn btn-warning btn-sm">
                            Editar
                        </a>
                        <form action="/actions/act_equipos.php" method="POST" class="d-inline"
                            onsumit="return confirm('Desea eliminar el equipo puede producir un desastre en la base de datos')">

                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo (int)$equipo['id']; ?>">
                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">

                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php /*Parte de label para modificar los usuarios*/ ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                    </div>
                    <div class="card-body">

                        <form action="/actions/act_buscar_equipos.php" method="GET">
                            <input type="text" class="form-control" id="buscar_equipo" name="buscar_equipo" required>

                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                        </form>

                        <?php/*Formulario de actualizar*/?>
                        <?php if($encontrarEquipo !== false): ?>

                        <form action="/actions/act_equipos.php" method="POST">
                            <?php /*se manera un if y un elseif donde se para una valor igual a actualizar el cual entrara en el if y ejecutara el insert*/?>
                            <input type="hidden" name="accion" value="actualizar">



                            <?php//Continuar aqui?>

                             <div class="form-group">
                                <input type="hidden" class="form-control" id="id" name="id"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['id'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>


                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50" required>
                            </div>

                            <div class="form-group">
                                <label for="categoria">Categoria</label>
                                <input type="text" class="form-control" id="categoria" name="categoria"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['Categoria'], ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50" required>
                            </div>

                            <div class="form-group">
                                <label for="codigo">Codigo</label>
                                <input type="text" class="form-control" id="codigo" name="codigo"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['Codigo'], ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50" required>

                            
                            <div class="form-group">
                                <label for="active">Estado</label>
                                <select class="form-control" id="active" name="active">
                                    <option value="1"
                                        <?php if((int)$encontrarEquipo['active'] === 1)echo 'selected'; ?>>
                                        Activo
                                    </option>

                                    <option value="0"
                                        <?php if((int)$encontrarEquipo['active'] === 0)echo 'selected'; ?>>
                                        Inactivo
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="imagen">Imagen</label>
                                <input type="text" class="form-control" id="imagen" name="imagen"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['Imagen'], ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50" required>
                            </div>



                            <div class="form-group">
                                <label for="version">Version</label>
                                <input type="hidden" class="form-control" id="version" name="version"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['version'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>

                            <div class="form-group">
                                <label for="eliminado_en">Eliminado en</label>
                                <input type="hidden" class="form-control" id="eliminado_en" name="eliminado_en"
                                    value="<?php echo htmlspecialchars($encontrarEquipo['eliminado_en'], ENT_QUOTES, 'UTF-8') ?>">

                            </div>


                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">


                            <button type="submit" class="btn btn-primary btn-block">Actualizar cuenta</button>
                        </form>
                        <?php endif; ?>





                        <?php if($mostrarE): ?>

                        <?php /*Formulario de registro para crear equipos*/ ?>
                        <form action="/actions/act_equipos.php" method="POST" enctype="multipart/form-data">

                            <?php /*se manera un if y un elseif donde se para una valor igual a crear el cual entrara en el if y ejecutara el insert*/ ?>

                            <input type="hidden" name="accion" value="crear">

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50"
                                    required>
                            </div>

                            
                            <div class="form-group">
                                <label for="categoria_id">categoria</label>
                                
                                <select class="form-control" id="categoria_id" name="categoria_id" required>
                                   <?php foreach($Categorias as $categoria): ?>
                                    <option value="<?php echo (int)$categoria["id"];?>">
                                        <?php echo htmlspecialchars($categoria["nombre"]);?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="codigo">codigo</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" maxlength="50"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="image">Imagenes</label>
                                <input type="file" class="form-control" id="image" name="image[]" accept="image/jpeg,image/png,image/webp" multiple required>
                            </div>

                           

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">

                            <button type="submit" class="btn btn-primary btn-block">Crear Equipo</button>
                        </form>
                        <?php endif; ?>


                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require_once 'common/footer.php'; ?>