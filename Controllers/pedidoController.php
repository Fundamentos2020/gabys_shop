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

        if (!isset($json_data->id_usuario) || !isset($json_data->total) || !isset($json_data->fecha_estimada)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->id_usuario) ? $response->addMessage("El id del usuario es obligatorio") : false);
                (!isset($json_data->total) ? $response->addMessage("El total es obligatorio") : false);
                (!isset($json_data->fecha_estimada) ? $response->addMessage("La fecha estimada obligatoria") : false);
                $response->send();
                exit();
        }

        //Validar pedido

        $id_usuario = $json_data->id_usuario;
        $total = $json_data->total;
        $fecha_estimada = $json_data->fecha_estimada;


        try {
            /*$query = $connection->prepare('SELECT id_pedido FROM pedido WHERE id_usuario = :id_usuario');
            $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $query->execute();
        
            $rowCount = $query->rowCount();
        
            if($rowCount !== 0) {//Si el producto ya existe
                $response = new Response();
                $response->setHttpStatusCode(409);
                $response->setSuccess(false);
                $response->addMessage("Ya hay un pedido existente con ese ID");
                $response->send();
                exit();
            }*/
            
            $query = $connection->prepare('INSERT INTO pedido(id_usuario, total, fecha_estimada) VALUES(:id_usuario, :total, STR_TO_DATE(:fecha_estimada, \'%Y-%m-%d\'))');
            $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $query->bindParam(':total', $total, PDO::PARAM_STR);
            $query->bindParam(':fecha_estimada', $fecha_estimada, PDO::PARAM_STR);
            $query->execute();
        
            $rowCount = $query->rowCount();
        
            if($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al ingresar pedido - inténtelo de nuevo");
                $response->send();
                exit();
            }
        
            $ultimoID = $connection->lastInsertId();
        
            $returnData = array();
            $returnData['id_pedido'] = $ultimoID;
            $returnData['id_usuario'] = $id_usuario;
            $returnData['total'] = $total;
            $returnData['fecha_estimada'] = $fecha_estimada;
        
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("Pedido realizado");
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch (PedidoException $e) {
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