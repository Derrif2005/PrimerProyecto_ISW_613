<?php


    //Obtiene todos los datos de los prestamos activos
    function getPrestamos(PDO $conn){
    $consulta = $conn->query('SELECT p.id, u.nombre AS usuario, o.nombre AS operador, p.estado, p.fecha_inicio, p.fecha_fin, p.prorrogas, p.active
    FROM prestamos p
    INNER JOIN usuarios u ON p.usuario_id = u.id
    INNER JOIN usuarios o ON p.operador_id = o.id
    WHERE p.active = true
    ORDER BY p.id'
    );
    //Devuelve los prestamos encontrados
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    //Busca un prestamo por id
    function findPrestamo(PDO $conn, int $id){
    $consulta = $conn->prepare('SELECT p.id, p.usuario_id, p.operador_id, u.nombre AS usuario, o.nombre AS operador, p.estado, p.fecha_inicio, p.fecha_fin, p.prorrogas, p.active
        FROM prestamos p
        INNER JOIN usuarios u ON p.usuario_id = u.id
        INNER JOIN usuarios o ON p.operador_id = o.id
        WHERE p.active = true AND p.id = :id LIMIT 1'
    );

    $consulta->execute(['id' => $id]);

    //Devuelve el prestamo encontrado
    return $consulta->fetch(PDO::FETCH_ASSOC);
    }


    //Obtiene todos los usuarios activos para realizar un prestamo
    function getUsuariosParaPrestamo(PDO $conn){
    $consulta = $conn->query('SELECT id, nombre FROM usuarios WHERE active = true AND id_rol = 3 ORDER BY nombre');
    //Devuelve los usuarios encontrados
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    //Obtiene todos los equipos activos para realizar un prestamo
    function getEquiposParaPrestamo(PDO $conn)
    {
    $consulta = $conn->query('SELECT e.id, e.nombre, e.codigo FROM equipos e 
    INNER JOIN categorias c ON e.categoria_id = c.id
    WHERE e.active = true AND c.active = true ORDER BY e.nombre'
    );
     //Devuelve los equipos encontrados
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
