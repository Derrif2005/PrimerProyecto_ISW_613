<?php


//Obtiene todo lo datos de la tabla usuarios y los devuelve en un array PDO
function getPrestamos(PDO $conn)
{
     $consulta = $conn->query(
        'SELECT p.id, p.usuario_id, p.operador_id, p.estado, p.fecha_inicio, p.fecha_fin, p.active FROM prestamos p
         WHERE p.active = true ORDER BY p.id'
    );

    //Devuelve los prestamos encontrados
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}





