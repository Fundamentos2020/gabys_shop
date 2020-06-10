document.addEventListener('DOMContentLoaded', cargaCarrito);

function cargaCarrito(e){
    var sesion = localStorage.getItem('usuario_sesion');
    sesionJson = JSON.parse(sesion);
    id_usuario = sesionJson.id_usuario;
    let productos = obtieneCarrito();
    const padre = document.getElementById('verProd');

    productos.forEach(prod => {
        html = `
            ${prod.id_producto}`;
            padre.innerHTML += html;
    });

}


function obtieneCarrito(){
    let productos;
    //Checar valores de local storage
    if(localStorage.getItem('productoCarrito' + id_usuario) === null)
    {
        productos = [];
    }
    else
    {
        productos = JSON.parse(localStorage.getItem('productoCarrito' + id_usuario));
    }
    return productos;
}