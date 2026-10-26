<?php
       // require_once __DIR__ . '/../utils/database.php';
       // require_once __DIR__ . '/../utils/provinces.php';
       // require_once __DIR__ . '/../utils/messages.php';


// $provinces = getProvinces($conn);
?>
<?php require_once 'common/header.php'; ?>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h4>Sign up</h4>
          </div>
          <div class="card-body">
         

            /*Formulario de registro*/
            <form action="/public/pages/register.php" method="POST">
              <div class="form-group">
                <label for="username">Nombre</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="50" required>
              </div>

              <div class="form-group">
                <label for="Correo">Correo</label>
                <input type="email" class="form-control" id="Correo" name="Correo" required>
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" required>
              </div>

              <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
              </div>

              <div class="form-group">
                <label for="activo_id">Estado</label>
                <select class="form-control" id="activo_id" name="activo_id" required>
                  <option value="" disabled selected>Seleccione un estado</option>
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>  
              
              <div class="form-group">
                <label for="rol_id">Rol</label>
                <select class="form-control" id="rol_id" name="rol_id" required>
                  <option value="" disabled selected>Seleccione un rol</option>
                  <option value="1">administrador</option>
                  <option value="2">operador</option>
                </select>
              </div>  
              
              <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>
            <p class="text-center mt-3 mb-0">Ya tienes una cuenta? <a href="/../public/index.php">Log in</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php require_once 'common/footer.php'; ?>
