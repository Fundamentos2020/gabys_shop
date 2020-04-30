document.addEventListener('DOMContentLoaded', cargaProductos);

function cargaProductos(e){
    e.preventDefault();

    const padre = document.getElementById('listaProductos');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function(prod){
                let html = "";
                html = `<option value="${prod.nombre}">${prod.nombre}</option>`;
                padre.innerHTML += html;
            });
        }
    }
    xhr.send();
}