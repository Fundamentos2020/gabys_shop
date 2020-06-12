document.addEventListener('DOMContentLoaded', carga);
document.addEventListener('DOMContentLoaded', checaSolicitud);
const inputFile = document.querySelector('#solicitud');

function carga() {
    const padre = document.getElementById('usuario');
    var sesion = localStorage.getItem('usuario_sesion');
    if (sesion === null) {
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    var html = `

    <input type="text" name="usuario" value="${sesionJson.id_usuario}" hidden>
    `;

    padre.innerHTML += html;
}

function checaSolicitud() {
    var sesion = localStorage.getItem('usuario_sesion');
    if (sesion === null) {
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    const xhr = new XMLHttpRequest();
    //xhr.open('GET', "http://localhost/Gaby's%20shop/productos/aprobado=0", true);
    xhr.open('GET', "http://localhost/Gaby's%20shop/solicitud/id_vendedor=" + sesionJson.id_usuario, true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    padre = document.getElementById('ver');
    
    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            //console.log(this.status);
            //console.log(this.responseText);
            var data = JSON.parse(this.responseText);
            if (data.success === true) {
                document.getElementById("sol").remove();
                solicitudes = data.data.solicitud;
                var html = "";
                if(solicitudes.aprobada === 0){
                    html += `
                        <div class="m-1-top-bot  p-1 b-line-b">
                        <h3>Solicitud de vendedor:
                        Tu solicitud aún no ha sido aprobada.</h3>
                        </div> 
                        <div class="m-1-top-bot p-1">
                        <h3>Productos pendientes por aprobar:</h3>
                        </div>
                    `;
                }
                else{
                    html += `
                        <div class="m-1-top-bot  p-1 b-line-b">
                        <h3>Solicitud de vendedor:
                        Tu solicitud ya ha sido aprobada.</h3>
                        </div> 
                        <div class="m-1-top-bot p-1">
                        <h3>Productos pendientes por aprobar:</h3>
                        </div>
                    `;
                    verProductosPendientes();
                }
                padre.innerHTML += html;
            }
        }
    }
    xhr.send();
}

function verProductosPendientes(){
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    const padre = document.getElementById('verProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "http://localhost/Gaby's%20shop/productos/aprobado=0", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            //console.log(this.status);
            //console.log(this.responseText);
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                productos.forEach(prod => {
                    if(prod.id_vendedor === sesionJson.id_usuario)
                    {
                        var html = "";
                        html += `
                            <div class="m-1-top-bot flexarchivo p-1 b-line-b">
                            <div class="border ImagenProd col-m-3 col-s-6">
                            <img src="${prod.imagen}">
                            </div>
                            <div class="DetallesProd col-m-9 p-l-1 col-s-4">
                                <div class="col-m-12 col-s-12 m-b-1">Nombre: ${prod.nombre}</div>
                                <div class="col-m-12 col-s-12 m-b-1">Descripcion: ${prod.descripcion}</div>
                                <div class="col-m-12 col-s-12 m-b-1">Precio: $${prod.precio}</div>
                                <button class="teal p-1 textwhite" onclick="location='./EditarProductoVendedor.html'">
                                    Editar
                                </button>
                            </div> 
                        </div> 
                        `;
                        padre.innerHTML += html;
                    }
                });
            }
            else {
                alert(data.messages);
            }
        }
        else {
            var html = "";
            html += `
            <div class="border ImagenProd col-m-3 col-s-6">
            Aun no has subido productos
            </div>
            `;
            padre.innerHTML += html;
        }
    }
    xhr.send();
}