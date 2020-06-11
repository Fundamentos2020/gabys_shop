document.addEventListener('DOMContentLoaded', cargaProductos);
const lista = document.getElementById('listaProductos');
lista.addEventListener('change', llena);

const botonActualiza = document.getElementById('bot-actualiza');
botonActualiza.addEventListener("click", actualizaProducto);


function actualizaProducto(){
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);

    if (sesionJson == null) {
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
        //window.location.href = "http://localhost:80/gabys_shop-master/index.html";
    }

    var id_producto = lista.value;
    var xhttp = new XMLHttpRequest();

    xhttp.open("PATCH", "http://localhost:80/Gaby's%20shop/productos/" + id_producto, false);
    //xhttp.open("PATCH", "http://localhost:80/gabys_shop-master/usuarios/" + sesionJson.id_usuario, false);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    const nombre = document.getElementById('nombreProducto').value;
    const descripcion = document.getElementById('desProd').value;
    const precio = document.getElementById('precioProd').value;
    const cantidad = document.getElementById('existProd').value;
    const foto = document.getElementById('foto').value;

    var json = {
        "nombre": nombre,
        "descripcion": descripcion,
        "precio": precio,
        "cantidad": cantidad,
        "foto": foto
    };

    var json_string = JSON.stringify(json);
    //console.log(json_string);
    xhttp.send(json_string);

    var data = JSON.parse(xhttp.responseText);

    if (data.success === true){
        //localStorage.setItem('ltareas_sesion', JSON.stringify(data.data));
        //window.location.href = client;
        alert("Producto actualizado!");
        //window.localtion.href = "PerfilComprador.html";
    }
    else{
        alert(data.messages);
        //window.location.href = client;
    }
}

//Funcion que llena el listBox con los productos existentes
function cargaProductos(e){
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
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
                    lista.innerHTML += html;
                });
            }
            else {
                alert(data.messages);
            }
        }
    }
    xhr.send();
}

//Funcion que autocompleta la informacion de un producto
function llena(e){
    e.preventDefault();
    var id_producto = lista.value;
    console.log(id_producto);

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "http://localhost/Gaby's%20shop/productos/" + id_producto, true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    const nombre = document.getElementById('nombreProducto');
    const descripcion = document.getElementById('desProd');
    const precio = document.getElementById('precioProd');
    const cantidad = document.getElementById('existProd');

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON
        const data = JSON.parse(this.responseText);
        if (data.success === true){
            prod = data.data.productos;
            nombre.value = prod.nombre;
            descripcion.value = prod.descripcion;
            precio.value = prod.precio;
            cantidad.value = prod.cantidad;
        }
        else {
            alert(data.messages);
        }
    }
    xhr.send();
}
