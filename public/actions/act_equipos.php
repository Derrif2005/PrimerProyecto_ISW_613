<?php


// Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/bitacora.php';
require_once __DIR__ . '/../../includes/buscar_equipos.php';
require_once __DIR__ . '/../../includes/imagenes_equipos.php';
require_once __DIR__ . '/../../includes/buscar_categorias.php';

//Comprobara si tiene una sesion iniciada
require_once __DIR__ . '/../../includes/auth.php';


//Mensajes de error
require_once __DIR__ . '/../../includes/mensajes.php';


requireLogin();
//Solo permite solicitudes mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo no permitido');
}

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
$active = $_POST['active'] ?? null;

$imagenes = $_FILES['image'] ?? null;

//determina si la accion es para crear o actualizar
$accion = $_POST['accion'] ?? '';

$versionn = $_POST['versionn'] ?? '';

//Valida la version al crear o actualizar
if ($accion === 'crear' || $accion === 'actualizar') {

    if (!is_string($versionn) || strlen(trim($versionn)) > 50) {
        header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;
    }

    $versionn = trim($versionn);

    //Si no se escribe una version, guarda NULL
    if ($versionn === '') {
        $versionn = null;
    }
}


validarAdmin($conn);





    //aqui empieza el metodo de actualizar

    if($accion === 'actualizar'){
        //comprueba que los datos para no subir solo del html para actualizar

         if(
            //comprueba el id 
            !is_string($id) || !ctype_digit($id) || (int)$id < 1 || 
            //comprueba el id de categoria
            !is_string($categoria_id) || !ctype_digit($categoria_id) || (int)$categoria_id < 1 || 
            //comprueba que entre un nombre tipo text y que no venga vacio
            !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
            //comprueba que entre un tipo text que no venga vacio 
            !is_string($codigo) || trim($codigo) === '' || strlen($codigo) > 50 ||
            //comprueba que entre un 0 o 1 para su estado
            !is_string($active) || !in_array($active, ['0', '1'], true)

        ) {
            header('Location: /actions/act_buscar_equipos.php?error=1');
            exit;
        }

         
   // Comprueba si se seleccionaron fotografias nuevas
    $hayImagenes = is_array($imagenes) &&
        isset($imagenes['error'][0]) &&
        $imagenes['error'][0] !== UPLOAD_ERR_NO_FILE;

    // Si hay fotografias, comprueba su estructura
    if ($hayImagenes && ( !isset($imagenes['name'], $imagenes['error'], $imagenes['tmp_name'], $imagenes['size']) ||
        !is_array($imagenes['name']) || !is_array($imagenes['error']) || !is_array($imagenes['tmp_name']) ||
        !is_array($imagenes['size']) ||
        count($imagenes['name']) === 0 ||
        count($imagenes['name']) !== count($imagenes['error']) ||
        count($imagenes['name']) !== count($imagenes['tmp_name']) ||
        count($imagenes['name']) !== count($imagenes['size'])
    )) {
        header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;
    }

      try{
        $conn->beginTransaction();
        $datosAntes = obtenerDatosBitacora($conn, 'equipos', $id);
        //Busca por id de equipo y comprueba que existe  
        $consulta = $conn->prepare('SELECT id FROM equipos WHERE id = :id LIMIT 1');
        $consulta->execute(['id'=> $id]);
        $id_existente = $consulta->fetch(PDO::FETCH_ASSOC);
        //si el equipo no existe, devuelve un error
        if($id_existente === false){
        $conn->rollBack();
        header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;
    }

    //Valida que la categoria existe 
    $consulta = $conn->prepare('SELECT id FROM categorias WHERE id = :id LIMIT 1');
      $consulta->execute(['id' => $categoria_id]);
      $categoria_existente = $consulta->fetch(PDO::FETCH_ASSOC);

      //se la categoria no existe, devuelve error
      if($categoria_existente === false){
        $conn->rollBack();
         header('Location: /actions/act_buscar_equipos.php?error=1');
        exit;
      }

      //Conserva la fecha anterior si sigue inactivo
$eliminado_en = $datosAntes['eliminado_en'];

if ($active === '1') {
    $eliminado_en = null;
} elseif ((int)$datosAntes['active'] === 1) {
    //Obtiene la fecha con el mismo reloj de MySQL
    $consultaFecha = $conn->query('SELECT NOW()');
    $eliminado_en = $consultaFecha->fetchColumn();
}

    //Actualiza el equipo
    $consulta = $conn->prepare('UPDATE equipos SET categoria_id = :categoria_id, nombre = :nombre, codigo = :codigo, active = :active,
     versionn = :versionn, eliminado_en = :eliminado_en WHERE id = :id');

    $consulta->execute([
    'categoria_id' => $categoria_id, 'nombre' => trim($nombre), 'codigo' => trim($codigo),'active' => $active,
    'versionn' => $versionn,'eliminado_en' => $eliminado_en,'id' => $id]);
     
    //Cuenta las imagenes que fallaron
    $imagenesFallidas = 0;
    //comprueba si se seleccionaron imagenes nueva
    if($hayImagenes){
        //hara el insert de las imagenes 
        $consultaImagen = $conn->prepare('INSERT INTO equipo_imagenes (equipo_id, imagen_ruta) VALUES(:equipo_id, :imagen_ruta)');

         //Recorre las imagenes recibidas por el form
            for($imagen = 0;$imagen < count($imagenes['name']); $imagen++){
            
            //obtiene los tados de la imagen
            $archivo = [
                'name' => $imagenes['name'][$imagen],
                'error' => $imagenes['error'][$imagen],
                'tmp_name' => $imagenes['tmp_name'][$imagen],
                'size' => $imagenes['size'][$imagen]
                ];

                //Guarda la imagen
                $resultado = storeImagenEquipo($archivo);

            //si la imagen falla, pasa ala siguiente
            if(isset($resultado['error'])){
                $imagenesFallidas++;
                continue;
            }
            $consultaImagen->execute(['equipo_id' => $id, 'imagen_ruta' => $resultado['filename']] );
            }
        }


        //Guarda los datos posteriores y registra la actividad
        $datosDespues = obtenerDatosBitacora($conn, 'equipos', $id);
        registrarBitacora($conn, 'actualizar', $id, $datosAntes, $datosDespues, 'equipos');
        $conn->commit();
         // Comprueba si alguna imagen fallo
        if ($imagenesFallidas > 0) {
            header('Location: /actions/act_buscar_equipos.php?parcial=1');
            exit;
        }

        // Si todo salio bien, regresa a equipos
        header('Location: /actions/act_buscar_equipos.php?success=1');
        exit;


      }catch(PDOException $error){
        if($conn->inTransaction()){
            $conn->rollBack();
        }

    //comprueba si existe un problema de integridad
    if ($error->getCode() === '23000') {
        header('Location: /actions/act_buscar_equipos.php?error=codigo_duplicado');
        exit;
    }

    //registra el error sin mostrarlo al usuario
    error_log('Error al actualizar equipos: ' . $error->getMessage());

    header('Location: /actions/act_buscar_equipos.php?error=1');
    exit;
    }   

      




        //Aqui empieza el insert
    } elseif($accion === 'crear'){
 
    if(
    //Comprueba los datos recididos tipo string y tipo int
    //comprueba el nombre
    !is_string($nombre) || trim($nombre) === '' || strlen($nombre) > 50 ||
    //Comprueba el codigo
    !is_string($codigo) || trim($codigo) === '' || strlen($codigo) > 50 ||
    //Comprueba el id de categoria
    !is_string($categoria_id) || !ctype_digit($categoria_id) || (int)$categoria_id < 1 
      
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
        $conn->beginTransaction();


    //Guarda la categoria 
   $consulta = $conn->prepare(
    'INSERT INTO equipos (categoria_id, nombre, codigo, versionn) VALUES (:categoria_id, :nombre, :codigo, :versionn)'
);

    $consulta->execute(['categoria_id' => $categoria_id, 'nombre' => trim($nombre), 'codigo' => trim($codigo),'versionn' => $versionn]);
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



        //Guarda los datos posteriores y registra la actividad
        $datosDespues = obtenerDatosBitacora($conn, 'equipos', $equipo_id);
        registrarBitacora($conn, 'crear', $equipo_id, null, $datosDespues, 'equipos');
        $conn->commit();
    if($imagenesFallidas > 0){
        header('Location: /actions/act_buscar_equipos.php?parcial=1');
        exit;
    }
    //Si todo sale bien, regresa a equipos 
    header('Location: /actions/act_buscar_equipos.php?success=1');
    exit;
        
    }catch(PDOException $error){
        if($conn->inTransaction()){
            $conn->rollBack();
        }

    //comprueba si existe un problema de integridad
    if ($error->getCode() === '23000') {
        header('Location: /actions/act_buscar_equipos.php?error=codigo_duplicado');
        exit;
    }

    //registra el error sin mostrarlo al usuario
    error_log('Error al crear equipo: ' . $error->getMessage());

    header('Location: /actions/act_buscar_equipos.php?error=1');
    exit;
    }


    /*Aqui termina la validacion de imagenes*/

    //Aqui empiza el delete
    } elseif($accion === 'eliminar'){
        //Comprueba que el id sea valido    
    if( !is_string($id) || !ctype_digit($id) || (int)$id < 1) 
        {
            header('Location: /actions/act_buscar_equipos.php?error=1');
            exit;
        }
        try{
        $conn->beginTransaction();
            $datosAntes = obtenerDatosBitacora($conn, 'equipos', $id);
            if((int)$datosAntes['active'] !== 1){
                throw new PDOException('El equipo ya esta inactivo');
            }
            //cambia el estado a inactivo
           $consulta = $conn->prepare('UPDATE equipos SET active = 0, eliminado_en = NOW() WHERE id = :id AND active = 1');

            $consulta->execute(['id' => $id]);

            


        //Guarda los datos posteriores y registra la actividad
        $datosDespues = obtenerDatosBitacora($conn, 'equipos', $id);
        registrarBitacora($conn, 'eliminar', $id, $datosAntes, $datosDespues, 'equipos');
        $conn->commit();
            // Si todo sale bien, regresa a equipos
            header('Location: /actions/act_buscar_equipos.php?success=1');
            exit;

        
        } catch(PDOException $error){
        if($conn->inTransaction()){
            $conn->rollBack();
        }

            error_log('Error al eliminar equipo: ' . $error->getMessage());

            header('Location: /actions/act_buscar_equipos.php?error=1');
            exit;
        }
    }
