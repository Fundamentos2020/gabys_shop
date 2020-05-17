document.addEventListener('DOMContentLoaded', cargaProducto);

const botonCarrito = null;

//console.log(botonCarrito);
//botonCarrito.addEventListener('click', agregaCarrito);

var pro = 2;
//var pro = 4;
var nombreP;
var precioP;

//eventListeners();

function eventListeners(){
    botonCarrito = document.getElementById('boton-carrito');

    console.log(botonCarrito);
}


function agregaCarrito(){
    let idUser = 0;
    let productoCarrito = [];
    productoCarrito['idUser'] = idUser;
    productoCarrito['idProd'] = pro;
    productoCarrito['nombre'] = nombreP;
    productoCarrito['precio'] = precioP;
    productoCarrito['cantidad'] = 2;

    console.log(productoCarrito);

    localStorage.setItem('productoCarrito', JSON.stringify(productoCarrito));
}

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
                            <div>
                                <!--<button id="boton-carrito">Agregar al Carrito</button>-->
                                <input type="button" value="Agregar al Carrito" name="" id="boton-carrito" onclick="agregaCarrito();">
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
                            <div>
                            <!--<button id="boton-carrito">Agregar al Carrito</button>-->
                            <input type="button" value="Agregar al Carrito" name="" id="boton-carrito" onclick="agregaCarrito();">
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
