document.addEventListener('DOMContentLoaded', cargaSolicitudes);

var pro = 3;
//var pro = 4;
var nombreP;
var precioP;



function cargaSolicitudes(e) {
    e.preventDefault();

    const padre = document.getElementById('solicitudes');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "solicitudProd.json", true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista

        if (this.status === 200) {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function (prod) {
                let html = "";
                html = `
                <div class="m-1-top-bot flexarchivo p-1 b-line-b">
                    <div class="border ImagenProd col-m-3 col-s-6">
                    <img src="${prod.url}">
                    </div>
                    <div class="DetallesProd col-m-9 p-l-1 col-s-4">
                        <div class="col-m-12 col-s-12 m-b-1">Nombre: ${prod.nombre}</div>
                        <div class="col-m-12 col-s-12 m-b-1">Descripcion: ${prod.descripcion}</div>
                        <div class="col-m-12 col-s-12 m-b-1">Precio: $${prod.precio}</div>
                        <div class="col-m-12 col-s-12 m-b-1">ID Vendedor: ${prod.id_vendedor}</div>
                        <div class="col-m-12 col-s-12 m-b-1">Vendedor: ${prod.vendedor}</div>
                        <div class="col-m-12 col-s-12 m-b-1">
                            <input type="radio">
                            <label for="">Aprobada</label>
                            <input type="radio">
                            <label for="">Desaprobada</label>
                        </div>
                    </div> 
                </div> 
                `
                padre.innerHTML += html;
            });
        }
    }
    xhr.send();
}   

                /*  <div class="col-m-12 col-s-12">Nombre: ${prod.nombre}</div>
                    <div class="col-m-12 col-s-12">Descripcion: ${prod.descripcion}</div>
                    
                    <div class="col-m-12 col-s-12">Precio: $${prod.precio}</div>
                    
                    <div class="col-m-4 col-s-12">
                    <div> <img src="${prod.url}" alt="Imagen del producto"> </div>
                    </div>
                    <div class="col-m-12 col-s-12">
                            <input type="radio">
                            <label for="">Aprobada</label>
                            <input type="radio">
                            <label for="">Desaprobada</label>
                    </div>
                </div>*/