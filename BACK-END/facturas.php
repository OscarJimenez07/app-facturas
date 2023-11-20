<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET,POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$servidor = "localhost"; 
$usuario = "root"; 
$contrasenia = ""; 
$IdBaseDatos = "appfacturas";

$conexionBD = new mysqli($servidor, $usuario, $contrasenia, $IdBaseDatos);

// Verificar la conexión
if ($conexionBD->connect_error) {
  die("Conexión fallida: " . $conexionBD->connect_error);
}

$crudnomina = mysqli_query($conexionBD,"SELECT * FROM facturas");
if(mysqli_num_rows($crudnomina) > 0){
    $crudnomina = mysqli_fetch_all($crudnomina,MYSQLI_ASSOC);
    echo json_encode($crudnomina);
}
else{ echo json_encode([["success"=>0]]); }


?>