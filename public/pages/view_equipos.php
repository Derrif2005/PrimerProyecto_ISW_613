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
                        <a href="/actions/act_buscar_usuarios.php?buscar_correo=<?php echo urlencode($user['correo']); ?>" class="btn btn-warning btn-sm">
                            Editar
                        </a>
                        <form action="/actions/act_usuarios.php" method="POST" class="d-inline" onsumit="return confirm('Desea eliminar el usuario puede producir un desastre en la base de datos')">
                    
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">
                            
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

                        <form action="/actions/act_buscar_usuarios.php" method="GET">
                            <input type="text" class="form-control" id="buscar_correo" name="buscar_correo" required>

                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                        </form>

                        <?php/*Formulario de actualizar*/?>
                        <?php if($encontrarUsuario !== false): ?>

                        <form action="/actions/act_usuarios.php" method="POST">
                            <?php /*se manera un if y un elseif donde se para una valor igual a actualizar el cual entrara en el if y ejecutara el insert*/?>
                            <input type="hidden" name="accion" value="actualizar">

                            <div class="form-group">
                                <label for="correo">Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo"
                                    value="<?php echo htmlspecialchars($encontrarUsuario['correo'], ENT_QUOTES, 'UTF-8') ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($encontrarUsuario['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50" required>
                            </div>



                            <div class="form-group">
                                <label for="id_rol">Tipo de rol</label>
                                <select class="form-control" id="id_rol" name="id_rol">
                                    <option value="2"
                                        <?php if((int)$encontrarUsuario['id_rol'] === 2) echo 'selected'?>>
                                        Operardor
                                    </option>

                                    <option value="1"
                                        <?php if((int)$encontrarUsuario['id_rol'] === 1) echo 'selected'?>>
                                        Administrador
                                    </option>

                                    <option value="3"
                                        <?php if((int)$encontrarUsuario['id_rol'] === 3) echo 'selected'?>>
                                        Profesor
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="active">Estado</label>
                                <select class="form-control" id="active" name="active">
                                    <option value="1"
                                        <?php if((int)$encontrarUsuario['active'] === 1)echo 'selected'; ?>>
                                        Activo
                                    </option>

                                    <option value="0"
                                        <?php if((int)$encontrarUsuario['active'] === 0)echo 'selected'; ?>>
                                        Inactivo
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="hidden" class="form-control" id="id" name="id"
                                    value="<?php echo htmlspecialchars($encontrarUsuario['id'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">

                            <button type="submit" class="btn btn-primary btn-block">Actualizar cuenta</button>
                        </form>
                        <?php endif; ?>


                        <?php if($mostrarU): ?>

                        <?php /*Formulario de registro*/ ?>
                        <form action="/actions/act_usuarios.php" method="POST">

                            <?php /*se manera un if y un elseif donde se para una valor igual a crear el cual entrara en el if y ejecutara el insert*/ ?>

                            <input type="hidden" name="accion" value="crear">

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="correo">Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" minlength="8"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirmar password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" minlength="8" required>
                            </div>

                            <div class="form-group">
                                <label for="id_rol">Tipo de rol</label>
                                <select class="form-control" id="id_rol" name="id_rol">
                                    <option value="2">
                                        Operardor
                                    </option>

                                    <option value="1">
                                        Administrador
                                    </option>

                                    <option value="3">
                                        Profesor
                                    </option>
                                </select>
                            </div>

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">

                            <button type="submit" class="btn btn-primary btn-block">Crear Categoria</button>
                        </form>
                        <?php endif; ?>


                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require_once 'common/footer.php'; ?>