document.addEventListener('DOMContentLoaded', cargaCarrito);
const padre = document.getElementById('verProd');

function cargaCarrito(e){
    var sesion = localStorage.getItem('usuario_sesion');
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
                        <div class="border ImagenProd col-m-2 col-s-6">
                        <img src="${pro.imagen}">
                        </div>
                        <div class="DetallesProd col-m-9 p-l-1 col-s-4">
                            <div class="col-m-12 col-s-12 m-b-1">${pro.nombre}</div>
                            <div class="col-m-12 col-s-12 m-b-1">Cantidad: ${cant}</div>
                            <div class="col-m-12 col-s-12 m-b-1">Precio: $${precioFinal}</div>
                            <div class="col-m-12 col-s-12 m-b-1">Subtotal: $${subtotal}</div>
        
                        </div> 
                    </div> `;
                    padre.innerHTML += html;
                return pro;
            }
        }
    }
    xhr.send();
    //console.log(pro);
    //return pro;
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