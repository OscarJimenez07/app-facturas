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

if (isset($_GET["Id"])) {
    // Obtén el valor de Id
    $id = $_GET["Id"];

    // Realiza la consulta SQL con GROUP BY correctamente
    $appfacturas = mysqli_query($conexionBD, "SELECT MAX(Fecha_Hora) AS Fecha_Hora, MAX(Nombre) AS Encargado, MAX(Estado) AS Estado, MAX(Modifico) AS Modifico
                                              FROM log_facturas
                                              WHERE Id = $id                                             
                                              GROUP BY Fecha_Hora, Modifico, Nombre, Estado
                                              ORDER BY Fecha_Hora ASC");

    // Verifica si la consulta devuelve resultados
    if ($appfacturas && mysqli_num_rows($appfacturas) > 0) {
        // Recupera los resultados como un array asociativo
        $appfacturas = mysqli_fetch_all($appfacturas, MYSQLI_ASSOC);

        // Imprime los resultados como JSON
        echo json_encode($appfacturas);
        exit();
    } else {
        // Si no hay resultados, imprime un JSON indicando el fallo
        echo json_encode(["success" => 0]);
    }
}

?>