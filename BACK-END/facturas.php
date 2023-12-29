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

if (isset($_GET["consultarFactura"])){
  $appfacturas = mysqli_query($conexionBD,"SELECT * 
                                          FROM facturas                                       
                                          WHERE Id =".$_GET["consultarFactura"]);
  if(mysqli_num_rows($appfacturas) > 0){
      $appfacturas = mysqli_fetch_all($appfacturas,MYSQLI_ASSOC);
      echo json_encode($appfacturas);
      exit();
  }
  else{  echo json_encode(["success"=>0]); }
}


if (isset($_GET["consultarValor"])){
  $appfacturas = mysqli_query($conexionBD,"SELECT  SUM(C.Valor) FROM facturas F 
                                          LEFT JOIN cargas C ON F.Id = C.IdFac
                                          WHERE F.Id =".$_GET["consultarValor"]);
  if(mysqli_num_rows($appfacturas) > 0){
      $appfacturas = mysqli_fetch_all($appfacturas,MYSQLI_ASSOC);
      echo json_encode($appfacturas);
      exit();
  }
  else{  echo json_encode(["success"=>0]); }
}

if (isset($_GET["consultar"])) {
  $consultaFacturas = mysqli_query($conexionBD, "SELECT * FROM facturas");
  if (mysqli_num_rows($consultaFacturas) > 0) {
    $facturas = mysqli_fetch_all($consultaFacturas, MYSQLI_ASSOC);
    echo json_encode($facturas);
  } else {
    echo json_encode(["success" => 0]);
  }
}

?>