document.addEventListener('DOMContentLoaded', cargaProductos);
document.addEventListener('DOMContentLoaded',Producto);

var pro="-1";

function cargaProductos(e){
    e.preventDefault();

    const padre = document.getElementById('visualProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function(prod){
                let html = "";
                /*html = `<option value="${prod.nombre}">${prod.nombre}</option>`;*/

                html = `
                <div class="Productos col-m-3 col-s-12 p-r-1">
                    <div class="prod border col-m-12 col-s-12">
                        <div class="col-m-12 col-s-6" onclick="verificaProd('${prod.id}'); location='/VerProductoComprador.html' ">
                            <img src="${prod.url}">
                        </div>
                        <div class="b-prod-top-s col-m-12 col-s-6">
                            <div class="m-1">${prod.nombre}</div>
                            <div class="m-1">Precio: $ ${prod.precio}</div>
                        </div>
                    </div>
                </div> `;

                padre.innerHTML += html;
            });
        }
    }
    xhr.send();
}
/*No funciona, creo debemos usar ya aqui el php para subir y solicitar variables*/
function Producto(e){
    e.preventDefault();

    const padre = document.getElementById('muestraPro');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function(prod){
                let html = "";
                /*html = `<option value="${prod.nombre}">${prod.nombre}</option>`;*/

                if(pro == prod.id)
                {
                    html = `
                    
                    <div class="border ImagenProd col-m-7 col-s-6">
                        <img src="${prod.url}">
                    </div>
                    <div class="DetallesProd col-m-4 p-l-1 col-s-4">
                    ${prod.nombre}
                    <br> 
                    Precio: ${prod.precio}
                    <br>
                    Oferta: SI/NO ${prod.descuento}
                    <br>
                    Disponibles: ${prod.cantidad}
                        <div>
                            <button>Agregar al Carrito</button>
                        </div>
                    </div>
                    
                    `;
                }

                padre.innerHTML += html;
            });
        }
    }
    xhr.send();
}


function verificaProd(e)
{
    pro=e;   
}


