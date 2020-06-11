document.addEventListener('DOMContentLoaded', verOfertas);

function verOfertas(e){
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    const padre = document.getElementById('ofertaProd');

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "http://localhost:80/Gaby's%20shop/productos", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);
    
    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                productos = data.data.productos;
                productos.forEach(prod => {
                    if(prod.descuento != 0)
                    {
                        var descuento = (prod.precio * prod.descuento) / 100;
                        var precioFinal = prod.precio - descuento;
                        
                        let html = "";
                        html = `
                        <div id="producto" class="Productos col-m-3 col-s-12 p-r-1" id="producto">
                            <div class="prod border col-m-12 col-s-12" onclick="verificaProd('${prod.id_producto}')">
                                <div class="col-m-12 col-s-6">
                                    <img src="${prod.imagen}">
                                </div>
                                <div class="b-prod-top-s col-m-12 col-s-6">
                                    <div class="m-1">${prod.nombre}</div>
                                    <div class="m-1">
                                        Precio:
                                        <div class="textTac">$${prod.precio} </div> 
                                        $${precioFinal}
                                    </div>
                                    <div class="m-1">Oferta: ${prod.descuento}%</div>
                                </div>
                            </div>
                        </div>
                        `;
                        padre.innerHTML += html;
                    }
                });
            }
        }
    }
    xhr.send();
}

function verificaProd(e)
{
    ///le mando parametros a la pagina para saber que producto vamos a manejar 
    ///lo concatene asi porque con el "." me daba problemas
    //var cadena1 = "http://localhost/gabys_shop-master/VerProductoComprador.html?id_producto=";
    var cadena1 = "http://localhost/Gaby's%20shop/VerProductoComprador.html?id_producto=";
    var cadena2 = e;
    var cadena3 = cadena1+cadena2;
    window.location.href = cadena3;
}

