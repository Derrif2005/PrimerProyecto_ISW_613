<?php

//Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/buscar_reportes.php';
require_once __DIR__ . '/../../includes/auth.php';


requireLogin();

validarPermiso($conn, 'reportes_ver');


//Obtiene las fechas del formulario
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';


//Arreglo para guardar los reportes
$Reportes = [];


//Comprueba si se recibieron fechas
if($fecha_inicio !== '' || $fecha_fin !== ''){

    //Comprueba que las fechas sean texto
    if(!is_string($fecha_inicio) || !is_string($fecha_fin) ||
        str_contains($fecha_inicio, "\0") || str_contains($fecha_fin, "\0")){
        header('Location: /actions/act_buscar_reporte.php?error=invalid_input');
        exit;
    }

    //Convierte las fechas
    $inicio = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha_inicio);
    $fin = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha_fin);

    //Comprueba que las fechas sean validas
    if($inicio === false || $fin === false ||
        $inicio->format('Y-m-d') !== $fecha_inicio ||
        $fin->format('Y-m-d') !== $fecha_fin ||
        $inicio > $fin){
        header('Location: /actions/act_buscar_reporte.php?error=invalid_input');
        exit;
    }

    try{

        //Obtiene los equipos mas prestados
        $Reportes = reporteMasPrestados($conn, $fecha_inicio, $fecha_fin);

    }catch(PDOException $error){

        //Registra el error
        error_log('Error al buscar reporte: ' . $error->getMessage());

        header('Location: /actions/act_buscar_reporte.php?error=1');
        exit;
    }
}

//Carga la vista
require __DIR__ . '/../pages/view_reportes.php';