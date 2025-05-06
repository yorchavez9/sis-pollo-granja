$(document).ready(function () {


    // Validar formulario antes de enviar
    $("#login_form").on("submit", function (e) {
        e.preventDefault();
        let usuario = $("[name='ingUsuario']").val();
        let password = $("[name='ingPassword']").val();

        if (usuario == "" || password == "") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Usuario y contraseña son obligatorios'
            });
            return false;
        }

        const datos = new FormData();
        datos.append('ingUsuario', usuario);
        datos.append('ingPassword', password);
        datos.append('action', "login");
        
        // Enviar formulario
        $.ajax({
            url: "ajax/login.ajax.php",
            type: "POST",
            data: datos,
            processData: false,  // Necesario para FormData
            contentType: false,  // Necesario para FormData
            dataType: "json",
            success: function (respuesta) {
   
                if (respuesta.status) {
                    window.location = respuesta.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: respuesta.message || 'Usuario o contraseña incorrectos'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                console.log(xhr);
                console.log(status);
            }
        });
    });
});