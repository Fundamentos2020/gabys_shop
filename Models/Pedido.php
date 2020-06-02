<?php

    class PedidoException extends Exception{}

    class Pedido{

        private $_id;
        private $_id_usuario;
        private $_total;
        private $_fecha_estimada;

        public function __construct($id, $id_usuario, $total, $fecha_estimada){
            $this->setId($id);
            $this->setIdUsuario($id_usuario);
            $this->setTotal($total);
            $this->setFechaEstimada($fecha_estimada);
       }

        public function getId(){
            return $this->_id;
        }

        public function setId($id){
            if($id !== null && (!is_numeric($id) || $id <=0 || $id >= 2147483647) || $this->_id !== null){
                throw new PedidoException("Error en ID de pedido");
            }
            $this->_id = $id;
        }

        public function getIdUsuario(){
            return $this->_id_usuario;
        }

        public function setIdUsuario($id_usuario){
            if(!is_numeric($id_usuario) || $id_usuario <=0 || $id_usuario >= 2147483647){
                throw new PedidoException("Error en ID de usuario, Pedido");
            }
            $this->_id_usuario = $id_usuario;
        }

        public function getTotal(){
            return $this->_total;
        }

        public function setTotal($total){
            if(!is_numeric($total) || $total <=0){
                throw new PedidoException("Error en total del pedido");
            }
            $this->_total = $total;
        }

        public function getFechaEstimada() {
            return $this->_fecha_estimada;
        }

        public function setFechaEstimada($fecha_estimada) {
            if ($fecha_estimada !== null && date_format(date_create_from_format('Y-m-d H:i', $fecha_estimada), 'Y-m-d H:i') !== $fecha_estimada) {
                throw new PedidoException("Error en fecha estimada de pedido");
            }
            $this->_fecha_estimada = $fecha_estimada;
        }


        public function getPedido(){
            $pedido = array();
            $pedido['id_pedido'] = $this->getId();
            $pedido['id_usuario'] = $this->getIdUsuario();
            $pedido['total'] = $this->getTotal();
            $pedido['fecha_estimada'] = $this->getFechaEstimada();
            return $pedido;
        }
    }
?>