<?php
//va ir al directoria a guardar la imagen
const EQUIPOS_IMAGENES_DIR = __DIR__ . '/../storage/equipos';

//Funciona para limitar el tamaño de la imagen a 2MB
const EQUIPO_IMAGEN_MAX_BYTES = 2 * 1024 * 1024;

// The extension is picked from the detected content type, never from the client's file name
//Guarda el tipo de imagen
const EQUIPO_IMAGEN_TYPES  = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];

/**
 * Validates an uploaded picture and moves it into the uploads folder
 * @param array $file The $_FILES entry of the upload
 * @return array ['filename' => string] on success, or ['error' => string] with a messages.php error code
 */

//la funcion recibe una sola fotografia
function storeImagenEquipo(array $file)
{
    //comprueba que file tenga la estrutura correcta
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['error' => 'invalid_picture'];
    }

    // Rechaza la imagen si supera el tamaña permito
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return ['error' => 'picture_too_large'];
    }

    // Comprueba que la imagen se haya recibido correctamente
    if(
        $file['error'] !== UPLOAD_ERR_OK || !isset($file['tmp_name'], $file['size']) ||
        !is_uploaded_file($file['tmp_name'])
    ){
        return ['error' => 'upload_failed'];
    }

    // Valida que la imagen no supere lo permitido
    if($file['size'] > EQUIPO_IMAGEN_MAX_BYTES){
        return['error' => 'picture_too_large'];
        }

    //comprueba el tipo de imagen
    $mimeType = (new finfo(FILEINFO_MIME_TYPE)) -> file($file['tmp_name']);

    if(!isset(EQUIPO_IMAGEN_TYPES[$mimeType]) || getimagesize($file['tmp_name']) === false){
        return['error' => 'invalid_picture'];
    }




    $filename = bin2hex(random_bytes(16)) . '.' . EQUIPO_IMAGEN_TYPES [$mimeType];


    if (!move_uploaded_file($file['tmp_name'], EQUIPOS_IMAGENES_DIR . '/' . $filename)) {
        error_log('Error al guardar la imagen ' . EQUIPOS_IMAGENES_DIR);
        return ['error' => 'upload_failed'];
    }

    return ['filename' => $filename];
}

function deleteProfilePicture($filename)
{
    // basename() keeps the delete inside the uploads folder
    $path = EQUIPOS_IMAGENES_DIR . '/' . basename((string) $filename);
    if ($filename !== null && $filename !== '' && is_file($path)) {
        unlink($path);
    }
}



