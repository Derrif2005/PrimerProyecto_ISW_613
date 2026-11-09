<?php 
    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../includes/auth.php';

    requireLogin();

    //Obtiene el id de la imagen
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if(!$id || $id < 1){
        http_response_code(400);
        exit;
    }

    //Busca la imagen en Mysql
    $consulta = $conn->prepare('SELECT imagen_ruta FROM equipo_imagenes WHERE id = :id LIMIT 1');

    $consulta->execute(['id' => $id]);

    $imagen = $consulta->fetch(PDO::FETCH_ASSOC);

    if($imagen === false){
        http_response_code(404);
        exit;
    }

    //contruye la ruta del archivo
    $nombre = $imagen['imagen_ruta'];

    if($nombre === '' || basename($nombre) !== $nombre ){
     http_response_code(404);
         exit;    
    }

    $ruta = __DIR__ . '/../../storage/equipos/' . $nombre;

    if(!is_file($ruta) || !is_readable($ruta)){
     http_response_code(404);
         exit;
    }
    
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($ruta);

    if(!in_array($tipo,['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)){
        http_response_code(415);
         exit;
    }

    // Envia la fotografia al navegador
    header('Content-Type: ' . $tipo);
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');

    readfile($ruta);
    exit;