<?php
    class Conexion{
        private static $connection;
        public static function conecta(){
            if(self::$connection === null)
            {
                    $dns = 'mysql:host=localhost;dbname=gaby\'s_shop;charset=utf8';
                    $username = 'root';
                    $password = '';
            
                    self::$connection = new PDO($dns, $username, $password);
                    self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    //self::$connection->setAttribute(PDO::ATTR_EMULATE, false);
                    return self::$connection;
            }
        }


    }
?>