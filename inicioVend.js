document.addEventListener('DOMContentLoaded', cargaProductos);
//document.addEventListener('DOMContentLoaded',Producto);
const botonBuscar = document.getElementById('bot-busca');
const textoBuscar = document.getElementById('text-busca');

botonBuscar.addEventListener('click', buscar);

//var pro="-1";
let productos=[];
/*Funcion que carga los productos al home de la página*/
function cargaProductos(e) {
    e.preventDefault();


    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    console.log(sesionJson);
    const padre = document.getElementById('visualProd');
    console.log("holi");
    console.log("comoestas");
    console.log(sesionJson.id_usuario);

    const xhr = new XMLHttpRequest();
    //xhr.open('GET', "http://localhost:80/Gaby's%20shop/Controllers/productoController.php", true);
    xhr.open("GET", "./Controllers/productoController.php", true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista

        if (this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                productos.forEach(producto => {
                    var html = "";
                    if(sesionJson.id_usuario == producto.id_vendedor){
                        html += `
                        <div class="Productos col-m-3 col-s-12 p-r-1" onclick="verificaProd('${producto.id_producto}')">
                            <div class="prod border col-m-12 col-s-12">
                                <div class="col-m-12 col-s-6">                                                      
                                <div class="b-prod-top-s col-m-12 col-s-6">
                                    <img src="${producto.imagen}">
                                    <div class="m-1">${producto.nombre}</div>
                                    <div class="m-1">Precio: $ ${producto.precio} </div>                
                                </div>
                                </div>
                            </div>
                        </div> `;
        
                    }
                    padre.innerHTML += html;
                });
            }
            else {
                alert(data.messages);
            }
        }
    }
    xhr.send();
}

function buscar(){
    borraProductos();

    const padre = document.getElementById('visualProd');

    let busca = textoBuscar.value;
    console.log(busca);

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista

        if (this.status === 200) {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function (prod) {

                let nombre = prod.nombre;
                let nombreMin = nombre.toLowerCase();
                let descripcion = prod.descripcion;
                let descripcionMin = descripcion.toLowerCase();
                if(nombre.includes(busca) || descripcion.includes(busca) || nombreMin.includes(busca) || descripcionMin.includes(busca))
                {//Si lo que se busca existe lo pone en la pagina
                    let html = "";

                    html = `
                        <div class="Productos col-m-3 col-s-12 p-r-1" id="producto">
                            <div class="prod border col-m-12 col-s-12">
                                <div class="col-m-12 col-s-6" onclick="verificaProd('${prod.id}'); location='/VerProductoComprador.html' ">
                                    <img src="${prod.url}">
                                </div>
                                <div class="b-prod-top-s col-m-12 col-s-6">
                                    <div class="m-1">${prod.nombre}</div>
                                    <div class="m-1">Precio: $ ${prod.precio}</div>
                                </div>
                            </div>
                        </div> `;

                    padre.innerHTML += html;   
                }
            });
        }
    }
    xhr.send();
}

/*Funcion que borra los productos que se muestran en el home*/
function borraProductos(){
    while(document.getElementById("producto"))
        document.getElementById("producto").remove();
}
