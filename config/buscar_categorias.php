<?php



function getCategorias(PDO $conn)
{
    return $conn->query('SELECT id, nombre, dias_max_prestamo, active FROM categorias WHERE active = true ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns an array of all provinces, each with 'id' and 'name' keys
 * @param PDO $conn The database connection
 * @return array An array of associative arrays, each representing a province
 */


//obtiene todos las categorias y si hay uno false no lo agrega ala lista
function getCatego(PDO $conn) {
    return $conn->query('SELECT nombre FROM categorias ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Finds a province by its ID
 * @param PDO $conn The database connection
 * @param int $provinceId The ID of the province to find
 * @return array|null An associative array with 'id' and 'name' keys if found, or null if not found
 */

//busca los nombre por en categoria
function findcategorias(PDO $conn, $nombre)
{
    $statement = $conn->prepare('SELECT id, nombre, dias_max_prestamo, active FROM categorias WHERE nombre = :nombre AND active = true LIMIT 1');
    $statement->execute(['nombre' => $nombre]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}
