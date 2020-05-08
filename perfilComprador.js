document.addEventListener('DOMContentLoaded', cargaInfo);

var idUser = 0;

function cargaInfo(e) {
    e.preventDefault();

    const padre = document.getElementById('infoPerfil');

    const xhr = new XMLHttpRequest();
    xhr.open('GET', "usuarios.json", true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista

        if (this.status === 200) {
            const p = JSON.parse(this.responseText);

            p.usuario.forEach(function (us) {
                let html = "";
                //html = `<option value="${prod.nombre}">${prod.nombre}</option>`;

                if (idUser == us.id) {
                    html = `
                    <div class="col-m-3 col-s-12 textcenter border m-t-1 textgeneral">
                        <div class="">
                            <img src="${us.foto}" alt="Aqui deberia estar la imagen">
                            <div>${us.nombre} ${us.apellidos}</div>
                        </div>
                        <div class="fondogris">
                            <div class="grisfuerte">
                                <a href="PerfilComprador.html">Mis datos</a>
                            </div>
                            <div>
                                <a href="PedidosComprador.html">Mis pedidos</a>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-m-9 textgeneral">
                        <div class="InfoPerFlex">
                            <div class="p-1"> <!--Nombre-->
                                <div>
                                    Nombre:
                                </div>
                                <div>
                                <input type="text" value="${us.nombre}" >
                                </div>
                            </div>
        
                            <div class="p-1" > <!--Apellidos-->
                                <div>
                                    Apellidos: 
                                </div>
                                <div>
                                    <input type="text" value="${us.apellidos}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Correo-->
                                <div>
                                    Correo: 
                                </div>
                                <div>
                                    <input type="text" value="${us.correo}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Actualizar contraseña-->
                                <div>
                                    Actualizar contraseña: 
                                </div>
                                <div>
                                    <input type="password" value="${us.password}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Direccion-->
                                <div>
                                    Direccion: 
                                </div>
                                <div>
                                    <input type="text" value="${us.direccion}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--C.P-->
                                <div>
                                    C.P: 
                                </div>
                                <div>
                                    <input type="text" value="${us.codigo_postal}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Ciudad-->
                                <div>
                                    Ciudad: 
                                </div>
                                <div>
                                    <input type="text" value="${us.ciudad}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Estado-->
                                <div>
                                    Estado: 
                                </div>
                                <div>
                                    <input type="text" value="${us.estado}">  
                                </div>
                            </div>
                        </div>
                        <div class="p-1">
                            <button class="teal p-1 textwhite">
                                Actualizar
                            </button>
                        </div>
                    </div>
                    `;
                }

                padre.innerHTML += html;
            });
        }
    }
    xhr.send();
}