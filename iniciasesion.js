const entrar = document.getElementById('entra');
entrar.addEventListener('click', login);

document.addEventListener("DOMContentLoaded", checaSesion);

function login(e) {
    e.preventDefault();
    var xhttp = new XMLHttpRequest();

    xhttp.open("POST","http://localhost:80/Gaby's%20shop/sesiones", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);

            if (data.success === true){
                localStorage.setItem('usuario_sesion', JSON.stringify(data.data));
                window.location.href = "http://localhost:80/Gaby's%20shop/home.html";
            }
        }
        else {
            var data = JSON.parse(this.responseText);

            alert(data.messages);
        }
    };
    
    var correo = document.getElementById('correo').value;
    var contrasena = document.getElementById('contrasena').value;

    var json = { "correo": correo, "contrasena": contrasena };
    var json_string = JSON.stringify(json);
    
    xhttp.send(json_string);   
}

function checaSesion(e){
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);

    if (sesionJson != null && Number.isInteger(sesionJson.id_sesion)) {    
        window.location.href = "http://localhost:80/Gaby's%20shop/home.html";
    }
}