document.addEventListener('DOMContentLoaded', cargaInfo);
document.addEventListener('DOMContentLoaded', cargaPedido);

const listaPedidos = document.getElementById('listaPedidos');
listaPedidos.addEventListener("change", cargaInfoPedido);

function cargaInfo() {
    const padre = document.getElementById('infoPerfil');
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    const xhr = new XMLHttpRequest();
    //console.log("Probando");
    xhr.open("GET", "http://localhost:80/Gaby's%20shop/usuarios/" + sesionJson.id_usuario, true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        //console.log("El estatus es " + this.status);
        if (this.status === 200) {
            //const p = JSON.parse(this.responseText);
            var data = JSON.parse(this.responseText);
            //console.log(data);
            if (data.success === true) {
                user = data.data.usuario;
                //console.log(user);
                var html = "";

                html = `
                <div class="">
                    <img src="${user.foto_perfil}" alt="Aqui deberia estar la imagen">
                    <div>${user.nombre} ${user.apellido_pat}</div>
                </div>
                <div class="fondogris">
                    <div>
                        <a href="PerfilComprador.html">Mis datos</a>
                    </div>
                    <div class="grisfuerte">
                        Mis pedidos
                    </div>
                </div>
                    `;
                padre.innerHTML += html;
            }
        }
    }
    xhr.send();
}

function cargaPedido(){
    const padre = document.getElementById('listaPedidos');
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    const xhttp = new XMLHttpRequest();

    xhttp.open("GET", "http://localhost/Gaby's%20shop/pedido/id_usuario/" + sesionJson.id_usuario, false);

    xhttp.send();

    var data = JSON.parse(xhttp.responseText);

    if (data.success === true){
        pedidos = data.data.pedidos;
        var html = "";
        pedidos.forEach(p => {
            html += 
                `<option value="${p.id_pedido}">${p.id_pedido}</option>`;
        });

        padre.innerHTML += html;
    }
    else {
        alert(data.messages);
    }
}

function cargaInfoPedido(){
    console.log(listaPedidos.value);
        var xhttp = new XMLHttpRequest();
        var id_pedido = listaPedidos.value;
        xhttp.open("GET", "http://localhost:80/Gaby's%20shop/pedido/" + id_pedido, false);
        const padre = document.getElementById('infoPedido');

        xhttp.onload = function() {
            if (this.status == 200) {
                var html = "";
                var data = JSON.parse(this.responseText);

                if (data.success == true) {
                    var infoPedido = data.data.pedido;
                    html += `
                        <div class="p-1">
                            N° de Pedido: ${infoPedido.id_pedido}
                        </div>
                        <div class="border-top p-1">
                            Detalles: 
                            <br>
                            Fecha estimada de entrega: ${infoPedido.fecha_estimada}
                            <br>
                            Total: $${infoPedido.total}
                        </div>
                        <div class="p-1">
                            <div class="fondoblanco border "> 
                                <div class="teal leng-box " style="width: 50%;"></div>
                        </div>
                        </div>
                        <div class="contenidoflex">
                            <div class="">
                                Tu pedido esta <br> en proceso
                            </div>
                            <div class="">
                                Tu pedido ha sido <br> enviado
                            </div>
                            <div class="">
                                Pedido recibido
                            </div>
                        </div>
                    `;
                }
                padre.innerHTML = html;
            }
        }
        xhttp.send();
}