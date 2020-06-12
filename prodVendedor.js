document.addEventListener('DOMContentLoaded', cargaProductos);

const botonDescuento = document.getElementById('bot-descuento');
botonDescuento.addEventListener("click", apruebaDescuento);

const listaProd = document.getElementById('listaProductos');
const listaDesc = document.getElementById('listaDescuentos');

listaProd.addEventListener("change", muestraInfo);

function muestraInfo(){
    borraProductos();
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    var id_producto = listaProd.value;
    var padre = document.getElementById('info-sel');
    const xhr = new XMLHttpRequest();
    xhr.open('GET', "http://localhost/Gaby's%20shop/productos/" + id_producto, true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);
    
    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                    var html = "";
                        var precioF;
                        if(productos.descuento == 0){
                            precioF = productos.precio;
                        }
                        else{
                            var descuento = (productos.precio * productos.descuento) / 100;
                            precioF = productos.precio - descuento;
                        }
                        html +=  `      
                                        <div class="ImagenProd col-m-3 col-s-6" id="holi">
                                        <img src="${productos.imagen}">
                                        </div>
                                        <div class="DetallesProd col-m-9 p-l-1 col-s-4" id="holi1">
                                            <div class="col-m-12 col-s-12 m-b-1">Nombre: ${productos.nombre}</div>
                                            <div class="col-m-12 col-s-12 m-b-1">Descripcion: ${productos.descripcion}</div>
                                            <div class="col-m-12 col-s-12 m-b-1">Precio: $${productos.precio}</div>
                                            <div class="col-m-12 col-s-12 m-b-1">Descuento actual: ${productos.nombre}</div>
                                            <div class="col-m-12 col-s-12 m-b-1">Precio con descuento: $${productos.nombre}</div> 
                                        </div>`;
                    padre.innerHTML += html;
            }
            else {
                alert(data.messages);
            }
        }
    }
    xhr.send();
}

function borraProductos(){
    while(document.getElementById("holi"))
        document.getElementById("holi").remove();
    while(document.getElementById("holi1"))
        document.getElementById("holi1").remove();
}

function apruebaDescuento(){
    var descuento = listaDesc.value;
    console.log(descuento);
    var id_producto = listaProd.value;
    console.log(id_producto);
    var xhttp = new XMLHttpRequest();

    xhttp.open("PATCH", "http://localhost:80/Gaby's%20shop/productos/" + id_producto, false);
    //xhttp.open("PATCH", "http://localhost:80/gabys_shop-master/usuarios/" + sesionJson.id_usuario, false);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("Authorization", sesionJson.token_acceso);
    var json = {
        "descuento": descuento
    };
    var json_string = JSON.stringify(json);
    //console.log(json_string);
    xhttp.send(json_string);

    var data = JSON.parse(xhttp.responseText);

    if (data.success === true){
        //localStorage.setItem('ltareas_sesion', JSON.stringify(data.data));
        //window.location.href = client;
        alert("Informacion actualizada!");
        //window.localtion.href = "PerfilComprador.html";
    }
    else{
        alert(data.messages);
        //window.location.href = client;
    }
}

function cargaProductos(e){
    e.preventDefault();

    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "http://localhost/Gaby's%20shop/productos", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                productos.forEach(producto => {
                    var html = "";
                    if(sesionJson.id_usuario == producto.id_vendedor){
                        html +=  `<option value="${producto.id_producto}">${producto.nombre}</option>`;
                    }
                    listaProd.innerHTML += html;
                });
            }
            else {
                alert(data.messages);
            }
        }
    }
    xhr.send();
}