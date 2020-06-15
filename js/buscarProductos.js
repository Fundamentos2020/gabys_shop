const botonBuscar = document.getElementById('bot-busca');
const textoBuscar = document.getElementById('text-busca');

botonBuscar.addEventListener('click', buscar);

function buscar(){
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    borraProductos();

    const padre = document.getElementById('visualProd');

    let busca = textoBuscar.value;

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "http://localhost:80/Gaby's%20shop/productos", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                productos.forEach(prod => {
                    let nombre = prod.nombre;
                    let nombreMin = nombre.toLowerCase();
                    let descripcion = prod.descripcion;
                    let descripcionMin = descripcion.toLowerCase();
                    if(nombre.includes(busca) || descripcion.includes(busca) || nombreMin.includes(busca) || descripcionMin.includes(busca)){
                        var html = "";
                        html += `
                            <div id="producto" class="Productos col-m-3 col-s-12 p-r-1" onclick="verificaProd('${prod.id_producto}')">
                                <div class="prod border col-m-12 col-s-12">
                                    <div class="col-m-12 col-s-6">                                                      
                                    <div class="b-prod-top-s col-m-12 col-s-6">
                                        <img src="${prod.imagen}">
                                        <div class="m-1">${prod.nombre}</div>
                                        <div class="m-1">Precio: $ ${prod.precio} </div>                
                                    </div>
                                    </div>
                                </div>
                            </div> `;
            
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

/*Funcion que borra los productos que se muestran en el home*/
function borraProductos(){
    while(document.getElementById("producto"))
        document.getElementById("producto").remove();
}