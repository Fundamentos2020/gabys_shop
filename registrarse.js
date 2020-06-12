const registrarse = document.getElementById('bot-reg');
registrarse.addEventListener('click', registrar);
/*const archivo = document.querySelector("#foto");
const botreg = document.getElementById('bot-reg');
botreg.addEventListener('click', () => {
    if(archivo.files.length > 0 ){
        alert("El archivo pesa " . archivo.files.length);
        var formData = new formData();
        formData.append("archivo",archivo.files[0]);
    }else{
        alert("inserta un archivo " . archivo.files.length);
    }    
});*/

var archivo;

function registrar(e) {
    e.preventDefault();
    //const padre = document.getElementById('bot-reg');
    var xhttp = new XMLHttpRequest();
    
    //xhttp.open("POST", "http://localhost:80/Gaby's%20shop/" + "usuarios", true);
    //xhttp.open("POST", "http://localhost:80/gabys_shop-master/" + "usuarios", true);
    xhttp.open("POST", "http://localhost/Gaby's%20shop/Controllers/usuarioController.php", true);
    //xhttp.open("POST", "http://localhost/gabys_shop-master/Controllers/usuarioController.php", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    //console.log(xhttp.status);
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);
            //sesiones(correo, contrasena);
            localStorage.setItem('user', JSON.stringify(data.data));
            console.log("entre a estatus 201");
            //console.log(data);

            var sesion = localStorage.getItem('user');
            sesionJson = JSON.parse(sesion);
            
            //console.log(sesionJson.id_usuario);

            var formData = new FormData();
            formData.append("archivo", archivo.files[0]);
            formData.append("usuario",sesionJson.id_usuario);

            console.log(formData);

            fetch("guardarfoto.php", {
                method: 'POST',
                body: formData,
            })
                .then(respuesta => respuesta.text())
                .then(decodificado => {
                    //console.log(decodificado);
                });
            sesiones(correo, contrasena);
        }
        else {
            var data = JSON.parse(this.responseText);
            console.log(data);
            console.log("Error al obtener estatus");
            alert(data);
            alert(data.messages);
        }
    };
    var correo = document.getElementById('correo').value;
    var contrasena = document.getElementById('cont').value;
    var nombre = document.getElementById('nom').value;
    var apPat = document.getElementById('apPat').value;
    var apMat = document.getElementById('apMat').value;
    var dir = document.getElementById('dir').value;
    var codPos = document.getElementById('codPos').value;
    var ciu = document.getElementById('ciu').value;
    var est = document.getElementById('est').value;
    var foto = document.getElementById('foto').value;
    var rol; 

    //let formData = new formData();
    //formData.append('imagen',foto);

    if(document.getElementById("ven").checked)
         rol = 1;//vendedor
    else
         rol = 0;//usuario
    var json = {
        "nombre": nombre,
        "apellido_pat": apPat,
        "apellido_mat": apMat,
        "correo": correo,
        "contrasena": contrasena,
        "direccion": dir,
        "cod_postal": codPos,
        "ciudad": ciu,
        "estado": est,
        "foto_perfil": foto,
        "rol": rol
        };
    var json_string = JSON.stringify(json);
    //console.log(this.status);

    xhttp.send(json_string);

    //alert("El nombre del archivo es " + archivo.files[0].name);
    

    if(rol == 1){
        //window.location.href = "http://localhost:80/gabys_shop-master/InicioVendedor.html";
        
        //window.location.href = "http://localhost:80/Gaby's%20shop/InicioVendedor.html";
    }else{
        //sesiones(correo, contrasena);
        //window.location.href = "http://localhost:80/Gaby's%20shop/Home.html";
    }
}

function sesiones(correo, contrasena){
    var xhttp = new XMLHttpRequest();

    xhttp.open("POST","http://localhost:80/Gaby's%20shop/sesiones", true);
    //xhttp.open("POST","http://localhost:80/gabys_shop-master/sesiones", true);
    //xhttp.setRequestHeader("Authorization", data.data.token_acceso);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);

            if (data.success === true){
                //var rol = JSON.stringify(data.data.rol);
                localStorage.setItem('usuario_sesion', JSON.stringify(data.data));
                var rol = data.data.rol;
                console.log(rol);
                if(rol == 0){
                    window.location.href = "http://localhost:80/Gaby's%20shop/home.html";
                    //window.location.href = "http://localhost:80/gabys_shop-master/home.html";
                }
                else if(rol == 1){
                    window.location.href = "http://localhost:80/Gaby's%20shop/inicioVendedor.html";
                    //window.location.href = "http://localhost:80/gabys_shop-master/inicioVendedor.html";
                }
            }
        }
        else {
            var data = JSON.parse(this.responseText);

            alert(data.messages);
        }
    };

    var json = { "correo": correo, "contrasena": contrasena };
    var json_string = JSON.stringify(json);
    
    xhttp.send(json_string);   
}

function cambiarFile(){
    var input = document.getElementById('foto');
    if(input.files && input.files[0]){
        //alert("archivo seleccionado: " + input.files[0].name);
        archivo = input;
    }else{alert("selecciona un archivo ");}
}