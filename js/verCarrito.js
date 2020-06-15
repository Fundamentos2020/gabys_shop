document.addEventListener('DOMContentLoaded', cargaCarrito);
const padre = document.getElementById('verProd');

const compra = document.getElementById('comprar');

compra.addEventListener("click", hacerPedido);
var exito = false;

function actualizaTotalPedido(id_ped, total){
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    id_usuario = sesionJson.id_usuario;

    var xhttp = new XMLHttpRequest();

    xhttp.open("PATCH", "http://localhost:80/Gaby's%20shop/pedido/" + id_ped, true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("Authorization", sesionJson.token_acceso);
    var json = { 
        "total": total
    };
    var json_string = JSON.stringify(json);
    xhttp.send(json_string);

    /*var data = JSON.parse(xhttp.responseText);
    if(data.success === true){
        alert("Compra exitosa!!");
    }
    else{
        alert("Error! intenta de nuevo");
    }*/
}

//var regresaIDpedido; 
function hacerPedido(){
    id_pe = crearPedido();
    console.log(id_pe);
}

function crearPedido(){
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    id_usuario = sesionJson.id_usuario;
    

    var id_ped;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "http://localhost/Gaby's%20shop/pedido", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);
            if(data.success === true){
                var ped = data.data;
                //console.log(ped.id_pedido);
                //console.log(typeof(ped.id_pedido));
                id_ped = ped.id_pedido;
                comprar(id_ped);
                //return id_ped;
            }
        }
        else {
            var data = JSON.parse(this.responseText);
            console.log(data);
            console.log("Error al obtener estatus Pedido");
            alert(data.messages);
        }
    };

    var id_user = sesionJson.id_usuario;
    var total = 0;
    var fecha_estimada = "2020-06-18";

    var json = {
        "id_usuario": id_user,
        "total": total,
        "fecha_estimada": fecha_estimada
        };
    var json_string = JSON.stringify(json);
    //console.log(this.status);
    xhr.send(json_string);

    //console.log(id_ped);
}

function comprar(id_pe){
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    id_usuario = sesionJson.id_usuario;
    //console.log(id_pe);
    total = 0;

    if(id_pe > 0){
        let productosCompra = obtieneCarrito();
        const xhr = new XMLHttpRequest();
        //Crear detalles de pedido
        productosCompra.forEach(prod => {
            //let p = obtenProducto(prod.id_producto, prod.cantidad);
            xhr.open("POST", "http://localhost/Gaby's%20shop/detalle_pedido", true);
            xhr.setRequestHeader("Content-Type", "application/json");

            xhr.setRequestHeader("Authorization", sesionJson.token_acceso);
            xhr.onload = function() {
                if (this.status == 201) {
                    var data = JSON.parse(this.responseText);
                    if(data.success === true){    
                        console.log(data);
                        console.log(total);
                        actualizaTotalPedido(id_pe, total);
                        localStorage.removeItem('productoCarrito' + id_usuario);
                        alert("Compra exitosa!!");
                    }
                }
                else {
                    var data = JSON.parse(this.responseText);
                    console.log(data);
                    console.log("Error al obtener estatus");
                    alert(data);
                    alert(data.messages);
                }
            }
            var id_producto = prod.id_producto;
            var cantidad = prod.cantidad;
            var subtotal = prod.precio * prod.cantidad;
            total += subtotal;

            var json = {
                "id_pedido": id_pe,
                "id_producto": id_producto,
                "cantidad": cantidad,
                "subtotal": subtotal
                };
            var json_string = JSON.stringify(json);
            //console.log(this.status);
            xhr.send(json_string);
        });
    }
    else{
        alert("Error en ID de pedido");
    }
}


function cargaCarrito(e){
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    id_usuario = sesionJson.id_usuario;
    let productos = obtieneCarrito();
    productos.forEach(prod => {
        let p = obtenProducto(prod.id_producto, prod.cantidad);
        //console.log(p);
        /*if(p.descuento != 0){
            var descuento = (p.precio * p.descuento) / 100;
            var precioFinal = p.precio - descuento;
            var subtotal = p.cantidad * precioFinal;
        }else{
            var precioFinal = p.precio;
            var subtotal = p.cantidad * p.precio;
        }
        html = `
            <div class="m-1-top-bot flexarchivo p-1 b-line-b">
                <div class="border ImagenProd col-m-2 col-s-6">
                <img src="${p.imagen}">
                </div>
                <div class="DetallesProd col-m-9 p-l-1 col-s-4">
                    <div class="col-m-12 col-s-12 m-b-1">${p.nombre}</div>
                    <div class="col-m-12 col-s-12 m-b-1">Cantidad: ${p.cantidad}</div>
                    <div class="col-m-12 col-s-12 m-b-1">Precio: $${precioFinal}</div>
                    <div class="col-m-12 col-s-12 m-b-1">Subtotal: $${subtotal}</div>

                </div> 
            </div> `;
            padre.innerHTML += html;*/
    });

}

function obtenProducto(id_prod, cant){
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    id_usuario = sesionJson.id_usuario;
    let pro;
    var xhr = new XMLHttpRequest();
    //xhr.open("GET", "./Controllers/productoController.php", true);
    xhr.open("GET", "http://localhost:80/Gaby's%20shop/productos/" + id_prod, true);

    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        //console.log("El estatus es " + this.status);
        if (this.status === 200) {
            //const p = JSON.parse(this.responseText);
            var data = JSON.parse(this.responseText);
            //console.log(data);
            if (data.success === true){
                pro = data.data.productos;
                console.log(pro);
                if(pro.descuento != 0){
                    var descuento = (pro.precio * pro.descuento) / 100;
                    var precioFinal = pro.precio - descuento;
                    var subtotal = cant * precioFinal;
                }else{
                    var precioFinal = pro.precio;
                    var subtotal = cant * pro.precio;
                }
                html = `
                    <div class="m-1-top-bot flexarchivo p-1 b-line-b">
                        <div class="border ImagenProd col-m-2 col-s-12">
                        <img src="${pro.imagen}">
                        </div>
                        <div class="DetallesProd col-m-9 p-l-1 col-s-12">
                            <div class="col-m-12 col-s-12 m-b-1">${pro.nombre}</div>
                            <div class="col-m-12 col-s-12 m-b-1">Cantidad: ${cant}</div>
                            <div class="col-m-12 col-s-12 m-b-1">Precio: $${precioFinal}</div>
                            <div class="col-m-12 col-s-12 m-b-1">Subtotal: $${subtotal}</div>
                            <button onclick="eliminaProductoCarrito(${pro.id_producto});">Eliminar</button> 
                        </div> 
                    </div> `;
                    padre.innerHTML += html;
                return pro;
            }
        }
    }
    xhr.send();
    //xhr.abort();
    //console.log(pro);
    //return pro;
}

function eliminaProductoCarrito(elim_id_producto){
    console.log(elim_id_producto);
    let productos = obtieneCarrito();
    let newProductos = [];
    productos.forEach(prod => {
        console.log(elim_id_producto);
        console.log(prod.id_producto);
        if(prod.id_producto != elim_id_producto){
            newProductos.push(prod);
        }
    });
    localStorage.removeItem('productoCarrito' + id_usuario);
    alert("Producto eliminado de tu carrito");
    localStorage.setItem('productoCarrito' + id_usuario, JSON.stringify(newProductos));
    window.location.href = "http://localhost:80/Gaby's%20shop/Carrito.html";
    //Checar valores de local storage
}

function obtieneCarrito(){
    let productos;
    //Checar valores de local storage
    if(localStorage.getItem('productoCarrito' + id_usuario) === null)
    {
        productos = [];
    }
    else
    {
        productos = JSON.parse(localStorage.getItem('productoCarrito' + id_usuario));
    }
    return productos;
}