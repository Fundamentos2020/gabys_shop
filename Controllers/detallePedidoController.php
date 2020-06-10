<?php

    require_once('../Models/DB.php');
    require_once('../Models/Pedido.php');
    require_once('../Models/Response.php');

    try{//Conexion al principio
        $connection = Conexion::conecta();
    }
    catch(PDOException $e){
        error_log("Error de conexión" . $e);

        $response = new Response();
        $response->setHttpStatusCode(500); 
        $response->setSuccess(false);
        $response->addMessage("Error en conexión a Base de datos");
        $response->send();
        exit();
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST'){//POST registro de nuevo predido
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

        if (!isset($json_data->id_pedido) || !isset($json_data->id_producto) || !isset($json_data->cantidad) || !isset($json_data->subtotal)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->id_pedido) ? $response->addMessage("El id del pedido es obligatorio") : false);
                (!isset($json_data->id_producto) ? $response->addMessage("El id_producto es obligatorio") : false);
                (!isset($json_data->cantidad) ? $response->addMessage("La cantidad es obligatoria") : false);
                (!isset($json_data->subtotal) ? $response->addMessage("El subtotal es obligatorio") : false);
                $response->send();
                exit();
        }

        //Validar detalle pedido

        $id_producto = $json_data->id_producto;
        $id_pedido = $json_data->id_pedido;
        $cantidad = $json_data->cantidad;
        $subtotal = $json_data->subtotal;


        try {
            $query = $connection->prepare('INSERT INTO detalle_pedido(id_pedido, id_producto, cantidad, subtotal) VALUES(:id_pedido, :id_producto, :cantidad, :subtotal)');
            $query->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $query->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $query->bindParam(':subtotal', $subtotal, PDO::PARAM_STR);
            $query->execute();
        
            $rowCount = $query->rowCount();
        
            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al ingresar detalle pedido - inténtelo de nuevo");
                $response->send();
                exit();
            }
        
            $returnData = array();
            $returnData['id_pedido'] = $id_pedido;
            $returnData['id_producto'] = $id_producto;
            $returnData['cantidad'] = $cantidad;
            $returnData['subtotal'] = $subtotal;
        
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Detalle pedido insertado");
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (DetallePedidoException $e) {
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