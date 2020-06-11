<?php

    class SesionesException extends Exception{}

    class Sesiones{

        private $_id_sesion;
        private $_id_usuario;
        private $_token_acceso;
        private $_caducidad_token_acceso;
        private $_token_actualizacion;
        private $_caducidad_token_actualizacion;

        public function __construct($id_sesion, $id_usuario, $token_acceso, $caducidad_token_acceso, $token_actualizacion, $caducidad_token_actualizacion){
            $this->setId($id_sesion);
            $this->setIdUsuario($id_usuario);
            $this->setTokenAcceso($token_acceso);
            $this->setCaducidadTokenAcceso($caducidad_token_acceso);
            $this->setTokenActualizacion($token_actualizacion);
            $this->setCaducidadTokenActualizacion($caducidad_token_actualizacion);
       }

        public function getId(){
            return $this->_id_sesion;
        }

        public function setId($id){
            if($id !== null && (!is_numeric($id) || $id <=0 || $id >= 2147483647) || $this->_id_sesion !== null){
                throw new SesionesException("Error en ID de sesion");
            }
            $this->_id_sesion = $id;
        }

        public function getIdUsuario(){
            return $this->_id_usuario;
        }

        public function setIdUsuario($id_usuario){
            if(!is_numeric($id_usuario) || $id_usuario <=0 || $id_usuario >= 2147483647){
                throw new SesionesException("Error en ID de usuario");
            }
            $this->_id_usuario = $id_usuario;
        }

        public function getTokenAcceso(){
            return $this->_token_acceso;
        }

        public function setTokenAcceso($token_acceso){
            if($token_acceso === null || strlen($token_acceso) > 100){
                throw new SesionesException("Error en token de acceso");
            }
            $this->_token_acceso = $token_acceso;
        }

        public function getCaducidadTokenAcceso() {
            return $this->_caducidad_token_acceso;
        }

        public function setCaducidadTokenAcceso($caducidad_token_acceso) {
            if ($caducidad_token_acceso !== null && date_format(date_create_from_format('Y-m-d H:i', $caducidad_token_acceso), 'Y-m-d H:i') !== $caducidad_token_acceso) {
                throw new SesionesException("Error en caducidad token de acceso");
            }
            $this->_caducidad_token_acceso = $caducidad_token_acceso;
        }

        public function getTokenActualizacion(){
            return $this->_token_actualizacion;
        }

        public function setTokenActualizacion($token_actualizacion){
            if($token_actualizacion === null || strlen($token_actualizacion) > 100){
                throw new SesionesException("Error en token de actualizacion");
            }
            $this->_token_actualizacion = $token_actualizacion;
        }

        public function getCaducidadTokenActualizacion() {
            return $this->_caducidad_token_actualizacion;
        }

        public function setCaducidadTokenActualizacion($caducidad_token_actualizacion) {
            if ($caducidad_token_actualizacion !== null && date_format(date_create_from_format('Y-m-d H:i', $caducidad_token_actualizacion), 'Y-m-d H:i') !== $caducidad_token_actualizacion) {
                throw new SesionesException("Error en caducidad token de actualizacion");
            }
            $this->_caducidad_token_actualizacion = $caducidad_token_actualizacion;
        }

        public function getSesion(){
            $sesion = array();
            $sesion['id_sesion'] = $this->getId();
            $sesion['id_usuario'] = $this->getIdUsuario();
            $sesion['token_acceso'] = $this->getTokenAcceso();
            $sesion['caducidad_token_acceso'] = $this->getCaducidadTokenAcceso();
            $sesion['token_actualizacion'] = $this->getTokenActualizacion();
            $sesion['caducidad_token_actualizacion'] = $this->getCaducidadTokenActualizacion();
            return $sesion;
        }
    }
?>