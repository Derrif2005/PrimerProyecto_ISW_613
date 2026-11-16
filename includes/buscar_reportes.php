<?php 

function reporteMasPrestados(PDO $conn, string $fecha_inicio, string $fecha_fin){
    $consulta = $conn->prepare('SELECT e.id, e.nombre, e.codigo, COUNT(*) AS cantidad
    FROM prestamos p
    INNER JOIN prestamo_detalle pd ON p.id = pd.prestamo_id
    INNER JOIN equipos e ON pd.equipo_id = e.id
    WHERE p.active = true AND p.estado IN (3, 4)
    AND DATE(p.fecha_inicio) BETWEEN :fecha_inicio AND :fecha_fin
    GROUP BY e.id, e.nombre, e.codigo
    ORDER BY cantidad DESC'
    );

    $consulta->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);

    //Devuelve los equipos mas prestados
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}