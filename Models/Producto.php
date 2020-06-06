<?php

    class ProductoException extends Exception{}

    class Producto{

        private $_id;
        private $_id_vendedor;
        private $_nombre;
        private $_descripcion;
        private $_precio;
        private $_cantidad;
        private $_descuento;
        private $_aprobado;
        private $_imagen;

        public function __construct($id, $id_vendedor, $nombre, $descripcion, $precio, $cantidad, $descuento, $aprobado, $imagen){
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
                throw new ProductoException("Error en ID de Producto");
            }
            $this->_id = $id;
        }

        public function getIdVendedor(){
            return $this->_id_vendedor;
        }

        public function setIdVendedor($id_vendedor){
            if(!is_numeric($id_vendedor) || $id_vendedor <=0 || $id_vendedor >= 2147483647){
                throw new ProductoException("Error en ID de Vendedor");
            }
            $this->_id_vendedor = $id_vendedor;
        }

        public function getNombreProducto(){
            return $this->_nombre;
        }

        public function setNombreProducto($nombre){
            if($nombre === null || strlen($nombre) > 50){
                throw new ProductoException("Error en el nombre de Producto");
            }
            $this->_nombre = $nombre;
        }

        public function getDescripcion(){
            return $this->_descripcion;
        }

        public function setDescripcion($descripcion){
            if($descripcion === null || strlen($descripcion) > 250){
                throw new ProductoException("Error en la descripcion");
            }
            $this->_descripcion = $descripcion;
        }

        public function getPrecio(){
            return $this->_precio;
        }

        public function setPrecio($precio){
            if(/*$precio > 0 ||*/ $precio === null){
                throw new ProductoException("Error en precio del producto");
            }
            $this->_precio = $precio;
        }

        public function getCantidad(){
            return $this->_cantidad;
        }

        public function setCantidad($cantidad){
            if($cantidad < 0 || $cantidad === null || $cantidad >= 2147483647){
                throw new ProductoException("Error en cantidad del producto");
            }
            $this->_cantidad = $cantidad;
        }

        public function getDescuento(){
            return $this->_descuento;
        }

        public function setDescuento($descuento){
            if($descuento < 0 || $descuento === null || $descuento >= 2147483647){
                throw new ProductoException("Error en descuento del producto");
            }
            $this->_descuento = $descuento;
        }

        public function getAprobado(){
            return $this->_aprobado;
        }

        public function setAprobado($aprobado){
            if(/*$aprobado !== true ||*/ $aprobado === null /*|| $aprobado !== false*/ ){
                throw new ProductoException("Error en aprobado del producto");
            }
            $this->_aprobado = $aprobado;
        }

        public function getImagen(){
            return $this->_imagen;
        }

        public function setImagen($imagen){
            if($imagen === null){
                throw new ProductoException("Error en imagen del producto");
            }
            $this->_imagen = $imagen;
        }


        public function getProducto(){
            $producto = array();
            $producto['id_producto'] = $this->getId();
            $producto['id_vendedor'] = $this->getIdVendedor();
            $producto['nombre'] = $this->getNombreProducto();
            $producto['descripcion'] = $this->getDescripcion();
            $producto['precio'] = $this->getPrecio();
            $producto['cantidad'] = $this->getCantidad();
            $producto['descuento'] = $this->getDescuento();
            $producto['aprobado'] = $this->getAprobado();
            $producto['imagen'] = $this->getImagen();
            return $producto;
        }
    }
?>