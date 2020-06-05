document.addEventListener('DOMContentLoaded', cargaProductos);
//document.addEventListener('DOMContentLoaded',Producto);
const botonBuscar = document.getElementById('bot-busca');
const textoBuscar = document.getElementById('text-busca');

botonBuscar.addEventListener('click', buscar);



//var pro="-1";

//let productos =[];

/*Funcion que carga los productos al home de la página*/
function cargaProductos(e) {
    //e.preventDefault();

    const padre = document.getElementById('visualProd');

    var xhr = new XMLHttpRequest();
    //xhr.open('GET', "productos.json", true);
    xhr.open("GET", "http://localhost:80/Gaby's%20shop/productos", true);
    //xhr.setRequestHeader("Content-Type", "application/json");
    
    console.log("entre 1");

    
    //console.log(this.responseText);
    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            console.log("entre 2");
            console.log(this.status);
            //console.log(this.responseText);
            var data = JSON.parse(this.responseText);
            //console.log(data);
            
            if (data.success === true){
                console.log("entre 3");
                let productos = data.data.productos;
                console.log(productos);
                //var html = "";
                productos.forEach(producto => {
                    var html = "";
                    html += `
                        <div class="Productos col-m-3 col-s-12 p-r-1" id="producto">
                            <div class="prod border col-m-12 col-s-12">
                                <div class="col-m-12 col-s-6" onclick="verificaProd('${producto.id}'); location='/VerProductoComprador.html' ">
                                    
                                </div>
                                <div class="b-prod-top-s col-m-12 col-s-6">
                                    <div class="m-1">${producto.nombre}</div>
                                    <div class="m-1">Precio: $ ${producto.precio}</div>
                                </div>
                            </div>
                        </div> `;
        
                    padre.innerHTML += html;
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
    
    //<img src="${producto.url}"></img>*/

    

    /*xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista

        if (this.status === 200) {
            const p = JSON.parse(this.responseText);

            p.producto.forEach(function (prod) {
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
            });
        }
    }
    xhr.send();*/
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