<?php

    class SolicitudException extends Exception{}

    class Solicitud{

        private $_id_solicitud;
        private $_id_vendedor;
        private $_solicitudRuta;
        private $_id_admi;
        private $_aprobada;

        public function __construct($id_solicitud, $id_vendedor, $solicitudRuta, $id_admi, $aprobada){
            $this->setId($id_solicitud);
            $this->setIdVendedor($id_vendedor);
            $this->setSolicitudR($solicitudRuta);
            $this->setIdAdmi($id_admi);
            $this->setAprobada($aprobada);
       }

        public function getId(){
            return $this->_id_solicitud;
        }

        public function setId($id){
            if($id !== null && (!is_numeric($id) || $id <=0 || $id >= 2147483647) || $this->_id_solicitud !== null){
                throw new SolicitudException("Error en ID de solicitud");
            }
            $this->_id_solicitud = $id;
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

        public function getIdAdmi(){
            return $this->_id_admi;
        }

        public function setIdAdmi($id_admi){
            if($id_admi !== null)
                if(!is_numeric($id_admi) /*|| /*$id_admi >=0*/ || $id_admi >= 2147483647){
                    throw new SolicitudException("Error en ID de admi, Solicitud");
                }
            $this->_id_admi = $id_admi;
        }

        public function getAprobada(){
            return $this->_aprobada;
        }

        public function setAprobada($aprobada){
            if(/*$aprobado !== true ||*/ $aprobada === null /*|| $aprobado !== false*/ ){
                throw new SolicitudException("Error en aprobada de solicitud");
            }
            $this->_aprobada = $aprobada;
        }

        public function getSolicitudR(){
            return $this->_solicitudRuta;
        }

        public function setSolicitudR($aprobada){
            if(/*$aprobado !== true ||*/ $aprobada === null /*|| $aprobado !== false*/ ){
                throw new SolicitudException("Error en aprobada de solicitud");
            }
            $this->_solicitudRuta = $aprobada;
        }

        public function getSolicitud(){
            $solicitud = array();
            $solicitud['id_solicitud'] = $this->getId();
            $solicitud['id_vendedor'] = $this->getIdVendedor();
            $solicitud['solicitudRuta'] = $this->getSolicitudR();
            $solicitud['id_admi'] = $this->getIdAdmi();
            $solicitud['aprobada'] = $this->getAprobada();
            return $solicitud;
        }
    }
?>