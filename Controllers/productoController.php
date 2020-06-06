<?php

    require_once('../Models/DB.php');
    require_once('../Models/Producto.php');
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

    if($_SERVER['REQUEST_METHOD'] === 'GET'){//GET
        try{
            $query = $connection->prepare('SELECT id_producto, id_vendedor, nombre, descripcion, precio, cantidad, descuento, aprobado, imagen FROM producto WHERE aprobado = 1');
            $query->execute();
    
            $rowCount = $query->rowCount();    
            $productos = array();
        
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $producto = new Producto($row['id_producto'], $row['id_vendedor'], $row['nombre'], $row['descripcion'], $row['precio'], $row['cantidad'], $row['descuento'], $row['aprobado'], $row['imagen']);
                $producto->setImagen("data:imagen/jpg;base64,". base64_encode($row['imagen']));
                $productos[] = $producto->getProducto();
            }
            $returnData = array();
            $returnData['total registros'] = $rowCount;
            $returnData['productos'] = $productos;


            $response = new Response();
            $response->setHttpStatusCode(200);//Cuando se ejecuto correctamente 
            $response->setSuccess(true);
            $response->setToCache(true);//Cache es solo para listados
            $response->setData($returnData);
            $response->send();
            //return $returnData;
            //json_encode($productos);
            exit();            
        }
        catch(ProductoException $e){//Error en Tarea
            $response = new Response();
            $response->setHttpStatusCode(500); 
            $response->setSuccess(false);
            $response->addMessage($e->getMessage());
            $response->send();
            exit();
        }
        catch(PDOException $e){//Error en la consulta
            error_log("Error en BD" . $e);

            $response = new Response();
            $response->setHttpStatusCode(500); 
            $response->setSuccess(false);
            $response->addMessage("Error en consulta de producto");
            $response->send();
            exit();
        }
    }
?>