<?php

    class UsuarioException extends Exception{}

    class Usuario{

        private $_id;
        private $_nombre;
        private $_apellido_pat;
        private $_apellido_mat;
        private $_correo;
        private $_contrasena;
        private $_direccion;
        private $_cod_postal;
        private $_ciudad;
        private $_estado;
        private $_foto_perfil;
        private $_rol;

        public function __construct($id, $nombre, $apellido_pat, $apellido_mat, $correo, $contrasena, $direccion, $cod_postal, 
        $ciudad, $estado, $foto_perfil, $rol){
            $this->setId($id);
            $this->setIdVendedor($id_vendedor);
            $this->setNombreProducto($nombre);
            $this->setDescripcion($descripcion);
            $this->setPrecio($precio);
            $this->setCantidad($cantidad);
            $this->setDescuento($descuento);
            $this->setAprobado($aprobado);
            $this->setImagen($imagen);
       }

        public function getId(){
            return $this->_id;
        }

        public function setId($id){
            if($id !== null && (!is_numeric($id) || $id <=0 || $id >= 2147483647) || $this->_id !== null){
                throw new UsuarioException("Error en ID de Usuario");
            }
            $this->_id = $id;
        }

        public function getNombre(){
            return $this->_nombre;
        }

        public function setNombre($nombre){
            if($nombre === null || strlen($nombre) > 50){
                throw new UsuarioException("Error en nombre de Usuario");
            }
            $this->_nombre = $nombre;
        }

        public function getApPaterno(){
            return $this->_apellido_pat;
        }

        public function setApPaterno($ape_apellido_pat){
            if($ape_apellido_pat === null || strlen($ape_apellido_pat) > 50){
                throw new UsuarioException("Error en apellido paterno de Usuario");
            }
            $this->_apellido_pat = $ape_apellido_pat;
        }

        public function getApMaterno(){
            return $this->_apellido_mat;
        }

        public function setApMaterno($ape_apellido_mat){
            if($ape_apellido_mat === null || strlen($ape_apellido_mat) > 50){
                throw new UsuarioException("Error en ID de Usuario");
            }
            $this->_apellido_mat = $ape_apellido_mat;
        }

        public function getCorreo(){
            return $this->_correo;
        }

        public function setCorreo($correo){
            if($correo === null || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 100){
                throw new UsuarioException("Error en el correo de Usuario");
            }
            $this->_correo = $correo;
        }

        public function getContrasena(){
            return $this->_contrasena;
        }

        public function setContrasena($contrasena){
            if($contrasena === null || strlen($contrasena) > 100){
                throw new UsuarioException("Error en la contrasena");
            }
            $this->_contrasena = $contrasena;
        }

        public function getDireccion(){
            return $this->_direccion;
        }

        public function setDireccion($direccion){
            if($direccion === null || strlen($direccion) > 100){
                throw new UsuarioException("Error en la direccion");
            }
            $this->_direccion = $direccion;
        }

        public function getCodigoPostal(){
            return $this->_cod_postal;
        }

        public function setCodigoPostal($cod_postal){
            if(/*$precio > 0 ||*/ $cod_postal === null){
                throw new UsuarioException("Error en codigo postal del usuario");
            }
            $this->_cod_postal = $cod_postal;
        }

        public function getCiudad(){
            return $this->_ciudad;
        }

        public function setCiudad($ciudad){
            if($ciudad === null || strlen($ciudad) > 50){
                throw new UsuarioException("Error en ciudad del usuario");
            }
            $this->_ciudad = $ciudad;
        }

        public function getEstado(){
            return $this->_estado;
        }

        public function setEstado($estado){
            if($estado === null || strlen($estado) > 50){
                throw new UsuarioException("Error en estado del usuario");
            }
            $this->_estado = $estado;
        }

        public function getFotoPerfil(){
            return $this->_foto_perfil;
        }

        public function setFotoPerfil($foto_perfil){
            if($foto_perfil === null){
                throw new UsuarioException("Error en foto de perfil del usuario");
            }
            $this->_foto_perfil = $foto_perfil;
        }

        public function getRol(){
            return $this->_rol;
        }

        public function setRol($rol){
            if($rol !== 0 && $rol !== 1 && $rol !== 2){
                throw new TareaException("Error en rol del usuario");
            }
            $this->_rol = $rol;
        }


        public function getProductos(){
            $producto = array();
            $producto['id_producto'] = $this->getId();
            $producto['id_vendedor'] = $this->getIdVendedor();
            $producto['nombre'] = $this->getNombreProducto();
            $producto['descripcion'] = $this->getDescripcion();
            $producto['precio'] = $this->getPrecio();
            $producto['cantidad'] = $this->getCantidad();
            $producto['descuento'] = $this->getDescuento();
            $producto['aprobado'] = $this->getAprobado();
            //$producto['imagen'] = $this->getImagen();
            return $producto;
        }
    }
?>