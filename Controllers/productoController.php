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

    /*if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
        $response = new Response();
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);
        $response->addMessage("No se encontró el token de acceso");
        $response->send();
        exit();
    }*/

    $accesstoken = $_SERVER['HTTP_AUTHORIZATION']; 


    /*try {
        $query = $connection->prepare('SELECT caducidad_token_acceso FROM sesiones WHERE token_acceso = :token_acceso');
        $query->bindParam(':token_acceso', $accesstoken, PDO::PARAM_STR);
        $query->execute();
    
        $rowCount = $query->rowCount();
    
        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Token de acceso no válido");
            $response->send();
            exit();
        }
    
        $row = $query->fetch(PDO::FETCH_ASSOC);
    
        //$consulta_idUsuario = $row['id_usuario'];
        $consulta_cadTokenAcceso = $row['caducidad_token_acceso'];
        //$consulta_activo = $row['activo'];
    
        if($consulta_activo !== 'SI') {
            $response = new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Cuenta de usuario no activa");
            $response->send();
            exit();
        }*/
        //date_default_timezone_set("America/Mexico_City");
    
        /*if (strtotime($consulta_cadTokenAcceso) < time()) {
            $response = new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Token de acceso ha caducado");
            $response->send();
            exit();
        }*/
    /*} 
    catch (PDOException $e) {
        error_log('Error en DB - ' . $e);
    
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al autenticar usuario");
        $response->send();
        exit();
    }*/


    if(array_key_exists('aprobado', $_GET)) {//Parametro con valor de aprobado
        $aprobado = $_GET['aprobado'];
        if ($aprobado == '' || !is_numeric($aprobado)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El valor de aprobado no es valido");
            $response->send();
            exit();
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'GET'){//GET
            try{
                $query = $connection->prepare('SELECT id_producto, id_vendedor, nombre, descripcion, precio, cantidad, descuento, aprobado, imagen FROM producto WHERE aprobado = :aprobado');
                $query->bindParam(':aprobado', $aprobado, PDO::PARAM_INT);
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
    }
    else{//Sin parametros
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
        }elseif($_SERVER['REQUEST_METHOD'] === 'POST'){//POST registro de nuevo producto
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

            if (!isset($json_data->id_vendedor) || !isset($json_data->nombre) || !isset($json_data->descripcion) 
                || !isset($json_data->precio) || !isset($json_data->cantidad) || !isset($json_data->descuento) 
                || !isset($json_data->aprobado) || !isset($json_data->imagen)) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    (!isset($json_data->id_vendedor) ? $response->addMessage("El vendedor es obligatorio") : false);
                    (!isset($json_data->nombre) ? $response->addMessage("El nombre es obligatorio") : false);
                    (!isset($json_data->descripcion) ? $response->addMessage("La descripcion obligatoria") : false);
                    (!isset($json_data->precio) ? $response->addMessage("El precio es obligatorio") : false);
                    (!isset($json_data->cantidad) ? $response->addMessage("La cantidad es obligatoria") : false);
                    (!isset($json_data->descuento) ? $response->addMessage("El descuento es obligatorio") : false);
                    (!isset($json_data->aprobado) ? $response->addMessage("La estado aprobado es obligatorio") : false);
                    (!isset($json_data->imagen) ? $response->addMessage("La imagen es obligatoria") : false);
                    $response->send();
                    exit();
            }

            $id_vendedor = $json_data->id_vendedor;
            $nombre = $json_data->nombre;
            $descripcion = $json_data->descripcion;
            $precio = $json_data->precio;
            $cantidad = $json_data->cantidad;
            $descuento = $json_data->descuento;
            $aprobado = $json_data->aprobado;
            $imagen = $json_data->imagen;

            try {
                $query = $connection->prepare('SELECT id_producto FROM producto WHERE nombre = :nombre AND id_vendedor = :id_vendedor');
                $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $query->bindParam(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
                $query->execute();
            
                $rowCount = $query->rowCount();
            
                if($rowCount !== 0) {//Si el producto ya existe
                    $response = new Response();
                    $response->setHttpStatusCode(409);
                    $response->setSuccess(false);
                    $response->addMessage("Ya tienes un proudcto con ese nombre");
                    $response->send();
                    exit();
                }
                
                $query = $connection->prepare('INSERT INTO producto(id_vendedor, nombre, descripcion, precio, cantidad, descuento, 
                    aprobado, imagen) VALUES(:id_vendedor, :nombre, :descripcion, :precio, :cantidad, :descuento, :aprobado, :imagen)');
                $query->bindParam(':id_vendedor', $id_vendedor, PDO::PARAM_INT);
                $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $query->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                $query->bindParam(':precio', $precio, PDO::PARAM_STR);
                $query->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $query->bindParam(':descuento', $descuento, PDO::PARAM_STR);
                $query->bindParam(':aprobado', $aprobado, PDO::PARAM_STR);
                $query->bindParam(':imagen', $imagen, PDO::PARAM_LOB);
                $query->execute();
            
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al ingresar producto - inténtelo de nuevo");
                    $response->send();
                    exit();
                }
            
                $ultimoID = $connection->lastInsertId();
            
                $returnData = array();
                $returnData['id_producto'] = $ultimoID;
                $returnData['id_vendedor'] = $id_vendedor;
                $returnData['nombre'] = $nombre;
            
                $response = new Response();
                $response->setHttpStatusCode(201);
                $response->setSuccess(true);
                $response->addMessage("Producto enviado para su revision");
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch (UsuarioException $e) {
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
    }
?>