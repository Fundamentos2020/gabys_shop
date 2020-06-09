document.addEventListener('DOMContentLoaded', cargaSolicitudes);

var pro = 3;
//var pro = 4;
var nombreP;
var precioP;



function cargaSolicitudes(e) {
    e.preventDefault();

    const padre = document.getElementById('solicitudes');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "http://localhost/Gaby's%20shop/productos/aprobado=0", true);

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
                            <div class="col-m-12 col-s-12 m-b-1">
                                <input type="radio">
                                <label for="">Aprobada</label>
                                <input type="radio">
                                <label for="">Desaprobada</label>
                            </div>
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