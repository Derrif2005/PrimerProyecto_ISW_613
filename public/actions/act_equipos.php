<?php

session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_equipos.php';
require_once __DIR__ . '/../../includes/imagenes_equipos.php';

//Comprobara si tiene una sesion iniciada
require_once __DIR__ . '/../../includes/auth.php';


//Mensajes de error
require_once __DIR__ . '/../../includes/mensajes.php';


requireLogin();


//Comprueba que el codigo es del CSRF
$token = $_POST['csrf_token'] ?? '';
if(
    !is_string($token) || 
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token) 
    ){
        header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;
    }


//recibe los datos del formulario   
$id = $_POST['id'] ?? null; 
$categoria_id = $_POST['categoria_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$codigo = $_POST['codigo'] ?? null;


$imagenes = $_FILES['image'] ?? null;

//determina si la accion es para crear o actualizar
$accion = $_POST['accion'] ?? '';


validarAdmin($conn);





    //aqui empieza el metodo de actualizar

    if($accion === 'actualizar'){
        //comprueba que los datos para no subir solo del html para actualizar



        //Aqui empieza el insert

    } elseif($accion === 'crear'){
 
    if(
    //Comprueba los datos recididos tipo string y tipo int
    //comprueba el nombre
    !is_string($nombre) || trim($nombre) === '' || 
    strlen($nombre) > 50 ||
    //Comprueba el codigo
    !is_string($codigo) || trim($codigo) === '' ||
    strlen($codigo) > 50 ||
    //Comprueba el id de categoria
    !is_string($categoria_id) || !ctype_digit($categoria_id) || 
    (int)$categoria_id < 1 
      
    ) {
        //si no pasan los datos saltara un error en la barra
    header('Location: /actions/act_buscar_equipos.php?error=1');
    exit;
     }

    //comprueba que se enviaron las fotos
    if(
        //Comprueba si la variable es un arreglo
        !is_array($imagenes) || 
        //comprueba si la variable tiene nombre si no salta el error
        !isset($imagenes['name'], $imagenes['error']) ||
        //Si recibe texto en vez de una arreglo entra en error
        !is_array($imagenes['name']) ||
        //Comprueba que en el array venga nombres de los archivos si pasa otra cosa devuelve el error
        !is_array($imagenes['error']) ||
        //Cuenta la cantidad de nombres y si no tiene pasa 0
        count($imagenes['name']) === 0 

        
    ) {
        //si uno da error se muestra en la barra
        header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;
    }


     try{


    //Guarda la categoria 
    $consulta = $conn->prepare('INSERT INTO equipos (categoria_id, nombre, codigo) VALUES(:categoria_id, :nombre, :codigo)');
    $consulta->execute(['categoria_id' => $categoria_id, 'nombre' => trim($nombre), 'codigo' => trim($codigo)]);

    $equipo_id = (int)$conn->lastInsertId();

    $consultaImagen = $conn->prepare('INSERT INTO equipo_imagenes (equipo_id, imagen_ruta) VALUES (:equipo_id, :imagen_ruta)');

    $imagenesFallidas = 0;

    //Recorre las imagenes recibidas por el form
    for($imagen = 0;$imagen < count($imagenes['name']); $imagen++){
    
    //obtiene los tados de la imagen
    $archivo = [
        'name' => $imagenes['name'][$imagen],
        'error' => $imagenes['error'][$imagen],
        'tmp_name' => $imagenes['tmp_name'][$imagen],
        'size' => $imagenes['size'][$imagen]
        ];

       
    //llama ala funcion para guardar las imagenes
    $resultado = storeImagenEquipo($archivo);

    if(isset($resultado['error'])){
        $imagenesFallidas++;
        continue;
    }


    //Registra la imagen en mysql
    $consultaImagen->execute(['equipo_id' => $equipo_id, 'imagen_ruta' => $resultado['filename']]);
    }


    if($imagenesFallidas > 0){
        header('Location: /actions/act_buscar_equipos.php?parcial=1');
        exit;
    }
    //Si todo sale bien, regresa a equipos 
    header('Location: /actions/act_buscar_equipos.php?success=1');
    exit;
        
    }catch(PDOException $error){
     // Si ocurre un error, regresa a equipos
        error_log('Error al crear equipo: ' . $error->getMessage());

        header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;

    }


    /*Aqui termina la validacion de imagenes*/

    //Aqui empiza el delete
    } elseif($accion === 'eliminar'){
        
    }
  




    
   