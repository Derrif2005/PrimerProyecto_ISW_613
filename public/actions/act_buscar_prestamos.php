
<?php

//Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_prestamos.php';
require_once __DIR__ . '/../../includes/buscar_detalleprestamos.php';
require_once __DIR__ . '/../../includes/auth.php';


requireLogin();

validarPermiso($conn, 'prestamos_ver');


//Comprueba si todavia tiene token
if (empty($_SESSION['csrf_token'])) {
    //Genera el token y lo guarda
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


//Obtiene el id del prestamo que se desea buscar
$buscar_id = $_GET['buscar_id'] ?? '0';

//Comprueba que el id sea valido
if (!is_string($buscar_id) || !ctype_digit($buscar_id)) {
    header('Location: /actions/act_buscar_prestamos.php?error=invalid_input');
    exit;
}


//Variables para mostrar los formularios
$mostrarP = isset($_GET['nuevo']) && $_GET['nuevo'] === '1';
$mostrarA = false;


//Obtiene los prestamos
if((int)$buscar_id > 0){

    //Busca el prestamo por id
    $Prestamo = findPrestamo($conn, (int)$buscar_id);

    //Comprueba si encontro el prestamo
    if($Prestamo !== false){

        $Prestamos = [$Prestamo];

        //Obtiene los equipos asociados al prestamo
        $Detalles = getDetallePrestamo($conn, (int)$buscar_id);

        //Muestra el formulario de actualizar
        $mostrarA = true;

    }else{
        $Prestamos = [];
    }

}else{
    $Prestamos = getPrestamos($conn);
}


//Carga los datos solamente si se muestra el formulario de crear
if($mostrarP){

    //Obtiene los usuarios
    $Usuarios = getUsuariosParaPrestamo($conn);

    //Obtiene los equipos
    $Equipos = getEquiposParaPrestamo($conn);
}


//Carga la vista
require __DIR__ . '/../pages/view_prestamo.php';
