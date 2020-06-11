document.addEventListener('DOMContentLoaded', cargaSolicitudes);

var pro = 3;
//var pro = 4;
var nombreP;
var precioP;


function guardaCambios(id_p){
    //e.preventDefault();

    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);

    const xhr = new XMLHttpRequest();
    //xhr.open('PATCH', "http://localhost/gabys_shop-master/solicitud/" + 1, false);
    //xhr.open('GET', "http://localhost/gabys_shop/solicitud", true);
    //xhr.open('PATCH',"http://localhost/Gaby's%20shop/solicitud/aprobado=0",false);
    xhr.open('PATCH',"http://localhost/Gaby's%20shop/solicitud/" + id_p, false);
    //xhr.open('PATCH',"http://localhost/gabys_shop-master/solicitud/aprobado=0",false);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);
    //console.log(id_p);
    //console.log(id_v);
    
    console.log("entre");
    console.log(xhr.status);
    var json = { 
        "id_admin": sesionJson.id_usuario,
        "aprobada": 1
    };

    var json_string = JSON.stringify(json);
    //console.log(json_string);
    xhr.send(json_string);

    //console.log(this.responseText);

    var data = JSON.parse(xhr.responseText);
    //console.log(data);
    //console.log(data);
    if (data.success === true){
        //localStorage.setItem('ltareas_sesion', JSON.stringify(data.data));
        //window.location.href = client;
        alert("Solicitud Aprobada");
        window.location.href = "SolicitudesVendedor.html";
    }
    else{
        alert(data.messages);
        //window.location.href = client;
    }
}


function cargaSolicitudes(e) {
    e.preventDefault();
    var sesion = localStorage.getItem('usuario_sesion');
    if(sesion === null){
        window.location.href = "http://localhost:80/Gaby's%20shop/index.html";
    }
    sesionJson = JSON.parse(sesion);

    const padre = document.getElementById('muestraSoli');

    const xhr = new XMLHttpRequest();
    //xhr.open('GET', "http://localhost/Gaby's%20shop/productos/aprobado=0", true);
    xhr.open('GET', "http://localhost/Gaby's%20shop/solicitud", true);
    xhr.setRequestHeader("Authorization", sesionJson.token_acceso);

    xhr.onload = function () {//Funcion que lee lo que hay en el JSON para llenar la lista
        if (this.status === 200) {
            //console.log(this.status);
            //console.log(this.responseText);
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                solicitudes = data.data.solicitudes;
                solicitudes.forEach(soli => {
                    var html = "";
                    html += `
                        <div class="m-1-top-bot flexarchivo p-1 b-line-b">
                        <div class="border ImagenProd col-m-3 col-s-6 fondoblanco">
                        <a href="./archivos/${soli.solicitudRuta}">Ver solicitud</a>
                        </div>
                        <div class="DetallesProd col-m-9 p-l-1 col-s-4">

                            <button class="teal p-1 textwhite" onclick="guardaCambios('${soli.id_solicitud}')">
                                Aprobar
                            </button>
                        </div> 
                    </div> 
                    `;
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