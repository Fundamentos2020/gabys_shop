<?php
    if(isset($_POST['subir'])){

        $nombre = $_FILES['archivo']['name'];
        $tipo = $_FILES['archivo']['type'];
        $tamano = $_FILES['archivo']['size'];
        $ruta = $_FILES['archivo']['tmp_name'];
        $destino = "archivos/".$nombre;

        if($nombre != ""){
            if(copy($ruta,$destino)){
                //echo "Exito";

                $dns = 'mysql:host=localhost;dbname=gaby\'s_shop;charset=utf8';
                $username = 'root';
                $password = '';
                $connection = new PDO($dns, $username, $password);

                $idv = $_POST['usuario'];
                $id1 = 0;
                $id2 = 0;
                $query = $connection->prepare('INSERT INTO solicitud(id_vendedor, solicitudRuta, aprobada) 
                VALUES(:idv, :nombre, :id2)');
                $query->bindParam(':idv', $idv, PDO::PARAM_INT);
                $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                //$query->bindParam(':id1', $id1, PDO::PARAM_INT);
                $query->bindParam(':id2', $id2, PDO::PARAM_INT);
                
                $query->execute();
                echo "se ha enviado la solicitud";
                echo "<br>";
                $boton = "button";
                //$direccion = "location.href='https://www.facebook.com'";
                //echo ;
                echo '<a href="./InicioVendedor.html">Regresar al inicio </a>';

            }else{
                echo "Error";
            }
        }
    }
?>