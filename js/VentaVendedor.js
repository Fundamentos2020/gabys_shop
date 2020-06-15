document.querySelector('#guardar').addEventListener('click',guardarProducto);

var id_v;
var id_p;
var foto;

function crearSolicitud(e){
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    var xhttp = new XMLHttpRequest();

    //xhttp.open("POST", "http://localhost:80/gabys_shop-master/" + "productos", true);
    xhttp.open("POST", "http://localhost:80/Gaby's%20shop/solicitud", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("Authorization", sesionJson.token_acceso);
    console.log(xhttp.status);
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);
            console.log("entre a estatus 201");
            //console.log(data);
        }
        else {
            var data = JSON.parse(this.responseText);
            console.log(data);
            console.log("Error al obtener estatus");
            alert(data);
            alert(data.messages);
        }
    };
}


function guardarProducto(e){
    ///window.alert("alertaaaaaaa");
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    var xhttp = new XMLHttpRequest();
    console.log(sesionJson.id_usuario);
    //xhttp.open("POST", "http://localhost:80/gabys_shop-master/" + "productos", true);
    xhttp.open("POST", "http://localhost:80/Gaby's%20shop/productos", true);
    //xhttp.open("POST", "http://localhost:80/gabys_shop-master/productos", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("Authorization", sesionJson.token_acceso);
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);
            console.log("entre a estatus 201");
            console.log(data);
            alert("Producto creado! Espera a que sea aprobado");
            //console.log(data.data.id_producto);

            var formData = new FormData();
            formData.append("archivo", foto.files[0]);
            formData.append("producto",data.data.id_producto);
            console.log(formData);

            fetch("../Controllers/guardarProducto.php", {
                method: 'POST',
                body: formData,
            })
                .then(respuesta => respuesta.text())
                .then(decodificado => {
                    //console.log(decodificado);
                });

        }
        else {
            var data = JSON.parse(this.responseText);
            console.log(data);
            console.log("Error al obtener estatus");
            alert(data);
            alert(data.messages);
        }
    };

    var id_vendedor = sesionJson.id_usuario;

    var nombre = document.getElementById('Nombre').value;
    var descripcion = document.getElementById('Descripcion').value;
    var precio = document.getElementById('Precio').value;
    var cantidad = document.getElementById('Cantidad').value;
    var imagen = document.getElementById('Imagen').value;

    var json = {
        "id_vendedor": id_vendedor,
        "nombre": nombre,
        "descripcion": descripcion,
        "precio": precio,
        "cantidad": cantidad,
        "descuento": 0,
        "aprobado": 0,
        "imagen": imagen
        };
    var json_string = JSON.stringify(json);
    //console.log(this.status);

    xhttp.send(json_string);

    /*id_v = id_vendedor;
    id_p = id_p
    crearSolicitud();*/
}

function cambiarFile(){
    var input = document.getElementById('Imagen');
    if(input.files && input.files[0]){
        //alert("archivo seleccionado: " + input.files[0].name);
        foto = input;
    }else{alert("selecciona un archivo ");}
}