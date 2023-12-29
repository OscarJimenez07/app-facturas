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

if ($conexionBD->connect_error) {
  die("Conexión fallida: " . $conexionBD->connect_error);
}

$data = json_decode(file_get_contents("php://input"));
if ($data && isset($data->UsrNam) && isset($data->Pass)) {
  $UsrNam = $data->UsrNam;
  $Pass = $data->Pass;
  
  $selectQuery = "SELECT Pass, IdRol FROM encargados WHERE UsrNam='$UsrNam'";
  $result = $conexionBD->query($selectQuery);
  
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashedPass = $row["Pass"];
    $IdRol = $row["IdRol"];
    
    if (password_verify($Pass, $hashedPass)) {
   
      session_start();
      $_SESSION['loggedin'] = true;
      $_SESSION['username'] = $UsrNam;
      $_SESSION['userRole'] = $IdRol; 
      echo json_encode(["success" => 1, "IdRol" => $IdRol]);
    } else {

      echo json_encode(["success" => 0]);
    }
  } else {

    echo json_encode(["success" => 0]);
  }
}

$conexionBD->close();
?>

