<?php 

    require_once('../Models/DB.php');
    require_once('../Models/Response.php');
    require_once('../Models/Solicitud.php');

    try {
        $connection = Conexion::conecta();
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
    if(array_key_exists('aprobado',$_GET)){
        $aprobado = $_GET['aprobado'];
        if ($aprobado == '' || !is_numeric($aprobado)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El valor de aprobado no es valido");
            $response->send();
            exit();
        }
        if($_SERVER['REQUEST_METHOD'] === 'PATCH'){
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

                $actualizaAdmin = false;
                $actualizaAprobar = false;
                $id_soli = $json_data->id_solicitud;
                $id_admin = $json_data->id_admin;
                if (isset($json_data->id_admin)) {
                    $actualizaAdmin = true;
                }
    
                if (isset($json_data->aprobada)) {
                    $actualizaAprobar = true;
                }
    
    
                $query = $connection->prepare('SELECT * FROM solicitud WHERE id_solicitud = :id_solicitud');
                $query->bindParam(':id_solicitud', $id_soli, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró la solicitud");
                    $response->send();
                    exit();
                }
    
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $solicitud = new Solicitud($row['id_solicitud'], $row['id_vendedor'], $row['solicitudRuta'], $row['id_admin'], $row['aprobada']);
                }
    
                $cadena_query = 'UPDATE solicitud SET aprobada = :_aprobado, id_admin = :id_admin WHERE id_solicitud = :id_solicitud';
                $query->bindParam(':_aprobado', $aprobado, PDO::PARAM_INT);
                $query->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
                $query->bindParam(':id_solicitud', $id_soli, PDO::PARAM_INT);
                $query = $connection->prepare($cadena_query);
    

                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al actualizar la informacion del usuario");
                    $response->send();
                    exit();
                }
    
                $query = $connection->prepare('SELECT * FROM solicitud WHERE id_solicitud = :id_solicitud');
                $query->bindParam(':id_solicitud', $id_soli, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontro la solicitud después de actualizar");
                    $response->send();
                    exit();
                }
    
                $infosolicitud = array();
    
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $solicitud = new Solicitud($row['id_solicitud'], $row['id_vendedor'], $row['solicitudRuta'], $row['id_admin'], $row['aprobada']);
                    $infosolicitud = $solicitud->getSolicitud();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['solicitud'] = $infosolicitud;
    
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage("Solicitud actualizada");
                $response->setData($returnData);
                $response->send();
                exit();
            }
            catch(UsuarioException $e){
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
                $response->addMessage("Error en consulta de solicitud");
                $response->send();
                exit();
            }
        }
    }
    else{
        if($_SERVER['REQUEST_METHOD'] === 'GET'){       
            try{
                $query = $connection->prepare('SELECT id_solicitud, id_vendedor, solicitudRuta, id_admin, aprobada FROM solicitud WHERE aprobada = 0');
                $query->execute();
    
                $rowCount = $query->rowCount();    
                $solicitudes = array();
            
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $solicitud = new solicitud($row['id_solicitud'], $row['id_vendedor'], $row['solicitudRuta'], $row['id_admin'], $row['aprobada']);
                    //$solicitud->setImagen("data:imagen/jpg;base64,". base64_encode($row['imagen']));
                    $solicitudes[] = $solicitud->getSolicitud();
                }
                $returnData = array();
                $returnData['total registros'] = $rowCount;
                $returnData['solicitudes'] = $solicitudes;
    
    
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
?>