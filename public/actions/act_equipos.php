<?php

session_start();

// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../config/buscar_equipos.php';

//Comprobara si tiene una sesion iniciada
require_once __DIR__ . '/../../config/auth.php';


//Mensajes de error
require_once __DIR__ . '/../../config/mensajes.php';


requireLogin();


//Comprueba que el codigo es del CSRF
$token = $_POST['csrf_token'] ?? '';
if(
    !is_string($token) || 
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token) 
    ){
        header('Location: /actions/act_buscar_categoria.php?error=1');
        exit;
    }


//recibe los datos del formulario   
$id = $_POST['id'] ?? null; 
$categoria_id = $_POST['categoria_id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$codigo = $_POST['codigo'] ?? null;
$active = $_POST['active'] ?? null;
$imagen_ruta = $_FILES['imagen'] ?? null;
$versionn = $_POST['version'] ?? null;


//determina si la accion es para crear o actualizar
$accion = $_POST['accion'] ?? '';


validarAdmin($conn);

/*Funciones para manejo de imagenes */ 
storeProfilePicture(){}

deleteProfilePicture(){}

profilePictureUrl(){}




    //aqui empieza el metodo de actualizar

    if($accion === 'actualizar'){
        //comprueba que los datos para no subir solo del html para actualizar



        //Aqui empieza el insert

    } elseif($accion === 'crear'){

    
    /*Esta parte sera para validar cuantas imagenes va recibir*/
    if($imagenes === null ){
        echo "No se recibieron imagenes";
        exit;
    } 

    $cantidadImagenes = count($imagenes['name']);
    echo "Cantidad de imagenes recibidas: " . $cantidadImagenes;
    exit;
    /*Aqui termina la validacion de imagenes*/

    //Aqui empiza el delete
    } elseif($accion === 'eliminar'){
        
    }
  




    
   