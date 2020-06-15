document.addEventListener('DOMContentLoaded', cargaProducto);

var pro = 3;
//var pro = 4;
var nombreP;
var precioP;



function cargaProducto(e){
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    e.preventDefault();

    const padre = document.getElementById('muestraProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function(prod){
                let html = "";
                //html = `<option value="${prod.nombre}">${prod.nombre}</option>`;

                if(pro == prod.id)
                {
                    var precioDesc = " ";
                    if(prod.descuento != 0){
                        var descuento = (prod.precio * prod.descuento) / 100;
                        var precioFinal = prod.precio - descuento;

                        nombreP = prod.nombre;
                        precioP = precioFinal;
                        html = `
                    
                        <div class="border ImagenProd col-m-7 col-s-6">
                            <img src="${prod.url}">
                        </div>
                        <div class="DetallesProd col-m-4 p-l-1 col-s-4">
                        ${prod.nombre}
                        <br> 
                        Precio:
                        <div class="textTac">$${prod.precio}</div>
                        $${precioFinal}
                        <br>
                        Oferta:${prod.descuento} % de descuento.
                        <br>
                            Descripción:
                            <div>
                                ${prod.descripcion}
                            </div>
                        </div>
                        `;
                    }
                    else{
                        nombreP = prod.nombre;
                        precioP = prod.precio;
                        html = `
                        <div class="border ImagenProd col-m-7 col-s-6">
                            <img src="${prod.url}">
                        </div>
                        <div class="DetallesProd col-m-4 p-l-1 col-s-4">
                        ${prod.nombre}
                        <br> 
                        Precio: ${prod.precio}
                        <br>
                        Oferta: ${prod.descuento} % de descuento.
                            <br>
                            Descripción:
                            <div>
                                ${prod.descripcion}
                            </div>
                        </div>
                        `;
                    }
                }

                padre.innerHTML += html;
            });
        }
    }
    xhr.send();
}