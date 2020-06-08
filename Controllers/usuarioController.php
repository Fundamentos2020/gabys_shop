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

    if(array_key_exists('id_usuario', $_GET)) {
        $id_usuario = $_GET['id_usuario'];
        if ($id_usuario == '' || !is_numeric($id_usuario)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("El id del usuario no puede estar vacío y debe ser numérico");
            $response->send();
            exit();
        }
        if($_SERVER['REQUEST_METHOD'] === 'GET'){//obtener info de usuario
            try {
                $query = $connection->prepare('SELECT * FROM usuario WHERE id_usuario = :id_usuario');
                $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
                if($rowCount !== 1){
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage("El id del usuario no fue encontrado");
                    $response->send();
                    exit();
                }
                
                $infousuario = array();
    
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $usuario = new Usuario($row['id_usuario'], $row['nombre'], $row['apellido_pat'], $row['apellido_mat'], $row['correo'], $row['contrasena'], $row['direccion'], $row['cod_postal'], $row['ciudad'], $row['estado'], $row['foto_perfil'], $row['rol']);
                    $usuario->setFotoPerfil("data:imagen/jpg;base64,". base64_encode($row['foto_perfil']));
                    $infousuario = $usuario->getUsuario();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['usuario'] = $infousuario;
    
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setToCache(true);
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
                $response->addMessage("Error en consulta de usuario");
                $response->send();
                exit();
            }
        }
        elseif($_SERVER['REQUEST_METHOD'] === 'PATCH'){//actualizar info de usuario
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
                //$actualizaApellidos = false;
                $actualizaCorreo = false;
                $actualizaContrasena = false;
                $actualizaDireccion = false;
                $actualizaCP = false;
                $actualizaCiudad = false;
                $actualizaEstado = false;

                $campos_query = "";

                if (isset($json_data->nombre)) {
                    $actualizaNombre = true;
                    $campos_query .= "nombre = :nombre, ";
                }

                if (isset($json_data->correo)) {
                    $actualizaCorreo = true;
                    $campos_query .= "correo = :correo, ";
                }

                if (isset($json_data->contrasena)) {
                    $actualizaContrasena = true;
                    $campos_query .= "contrasena = :contrasena, ";
                }

                if (isset($json_data->direccion)) {
                    $actualizaDireccion = true;
                    $campos_query .= "direccion = :direccion, ";
                }

                if (isset($json_data->cod_postal)) {
                    $actualizaCP = true;
                    $campos_query .= "cod_postal = :cod_postal, ";
                }

                if (isset($json_data->ciudad)) {
                    $actualizaCiudad = true;
                    $campos_query .= "ciudad = :ciudad, ";
                }

                if (isset($json_data->estado)) {
                    $actualizaEstado = true;
                    $campos_query .= "estado = :estado, ";
                }

                $campos_query = rtrim($campos_query, ", ");

                if ($actualizaNombre === false && $actualizaCorreo === false && $actualizaContrasena === false && $actualizaDireccion === false 
                        && $actualizaCP === false && $actualizaCiudad === false && $actualizaEstado === false) {
                    $response = new Response();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage("No hay campos para actualizar");
                    $response->send();
                    exit();
                }

                $query = $connection->prepare('SELECT * FROM usuario WHERE id_usuario = :id_usuario');
                $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
            
                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el usuario");
                    $response->send();
                    exit();
                }

                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $usuario = new Usuario($row['id_usuario'], $row['nombre'], $row['apellido_pat'], $row['apellido_mat'], $row['correo'], 
                    $row['contrasena'], $row['direccion'], $row['cod_postal'], $row['ciudad'], $row['estado'], $row['foto_perfil'], $row['rol']);
                }
    
                $cadena_query = 'UPDATE usuario SET ' . $campos_query . ' WHERE id_usuario = :id_usuario';
                $query = $connection->prepare($cadena_query);

                if($actualizaNombre === true) {
                    $usuario->setNombre($json_data->nombre);
                    $up_nombre = $usuario->getnombre();
                    $query->bindParam(':nombre', $up_nombre, PDO::PARAM_STR);
                }

                if($actualizaCorreo === true) {
                    $usuario->setCorreo($json_data->correo);
                    $up_correo = $usuario->getCorreo();
                    $query->bindParam(':correo', $up_correo, PDO::PARAM_STR);
                }
                
                if($actualizaContrasena === true) {
                    $contrasena_hash = password_hash($json_data->contrasena, PASSWORD_DEFAULT);
                    $usuario->setContrasena($contrasena_hash);
                    $up_contrasena = $usuario->getContrasena();
                    $query->bindParam(':contrasena', $up_contrasena, PDO::PARAM_STR);
                }
                
                if($actualizaDireccion === true) {
                    $usuario->setDireccion($json_data->direccion);
                    $up_direccion = $usuario->getDireccion();
                    $query->bindParam(':direccion', $up_direccion, PDO::PARAM_STR);
                }

                if($actualizaCP === true) {
                    $usuario->setCodigoPostal($json_data->cod_postal);
                    $up_cod_postal = $usuario->getCodigoPostal();
                    $query->bindParam(':cod_postal', $up_cod_postal, PDO::PARAM_STR);
                }

                if($actualizaCiudad === true) {
                    $usuario->setCiudad($json_data->ciudad);
                    $up_ciudad = $usuario->getCiudad();
                    $query->bindParam(':ciudad', $up_ciudad, PDO::PARAM_STR);
                }
                
                if($actualizaEstado === true) {
                    $usuario->setEstado($json_data->estado);
                    $up_estado = $usuario->getEstado();
                    $query->bindParam(':estado', $up_estado, PDO::PARAM_STR);
                }

                $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $query->execute();
    
                $rowCount = $query->rowCount();
    
                if ($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(500);
                    $response->setSuccess(false);
                    $response->addMessage("Error al actualizar la informacion del usuario");
                    $response->send();
                    exit();
                }

                $query = $connection->prepare('SELECT * FROM usuario WHERE id_usuario = :id_usuario');
                $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $query->execute();

                $rowCount = $query->rowCount();

                if($rowCount === 0) {
                    $response = new Response();
                    $response->setHttpStatusCode(404);
                    $response->setSuccess(false);
                    $response->addMessage("No se encontró el usuario después de actualizar");
                    $response->send();
                    exit();
                }

                $infousuario = array();
    
                while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $usuario = new Usuario($row['id_usuario'], $row['nombre'], $row['apellido_pat'], $row['apellido_mat'], $row['correo'], $row['contrasena'], $row['direccion'], $row['cod_postal'], $row['ciudad'], $row['estado'], $row['foto_perfil'], $row['rol']);
                    $usuario->setFotoPerfil("data:imagen/jpg;base64,". base64_encode($row['foto_perfil']));
                    $infousuario = $usuario->getUsuario();
                }
    
                $returnData = array();
                $returnData['total_registros'] = $rowCount;
                $returnData['usuario'] = $infousuario;
    
                $response = new Response();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->addMessage("Usuario actualizado");
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
                $response->addMessage("Error en consulta de usuario");
                $response->send();
                exit();
            }
        }
    }
    elseif (empty($_GET)){
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
                $query->bindParam(':rol', $rol, PDO::PARAM_STR);
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
    }
?>