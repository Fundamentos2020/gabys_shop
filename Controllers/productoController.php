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

    if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
        $response = new Response();
        $response->setHttpStatusCode(401);
        $response->setSuccess(false);
        $response->addMessage("No se encontró el token de acceso");
        $response->send();
        exit();
    }

    $accesstoken = $_SERVER['HTTP_AUTHORIZATION']; 


    try {
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

        date_default_timezone_set("America/Mexico_City");
    
        if (strtotime($consulta_cadTokenAcceso) < time()) {
            $response = new Response();
            $response->setHttpStatusCode(401);
            $response->setSuccess(false);
            $response->addMessage("Token de acceso ha caducado");
            $response->send();
            exit();
        }
    } 
    catch (PDOException $e) {
        error_log('Error en DB - ' . $e);
    
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Error al autenticar usuario");
        $response->send();
        exit();
    }


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
        elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){//Admin aprueba producto
            try {
                if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage('Encabezado "Content type" no es JSON');
                    $response->send();
                    exit();
                }
    
                $patchData = file_get_contents('php://input');
    
                if (!$json_data = json_decode($patchData)) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
                    $response->send();
                    exit();
                }

                $actualizaAprobado = false;

                if (isset($json_data->aprobado)) {
                    $actualizaAprobado = true;
                }

                $id_producto = $json_data->id_producto;
                $aprobado = $json_data->aprobado;
                //echo $aprobado;

                if ($actualizaAprobado === false) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage("No hay campos para actualizar");
                    $response->send();
                    exit();
                }

                $query = $connection->prepare('SELECT * FROM producto WHERE id_producto = :id_producto');
                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el producto");
                    $response->send();
                    exit();
                }

                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $producto = new Producto($row['id_producto'], $row['id_vendedor'], $row['nombre'], $row['descripcion'], $row['precio'], $row['cantidad'], 
                    $row['descuento'], $row['aprobado'], $row['imagen']);
                }

                $query = $connection->prepare('UPDATE producto SET aprobado = :_aprobado WHERE id_producto = :id_producto');
                $query->bindParam(':_aprobado', $aprobado, PDO::PARAM_STR);
                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al aprobar el producto");
                    $response->send();
                    exit();
                }

                
                $query = $connection->prepare('SELECT * FROM producto WHERE id_producto = :id_producto');
                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el producto despues de aprobar");
                    $response->send();
                    exit();
                }

                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $producto = new Producto($row['id_producto'], $row['id_vendedor'], $row['nombre'], $row['descripcion'], $row['precio'], $row['cantidad'], 
                        $row['descuento'], $row['aprobado'], $row['imagen']);
                    $producto->setImagen("data:imagen/jpg;base64,". base64_encode($row['imagen']));
                    $infoproducto = $producto->getProducto();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['producto'] = $infoproducto;
    
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage("Producto aprobado");
                $response->setData($returnData);
                $response->send();
                exit();

            }
            catch(ProductoException $e){
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage($e->getMessage());
                $response->send();
                exit();
            }
            catch(PDOException $e) {
                error_log("Error en BD - " . $e);
    
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error en consulta de producto");
                $response->send();
                exit();
            }
        }
    }
    elseif(array_key_exists('id_producto', $_GET)){//Parametro con id del producto, GET con el ID del producto
        $id_producto = $_GET['id_producto'];
        if ($id_producto == '' || !is_numeric($id_producto)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El valor de id producto no es valido");
            $response->send();
            exit();
        }
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            try{
                $query = $connection->prepare('SELECT id_producto, id_vendedor, nombre, descripcion, precio, cantidad, descuento, aprobado, imagen FROM producto WHERE id_producto = :id_producto');
                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();
        
                $rowCount = $query->rowCount();    
                $productos = array();
            
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $producto = new Producto($row['id_producto'], $row['id_vendedor'], $row['nombre'], $row['descripcion'], $row['precio'], $row['cantidad'], $row['descuento'], $row['aprobado'], $row['imagen']);
                    $producto->setImagen("data:imagen/jpg;base64,". base64_encode($row['imagen']));
                    $infoproducto = $producto->getProducto();
                }
                
                $returnData = array();
                $returnData['total registros'] = $rowCount;
                $returnData['productos'] = $infoproducto;


                $response = new Response();
                $response->setHttpStatusCode(200);//Cuando se ejecuto correctamente 
                $response->setSuccess(true);
                $response->setToCache(true);//Cache es solo para listados
                $response->setData($returnData);
                $response->send();
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
        elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){
            try {
                if ($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage('Encabezado "Content type" no es JSON');
                    $response->send();
                    exit();
                }
    
                $patchData = file_get_contents('php://input');
    
                if (!$json_data = json_decode($patchData)) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage('El cuerpo de la solicitud no es un JSON válido');
                    $response->send();
                    exit();
                }

                $actualizaNombre = false;
                $actualizaDescripcion = false;
                $actualizaPrecio = false;
                $actualizaCantidad = false;
                $actualizaDescuento = false;
                //$actualizaImagen = false;

                $campos_query = "";

                if (isset($json_data->nombre)) {
                    $actualizaNombre = true;
                    $campos_query .= "nombre = :nombre, ";
                }

                if (isset($json_data->descripcion)) {
                    $actualizaDescripcion = true;
                    $campos_query .= "descripcion = :descripcion, ";
                }

                if (isset($json_data->precio)) {
                    $actualizaPrecio = true;
                    $campos_query .= "precio = :precio, ";
                }

                if (isset($json_data->cantidad)) {
                    $actualizaCantidad = true;
                    $campos_query .= "cantidad = :cantidad, ";
                }

                if (isset($json_data->descuento)) {
                    $actualizaDescuento = true;
                    $campos_query .= "descuento = :descuento, ";
                }

                /*if (isset($json_data->imagen)) {
                    $actualizaImagen = true;
                    $campos_query .= "imagen = :imagen, ";
                }*/

                $campos_query = rtrim($campos_query, ", ");

                if ($actualizaNombre === false && $actualizaDescripcion === false && $actualizaPrecio === false && $actualizaCantidad === false 
                        && $actualizaDescuento === false /*&& $actualizaImagen === false*/) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage("No hay campos para actualizar");
                    $response->send();
                    exit();
                }

                $query = $connection->prepare('SELECT * FROM producto WHERE id_producto = :id_producto');
                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el producto");
                    $response->send();
                    exit();
                }

                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $producto = new Producto($row['id_producto'], $row['id_vendedor'], $row['nombre'], $row['descripcion'], $row['precio'],
                    $row['cantidad'], $row['descuento'], $row['aprobado'], $row['imagen']);
                }
    
                $cadena_query = 'UPDATE producto SET ' . $campos_query . ' WHERE id_producto = :id_producto';
                $query = $connection->prepare($cadena_query);

                if($actualizaNombre === true) {
                    $producto->setNombreProducto($json_data->nombre);
                    $up_nombre = $producto->getNombreProducto();
                    $query->bindParam(':nombre', $up_nombre, PDO::PARAM_STR);
                }

                if($actualizaDescripcion === true) {
                    $producto->setDescripcion($json_data->descripcion);
                    $up_descripcion = $producto->getDescripcion();
                    $query->bindParam(':descripcion', $up_descripcion, PDO::PARAM_STR);
                }

                if($actualizaPrecio === true) {
                    $producto->setPrecio($json_data->precio);
                    $up_precio = $producto->getPrecio();
                    $query->bindParam(':precio', $up_precio, PDO::PARAM_STR);
                }

                if($actualizaCantidad === true) {
                    $producto->setCantidad($json_data->cantidad);
                    $up_cantidad = $producto->getCantidad();
                    $query->bindParam(':cantidad', $up_cantidad, PDO::PARAM_INT);
                }

                if($actualizaDescuento === true) {
                    $producto->setDescuento($json_data->descuento);
                    $up_descuento = $producto->getDescuento();
                    $query->bindParam(':descuento', $up_descuento, PDO::PARAM_INT);
                }

                /*if($actualizaImagen === true) {
                    $producto->setImagen($json_data->imagen);
                    $up_imagen = $producto->getImagen();
                    $query->bindParam(':imagen', $up_imagen, PDO::PARAM_LOB);
                }*/

                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al actualizar la informacion del producto");
                    $response->send();
                    exit();
                }

                $query = $connection->prepare('SELECT * FROM producto WHERE id_producto = :id_producto');
                $query->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
                $query->execute();

                $rowCount = $query->rowCount();

                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el producto después de actualizar");
                    $response->send();
                    exit();
                }

                $infoproducto = array();
    
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $producto = new Producto($row['id_producto'], $row['id_vendedor'], $row['nombre'], $row['descripcion'], $row['precio'],
                    $row['cantidad'], $row['descuento'], $row['aprobado'], $row['imagen']);
                    $producto->setImagen("data:imagen/jpg;base64,". base64_encode($row['imagen']));
                    $infoproducto = $producto->getProducto();
                }

                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['producto'] = $infoproducto;
    
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage("Producto actualizado");
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch(ProductoException $e){
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage($e->getMessage());
                $response->send();
                exit();
            }
            catch(PDOException $e) {
                error_log("Error en BD - " . $e);
    
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
                    /*$newquery = $connection->prepare('SELECT * FROM detalle_pedido WHERE id_producto = :id_producto');
                    $newquery->bindParam(':id_producto', $producto->getId, PDO::PARAM_INT);
                    $newquery->execute();
                    //$detalles = 0;
                    $rowDetalles = $newquery->rowCount();
                    if($rowDetalles === 0){
                        $detalles = 0;
                    }
                    else{
                        $detalles = $rowDetalles;
                    }
                    $productos['detalles'] = $rowDetalles;*/
                }
                $returnData = array();
                $returnData['total registros'] = $rowCount;
                $returnData['productos'] = $productos;
                //$returnData['detalles'] = $detalles;
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
                    $response->addMessage("Ya tienes un producto con ese nombre");
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
            catch (ProductoException $e) {
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