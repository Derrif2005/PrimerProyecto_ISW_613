<?php   
    require_once __DIR__ . '/pages/common/header.php';
    require_once __DIR__ . '/../config/auth.php';
    $rememberedUsername = $_COOKIE['remembered_username'] ?? '';
?>

<body>
     <?php
    // Mostrar la barra de navegación.
    require_once __DIR__ . '/pages/common/navbar.php';
    ?>

    <div class="container mt-5">
        <h1 class="text-center">Prestamos UTN</h1>
        <p class="text-center">Esta pagina es para uso estudiantil ya que los estudiantes ocupan equipos y la
            universidad se los facilita</p>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Iniciar Sesion</h4>
                    </div>
                    <div class="card-body">

                    <?php 
                        if(isset($_GET['error']) && $_GET['error'] === 'invalid_credentials') {
                            echo '<p style="color: red;">Correo o contrasea incorrectos</p>';                  
                        }

                    ?>
                        <form action="actions/login.php" method="POST">
                            <div class="form-group">
                                <label for="correo">correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <?php/*pendiente de revisar*/?>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" <?php echo $rememberedUsername !== '' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="remember_me">Recuerdame</label>
                            </div>


                            <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
                        </form>
                     

                        <p class="text-center mt-3 mb-0"> No tienes una cuenta? 
                            <a href="/pages/register.php"> Registrate </a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <?php
    require_once __DIR__ . '/pages/common/footer.php';
    ?>