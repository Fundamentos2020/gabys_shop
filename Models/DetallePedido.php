<?php

    class DetallePedidoException extends Exception{}

    class DetallePedido{

        private $_id_pedido;
        private $_id_producto;
        private $_cantidad;
        private $_subtotal;

        public function __construct($id_pedido, $id_producto, $cantidad, $subtotal){
            $this->setIdPedido($id_pedido);
            $this->setIdProducto($id_prducto);
            $this->setCantidad($cantidad);
            $this->setSubtotal($subtotal);
       }

        public function getIdPedido(){
            return $this->_id_pedido;
        }

        public function setIdPedido($id_pedido){
            if(!is_numeric($id_pedido) || $id_pedido <=0 || $id_pedido >= 2147483647){
                throw new DetallePedidoException("Error en ID de pedido, DetallePedido");
            }
            $this->_id_pedido = $id_pedido;
        }

        public function getIdProducto(){
            return $this->_id_producto;
        }

        public function setIdProducto($id_producto){
            if(!is_numeric($id_producto) || $id_producto <=0 || $id_producto >= 2147483647){
                throw new DetallePedidoException("Error en ID de producto, DetallePedido");
            }
            $this->_id_producto = $id_producto;
        }

        public function getCantidad(){
            return $this->_cantidad;
        }

        public function setCantidad($cantidad){
            if(!is_numeric($cantidad) || $cantidad <=0){
                throw new DetallePedidoException("Error en cantidad en detallepedido");
            }
            $this->_cantidad = $cantidad;
        }

        public function getSubtotal(){
            return $this->_subtotal;
        }

        public function setSubtotal($subtotal){
            if(!is_numeric($subtotal) || $subtotal <=0){
                throw new DetallePedidoException("Error en subtotal del detalle pedido");
            }
            $this->_subtotal = $subtotal;
        }


        public function getDetallePedido(){
            $detallePedido = array();
            $detallePedido['id_pedido'] = $this->getIdPedido();
            $detallePedido['id_producto'] = $this->getIdProducto();
            $detallePedido['cantidad'] = $this->getCantidad();
            $detallePedido['subtotal'] = $this->getSubtotal();
            return $detallePedido;
        }
    }
?>