document.addEventListener('DOMContentLoaded', cargaProductos);

document.getElementById("listaProductos").addEventListener('click', llena);
document.getElementById("listaProductos").addEventListener('touchend', llena);

//Funcion que llena el listBox con los productos existentes
function cargaProductos(e){
    e.preventDefault();

    const lista = document.getElementById('listaProductos');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function(prod){
                let html = "";
                html = `<option value="${prod.nombre}">${prod.nombre}</option>`;
                lista.innerHTML += html;
            });
        }
    }
    xhr.send();
}

//Funcion que autocompleta la informacion de un producto
function llena(e){
    e.preventDefault();
    const listaProd = document.getElementById('listaProductos');
    const productoSeleccionado = listaProd.options[listaProd.selectedIndex].value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

    const nombre = document.getElementById('nombreProducto');
    const descripcion = document.getElementById('desProd');
    const precio = document.getElementById('precioProd');
    const existencias = document.getElementById('existProd');

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);
            p.producto.forEach(function(prod){
                if(prod.nombre === productoSeleccionado)
                {
                    //let html = '';
                    nombre.value = prod.nombre;
                    descripcion.value = prod.descripcion;
                    precio.value = prod.precio;
                    existencias.value = prod.cantidad;
                }            
            });
        }
    }
    xhr.send();
}