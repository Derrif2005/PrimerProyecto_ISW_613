<?php
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/buscar_categorias.php';
    require_once __DIR__ . '/../../includes/auth.php';


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
        <?php /*btn para mostrar el formulario de crear*/ ?>
        <a href="/actions/act_buscar_categoria.php?nuevo=1" class="btn btn-primary mb-3">Nueva categoria</a>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dias Maximos de prestamo</th>
                    <th>Estado</th>
                    <th>acciones</th>
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
                    <td>
                        <?php /*este codigo se puede utilizar*/  ?>
                        <a href="/actions/act_buscar_categoria.php?buscar_categoria=<?php echo urlencode($categoria['nombre']); ?>" class="btn btn-warning btn-sm">
                            Editar
                        </a>
                        <form action="/actions/act_categoria.php" method="POST" class="d-inline" onsubmit="return confirm('Desea eliminar la categoria puede producir un desastre en la base de datos')">
                    
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo (int)$categoria['id']; ?>">
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

                        <form action="/actions/act_buscar_categoria.php" method="GET">
                            <input type="text" class="form-control" id="buscar_categoria" name="buscar_categoria"
                                required>
                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                        </form>

                        <?php/*Formulario de actualizar*/?>
                        <?php if($encontrarCategoria !== false): ?>
                        <form action="/actions/act_categoria.php" method="POST">

                            <?php /*se manera un if y un elseif donde se para una valor igual a actualizar el cual entrara en el if y ejecutara el insert*/?>
                            <input type="hidden" name="accion" value="actualizar">

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
                                    name="dias_max_prestamo" min="1" step="1"
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

                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">

                            <button type="submit" class="btn btn-primary btn-block">Actualizar categoria</button>
                        </form>
                        <?php endif; ?>



                        <?php if($mostrar): ?>

                        <?php /*Formulario de registro*/ ?>
                        <form action="/actions/act_categoria.php" method="POST">

                            <?php /*se manera un if y un elseif donde se para una valor igual a crear el cual entrara en el if y ejecutara el insert*/ ?>

                            <input type="hidden" name="accion" value="crear">

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="dias_max_prestamo">Dias maximos de prestamo</label>
                                <input type="number" class="form-control" id="dias_max_prestamo"
                                    name="dias_max_prestamo" min="1" step="1" required>
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