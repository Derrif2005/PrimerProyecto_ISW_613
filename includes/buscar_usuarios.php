<?php


//Obtiene todo lo datos de la tabla usuarios y los devuelve en un array PDO
function getUsers(PDO $conn, int $limite, int $offset)
{
    $consulta = $conn->prepare(
        'SELECT u.id, u.nombre, u.correo, u.active, r.nombre AS rol FROM usuarios u
         LEFT JOIN roles r ON r.id = u.id_rol WHERE u.active = true ORDER BY u.id 
         -- se implementan limit para limitar ala cantidad solicitado por el usuario 
         -- y se implementa offset para saltar las "n" filas que se usarion en limit
         LIMIT :limite OFFSET :offset');

         // Indica cuantos registros queremos mostrar
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);

        // Indica cuantos registros vamos a saltar
         $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Ejecuta la consulta SQL
        $consulta->execute();

        // Devuelve los usuarios encontrados
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


//Contara usuarios por medio del count
function ContarUsuarios(PDO $conn){

    //La consulta contara la cantidad de usuarios activos y los llenara en el $consulta
    $consulta = $conn->query('SELECT COUNT(*) FROM usuarios  WHERE u.active = true')
    //Retornara un entero con la cantidad de usuarios 
    return (int)$consulta->fetchColumn();
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
