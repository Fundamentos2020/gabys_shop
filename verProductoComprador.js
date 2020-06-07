document.addEventListener('DOMContentLoaded', cargaProducto);

const botonCarrito = null;

//console.log(botonCarrito);
//botonCarrito.addEventListener('click', agregaCarrito);

var pro = 0;
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
    //e.preventDefault();
    //window.alert(window.name);
    var id = window.name;

    ///Como mando un parametro, que es el id_producto lo obtengo para poder hacer la seleccion al tener el get
    let parametro = new URLSearchParams(location.search);
    var param = parametro.get('id_producto');
    console.log(param);
    const padre = document.getElementById('muestraProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "./Controllers/productoController.php", true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data;
                var html = "";
                productos.productos.forEach(prod => {
                    console.log(prod.id_producto);
                    console.log(param);
                    if(param == prod.id_producto)
                    {
                        var precioDesc = " ";
                        if(prod.descuento != 0){
                            var descuento = (prod.precio * prod.descuento) / 100;
                            var precioFinal = prod.precio - descuento;

                            nombreP = prod.nombre;
                            precioP = precioFinal;
                            html = `
                        
                            <div class="border ImagenProd col-m-7 col-s-6">
                                <img src="${prod.imagen}">
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
                                <img src="${prod.imagen}">
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

                        padre.innerHTML += html;
                    }
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
