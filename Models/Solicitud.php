<?php

    class SolicitudException extends Exception{}

    class Solicitud{

        private $_id;
        private $_id_vendedor;
        private $_archivo;
        private $_id_admi;
        private $_aprobada;

        public function __construct($id, $id_vendedor, $archivo, $id_admi, $aprobada){
            $this->setId($id);
            $this->setIdVendedor($id_vendedor);
            $this->setArchivo($archivo);
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

        public function getArchivo(){
            return $this->_archivo;
        }

        public function setArchivo($archivo){
            if($archivo === null){
                throw new SolicitudException("Error en archivo, Solicitud");
            }
            $this->_archivo = $archivo;
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
            $solicitud['archivo'] = $this->getArchivo();
            $solicitud['id_admi'] = $this->getIdAdmi();
            $solicitud['aprobada'] = $this->getAprobada();
            return $solicitud;
        }
    }
?>