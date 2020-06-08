const entrar = document.getElementById('entra');
entrar.addEventListener('click', login);

document.addEventListener("DOMContentLoaded", checaSesion);

function login(e) {
    e.preventDefault();
    var xhttp = new XMLHttpRequest();

    xhttp.open("POST","http://localhost:80/Gaby's%20shop/sesiones", true);
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
                }
                else if(rol == 1){
                    window.location.href = "http://localhost:80/Gaby's%20shop/inicioVendedor.html";
                }
                else if(rol == 2){
                    window.location.href = "http://localhost:80/Gaby's%20shop/inicioAdmin.html";
                }
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
        var rol = sesionJson.rol;
        if(rol == 0){
            window.location.href = "http://localhost:80/Gaby's%20shop/home.html";
        }
        else if(rol == 1){
            window.location.href = "http://localhost:80/Gaby's%20shop/inicioVendedor.html";
        }
        else if(rol == 2){
            window.location.href = "http://localhost:80/Gaby's%20shop/inicioAdmin.html";
        }
    }
}