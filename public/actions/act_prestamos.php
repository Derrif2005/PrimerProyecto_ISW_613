<?php


//Carga la conexion MYSQL
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/bitacora.php';
require_once __DIR__ . '/../../includes/buscar_prestamos.php';
require_once __DIR__ . '/../../includes/buscar_detalleprestamos.php';

//Comprueba si tiene una sesion iniciada
require_once __DIR__ . '/../../includes/auth.php';

//Mensajes de error
require_once __DIR__ . '/../../includes/mensajes.php';

//requiere inicar sesion
requireLogin();
//valida que tenga el permiso neceseraio
validarPermiso($conn, 'prestamos_crear');


//Comprueba que la solicitud sea mediante POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /actions/act_buscar_prestamos.php?error=1');
    exit;
}


//Comprueba que el codigo es del CSRF
$token = $_POST['csrf_token'] ?? '';

if(!is_string($token) ||empty($_SESSION['csrf_token']) ||!hash_equals($_SESSION['csrf_token'], $token)){
    header('Location: /actions/act_buscar_prestamos.php?error=1');
    exit;
}


//Recibe los datos del formulario

//Prestamos
$id = $_POST['id'] ?? null;
$usuario_id = $_POST['usuario_id'] ?? null;
$operador_id = $_SESSION['user_id'];
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;
$estado = $_POST['estado'] ?? null;

//Detalle prestamo
$equipo_id = $_POST['equipo_id'] ?? [];
$detalle_id = $_POST['detalle_id'] ?? [];
$devolver_todo = $_POST['devolver_todo'] ?? '0';
$nueva_fecha_fin = $_POST['nueva_fecha_fin'] ?? null;

//Determina si la accion es para crear o actualizar
$accion = $_POST['accion'] ?? '';
 


//Aqui empieza el metodo de crear

if($accion === 'crear'){

    //Comprueba los datos recibidos del formulario
    if(!is_string($usuario_id) || !ctype_digit($usuario_id) || (int)$usuario_id < 1 ||
        !is_string($fecha_inicio) || !is_string($fecha_fin) || str_contains($fecha_inicio, "\0") || str_contains($fecha_fin, "\0") ||
        !is_array($equipo_id) || empty($equipo_id))
    {
        header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=invalid_input');
        exit;
    }


    //Convierte las fechas recibidas del formulario
    $inicio = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $fecha_inicio);

    $fin = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $fecha_fin);


    //Comprueba que las fechas sean validas
    if($inicio === false ||$fin === false ||
    $inicio->format('Y-m-d\TH:i') !== $fecha_inicio ||
    $fin->format('Y-m-d\TH:i') !== $fecha_fin ||
    $inicio >= $fin
    ){
        header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=fecha_invalida');
        exit;
    }


    //Convierte las fechas al formato MYSQL
    $fecha_inicio = $inicio->format('Y-m-d H:i:s');
    $fecha_fin = $fin->format('Y-m-d H:i:s');

    //Recorre todos los equipos recibidos del formulario
    foreach($equipo_id as $equipo){

        //Comprueba que el id del equipo sea valido
        if(!is_string($equipo) || !ctype_digit($equipo) || (int)$equipo < 1){

            header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=invalid_input');
            exit;
        }
    }

    //Convierte los id a numeros enteros
    $equipo_id = array_map('intval', $equipo_id);

    //Elimina los equipos duplicados del arreglo
    $equipo_id = array_unique($equipo_id);

    //Ordena los equipos por id para reducir problemas de bloqueo
    sort($equipo_id, SORT_NUMERIC);

    try{
        //Inicia la transaccion
        $conn->beginTransaction();
        //Comprueba que el usuario exista y este activo
        $consulta = $conn->prepare('SELECT id FROM usuarios WHERE id = :id AND active = true LIMIT 1');
        $consulta->execute(['id' => $usuario_id]);
        $usuario_existente = $consulta->fetch(PDO::FETCH_ASSOC);

         //Comprueba que el usuario exista
        if(!$usuario_existente){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=invalid_input');
            exit;
        }

        //Recorre y bloquea todos los equipos seleccionados
        foreach($equipo_id as $equipo){
            //Llama a la funcion para bloquear el equipo
            $equipoEncontrado = bloquearEquipo($conn, $equipo);
            //Comprueba que el equipo exista y este activo
            if($equipoEncontrado === false){
                $conn->rollBack();
                header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=equipo_no_disponible');
                exit;
            }

            //Obtiene los dias maximos permitidos por la categoria
            $dias = (int)$equipoEncontrado['dias_max_prestamo'];
            //Calcula la fecha maxima permitida
            $fecha_maxima = $inicio->modify('+' . $dias . ' days');
            //Comprueba que el prestamo no supere los dias permitidos
            if($fin > $fecha_maxima){
                $conn->rollBack();
                header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=plazo_excedido');
                exit;
            }
        }


        //Recorre los equipos para comprobar si existe un traslape
        foreach($equipo_id as $equipo){
            //Llama a la funcion que verifica las fechas
            $traslape = verificarTraslape($conn, $equipo, $fecha_inicio, $fecha_fin);
            //Si existe un traslape cancela el prestamo
            if($traslape === true){
                $conn->rollBack();
                header('Location: /actions/act_buscar_prestamos.php?nuevo=1&error=equipo_no_disponible');
                exit;
            }
        }

        //Registra el prestamo
        $consulta = $conn->prepare('INSERT INTO prestamos
            (usuario_id, operador_id, estado, fecha_inicio, fecha_fin)
            VALUES(:usuario_id, :operador_id, 1, :fecha_inicio, :fecha_fin)');

        $consulta->execute(['usuario_id' => $usuario_id,
            'operador_id' => $operador_id,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);

        //Obtiene el id del prestamo que se acaba de crear
        $prestamo_id = (int)$conn->lastInsertId();

        //Prepara la consulta para registrar los detalles del prestamo
        $consultaDetalle = $conn->prepare('INSERT INTO prestamo_detalle
            (prestamo_id, equipo_id)
            VALUES(:prestamo_id, :equipo_id)');

        //Recorre y registra todos los equipos seleccionados
        foreach($equipo_id as $equipo){
            $consultaDetalle->execute(['prestamo_id' => $prestamo_id, 'equipo_id' => $equipo]);
        }


        //Guarda los datos posteriores y registra la actividad
        $datosDespues = obtenerDatosBitacora($conn, 'prestamos', $prestamo_id);
        registrarBitacora($conn, 'crear', $prestamo_id, null, $datosDespues, 'prestamos');
        //Confirma todos los cambios realizados
        $conn->commit();

        //Si todo sale bien regresa a prestamos
        header('Location: /actions/act_buscar_prestamos.php?success=1');
        exit;

    }catch(PDOException $error){

        //Cancela la transaccion si esta activa
        if($conn->inTransaction()){
            $conn->rollBack();
        }

        //Registra el error sin mostrarlo al usuario
        error_log('Error al crear prestamo: ' . $error->getMessage());

        //Comprueba si existe un problema de integridad
        if($error->getCode() === '23000'){

            header('Location: /actions/act_buscar_prestamos.php?error=integridad');
            exit;
        }

        header('Location: /actions/act_buscar_prestamos.php?error=1');
        exit;
    }


//Aqui empieza el metodo de actualizar
}elseif($accion === 'actualizar'){

    //Comprueba que el id y el estado sean validos
    if(!is_string($id) || !ctype_digit($id) || (int)$id < 1 ||
        !in_array($estado, ['1', '2', '3', '4', '5'], true)){
        header('Location: /actions/act_buscar_prestamos.php?error=invalid_input');
        exit;
    }

    $estado = (int)$estado;

    //Comprueba los equipos seleccionados para devolver
    if($estado === 4 && $devolver_todo !== '1'){
        if(!is_array($detalle_id) || empty($detalle_id)){
            header('Location: /actions/act_buscar_prestamos.php?buscar_id=' . (int)$id . '&error=invalid_input');
            exit;
        }

        foreach($detalle_id as $detalle){
            if(!is_string($detalle) || !ctype_digit($detalle) || (int)$detalle < 1){
                header('Location: /actions/act_buscar_prestamos.php?error=invalid_input');
                exit;
            }
        }

        $detalle_id = array_unique(array_map('intval', $detalle_id));
    }

    try{
        //Inicia la transaccion
        $conn->beginTransaction();

        //Obtiene y bloquea el prestamo que se desea actualizar
        $consulta = $conn->prepare('SELECT estado FROM prestamos WHERE id = :id AND active = true FOR UPDATE');
        $consulta->execute(['id' => $id]);
        $prestamo = $consulta->fetch(PDO::FETCH_ASSOC);

        if($prestamo === false){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=invalid_input');
            exit;
        }

        $datosAntes = obtenerDatosBitacora($conn, 'prestamos', $id);
        $estado_actual = (int)$prestamo['estado'];

        //Comprueba que se permita el cambio de estado
        if(!(($estado_actual === 1 && ($estado === 2 || $estado === 5)) ||
            ($estado_actual === 2 && ($estado === 3 || $estado === 5)) ||
            ($estado_actual === 3 && $estado === 4))){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=estado_invalido');
            exit;
        }

        //Si selecciona devuelto registra la devolucion de los equipos
        if($estado === 4){

            //Devuelve todos los equipos pendientes
            if($devolver_todo === '1'){
                $consulta = $conn->prepare('UPDATE prestamo_detalle SET fecha_devolucion = NOW() WHERE prestamo_id = :id AND fecha_devolucion IS NULL');
                $consulta->execute(['id' => $id]);

                if($consulta->rowCount() === 0){
                    $conn->rollBack();
                    header('Location: /actions/act_buscar_prestamos.php?error=devolucion_invalida');
                    exit;
                }

            }else{
                //Devuelve solamente los equipos seleccionados
                $consulta = $conn->prepare('UPDATE prestamo_detalle SET fecha_devolucion = NOW() WHERE id = :detalle_id AND prestamo_id = :id AND fecha_devolucion IS NULL');

                foreach($detalle_id as $detalle){
                    $consulta->execute(['detalle_id' => $detalle, 'id' => $id]);

                    if($consulta->rowCount() === 0){
                        $conn->rollBack();
                        header('Location: /actions/act_buscar_prestamos.php?error=devolucion_invalida');
                        exit;
                    }
                }
            }

            //Comprueba si quedan equipos pendientes de devolver
            $consulta = $conn->prepare('SELECT id FROM prestamo_detalle WHERE prestamo_id = :id AND fecha_devolucion IS NULL LIMIT 1 FOR UPDATE');
            $consulta->execute(['id' => $id]);
            $pendiente = $consulta->fetch(PDO::FETCH_ASSOC);

            //Si quedan equipos pendientes conserva el estado entregado
            if($pendiente !== false){
        //Guarda los datos posteriores y registra la actividad
        $datosDespues = obtenerDatosBitacora($conn, 'prestamos', $id);
        registrarBitacora($conn, 'devolver', $id, $datosAntes, $datosDespues, 'prestamos');
                $conn->commit();
                header('Location: /actions/act_buscar_prestamos.php?buscar_id=' . (int)$id . '&success=devolucion_parcial');
                exit;
            }
        }

        //Actualiza el estado del prestamo
        $consulta = $conn->prepare('UPDATE prestamos SET estado = :estado WHERE id = :id AND active = true');
        $consulta->execute(['estado' => $estado, 'id' => $id]);

        //Distingue una devolucion de otro cambio de estado
        $accionBitacora = 'cambiar_estado';
        if($estado === 4){
            $accionBitacora = 'devolver';
        }
        $datosDespues = obtenerDatosBitacora($conn, 'prestamos', $id);
        registrarBitacora($conn, $accionBitacora, $id, $datosAntes, $datosDespues, 'prestamos');
        //Confirma todos los cambios
        $conn->commit();

        if($estado === 4){
            header('Location: /actions/act_buscar_prestamos.php?success=devolucion_completa');
            exit;
        }

        header('Location: /actions/act_buscar_prestamos.php?success=1');
        exit;

    }catch(PDOException $error){

        //Cancela la transaccion si esta activa
        if($conn->inTransaction()){
            $conn->rollBack();
        }

        error_log('Error al actualizar prestamo: ' . $error->getMessage());

        header('Location: /actions/act_buscar_prestamos.php?error=1');
        exit;
    }


//Aqui empieza el metodo de prorrogar
}elseif($accion === 'prorrogar'){

    //Comprueba que el id y la fecha sean validos
    if(!is_string($id) || !ctype_digit($id) || (int)$id < 1 ||
        !is_string($nueva_fecha_fin) || str_contains($nueva_fecha_fin, "\0")){
        header('Location: /actions/act_buscar_prestamos.php?error=invalid_input');
        exit;
    }

    //Convierte la nueva fecha
    $nuevoFin = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $nueva_fecha_fin);

    //Comprueba que la fecha sea valida
    if($nuevoFin === false || $nuevoFin->format('Y-m-d\TH:i') !== $nueva_fecha_fin){
        header('Location: /actions/act_buscar_prestamos.php?error=fecha_invalida');
        exit;
    }

    try{
        //Inicia la transaccion
        $conn->beginTransaction();

        //Consulta y bloquea el prestamo
        $consulta = $conn->prepare('SELECT estado, fecha_fin, prorrogas FROM prestamos WHERE id = :id AND active = true FOR UPDATE');
        $consulta->execute(['id' => $id]);
        $prestamo = $consulta->fetch(PDO::FETCH_ASSOC);

        //Comprueba que el prestamo exista y este entregado
        if($prestamo === false || (int)$prestamo['estado'] !== 3){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=estado_invalido');
            exit;
        }

        $datosAntes = obtenerDatosBitacora($conn, 'prestamos', $id);
        //Comprueba que no haya utilizado la prorroga
        if((int)$prestamo['prorrogas'] !== 0){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=prorroga_utilizada');
            exit;
        }

        //Consulta los dias maximos permitidos para la prorroga
        $consulta = $conn->query("SELECT valor FROM configuracion WHERE clave = 'prorroga_max_dias'");
        $dias = $consulta->fetchColumn();

        //Comprueba que exista la configuracion
        if($dias === false || (int)$dias < 1){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=configuracion_invalida');
            exit;
        }

        //Obtiene la fecha de finalizacion actual
        $finActual = new DateTimeImmutable($prestamo['fecha_fin']);

        //Calcula la fecha maxima permitida
        $fecha_maxima = $finActual->modify('+' . (int)$dias . ' days');

        //Comprueba que la nueva fecha sea mayor a la actual
        if($nuevoFin <= $finActual){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=fecha_invalida');
            exit;
        }

        //Comprueba que no supere los dias permitidos
        if($nuevoFin > $fecha_maxima){
            $conn->rollBack();
            header('Location: /actions/act_buscar_prestamos.php?error=prorroga_excedida');
            exit;
        }

        //Convierte la nueva fecha al formato MYSQL
        $nueva_fecha_fin = $nuevoFin->format('Y-m-d H:i:s');

        //Consulta y bloquea los equipos pendientes de devolver
        $consulta = $conn->prepare('SELECT e.id FROM equipos e
        INNER JOIN prestamo_detalle d ON e.id = d.equipo_id
        WHERE d.prestamo_id = :id AND d.fecha_devolucion IS NULL
        ORDER BY e.id FOR UPDATE'
        );
        $consulta->execute(['id' => $id]);
        $equipos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        //Recorre los equipos para comprobar los traslapes
        foreach($equipos as $equipo){

            $traslape = verificarTraslape($conn, (int)$equipo['id'], $prestamo['fecha_fin'], $nueva_fecha_fin, (int)$id);

            if($traslape === true){
                $conn->rollBack();
                header('Location: /actions/act_buscar_prestamos.php?error=equipo_no_disponible');
                exit;
            }
        }

        //Actualiza la fecha de finalizacion y registra la prorroga
        $consulta = $conn->prepare('UPDATE prestamos SET fecha_fin = :fecha_fin, prorrogas = 1 WHERE id = :id AND estado = 3 AND prorrogas = 0 AND active = true');
        $consulta->execute(['fecha_fin' => $nueva_fecha_fin, 'id' => $id]);


        //Guarda los datos posteriores y registra la actividad
        $datosDespues = obtenerDatosBitacora($conn, 'prestamos', $id);
        registrarBitacora($conn, 'prorrogar', $id, $datosAntes, $datosDespues, 'prestamos');
        //Confirma los cambios
        $conn->commit();

        header('Location: /actions/act_buscar_prestamos.php?success=prorroga');
        exit;

    }catch(PDOException $error){

        //Cancela la transaccion si esta activa
        if($conn->inTransaction()){
            $conn->rollBack();
        }

        //Registra el error
        error_log('Error al prorrogar prestamo: ' . $error->getMessage());

        header('Location: /actions/act_buscar_prestamos.php?error=1');
        exit;
    }
}


//Si la accion no existe devuelve un error
header('Location: /actions/act_buscar_prestamos.php?error=1');
exit;
