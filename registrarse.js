const registrarse = document.getElementById('bot-reg');
registrarse.addEventListener('click', registrar);

function registrar(e) {
    e.preventDefault();
    //const padre = document.getElementById('bot-reg');
    var xhttp = new XMLHttpRequest();

    xhttp.open("POST", "http://localhost:80/Gaby's%20shop/" + "usuarios", true);
    //xhttp.open("POST", "http://localhost/Gaby's%20shop/Controllers/usuarioController.php", true);
    
    xhttp.setRequestHeader("Content-Type", "application/json");
    console.log(xhttp.status);
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);
        }
        else {
            var data = JSON.parse(this.responseText);
            console.log(data);
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
    if(document.getElementById("ven").value == true)
        var rol = 1;//vendedor
    else
        var rol = 0;//usuario
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
}