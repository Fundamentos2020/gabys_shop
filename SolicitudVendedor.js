document.addEventListener('DOMContentLoaded',carga);
const inputFile = document.querySelector('#solicitud');

function carga(e){
    const padre = document.getElementById('usuario');
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);

    var html=`<input type="text" name="usuario" value="${sesionJson.id_usuario}" hidden>`;

    padre.innerHTML += html;
}

