<?php 

$correo = $_POST['correo'] ?? ";
if(!is_string($correo)){
    exit('dato invalido');
}

echo 'Correo recibido: ' .htmlspecialchars($correo, ENT_QUOTES, 'UTF-8');

?>