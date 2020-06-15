const cerrar = document.getElementById('cerrar_sesion');
cerrar.addEventListener('click', cerrar_sesion);

function cerrar_sesion(e) {
    e.preventDefault();
    var xhttp = new XMLHttpRequest();
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);

    console.log(sesionJson.id_sesion);
    
    xhttp.open("DELETE","http://localhost:80/Gaby's%20shop/sesiones/" + sesionJson.id_sesion, false);
    xhttp.setRequestHeader("Authorization", sesionJson.token_acceso);
    xhttp.setRequestHeader("Content-Type", "application/json");
    
    /*var json = {"token_acceso": sesion.token_acceso };
    var json_string = JSON.stringify(json);*/

    xhttp.send();

    var data = JSON.parse(xhttp.responseText);

    if (data.success === true){
        localStorage.removeItem('usuario_sesion');
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    else{
        alert(data.messages);
    }  
}