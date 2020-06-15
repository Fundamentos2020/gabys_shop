<?php
        $nombre = $_FILES['archivo']['name'];
        $tipo = $_FILES['archivo']['type'];
        $tamano = $_FILES['archivo']['size'];
        $ruta = $_FILES['archivo']['tmp_name'];
        $destino = "Imagenes/".$nombre;

        $id = $_POST['usuario'];

        
        if($nombre != ""){
            if(copy($ruta,$destino)){
                //echo $id;

                $dns = 'mysql:host=localhost;dbname=gaby\'s_shop;charset=utf8';
                $username = 'root';
                $password = '';
                $connection = new PDO($dns, $username, $password);

                $bytesArchivo = file_get_contents("Imagenes/".$nombre);

                $query = $connection->prepare('UPDATE usuario SET foto_perfil = :foto_perfil WHERE id_usuario = :idusuario');
                $query->bindParam(':foto_perfil', $bytesArchivo);
                $query->bindParam(':idusuario', $id, PDO::PARAM_INT);
                $query->execute();
            }
        }
?>