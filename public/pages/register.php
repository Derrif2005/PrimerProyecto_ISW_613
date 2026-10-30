<?php
//Se inicia la sesión 
session_start();

//Es un codigo de seguridad para el formulario
if(empty($_SESSION['csrf_token'])){
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


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
            <form action="/actions/act_register.php" method="POST">
              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50" required>
              </div>

              <div class="form-group">
                <label for="correo">Correo</label>
                <input type="email" class="form-control" id="Correo" name="correo" required>
              </div>

              <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" required>
              </div>

              <div class="form-group">
                <label for="confirm_password">Confirmar password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
              </div>

              <p class="text-muted">Rol de cuenta: Operador </p>

              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');?>">
              
              <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>
            <p class="text-center mt-3 mb-0">Ya tienes una cuenta? <a href="/">Log in</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php require_once 'common/footer.php'; ?>
