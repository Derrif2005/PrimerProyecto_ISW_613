<?php

//Registra los cambios sin guardar contrasenas
function registrarBitacora($conn, $accion, $id, $antes, $despues, $entidad = 'usuarios'){
    $valoresAntes = null;
    $valoresDespues = null;
    if($antes !== null){
        $valoresAntes = json_encode($antes, JSON_INVALID_UTF8_SUBSTITUTE);
    }
    if($despues !== null){
        $valoresDespues = json_encode($despues, JSON_INVALID_UTF8_SUBSTITUTE);
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $consulta = $conn->prepare("INSERT INTO bitacora
        (usuario_id, accion, entidad, entidad_id, valores_antes, valores_despues, ip)
        VALUES (:usuario, :accion, :entidad, :id, :antes, :despues, :ip)");
    $consulta->execute([
        'usuario' => $_SESSION['user_id'],
        'accion' => $accion,
        'entidad' => $entidad,
        'id' => $id,
        'antes' => $valoresAntes,
        'despues' => $valoresDespues,
        'ip' => $ip
    ]);
}

//Consulta los datos que se guardaran en la bitacora.
//Se utiliza dentro de una transaccion para leer los datos antes y despues.
function obtenerDatosBitacora($conn, $entidad, $id){
    if($entidad === 'categorias'){
        $sql = 'SELECT id, nombre, dias_max_prestamo, active FROM categorias WHERE id = :id FOR UPDATE';
    } elseif($entidad === 'equipos'){
        $sql = 'SELECT id, categoria_id, nombre, codigo, active, imagen_ruta, versionn, eliminado_en FROM equipos WHERE id = :id FOR UPDATE';
    } elseif($entidad === 'prestamos'){
        $sql = 'SELECT id, usuario_id, operador_id, estado, fecha_inicio, fecha_fin, active, prorrogas FROM prestamos WHERE id = :id FOR UPDATE';
    } else {
        throw new PDOException('Entidad no permitida para la bitacora');
    }
    $consulta = $conn->prepare($sql);
    $consulta->execute(['id' => $id]);
    $datos = $consulta->fetch(PDO::FETCH_ASSOC);
    if($datos === false){
        throw new PDOException('No existe el registro para la bitacora');
    }
    if($entidad === 'equipos'){
        $consulta = $conn->prepare('SELECT id, imagen_ruta FROM equipo_imagenes WHERE equipo_id = :id ORDER BY id FOR UPDATE');
        $consulta->execute(['id' => $id]);
        $datos['imagenes'] = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    if($entidad === 'prestamos'){
        $consulta = $conn->prepare('SELECT id, equipo_id, fecha_devolucion FROM prestamo_detalle WHERE prestamo_id = :id ORDER BY id FOR UPDATE');
        $consulta->execute(['id' => $id]);
        $datos['detalles'] = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    return $datos;
}
