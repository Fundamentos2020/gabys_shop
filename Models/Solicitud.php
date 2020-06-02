<?php

    class SolicitudException extends Exception{}

    class Solicitud{

        private $_id;
        private $_id_vendedor;
        private $_id_producto;
        private $_id_admi;
        private $_aprobada;

        public function __construct($id, $id_vendedor, $id_producto, $id_admi, $aprobada){
            $this->setId($id);
            $this->setIdVendedor($id_vendedor);
            $this->setIdProducto($id_producto);
            $this->setIdAdmi($id_admi);
            $this->setAprobada($aprobada);
       }

        public function getId(){
            return $this->_id;
        }

        public function setId($id){
            if($id !== null && (!is_numeric($id) || $id <=0 || $id >= 2147483647) || $this->_id !== null){
                throw new SolicitudException("Error en ID de solicitud");
            }
            $this->_id = $id;
        }

        public function getIdVendedor(){
            return $this->_id_vendedor;
        }

        public function setIdVendedor($id_vendedor){
            if(!is_numeric($id_vendedor) || $id_vendedor <=0 || $id_vendedor >= 2147483647){
                throw new SolicitudException("Error en ID de Vendedor, Solicitud");
            }
            $this->_id_vendedor = $id_vendedor;
        }

        public function getIdProducto(){
            return $this->_id_producto;
        }

        public function setIdProducto($id_producto){
            if(!is_numeric($id_producto) || $id_producto <=0 || $id_producto >= 2147483647){
                throw new SolicitudException("Error en ID de producto, Solicitud");
            }
            $this->_id_producto = $id_producto;
        }

        public function getIdAdmi(){
            return $this->_id_admi;
        }

        public function setIdAdmi($id_admi){
            if(!is_numeric($id_admi) || $id_admi <=0 || $id_admi >= 2147483647){
                throw new SolicitudException("Error en ID de admi, Solicitud");
            }
            $this->_id_admi = $id_admi;
        }

        public function getAprobada(){
            return $this->_aprobada;
        }

        public function setAprobada($aprobada){
            if(/*$aprobado !== true ||*/ $aprobado === null /*|| $aprobado !== false*/ ){
                throw new SolicitudException("Error en aprobada de solicitud");
            }
            $this->_aprobada = $aprobada;
        }


        public function getSolicitud(){
            $solicitud = array();
            $solicitud['id_solicitud'] = $this->getId();
            $solicitud['id_vendedor'] = $this->getIdVendedor();
            $solicitud['id_producto'] = $this->getIdProducto();
            $solicitud['id_admi'] = $this->getIdAdmi();
            $solicitud['aprobada'] = $this->getAprobada();
            return $solicitud;
        }
    }
?>