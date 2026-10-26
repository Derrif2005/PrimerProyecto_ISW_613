<?php   
    require_once __DIR__ . '/pages/common/header.php';
    require_once __DIR__ . '/pages/common/navbar.php';
   
    //require_once __DIR__ . '/../config/conexion.php';


?>

<body>
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

                        <form action="actions/login.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                        <p class="text-center mt-3 mb-0"> No tienes una cuenta? 
                            <a href="/pages/register.php"> Registrate </a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <?php require_once __DIR__ . '/pages/common/footer.php'; ?>