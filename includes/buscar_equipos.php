<?php

function getEquipos(PDO $conn)
{
    return $conn->query( 'SELECT e.id, e.categoria_id, c.nombre AS categoria, e.nombre,  e.codigo, e.active, e.imagen_ruta, e.versionn, e.eliminado_en  
         FROM equipos e
         LEFT JOIN categorias c ON c.id = e.categoria_id WHERE e.active = true
         ORDER BY e.id'
    )->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Returns an array of all provinces, each with 'id' and 'name' keys
 * @param PDO $conn The database connection
 * @return array An array of associative arrays, each representing a province
 */


//obtiene todos los nombres 
function getImagenesEquipo(PDO $conn, $equipo_id) {
    $consulta = $conn->prepare('SELECT id FROM equipo_imagenes WHERE equipo_id :equipo_id ORDER BY id');
    $consulta->execute(['equipo_id' => $equipo_id]);
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

/* 
 * Finds a province by its ID
 * @param PDO $conn The database connection
 * @param int $provinceId The ID of the province to find
 * @return array|null An associative array with 'id' and 'name' keys if found, or null if not found
 */




//busca los equipos por nombre
function findNombresEquipos(PDO $conn, $nombreEquipo)
{
    $statement = $conn->prepare('SELECT id, nombre, categoria_id, codigo, active, imagen_ruta, versionn, eliminado_en FROM equipos WHERE nombre = :nombreEquipo AND active = true LIMIT 1');
    $statement->execute(['nombreEquipo' => $nombreEquipo]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}
