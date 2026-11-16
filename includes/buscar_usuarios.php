<?php


//Obtiene todo lo datos de la tabla usuarios y los devuelve en un array PDO
function getUsers(PDO $conn, int $limite, int $offset, string $filtro = '')
{
    $consulta = $conn->prepare(
        'SELECT u.id, u.nombre, u.correo, u.active, r.nombre AS rol FROM usuarios u
         LEFT JOIN roles r ON r.id = u.id_rol WHERE u.active = true 
         AND u.nombre LIKE :filtro
         -- se implementan limit para limitar ala cantidad solicitado por el usuario 
         -- y se implementa offset para saltar las "n" filas que se usarion en limit
         
         ORDER BY u.id  LIMIT :limite OFFSET :offset');

         //nombre del usuario que se desea buscar
        $consulta->bindValue(':filtro', '%' . $filtro . '%');

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
function ContarUsuarios(PDO $conn, string $filtro = ''){

    //La consulta contara la cantidad de usuarios activos y los llenara en el $consulta
     $consulta = $conn->prepare('SELECT COUNT(*) FROM usuarios  WHERE active = true AND nombre LIKE :filtro');
    //Retornara un entero con la cantidad de usuarios 
    //nombre del usuario que se desea buscar
    $consulta->bindValue(':filtro', '%' . $filtro . '%');

    //ejecuta la consulta
    $consulta->execute();

    return (int)$consulta->fetchColumn();
}


//obtiene todos los correos 
function getcorreo(PDO $conn) {
    return $conn->query('SELECT correo FROM usuarios ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
}


//busca los usuarios por correo
function findcorreo(PDO $conn, $correo)
{
    $statement = $conn->prepare('SELECT id, nombre, correo, id_rol, active FROM usuarios WHERE correo = :correo LIMIT 1');
    $statement->execute(['correo' => $correo]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}

//obtiene el rol actual del usuario
function obtenerRolActual(PDO $conn, int $id){

    $consulta = $conn->prepare('SELECT id_rol FROM usuarios
    WHERE id = :id AND active = true LIMIT 1');

    $consulta->execute(['id' => $id]);

    //obtiene el rol del usuario
    $rol = $consulta->fetchColumn();

    if($rol === false){
        return false;
    }

    return (int)$rol;
}