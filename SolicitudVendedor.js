document.addEventListener('DOMContentLoaded',carga);
const inputFile = document.querySelector('#solicitud');

function carga(e){
    const padre = document.getElementById('usuario');
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    var html=`<input type="text" name="usuario" value="${sesionJson.id_usuario}" hidden>`;

    padre.innerHTML += html;
}

