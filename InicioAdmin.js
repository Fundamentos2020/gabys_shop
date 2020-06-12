document.addEventListener('DOMContentLoaded', cargaProductos);

let productos =[];

/*Funcion que carga los productos al home de la página*/
//El administrador vera los mismos productos aprobados que el comprador
function cargaProductos(e) {
    //e.preventDefault();

    const padre = document.getElementById('visualProd');
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);
    var xhr = new XMLHttpRequest();
    //xhr.open("GET", "./Controllers/productoController.php", true);
    xhr.open("GET", "http://localhost:80/Gaby's%20shop/productos", true);

    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    //console.log(this.responseText);
    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            //console.log(this.status);
            //console.log(this.responseText);
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                productos.forEach(producto => {
                    var html = "";
                    html += `
                        <div id="producto" class="Productos col-m-3 col-s-12 p-r-1" onclick="verificaProd('${producto.id_producto}')">
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
}

function verificaProd(e)
{
    ///le mando parametros a la pagina para saber que producto vamos a manejar 
    ///lo concatene asi porque con el "." me daba problemas
    //var cadena1 = "http://localhost/gabys_shop-master/VerProductoComprador.html?id_producto=";
    var cadena1 = "http://localhost/Gaby's%20shop/VerProductoAdmin.html?id_producto=";
    var cadena2 = e;
    var cadena3 = cadena1+cadena2;
    window.location.href = cadena3;
}