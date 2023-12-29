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

if (isset($_GET["consultarCargas"])){
    $appfacturas = mysqli_query($conexionBD,"SELECT  C.* FROM facturas F 
                                            LEFT JOIN cargas C ON F.Id = C.IdFac
                                            WHERE F.Id =".$_GET["consultarCargas"]);
    if(mysqli_num_rows($appfacturas) > 0){
        $appfacturas = mysqli_fetch_all($appfacturas,MYSQLI_ASSOC);
        echo json_encode($appfacturas);
        exit();
    }
    else{  echo json_encode(["success"=>0]); }
}

if (isset($_GET["consultarBoton"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Id = !empty($data->Id) ? $data->Id : null;
  $UsrNam = !empty($data->UsrNam) ? $data->UsrNam : null;
  
  $consultaSQL = "SELECT MIN(resultado) AS resultado
  FROM ( SELECT 
                                                CASE WHEN Usuario = '$UsrNam' 
                                                THEN 1 ELSE 0 END AS resultado
                                                FROM facturas 
                                                WHERE Id = '$Id'
                  UNION
         SELECT 
                  CASE 
                    WHEN COALESCE(SUM(c.Valor), 0) + COALESCE((SELECT Valor FROM facturas WHERE Id = '$Id'), 0) <= e.Monto THEN 1
                    ELSE 0
                  END AS resultado
                FROM encargados e
                LEFT JOIN cargas c ON e.UsrNam = c.Usuario
                WHERE e.UsrNam = '$UsrNam') AS subconsulta";

  // Ejecutar la consulta
  $appfacturas = mysqli_query($conexionBD, $consultaSQL);

  if ($appfacturas !== false) {
    // Verificar si hay filas en el resultado
    if (mysqli_num_rows($appfacturas) > 0) {
      // Obtener el resultado y enviarlo como JSON
      $appfacturas = mysqli_fetch_all($appfacturas, MYSQLI_ASSOC);
      echo json_encode($appfacturas);
      exit();
    } else {
      // Enviar respuesta JSON en caso de que no haya filas
      echo json_encode(["success" => 0]);
    }
  } else {
    // Manejar errores en la ejecución de la consulta
    echo json_encode(["error" => mysqli_error($conexionBD)]);
  }
}


if (isset ($_GET["editar"])){
  $consulta = mysqli_query($conexionBD, "SELECT Estado AS resultado, Usuario AS usuario  FROM Facturas WHERE Id =".$_GET["editar"]);

  if(mysqli_num_rows($consulta) > 0){
    $consulta = mysqli_fetch_all($consulta,MYSQLI_ASSOC);
    echo json_encode($consulta);
}
else{ echo json_encode([["success"=>0]]); }
}


if (isset($_GET["getOk"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Id = !empty($data->Id) ? $data->Id : null;
  $Usuario = !empty($data->Usuario) ? $data->Usuario : null;
  
                                                $consultaSQL = "SELECT 
                                                CASE WHEN Usuario = '$Usuario' AND Estado = 'PENDIENTE POR AUTORIZAR'
                                                THEN 1 ELSE 0 END AS resultado
                                                FROM facturas 
                                                WHERE Id = '$Id'";

  $appfacturas = mysqli_query($conexionBD, $consultaSQL);

  if ($appfacturas !== false) {
    if (mysqli_num_rows($appfacturas) > 0) {
      $appfacturas = mysqli_fetch_all($appfacturas, MYSQLI_ASSOC);
      echo json_encode($appfacturas);
      exit();
    } else {
      echo json_encode(["success" => 0]);
    }
  } else {
    echo json_encode(["error" => mysqli_error($conexionBD)]);
  }
}

if (isset($_GET["getContabilizada"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Id = !empty($data->Id) ? $data->Id : null;
  $Usuario = !empty($data->Usuario) ? $data->Usuario : null;

  $consultaSQL = "SELECT MIN(resultado) AS resultado
                  FROM (
                      SELECT 
                          CASE WHEN IdRol = '2' THEN 1 ELSE 0 END AS resultado
                      FROM encargados 
                      WHERE UsrNam = '$Usuario'

                      UNION

                      SELECT 
                          CASE WHEN Estado = 'TRAMITADA' THEN 1 ELSE 0 END AS resultado
                      FROM facturas 
                      WHERE Id = '$Id'
                  ) AS subconsulta";

$appfacturas = mysqli_query($conexionBD, $consultaSQL);

if ($appfacturas !== false) {
  if (mysqli_num_rows($appfacturas) > 0) {
    $appfacturas = mysqli_fetch_all($appfacturas, MYSQLI_ASSOC);
    echo json_encode($appfacturas);
    exit();
  } else {
    echo json_encode(["success" => 0]);
  }
} else {
  echo json_encode(["error" => mysqli_error($conexionBD)]);
}
}


if (isset($_GET["Contabilizar"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Id = !empty($data->Id) ? $data->Id : null;
  $Modifico = !empty($data->Modifico) ? $data->Modifico : null;

     $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Estado = 'CONTABILIZADA', Modifico = '$Modifico'  WHERE Id = '$Id'");
     if ($updateEstado) {
      echo json_encode(["success" => 1]);
      exit();
  } else {
      echo json_encode(["success" => 0, "error" => "No se encontró un encargado del nivel superior"]);
      exit();
  }

}




if (isset($_GET["Ok"])) {
  $data = json_decode(file_get_contents("php://input"));
  $IdFac = !empty($data->IdFac) ? $data->IdFac : null;
  $Modifico = !empty($data->Modifico) ? $data->Modifico : null;

            $nivelActual = mysqli_query($conexionBD, "SELECT Nivel FROM encargados WHERE UsrNam = '$Modifico'");
            $rowNivel = mysqli_fetch_assoc($nivelActual);
            $nivel = $rowNivel['Nivel'];
        
            $encargadoSiguiente = mysqli_query($conexionBD, "SELECT E.Nombre, E.UsrNam, E.Correo FROM encargados_cuentas EC
                                                            JOIN encargados E ON EC.Encargado_Id = E.Id
                                                            WHERE EC.Cuenta_Id = (SELECT Cuenta_Id FROM encargados_cuentas WHERE Encargado_Id = E.Id)
                                                            AND E.Nivel = ($nivel + 1) LIMIT 1");
        
            if ($encargadoSiguiente && $rowEncargado = mysqli_fetch_assoc($encargadoSiguiente)) {
                $nombreEncargado = $rowEncargado['Nombre'];
                $usuarioEncargado = $rowEncargado['UsrNam'];
                $correoEncargado = $rowEncargado['Correo'];

                        $nuevoEstado = "PENDIENTE POR AUTORIZAR";
                        $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Estado = '$nuevoEstado', Encargado = '$nombreEncargado', Usuario = '$usuarioEncargado', Correo = '$correoEncargado', Modifico = '$Modifico' WHERE Id = '$IdFac'");
            } else {
                echo json_encode(["success" => 0, "error" => "No se encontró un encargado del nivel superior"]);
                exit();
            }
}



if (isset($_GET["insertarCargas"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Cuenta = !empty($data->Cuenta) ? $data->Cuenta : null;
  $Centro = !empty($data->Centro) ? $data->Centro : null;
  $Porcentaje = !empty($data->Porcentaje) ? $data->Porcentaje : null;
  $Valor = !empty($data->Valor) ? $data->Valor : null;
  $IdFac = !empty($data->IdFac) ? $data->IdFac : null;
  $UsrNam = !empty($data->UsrNam) ? $data->UsrNam : null;
  $Modifico = !empty($data->Modifico) ? $data->Modifico : null;

  $checkBalance = mysqli_query($conexionBD, "SELECT SUM(Valor) AS sumaValores FROM cargas WHERE Usuario = '$UsrNam'");
  $getCupo = mysqli_query($conexionBD, "SELECT Monto FROM encargados WHERE UsrNam = '$UsrNam'");
  $rowBalance = mysqli_fetch_assoc($checkBalance);
  $valorActual = $rowBalance['sumaValores'];
  $rowCupo = mysqli_fetch_assoc($getCupo);
  $cupo = $rowCupo['Monto'];
  $appfacturas = mysqli_query($conexionBD, "INSERT INTO cargas(Cuenta, Centro, Porcentaje, Valor, Usuario, IdFac) VALUES ('$Cuenta', '$Centro', '$Porcentaje', '$Valor', '$UsrNam', '$IdFac')");
 

      if ($appfacturas ) {
        $sumaValores = mysqli_query($conexionBD, "SELECT SUM(Valor) AS sumaValores FROM cargas WHERE IdFac = '$IdFac'");
        $resultadoSuma = mysqli_fetch_assoc($sumaValores);
        $totalValor = $resultadoSuma['sumaValores'];

          $verificarCargas = mysqli_query($conexionBD, "SELECT COUNT(*) AS totalCargas FROM cargas WHERE IdFac = '$IdFac'");
          $resultadoVerificacion = mysqli_fetch_assoc($verificarCargas);
          $totalCargas = $resultadoVerificacion['totalCargas'];
          $nuevoEstado = ($totalCargas > 0) ? 'RADICADA' : 'SIN RADICAR';

          $consultaEncargado = mysqli_query($conexionBD, "SELECT Nombre, Correo FROM encargados WHERE UsrNam = '$UsrNam'");
          $datosEncargado = mysqli_fetch_assoc($consultaEncargado);
          $encargado = $datosEncargado['Nombre'];
          $correoEncargado = $datosEncargado['Correo'];
  
          $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Estado = '$nuevoEstado', Encargado = '$encargado', Usuario = '$UsrNam', Correo = '$correoEncargado', Valor = '$totalValor', Modifico = '$Modifico'  WHERE Id = '$IdFac'");
  

          if ($valorActual + $Valor > $cupo) {
            $nivelActual = mysqli_query($conexionBD, "SELECT Nivel FROM encargados WHERE UsrNam = '$UsrNam'");
            $rowNivel = mysqli_fetch_assoc($nivelActual);
            $nivel = $rowNivel['Nivel'];
        
            $encargadoSiguiente = mysqli_query($conexionBD, "SELECT E.Nombre, E.UsrNam, E.Correo FROM encargados_cuentas EC
                                                            JOIN encargados E ON EC.Encargado_Id = E.Id
                                                            WHERE EC.Cuenta_Id = (SELECT Cuenta_Id FROM encargados_cuentas WHERE Encargado_Id = E.Id)
                                                            AND E.Nivel = ($nivel + 1) LIMIT 1");
        
            if ($encargadoSiguiente && $rowEncargado = mysqli_fetch_assoc($encargadoSiguiente)) {
                $nombreEncargado = $rowEncargado['Nombre'];
                $usuarioEncargado = $rowEncargado['UsrNam'];
                $correoEncargado = $rowEncargado['Correo'];

                        $nuevoEstado = "PENDIENTE POR AUTORIZAR";
                        $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Estado = '$nuevoEstado', Encargado = '$nombreEncargado', Usuario = '$usuarioEncargado', Correo = '$correoEncargado', Modifico = '$Modifico' WHERE Id = '$IdFac'");
                        recalcularPorcentajes($IdFac);
            } else {
                echo json_encode(["success" => 0, "error" => "No se encontró un encargado del nivel superior"]);
                exit();
            }
        } else {
          $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Estado = 'TRAMITADA', Encargado = '$encargado', Usuario = '$UsrNam', Correo = '$correoEncargado', Valor = '$totalValor', Modifico = '$Modifico' WHERE Id = '$IdFac'");
        }

      } else {

          echo json_encode(["success" => 0, "error" => "Error en la actualización de la tabla facturas o en la resta del monto"]);
          exit();
      }
}



if (isset($_GET["borrarCargas"])) {
  $idCarga = $_GET["borrarCargas"];
  $usrNam = $_GET["UsrNam"]; // Obtener el UsrNam desde la URL
  $idFacAntesBorrar = obtenerIdFac($idCarga);
  $valorCarga = obtenerValorCarga($idCarga);

  $crudnomina = mysqli_query($conexionBD, "DELETE FROM cargas WHERE Id = $idCarga");

  if ($crudnomina) {
    $sumaValores = mysqli_query($conexionBD, "SELECT SUM(Valor) AS sumaValores FROM cargas WHERE IdFac = '$idFacAntesBorrar'");
    $resultadoSuma = mysqli_fetch_assoc($sumaValores);
    $totalValor = $resultadoSuma['sumaValores'];

    $updateFactura = mysqli_query($conexionBD, "UPDATE facturas SET Valor = '$totalValor' WHERE Id = '$idFacAntesBorrar'");
   
    if ($updateFactura) {
      $verificarCargas = mysqli_query($conexionBD, "SELECT COUNT(*) AS totalCargas FROM cargas WHERE IdFac = '$idFacAntesBorrar'");
      $resultadoVerificacion = mysqli_fetch_assoc($verificarCargas);
      $totalCargas = $resultadoVerificacion['totalCargas'];

      $nuevoEstado = ($totalCargas > 0) ? 'RADICADA' : 'SIN RADICAR';
      $updateEstado = mysqli_query($conexionBD, "UPDATE facturas SET Estado = '$nuevoEstado' WHERE Id = '$idFacAntesBorrar'");
      recalcularPorcentajes($idFacAntesBorrar);
    } else {
      echo json_encode(["success" => 0, "error" => "Error en la actualización de la tabla facturas después de borrar carga"]);
    }
  } else {
    echo json_encode(["success" => 0, "error" => "Error en borrar de la tabla cargas"]);
  }

  exit();
}



if (isset($_GET["autorizar"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Usuario = !empty($data->Usuario) ? $data->Usuario : null;
  $Id = !empty($data->Id) ? $data->Id : null;
  $Modifico = !empty($data->Modifico) ? $data->Modifico : null;
  $consultaSelect = mysqli_query($conexionBD, "SELECT Valor FROM facturas WHERE Id = '$Id'");
  $fila = mysqli_fetch_assoc($consultaSelect);

  // Verifica si la consulta fue exitosa y si existe la fila
  if ($consultaSelect && $fila) {
    $valor = $fila['Valor'];
    
    $Insert = mysqli_query($conexionBD, "INSERT INTO cargas (Valor, Usuario) VALUES ('$valor', '$Usuario')");
    
    if ($Insert) {
      $consultaUpdate = mysqli_query($conexionBD, "UPDATE facturas SET Estado='TRAMITADA', Modifico= '$Modifico' WHERE Id = '$Id'");
      if (!$consultaUpdate) {
        // Manejar error de actualización
        echo json_encode(array('error' => 'Error actualizando el estado de la factura'));
      }
    } else {
      // Manejar error de inserción
      echo json_encode(array('error' => 'Error al insertar en la tabla cargas'));
    }
  } else {
    // Manejar error de consulta o no se encontraron filas
    echo json_encode(array('error' => 'Error al consultar la tabla facturas o no se encontraron filas'));
  }
}







// Función para obtener el valor de la carga antes de borrarla
function obtenerValorCarga($idCarga) {
  global $conexionBD;
  $result = mysqli_query($conexionBD, "SELECT Valor FROM cargas WHERE Id = '$idCarga'");
  $row = mysqli_fetch_assoc($result);
  return $row['Valor'];
}


function obtenerIdFac($idCarga)
{
  global $conexionBD;
  $result = mysqli_query($conexionBD, "SELECT IdFac FROM cargas WHERE Id = $idCarga");
  $row = mysqli_fetch_assoc($result);
  return $row['IdFac'];
}

function recalcularPorcentajes($idFac)
{
  global $conexionBD;
  $sqlSumaValor = "SELECT SUM(Valor) as total FROM cargas WHERE IdFac = $idFac";
  $resultSumaValor = mysqli_query($conexionBD, $sqlSumaValor);

  if ($resultSumaValor) {
      $rowSumaValor = mysqli_fetch_assoc($resultSumaValor);
      $totalValor = $rowSumaValor['total'];
      $sqlActualizarPorcentajes = "UPDATE cargas SET Porcentaje = ROUND((Valor / $totalValor) * 100, 2) WHERE IdFac = $idFac";
      mysqli_query($conexionBD, $sqlActualizarPorcentajes);
  }
}


if (isset($_GET["actualizarEstado"])) {
  $data = json_decode(file_get_contents("php://input"));
  $Id = !empty($data->Id) ? $data->Id : null;
  $Estado = !empty($data->Estado) ? $data->Estado : null;
  $appfacturas = mysqli_query($conexionBD, "UPDATE facturas SET Estado = '$Estado' WHERE Id = '$Id'");
  if ($appfacturas) {
      echo json_encode(["success" => 1]);
      exit();
  } else {
      echo json_encode(["success" => 0, "error" => "Error en la actualización del estado de la factura"]);
      exit();
  }
}


?>
