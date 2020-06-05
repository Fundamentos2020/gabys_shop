<?php
    require_once('../Models/DB.php');
    require_once('../Models/Usuario.php');
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

    //if (empty($_GET)){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){//Registro de nuevo usuario
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
        
            if (!isset($json_data->correo) || !isset($json_data->contrasena) || !isset($json_data->nombre) || !isset($json_data->apellido_pat) 
            || !isset($json_data->apellido_mat) || !isset($json_data->direccion) || !isset($json_data->cod_postal) || !isset($json_data->ciudad)
            || !isset($json_data->estado) || !isset($json_data->foto_perfil) || !isset($json_data->rol)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($json_data->correo) ? $response->addMessage("El correo es obligatorio") : false);
                (!isset($json_data->contrasena) ? $response->addMessage("La contraseña es obligatoria") : false);
                (!isset($json_data->nombre) ? $response->addMessage("El nombre es obligatorio") : false);
                (!isset($json_data->apellido_pat) ? $response->addMessage("El apellido paterno es obligatorio") : false);
                (!isset($json_data->apellido_mat) ? $response->addMessage("El apellido materno es obligatorio") : false);
                (!isset($json_data->direccion) ? $response->addMessage("La direccion es obligatoria") : false);
                (!isset($json_data->cod_postal) ? $response->addMessage("El codigo postal es obligatorio") : false);
                (!isset($json_data->ciudad) ? $response->addMessage("La ciudad es obligatoria") : false);
                (!isset($json_data->estado) ? $response->addMessage("El estado es obligatorio") : false);
                (!isset($json_data->foto_perfil) ? $response->addMessage("La foto de perfil es obligatoria") : false);
                (!isset($json_data->rol) ? $response->addMessage("El rol es obligatorio") : false);
                $response->send();
                exit();
            }
        
            //Validar longitud
            
            $nombre = $json_data->nombre;
            $apellido_pat = $json_data->apellido_pat;
            $apellido_mat = $json_data->apellido_mat;
            $correo = $json_data->correo;
            $contrasena = $json_data->contrasena;
            $direccion = $json_data->direccion;
            $cod_postal = $json_data->cod_postal;
            $ciudad = $json_data->ciudad;
            $estado = $json_data->estado;
            $foto_perfil = $json_data->foto_perfil;
            $rol = $json_data->rol;
        
            try {
                $query = $connection->prepare('SELECT id_usuario FROM usuario WHERE correo = :correo');
                $query->bindParam(':correo', $correo, PDO::PARAM_STR);
                $query->execute();
            
                $rowCount = $query->rowCount();
            
                if($rowCount !== 0) {//Si el usuario ya existe
                    $response = new Response();
                    $response->setHttpStatusCode(409);
                    $response->setSuccess(false);
                    $response->addMessage("El correo ya esta registrado");
                    $response->send();
                    exit();
                }
                //Encripta la contraseña
                $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
                $query = $connection->prepare('INSERT INTO usuario(nombre, apellido_pat, apellido_mat, correo, contrasena, direccion, 
                    cod_postal, ciudad, estado, foto_perfil, rol) VALUES(:nombre, :apellido_pat, :apellido_mat, :correo, :contrasena, :direccion,
                    :cod_postal, :ciudad, :estado, :foto_perfil, :rol)');
                $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $query->bindParam(':apellido_pat', $apellido_pat, PDO::PARAM_STR);
                $query->bindParam(':apellido_mat', $apellido_mat, PDO::PARAM_STR);
                $query->bindParam(':correo', $correo, PDO::PARAM_STR);
                $query->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
                $query->bindParam(':direccion', $direccion, PDO::PARAM_STR);
                $query->bindParam(':cod_postal', $cod_postal, PDO::PARAM_STR);
                $query->bindParam(':ciudad', $ciudad, PDO::PARAM_STR);
                $query->bindParam(':estado', $estado, PDO::PARAM_STR);
                $query->bindParam(':foto_perfil', $foto_perfil, PDO::PARAM_LOB);
                $query->bindParam(':rol', $rol, PDO::PARAM_INT);
                $query->execute();
            
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al crear usuario - inténtelo de nuevo");
                    $response->send();
                    exit();
                }
            
                $ultimoID = $connection->lastInsertId();
            
                $returnData = array();
                $returnData['id_usuario'] = $ultimoID;
                $returnData['nombre'] = $nombre;
                $returnData['correo'] = $correo;
            
                $response = new Response();
                $response->setHttpStatusCode(201);
                $response->setSuccess(true);
                $response->addMessage("Usuario creado");
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
                $response->addMessage("Error al crear usuario");
                $response->send();
                exit();
            }
        }
    //}
?>