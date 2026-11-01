<?php
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../config/buscar_usuarios.php';
    require_once __DIR__ . '/../../config/auth.php';
    require_once __DIR__ . '/../actions/act_buscar_usuarios.php';


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
      <h1 class="display-4">Usuarios</h1>
      <p class="lead">Lista de usuarios</p>
      <hr class="my-4">
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Estado</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
          <td scope="row"><?php echo (int) $user['id']; ?></td>
          <td><?php echo htmlspecialchars($user['nombre']); ?></td>
          <td><?php echo htmlspecialchars($user['correo']); ?></td>
          <td><?php echo htmlspecialchars($user['rol']); ?></td>
            <td><?php if((int)$user['active'] === 1){
                echo 'Activo';              
            } else { 
                echo 'Inactivo';
            }  
             ?>
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
              <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" class="form-control" id="Correo" name="correo" value="<?php echo htmlspecialchars($encontrarUsuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
                
              </div>


              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($encontrarUsuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" maxlength="50" required>
              </div>


              <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" required>
              </div>

              <div class="form-group">
                <label for="confirm_password">Confirmar password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
              </div>



              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">
              
              <button type="submit" class="btn btn-primary btn-block">Actualizar cuenta</button>
          
            </form>
            <?php endif; ?>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<?php require_once 'common/footer.php'; ?>













