<?php

//Obtiene todos los equipos asociados al prestamo
function getDetallePrestamo(PDO $conn, int $id){
    $consulta = $conn->prepare('SELECT d.id AS detalle_id, d.prestamo_id, d.equipo_id, e.nombre AS equipo, e.codigo, d.fecha_devolucion
    FROM prestamo_detalle d
    INNER JOIN equipos e ON d.equipo_id = e.id
    WHERE d.prestamo_id = :id'
    );

    $consulta->execute(['id' => $id]);

    //Devuelve los equipos del prestamo
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


//Consulta y bloquea el equipo durante la transaccion
function bloquearEquipo(PDO $conn, int $id){
    $consulta = $conn->prepare('SELECT e.id, c.dias_max_prestamo FROM equipos e INNER JOIN categorias c ON e.categoria_id = c.id WHERE e.id = :id AND e.active = true AND c.active = true FOR UPDATE');

    $consulta->execute(['id' => $id]);

    //Devuelve el equipo encontrado
    return $consulta->fetch(PDO::FETCH_ASSOC);
}


//Comprueba si existe un prestamo con fechas traslapadas
function verificarTraslape(PDO $conn, int $equipo_id, string $fecha_inicio, string $fecha_fin, int $prestamo_id = 0){
    $consulta = $conn->prepare('SELECT p.id FROM prestamos p
    INNER JOIN prestamo_detalle d ON p.id = d.prestamo_id
    WHERE d.equipo_id = :equipo_id AND p.active = true AND p.estado IN (1, 2, 3) AND d.fecha_devolucion IS NULL
    AND p.id != :prestamo_id
    AND p.fecha_inicio < :fecha_fin
    AND p.fecha_fin > :fecha_inicio LIMIT 1 FOR UPDATE'
    );

    $consulta->execute(['equipo_id' => $equipo_id,'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'prestamo_id' => $prestamo_id]);

    //Devuelve true si existe un traslape
    return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
}
