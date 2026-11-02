<?php
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../config/buscar_categorias.php';
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
            <h1 class="display-4">Categorias</h1>
            <p class="lead">Lista de Categorias</p>
            <hr class="my-4">
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dias Maximos de prestamo</th>
                     <th>Estado</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($Categorias as $categoria): ?>
                <tr>
                    <td scope="row"><?php echo (int) $categoria['id']; ?></td>
                    <td><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                    <td><?php echo (int)$categoria['dias_max_prestamo']; ?></td>
                    <td><?php if((int)$categoria['active'] === 1){ 
                            echo 'Activo';              
                        } else { 
                            echo 'Inactivo';
                        } 
                        ?>
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

                        <form action="/actions/act_buscar_categoria.php" method="GET">
                            <input type="text" class="form-control" id="buscar_categoria" name="buscar_categoria"
                                required>
                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                        </form>

                        <?php/*Formulario de actualizar*/?>
                        <?php if($encontrarCategoria !== false): ?>
                        <form action="/actions/act_categoria.php" method="POST">

                            <div class="form-group">
                                <input type="hidden" class="form-control" id="id" name="id"
                                    value="<?php echo htmlspecialchars($encontrarCategoria['id'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($encontrarCategoria['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50" required>
                            </div>

                            <div class="form-group">
                                <input type="number" class="form-control" id="dias_max_prestamo"
                                    name="dias_max_prestamo"
                                    value="<?php echo htmlspecialchars($encontrarCategoria['dias_max_prestamo'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>

                              <div class="form-group">
                                <label for="active">Estado</label>
                                <select class="form-control" id="active" name="active">
                                    <option value="1"
                                        <?php if((int)$encontrarCategoria['active'] === 1)echo 'selected'; ?>>
                                        Activo
                                    </option>

                                    <option value="0"
                                        <?php if((int)$encontrarCategoria['active'] === 0)echo 'selected'; ?>>
                                        Inactivo
                                    </option>
                                </select>
                            </div>

                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">

                            <button type="submit" class="btn btn-primary btn-block">Actualizar categoria</button>
                        </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require_once 'common/footer.php'; ?>