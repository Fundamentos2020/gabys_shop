<?php
    require_once('../Models/DB.php');
    //require_once('../Models/Sesiones.php');
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

    if (array_key_exists('id_sesion', $_GET)) {
        $id_sesion = $_GET['id_sesion'];
    
        if ($id_sesion === '' || !is_numeric($id_sesion)) {
            $response = new Response();
    
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            ($id_sesion === '' ? $response->addMessage("Id de la sesión no puede estar vacío") : false);
            (!is_numeric($id_sesion) ? $response->addMessage("Id de la sesión debe ser numérico") : false);
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
    
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            try {
                $query = $connection->prepare('DELETE FROM sesiones WHERE id_sesion = :id AND token_acceso = :token_acceso');
                $query->bindParam(':id', $id_sesion, PDO::PARAM_INT);
                $query->bindParam(':token_acceso', $accesstoken, PDO::PARAM_STR);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
        
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage("Error al cerrar la sesión usando el token dado");
                    $response->send();
                    exit();
                }
    
                $returnData = array();
                $returnData['id_sesion'] = $id_sesion;
    
                $response = new Response();
        
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setData($returnData);
                $response->addMessage("Sesión cerrada");
                $response->send();
                exit();
            }
            catch (PDOException $ex) {
                $response = new Response();
        
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al cerrar la sesión - inténtelo de nuevo");
                $response->send();
                exit();
            }
        }
        elseif($_SERVER['REQUEST_METHOD'] === 'PATCH') {//Actualizar token
    
            if($_SERVER['CONTENT_TYPE'] !== 'application/json') {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Encabezado Content Type no es JSON");
                $response->send();
                exit();
            }
    
            $patchData = file_get_contents('php://input');
    
            if (!$jsonData = json_decode($patchData)){
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Cuerpo de la solicitud no es un JSON válido");
                $response->send();
                exit();
            }
    
            if (!isset($jsonData->token_actualizacion) || strlen($jsonData->token_actualizacion) < 1) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($jsonData->token_actualizacion) ? $response->addMessage("No se encontró el token de actualización") : false);
                (strlen($jsonData->token_actualizacion) < 1 ? $response->addMessage("El token de actualización no debe ser vacío") : false);
                $response->send();
                exit();
            }
    
            try{
                $token_actualizacion = $jsonData->token_actualizacion;
    
                $query = $connection->prepare('SELECT sesiones.id AS id_sesion, sesiones.id_usuario, token_acceso, 
                token_actualizacion, caducidad_token_acceso, caducidad_token_actualizacion FROM sesiones, usuario 
                WHERE sesiones.id_usuario = usuarios.id_usuario AND sesiones.id = :id_sesion AND sesiones.token_acceso = :token_acceso 
                AND token_actualizacion = :token_actualizacion');
                $query = $connection->prepare('SELECT id_sesion, id_usuario, token_acceso, caducidad_token_acceso, token_actualizacion, caducidad_token_actualizacion FROM sesiones 
                    WHERE id_usuario = :id_usuario AND id_sesion = :id_sesion AND token_acceso = :token_acceso 
                AND token_actualizacion = :token_actualizacion');
                $query->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
                $query->bindParam(':token_acceso', $accesstoken, PDO::PARAM_STR);
                $query->bindParam(':token_actualizacion', $token_actualizacion, PDO::PARAM_STR);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
    
                    $response->setHttpStatusCode(401);
                    $response->setSuccess(false);
                    $response->addMessage("Token de acceso o token de actualización es incorrecto para el id de la sesión");
                    $response->send();
                    exit();
                }
    
                $row = $query->fetch(PDO::FETCH_ASSOC);
    
                $consulta_id = $row['id_sesion'];
                $consulta_id_usuario = $row['id_usuario'];
                //$consulta_activo = $row['activo'];
                $consulta_tokenAcceso = $row['token_acceso'];
                $consulta_tokenActualizacion = $row['token_actualizacion'];
                $consulta_cadTokenAcceso = $row['caducidad_token_acceso'];
                $consulta_cadTokenActualizacion = $row['caducidad_token_actualizacion'];
    
                /*if ($consulta_activo !== 'SI') {
                    $response = new Response();
    
                    $response->setHttpStatusCode(401);
                    $response->setSuccess(false);
                    $response->addMessage("Usuario no activo");
                    $response->send();
                    exit();
                }*/
    
                if (strtotime($consulta_cadTokenActualizacion) < time()) {
                    $response = new Response();
    
                    $response->setHttpStatusCode(401);
                    $response->setSuccess(false);
                    $response->addMessage("Token de actualización ha caducado - inicie sesión de nuevo");
                    $response->send();
                    exit();
                }
    
                $token_acceso = base64_encode(bin2hex(openssl_random_pseudo_bytes(24) . time()));
                $token_actualizacion = base64_encode(bin2hex(openssl_random_pseudo_bytes(24) . time()));
                $caducidad_tacceso_s = 1200;
                $caducidad_tactualizacion_s = 1296000;
    
                $query = $connection->prepare('UPDATE sesiones SET token_acceso = :token_acceso, caducidad_token_acceso = DATE_ADD(NOW(), INTERVAL :caducidad_tacceso_s SECOND), token_actualizacion = :token_actualizacion, caducidad_token_actualizacion = DATE_ADD(NOW(), INTERVAL :caducidad_tactualizacion_s SECOND) WHERE id = :id_sesion AND id_usuario = :id_usuario AND token_acceso = :consulta_tokenAcceso AND token_actualizacion = :consulta_tokenActualizacion');
                $query->bindParam(':token_acceso', $token_acceso, PDO::PARAM_STR);
                $query->bindParam(':caducidad_tacceso_s', $caducidad_tacceso_s, PDO::PARAM_INT);
                $query->bindParam(':token_actualizacion', $token_actualizacion, PDO::PARAM_STR);
                $query->bindParam(':caducidad_tactualizacion_s', $caducidad_tactualizacion_s, PDO::PARAM_INT);
                $query->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
                $query->bindParam(':id_usuario', $consulta_id_usuario, PDO::PARAM_INT);
                $query->bindParam(':consulta_tokenAcceso', $consulta_tokenAcceso, PDO::PARAM_STR);
                $query->bindParam(':consulta_tokenActualizacion', $consulta_tokenActualizacion, PDO::PARAM_STR);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
    
                    $response->setHttpStatusCode(401);
                    $response->setSuccess(false);
                    $response->addMessage("El token de acceso no pudo ser actualizado - inicie sesión de nuevo");
                    $response->send();
                    exit();
                }
    
                $returnData = array();
                $returnData['id_sesion'] = $id_sesion;
                $returnData['token_acceso'] = $token_acceso;
                $returnData['caducidad_token_acceso'] = $caducidad_tacceso_s;
                $returnData['token_actualizacion'] = $token_actualizacion;
                $returnData['caducidad_token_actualizacion'] = $caducidad_tactualizacion_s;
                $returnData['id_usuario'] = $consulta_id_usuario;
    
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage('Token actualizado');
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch(PDOException $e) {
                error_log('Error en BD - ' . $e);
    
                $response = new Response();
    
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Error al actualizar el token - inicie sesión de nuevo");
                $response->send();
                exit();
            }
        }
        else {
            $response = new Response();
        
            $response->setHttpStatusCode(405);
            $response->setSuccess(false);
            $response->addMessage("Método no permitido");
            $response->send();
            exit();
        }
    }
    elseif (empty($_GET)) {//Cuando aun no hay sesion iniciada
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    
        if(!$jsonData = json_decode($postData)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Cuerpo de la solicitud no es un JSON válido");
            $response->send();
            exit();
        }
    
        if (!isset($jsonData->correo) || !isset($jsonData->contrasena)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($jsonData->correo) ? $response->addMessage("El correo es obligatorio") : false);
            (!isset($jsonData->contrasena) ? $response->addMessage("La contraseña es obligatoria") : false);
            $response->send();
            exit();
        }
    
        try {
            $correo = $jsonData->correo;
            $contrasena = $jsonData->contrasena;
        
            $query = $connection->prepare('SELECT id_usuario, correo, contrasena, rol FROM usuario WHERE correo = :correo');
            $query->bindParam(':correo', $correo, PDO::PARAM_STR);
            $query->execute();
    
            $rowCount = $query->rowCount();
    
            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Correo o contraseña incorrectos");
                $response->send();
                exit();
            }
    
            $row = $query->fetch(PDO::FETCH_ASSOC);
    
            $consulta_id = $row['id_usuario'];
            $consulta_correo = $row['correo'];
            $consulta_contrasena = $row['contrasena'];
            $consulta_rol = $row['rol'];
            /*$consulta_activo = $row['activo'];
    
            if ($consulta_activo !== 'SI') {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Nombre de usuario no activo");
                $response->send();
                exit();
            }*/
    
            if(!password_verify($contrasena, $consulta_contrasena)) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("correo o contraseña incorrectos");
                $response->send();
                exit();
            }
    
            $token_acceso = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
            $token_actualizacion = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
    
            $caducidad_tacceso_s = 1200;
            $caducidad_tactualizacion_s = 1296000;
        }
        catch(PDOException $e){
            error_log('Error en DB - ' . $e);
    
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al iniciar sesión");
            $response->send();
            exit();
        }
    
        try{//Insertar la sesion
            $connection->beginTransaction();
    
            $query = $connection->prepare('INSERT INTO sesiones(id_usuario, token_acceso, caducidad_token_acceso, token_actualizacion, caducidad_token_actualizacion) VALUES (:id_usuario, :token_acceso, DATE_ADD(NOW(), INTERVAL :caducidad_tacceso_s SECOND), :token_actualizacion, DATE_ADD(NOW(), INTERVAL :caducidad_tactualizacion_s SECOND))');
            $query->bindParam(':id_usuario', $consulta_id, PDO::PARAM_INT);
            $query->bindParam(':token_acceso', $token_acceso, PDO::PARAM_STR);
            $query->bindParam(':caducidad_tacceso_s', $caducidad_tacceso_s, PDO::PARAM_INT);
            $query->bindParam(':token_actualizacion', $token_actualizacion, PDO::PARAM_STR);
            $query->bindParam(':caducidad_tactualizacion_s', $caducidad_tactualizacion_s, PDO::PARAM_INT);
            $query->execute();
    
            $ultimoID = $connection->lastInsertId();
    
            $connection->commit();
    
            $returnData = array();
            $returnData['id_sesion'] = intval($ultimoID);
            $returnData['token_acceso'] = $token_acceso;
            $returnData['caducidad_token_acceso'] = $caducidad_tacceso_s;
            $returnData['token_actualizacion'] = $token_actualizacion;
            $returnData['caducidad_token_actualizacion'] = $caducidad_tactualizacion_s;
            $returnData['id_usuario'] = $consulta_id;
            $returnData['rol'] = $consulta_rol;

            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->setData($returnData);
            $response->send();
            exit();
        }
        catch(PDOException $e) {
            $connection->rollBack();
    
            error_log('Error en DB - ' . $e);
    
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Error al iniciar sesión");
            $response->send();
            exit();
        }
        //echo 'listo';
    }
    else{
        $response = new Response();
        $response->setHttpStatusCode(404);
        $response->setSuccess(false);
        $response->addMessage("Ruta no encontrada");
        $response->send();
        exit();
    }


?>