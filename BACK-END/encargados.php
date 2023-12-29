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

if (isset($_GET["consultarEncargados"])) {
    $idConsulta = mysqli_real_escape_string($conexionBD, $_GET["consultarEncargados"]);

    $query = "SELECT MAX(Nombre) as Nombre, MAX(UsrNam) as UsrNam, MAX(Correo) as Correo
              FROM log_facturas
              WHERE Id = $idConsulta
              GROUP BY Nombre, UsrNam, Correo";

    $result = mysqli_query($conexionBD, $query);

    if ($result) {
        $appfacturas = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($appfacturas);
        exit();
    } else {
        echo json_encode(["success" => 0]);
    }
}

if (isset($_GET["Encargado"])){
    $Encargado = $conexionBD->real_escape_string($_GET["Encargado"]);

    $appfacturas = mysqli_query($conexionBD, "SELECT Cupo
                                            FROM encargados                                        
                                            WHERE UsrNam = '$Encargado'");

    if(mysqli_num_rows($appfacturas) > 0){
        $appfacturas = mysqli_fetch_all($appfacturas, MYSQLI_ASSOC);
        echo json_encode($appfacturas);
        exit();
    } else {
        echo json_encode(["success" => 0]);
    }
}



$crudnomina = mysqli_query($conexionBD,"SELECT * FROM encargados");
if(mysqli_num_rows($crudnomina) > 0){
    $crudnomina = mysqli_fetch_all($crudnomina,MYSQLI_ASSOC);
    echo json_encode($crudnomina);
}
else{ echo json_encode([["success"=>0]]); }



?>