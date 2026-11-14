<?php


//obtiene todos las categorias y si hay uno false no lo agrega ala lista
function getCategorias(PDO $conn, int $limite, int $offset, string $filtro = '')
{
    $consulta = $conn->prepare(
            'SELECT id, nombre, dias_max_prestamo, active FROM categorias
            WHERE active = true AND nombre LIKE :filtro
            ORDER BY id LIMIT :limite OFFSET :offset'
        );

        //nombre de la categoria que se desea buscar
        $consulta->bindValue(':filtro', '%' . $filtro . '%');


        //cantidad de categorias que se mostraran
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);

        //caantidad de categorais que se saltaran
        $consulta->bindValue(':offset', $offset, PDO::PARAM_INT);

        //ejecuta la consults
        $consulta->execute();

        //fevuelve las categorias encontradas
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

 //cuenta la cantidad total de categorias activas
    function ContarCategorias(PDO $conn, string $filtro = '')
    {
        //cuenta las categorias que no estan eliminadas
        $consulta = $conn->prepare('SELECT COUNT(*) FROM categorias WHERE active = true AND nombre LIKE :filtro');

        //nombre de la categoria que se desea buscar
        $consulta->bindValue(':filtro', '%' . $filtro . '%');

        //ejecuta la consulta
        $consulta->execute();

        //devuelve el total como numero entero
        return (int)$consulta->fetchColumn();
    }



//obtiene todos los nombres de categorias
function getCatego(PDO $conn) {
    return $conn->query('SELECT nombre FROM categorias WHERE active = true ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
}


//busca los nombre por en categoria
function findcategorias(PDO $conn, $nombre)
{
    $statement = $conn->prepare('SELECT id, nombre, dias_max_prestamo, active FROM categorias WHERE nombre = :nombre AND active = true LIMIT 1');
    $statement->execute(['nombre' => $nombre]);
    return $statement->fetch(PDO::FETCH_ASSOC);
}


//obtiene todas las categorias activas para el select de equipos
function getCategoriasParaSelect(PDO $conn)
{
    $consulta = $conn->query( 'SELECT id, nombre FROM categorias WHERE active = true ORDER BY nombre');

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}