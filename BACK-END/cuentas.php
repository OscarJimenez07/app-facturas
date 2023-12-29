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

if (isset($_GET["Encargado"])){
  $Encargado = $conexionBD->real_escape_string($_GET["Encargado"]);

  $appfacturas = mysqli_query($conexionBD, "SELECT CU.Numero
                                          FROM encargados_cuentas EC
                                          JOIN encargados E ON EC.Encargado_Id = E.Id
                                          LEFT JOIN cuentas CU ON EC.Cuenta_Id = CU.Id
                                          WHERE E.UsrNam = '$Encargado'");

  if(mysqli_num_rows($appfacturas) > 0){
      $appfacturas = mysqli_fetch_all($appfacturas, MYSQLI_ASSOC);
      echo json_encode($appfacturas);
      exit();
  } else {
      echo json_encode(["success" => 0]);
  }
}


?>