document.addEventListener('DOMContentLoaded', cargaProductos);

const botonDescuento = document.getElementById('bot-descuento');
botonDescuento.addEventListener("click", apruebaDescuento);

const listaProd = document.getElementById('listaProductos');
const listaDesc = document.getElementById('listaDescuentos');


function apruebaDescuento(){
    var descuento = listaDesc.value;
    console.log(descuento);
    var id_producto = listaProd.value;
    console.log(id_producto);
    var xhttp = new XMLHttpRequest();

    xhttp.open("PATCH", "http://localhost:80/Gaby's%20shop/productos/" + id_producto, false);
    //xhttp.open("PATCH", "http://localhost:80/gabys_shop-master/usuarios/" + sesionJson.id_usuario, false);
    xhttp.setRequestHeader("Content-Type", "application/json");
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
    sesionJson = JSON.parse(sesion);

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "http://localhost/Gaby's%20shop/productos", true);

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