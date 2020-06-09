<?php 

require_once('../Models/DB.php');
require_once('../Models/Response.php');

try {
    $connection = DB::getConnection();
}
catch (PDOException $e){
    error_log("Error de conexión - " . $e);

    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Error en conexión a Base de datos");
    $response->send();
    exit();
}

//Checar sesiones


if($_SERVER['REQUEST_METHOD'] === 'POST'){//POST registro de nueva solicitud
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Método no permitido");
        $response->send();
        exit();
    }
    
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Encabezado Content Type no es JSON");
        $response->send();
        exit();
    }

    $postData = file_get_contents('php://input');

    if (!$json_data = json_decode($postData)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("El cuerpo de la solicitud no es un JSON válido");
        $response->send();
        exit();
    }

    if (!isset($json_data->id_vendedor) || !isset($json_data->id_producto)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($json_data->id_vendedor) ? $response->addMessage("El vendedor es obligatorio") : false);
            (!isset($json_data->nombre) ? $response->addMessage("El producto es obligatorio") : false);
            $response->send();
            exit();
    }

    $id_vendedor = $json_data->id_vendedor;
    $id_producto = $json_data->id_producto;
    $aprobado = $json_data->aprobada;

    try {
        $query = $connection->prepare('SELECT id_solicitud FROM solicitud WHERE id_vendedor = :id_vendedor AND id_producto = :id_producto');
        $query->bindParam(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
        $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $query->execute();
    
        $rowCount = $query->rowCount();
    
        if($rowCount !== 0) {//Si el producto ya existe
            $response = new Response();
            $response->setHttpStatusCode(409);
            $response->setSuccess(false);
            $response->addMessage("Ya tienes una solicitud de ese producto");
            $response->send();
            exit();
        }

        $query = $connection->prepare('INSERT INTO solicitud(id_vendedor, id_producto, aprobada) VALUES(:id_vendedor, :id_producto, :aprobada)');
        $query->bindParam(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
        $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $query->bindParam(':aprobada', $aprobada, PDO::PARAM_STR);
        $query->execute();
    
        $rowCount = $query->rowCount();
    
        if($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al ingresar solicitud - inténtelo de nuevo");
            $response->send();
            exit();
        }
    
        $ultimoID = $connection->lastInsertId();
    
        $returnData = array();
        $returnData['id_solicitud'] = $id_solicitud;
        $returnData['id_producto'] = $id_producto;
        $returnData['id_vendedor'] = $id_vendedor;
        $returnData['aprobada'] = $aprobada;
    
        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("Solicitud enviada para su revision");
        $response->setData($returnData);
        $response->send();
        exit();
    }
    catch (SolicitudException $e) {
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage($e->getMessage());
        $response->send();
        exit();
    }
    catch(PDOException $e) {
        error_log('Error en BD - ' . $e);
    
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al ingresar producto");
        $response->send();
        exit();
    }
}



?>