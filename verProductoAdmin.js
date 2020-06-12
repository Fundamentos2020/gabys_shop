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

function desaprobar(id_p, id_v){
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
        "aprobado": 0
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
        alert("Producto desaprobado");
        window.location.href = "InicioAdmin.html";
    }
    else{
        alert(data.messages);
        //window.location.href = client;
    }
}

function cargaProducto(e){
    //e.preventDefault();
    //window.alert(window.name);
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    var id = window.name;

    ///Como mando un parametro, que es el id_producto lo obtengo para poder hacer la seleccion al tener el get
    let parametro = new URLSearchParams(location.search);
    var param = parametro.get('id_producto');
    //console.log(param);
    const padre = document.getElementById('muestraProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "./Controllers/productoController.php", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                var html = "";
                productos.forEach(prod => {
                    //console.log(prod.id_producto);
                    //console.log(param);
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
                            <div class="textTac">$ ${prod.precio}</div>
                            $ ${precioFinal}
                            <br>
                            Oferta:${prod.descuento} % de descuento.
                            <br>
                                <div>
                                    <!--<button id="boton-carrito">Agregar al Carrito</button>-->
                                    <input type="button" value="Desaprobar" name="" id="boton-carrito" onclick="desaprobar(${prod.id_producto}, ${prod.id_vendedor});">
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
                            Precio: $ ${prod.precio}
                            <br>
                            Oferta: ${prod.descuento} % de descuento.
                            <br>
                                <div>
                                <!--<button id="boton-carrito">Agregar al Carrito</button>-->
                                <input type="button" value="Desaprobar" name="" id="boton-carrito" onclick="desaprobar(${prod.id_producto}, ${prod.id_vendedor});">
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
