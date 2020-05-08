document.addEventListener('DOMContentLoaded', cargaProducto);

var pro = 2;
//var pro = 4;

function cargaProducto(e){
    e.preventDefault();

    const padre = document.getElementById('muestraProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

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
                            <div>
                                <button>Agregar al Carrito</button>
                            </div>
                            <br>
                            Descripción:
                            <div>
                                ${prod.descripcion}
                            </div>
                        </div>
                        `;
                    }
                    else{
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
                            <div>
                                <button>Agregar al Carrito</button>
                            </div>
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