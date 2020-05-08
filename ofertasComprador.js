document.addEventListener('DOMContentLoaded', verOfertas);

function verOfertas(e){
    e.preventDefault();

    const padre = document.getElementById('ofertaProd');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "productos.json", true);
    
    xhr.onload = function(){//Funcion que lee lo que hay en el JSON para llenar la lista
    
        if(this.status === 200)
        {
            const p = JSON.parse(this.responseText);
            

            p.producto.forEach(function(prod){
                if(prod.descuento != 0)
                {
                    var descuento = (prod.precio * prod.descuento) / 100;
                    var precioFinal = prod.precio - descuento;
                    
                    let html = "";
                    html = `
                    <div class="Productos col-m-3 col-s-12 p-r-1">
                        <div class="prod border col-m-12 col-s-12">
                            <div class="col-m-12 col-s-6" onclick="location='/VerProductoComprador.html'">
                                <img src="${prod.url}">
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
    xhr.send();
}