<?php
        $nombre = $_FILES['archivo']['name'];
        $tipo = $_FILES['archivo']['type'];
        $tamano = $_FILES['archivo']['size'];
        $ruta = $_FILES['archivo']['tmp_name'];
        $destino = "Imagenes/".$nombre;

        $id = $_POST['producto'];
  
        if($nombre != ""){
            if(copy($ruta,$destino)){
                //echo $id;

                $dns = 'mysql:host=localhost;dbname=gaby\'s_shop;charset=utf8';
                $username = 'root';
                $password = '';
                $connection = new PDO($dns, $username, $password);

                $bytesArchivo = file_get_contents("Imagenes/".$nombre);

                $query = $connection->prepare('UPDATE producto SET imagen = :imagen WHERE id_producto = :producto');
                $query->bindParam(':imagen', $bytesArchivo);
                $query->bindParam(':producto', $id, PDO::PARAM_INT);
                $query->execute();
            }
        }
?>