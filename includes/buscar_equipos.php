<?php

//obtiene los equipos activos por pagina
function getEquipos(PDO $conn, int $limite, int $offset, string $filtro = '')
{
    //consulta los equipos con su respectiva categoria
    $consulta = $conn->prepare(
        'SELECT e.id, e.categoria_id, c.nombre AS categoria, e.nombre, e.codigo, e.active, e.imagen_ruta, e.versionn, e.eliminado_en
         FROM equipos e LEFT JOIN categorias c ON c.id = e.categoria_id WHERE e.active = true
         AND e.nombre LIKE :filtro ORDER BY e.id LIMIT :limite OFFSET :offset'
    );

    //nombre del equipo que se desea buscar
    $consulta->bindValue(':filtro', '%' . $filtro . '%');

    //cantidad de equipos que se mostraran
    $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);

    //cantidad de equipos que se saltaran
    $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

    //ejecuta la consulta
    $consulta->execute();

    //devuelve los equipos encontrados
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


//cuenta la cantidad total de equipos activos
function ContarEquipos(PDO $conn, string $filtro = '')
{
    //cuenta los equipos que no estan eliminados
    $consulta = $conn->prepare(
        'SELECT COUNT(*) FROM equipos WHERE active = true  AND nombre LIKE :filtro'
    );

     //nombre del equipo que se desea buscar
    $consulta->bindValue(':filtro', '%' . $filtro . '%');

    //ejecuta la consulta
    $consulta->execute();

    //devuelve el total como numero entero
    return (int)$consulta->fetchColumn();
}


//obtiene las imagenes por id 
function getImagenesEquipo(PDO $conn, $equipo_id) {
    $consulta = $conn->prepare('SELECT id FROM equipo_imagenes WHERE equipo_id = :equipo_id ORDER BY id');
    $consulta->execute(['equipo_id' => $equipo_id]);
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}



//busca los equipos por nombre
function findNombresEquipos(PDO $conn, $nombreEquipo)
{
    $statement = $conn->prepare('SELECT id, nombre, categoria_id, codigo, active, imagen_ruta, versionn, eliminado_en FROM equipos WHERE nombre = :nombreEquipo AND active = true LIMIT 1');
    $statement->execute(['nombreEquipo' => $nombreEquipo]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}
