document.addEventListener('DOMContentLoaded', cargaInfo);

var idUser = 0;

function cargaInfo(e) {
    e.preventDefault();

    const padre = document.getElementById('infoPerfil');
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    const xhr = new XMLHttpRequest();
    console.log(sesionJson.id_usuario);
    xhr.open("GET","http://localhost:80/Gaby's%20shop/usuarios/" + sesionJson.id_usuario, true);
    //xhr.open('GET', "usuarios.json", true);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista

        if (this.status === 200) {
            //const p = JSON.parse(this.responseText);
            var data = JSON.parse(this.responseText);
            console.log(data);
            if (data.success === true){
                user = data.data.usuario;
                console.log(user);
                    var html = "";
                    
                    html = `
                    <div class="col-m-3 col-s-12 textcenter border m-t-1 textgeneral">
                        <div class="">
                        <div class="ImagenProd">
                            <img src="${user.foto_perfil}" alt="Aqui deberia estar la imagen">
                        </div>
                            <div>${user.nombre} ${user.apellido_pat}</div>
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
                                <input type="text" id="nombre" value="${user.nombre}" >
                                </div>
                            </div>
        
                            <div class="p-1" > <!--Apellidos-->
                                <div>
                                    Apellidos: 
                                </div>
                                <div>
                                    <input type="text" value="${user.apellido_pat} ${user.apellido_mat}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Correo-->
                                <div>
                                    Correo: 
                                </div>
                                <div>
                                    <input type="text" id="correo" value="${user.correo}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Actualizar contraseña-->
                                <div>
                                    Actualizar contraseña: 
                                </div>
                                <div>
                                    <input type="password" id="contrasena" value="">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Direccion-->
                                <div>
                                    Direccion: 
                                </div>
                                <div>
                                    <input type="text" id="direccion" value="${user.direccion}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--C.P-->
                                <div>
                                    C.P: 
                                </div>
                                <div>
                                    <input type="text" id="cod_postal" value="${user.cod_postal}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Ciudad-->
                                <div>
                                    Ciudad: 
                                </div>
                                <div>
                                    <input type="text" id="ciudad" value="${user.ciudad}">  
                                </div>
                            </div>
        
                            <div class="p-1"> <!--Estado-->
                                <div>
                                    Estado: 
                                </div>
                                <div>
                                    <input type="text" id="estado" value="${user.estado}">  
                                </div>
                            </div>
                        </div>
                        <div class="p-1">
                            <button class="teal p-1 textwhite" onclick="actualizaInfo();">
                                Actualizar
                            </button>
                        </div>
                    </div>
                    `;
                    padre.innerHTML += html;
            }
        }
    }
    xhr.send();
}

function actualizaInfo(){

    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);

    if (sesionJson == null) {
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }

    var xhttp = new XMLHttpRequest();

    xhttp.open("PATCH", "http://localhost:80/Gaby's%20shop/usuarios/" + sesionJson.id_usuario, false);
    xhttp.setRequestHeader("Content-Type", "application/json");

    //Llenar el json con la informacion de los textbox
    var correo = document.getElementById('correo').value;
    var contrasena = document.getElementById('contrasena').value;
    var nombre = document.getElementById('nombre').value;
    /*var apPat = document.getElementById('apPat').value;
    var apMat = document.getElementById('apMat').value;*/
    var dir = document.getElementById('direccion').value;
    var codPos = document.getElementById('cod_postal').value;
    var ciu = document.getElementById('ciudad').value;
    var est = document.getElementById('estado').value;
    //var foto = document.getElementById('foto').value;

    var json = { 
        "nombre": nombre,
        /*"apellido_pat": apPat,
        "apellido_mat": apMat,*/
        "correo": correo,
        "contrasena": contrasena,
        "direccion": dir,
        "cod_postal": codPos,
        "ciudad": ciu,
        "estado": est
    };
    var json_string = JSON.stringify(json);

    xhttp.send(json_string);

    var data = JSON.parse(xhttp.responseText);

    if (data.success === true){
        //localStorage.setItem('ltareas_sesion', JSON.stringify(data.data));
        //window.location.href = client;
        alert("Informacion actualizada!");
    }
    else{
        alert(data.messages);
        //window.location.href = client;
    }

    //xhttp.setRequestHeader("Authorization", sesionJson.token_acceso);




}