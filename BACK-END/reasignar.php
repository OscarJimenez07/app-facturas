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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $Id = !empty($data->Id) ? $conexionBD->real_escape_string($data->Id) : null;
    $Encargado = !empty($data->Encargado) ? $conexionBD->real_escape_string($data->Encargado) : null;
    $Usuario = !empty($data->Usuario) ? $conexionBD->real_escape_string($data->Usuario) : null;
    $Correo = !empty($data->Correo) ? $conexionBD->real_escape_string($data->Correo) : null;
    $Modifico = !empty($data->Modifico) ? $conexionBD->real_escape_string($data->Modifico) : null;

    $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Encargado = '$Encargado', Usuario = '$Usuario', Correo = '$Correo', Estado = 'REASIGNADA', Modifico = '$Modifico' WHERE Id = '$Id'");

    // if ($updateEstado) {
    //     // Llamada al script Python
    //     $cmd = "python enviarCorreo.py \"$Encargado\" \"$Correo\" \"$Id\"";
    //     shell_exec($cmd);
    //     echo json_encode(["success" => 1, "email_sent" => 1]);
    //     exit();
    // } else {
    //     echo json_encode(["success" => 0]);
    // }
}
?>
