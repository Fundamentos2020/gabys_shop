document.addEventListener('DOMContentLoaded', cargaSolicitudes);

var pro = 3;
//var pro = 4;
var nombreP;
var precioP;



function guardaCambios(id_p, id_v){
    //e.preventDefault();

    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    const xhr = new XMLHttpRequest();
    xhr.open('PATCH', "http://localhost/Gaby's%20shop/productos/aprobado=0", false);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);
    //console.log(id_p);
    //console.log(id_v);
    
    var json = { 
        "id_producto": id_p,
        "id_vendedor": id_v,
        "aprobado": 1
    };

    var json_string = JSON.stringify(json);
    //console.log(json_string);
    xhr.send(json_string);

    //console.log(this.responseText);

    var data = JSON.parse(xhr.responseText);
    //console.log(data);
    if (data.success === true){
        //localStorage.setItem('ltareas_sesion', JSON.stringify(data.data));
        //window.location.href = client;
        alert("Producto Aprobado");
        window.location.href = "SolicitudVentas.html";
    }
    else{
        alert(data.messages);
        //window.location.href = client;
    }
}


function cargaSolicitudes(e) {
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    const padre = document.getElementById('solicitudes');

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
                            <div class="col-m-12 col-s-12 m-b-1">ID Vendedor: ${prod.id_vendedor}</div>

                            <button class="teal p-1 textwhite" onclick="guardaCambios('${prod.id_producto}, ${prod.id_vendedor}')">
                                Aprobar
                            </button>
                        </div> 
                    </div> 
                    `;
                    padre.innerHTML += html;
                });
            }
            else {
                alert(data.messages);
            }
        }
        else {
            var data = JSON.parse(this.responseText);
            
            alert(data.messages);
        }
    }
    xhr.send();
}   