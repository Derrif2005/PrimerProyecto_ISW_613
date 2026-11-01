<?php

function getUsers(PDO $conn)
{
    return $conn->query(
        'SELECT u.id, u.nombre, u.correo, u.active, r.nombre AS rol 
         FROM usuarios u
         LEFT JOIN roles r ON r.id = u.id_rol
         ORDER BY u.id'
    )->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Returns an array of all provinces, each with 'id' and 'name' keys
 * @param PDO $conn The database connection
 * @return array An array of associative arrays, each representing a province
 */

//obtiene todos los correos 
function getcorreo(PDO $conn) {
    return $conn->query('SELECT correo FROM usuarios ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Finds a province by its ID
 * @param PDO $conn The database connection
 * @param int $provinceId The ID of the province to find
 * @return array|null An associative array with 'id' and 'name' keys if found, or null if not found
 */

//busca los usuarios por correo
function findcorreo(PDO $conn, $correo)
{
    $statement = $conn->prepare('SELECT id, nombre, correo, id_rol, active FROM usuarios WHERE correo = :correo LIMIT 1');
    $statement->execute(['correo' => $correo]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}
