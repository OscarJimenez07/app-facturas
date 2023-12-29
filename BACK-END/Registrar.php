<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST");
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

$nombreUsuario = "soporte";
$password = "Ardisa123+";
$IdRol = 1;

$passEncriptada = password_hash($password, PASSWORD_DEFAULT);

$consulta = $conexionBD->prepare("INSERT INTO usuarios (UsrNam, Pass, IdRol) VALUES (?, ?, ?)");

$consulta->bind_param("ssi", $nombreUsuario, $passEncriptada, $IdRol);

if ($consulta->execute()) {
    echo json_encode(["success" => 1, "message" => "Usuario registrado con éxito"]);
} else {
    echo json_encode(["success" => 0, "error" => $conexionBD->error]);
}

$consulta->close();
$conexionBD->close();
?>


