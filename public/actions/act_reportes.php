
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


//Comprueba que las fechas sean validas
if(!is_string($fecha_inicio) || !is_string($fecha_fin) ||
    str_contains($fecha_inicio, "\0") || str_contains($fecha_fin, "\0")){
    header('Location: /actions/act_buscar_reporte.php?error=invalid_input');
    exit;
}


//Convierte las fechas
$inicio = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha_inicio);
$fin = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha_fin);


//Comprueba el rango de fechas
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
    error_log('Error al exportar reporte: ' . $error->getMessage());

    header('Location: /actions/act_buscar_reporte.php?error=1');
    exit;
}


//Evita que los datos se interpreten como formulas
function limpiarCSV(string $valor){

    if(preg_match('/^[\x00-\x20]*[=+\-@]|^[\t\r\n]/', $valor)){
        return "'" . $valor;
    }

    return $valor;
}


//Indica al navegador que debe descargar un archivo CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="equipos_mas_prestados.csv"');


//Abre la salida del archivo
$archivo = fopen('php://output', 'w');


//Agrega los encabezados del archivo
fputcsv($archivo, ['ID', 'Nombre', 'Codigo', 'Cantidad de prestamos'], ',', '"', '');


//Recorre los resultados del reporte
foreach($Reportes as $reporte){

    fputcsv($archivo, [
        (int)$reporte['id'],
        limpiarCSV($reporte['nombre']),
        limpiarCSV($reporte['codigo']),
        (int)$reporte['cantidad']
    ], ',', '"', '');
}


//Cierra el archivo
fclose($archivo);
exit;

