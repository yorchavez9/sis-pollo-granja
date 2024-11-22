$(document).ready(function () {
    /* ===========================================
    GUARDAR USUARIO ROLES
    =========================================== */
    $("#btn_guardar_usuario_roles").click(function (e) {
        e.preventDefault();

        // Validar selección del usuario
        var idUsuario = $("#id_usuario_roles").val();
        if (!idUsuario) {
            $("#error_usuario_roles").text("Debe seleccionar un usuario.");
            return;
        } else {
            $("#error_usuario_roles").text("");
        }

        // Capturar los roles seleccionados
        var rolesSeleccionados = [];
        $(".usuario_roles:checked").each(function () {
            rolesSeleccionados.push($(this).val());
        });

        if (rolesSeleccionados.length === 0) {
            Swal.fire({
                title: "¡Error!",
                text: "Debe seleccionar al menos un rol.",
                icon: "warning",
            });
            return;
        }

        // Crear objeto FormData y enviar datos
        var datos = new FormData();
        datos.append("id_usuario_roles", idUsuario);
        datos.append("usuario_roles", JSON.stringify(rolesSeleccionados)); // Enviar como JSON
  
        $.ajax({
            url: "ajax/UsuarioRoles.ajax.php", // Cambiar URL según corresponda
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                try {
                    var res = JSON.parse(respuesta);

                    if (res === "ok") {
                        // Reiniciar formulario y cerrar modal
                        $("#form_nuevo_usuario_roles")[0].reset();
                        $("#modal_nuevo_usuario_rol").modal("hide");

                        Swal.fire({
                            title: "¡Correcto!",
                            text: "Los roles han sido asignados al usuario.",
                            icon: "success",
                        });

                        // Actualizar la tabla o los datos
                        mostrarUsuariosConRoles(); // Función ficticia para recargar datos
                    } else {
                        Swal.fire({
                            title: "¡Error!",
                            text: "No se pudo guardar los datos.",
                            icon: "error",
                        });
                    }
                } catch (error) {
                    console.error("Error en la respuesta: ", error, respuesta);
                }
            },
        });
    });


    /* ===========================================
    MOSTRAR ROLES USUARIO
    =========================================== */
    function mostrarUsuariosConRoles() {
        $.ajax({
            url: "ajax/UsuarioRoles.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (usuario_roles) {
                var tbody = $("#data_usuario_roles");
                tbody.empty();

                // Agrupar roles por usuario
                var usuariosConRoles = {};

                usuario_roles.forEach(function (usuario_rol) {
                    if (!usuariosConRoles[usuario_rol.nombre_usuario]) {
                        usuariosConRoles[usuario_rol.nombre_usuario] = {
                            id_usuario: usuario_rol.id_usuario,
                            roles: []
                        };
                    }
                    usuariosConRoles[usuario_rol.nombre_usuario].roles.push(usuario_rol.nombre_rol);
                });

                // Generar las filas de la tabla
                var index = 1;
                for (var usuario in usuariosConRoles) {
                    var rolesHTML = usuariosConRoles[usuario].roles.map(function (rol) {
                        return `<small class="badge bg-secondary p-2 me-2">${rol}</small>`;
                    }).join(''); // Roles como elementos <small> separados por espacio

                    var fila = `
                                <tr>
                                    <td>${index++}</td>
                                    <td><strong>${usuario}</strong></td>
                                    <td>${rolesHTML}</td>
                                    <td class="text-center">
                                        <a href="#" class="me-3 btnEditarUsuarioRol" idUsuarioRol="${usuariosConRoles[usuario].id_usuario}" data-bs-toggle="modal" data-bs-target="#modal_editar_usuario_rol">
                                            <i class="text-warning fas fa-edit fa-lg"></i>
                                        </a>
                                        <a href="#" class="me-3 confirm-text btnEliminarUsuarioRol" idUsuarioRol="${usuariosConRoles[usuario].id_usuario}">
                                            <i class="text-danger fa fa-trash fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                            `;
                    tbody.append(fila);
                }

                // Inicializar DataTables después de cargar los datos
                $('#tabla_usuario_roles').DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los usuarios y roles:", error);
            },
        });
    }

    /*=============================================
    EDITAR USUARIO ROLES
    =============================================*/
    $("#tabla_usuario_roles").on("click", ".btnEditarUsuarioRol", function (e) {
        e.preventDefault();

        let idUsuarioRol = $(this).attr("idUsuarioRol");

        const datos = new FormData();
        datos.append("idUsuarioRol", idUsuarioRol);

        $.ajax({
            url: "ajax/UsuarioRoles.ajax.php", // Asegúrate de que esta URL sea correcta
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {

                // Verifica que la respuesta contiene los roles
                if (Array.isArray(respuesta)) {
                    // Extraemos solo los 'id_rol' de los roles del usuario
                    let rolesUsuario = respuesta.map(function (rol) {
                        return rol.id_rol; // Cambié a id_rol
                    });

                    // Llenar el nombre de usuario (si es necesario)
                    $("#edit_id_usuario_roles").val(respuesta[0].id_usuario);

                    // Marcar los checkboxes correspondientes
                    $(".edit_usuario_roles").each(function () {
                        const roleId = $(this).val(); // Obtenemos el valor del checkbox, que es el 'id_rol'
                        // Si el 'id_rol' está en el array de rolesUsuario, lo marcamos
                        if (rolesUsuario.includes(parseInt(roleId))) { // Asegúrate de que el valor sea un número
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                } else {
                    console.error('La respuesta no contiene los roles correctamente:', respuesta);
                }
            },
        });
    });

    /*===========================================
    ACTUALIZAR USUARIO ROLES
    =========================================== */
    $("#btn_update_usuario_roles").click(function (e) {
        e.preventDefault();

        var isValid = true;
        var idUsuario = $("#edit_id_usuario_roles").val();
        var rolesSeleccionados = [];

        $(".edit_usuario_roles:checked").each(function () {
            rolesSeleccionados.push($(this).val());
        });

        if (!idUsuario) {
            isValid = false;
            $("#error_usuario_roles").text("Por favor, selecciona un usuario.");
        } else if (rolesSeleccionados.length === 0) {
            isValid = false;
            $("#error_usuario_roles").text("Por favor, selecciona al menos un rol.");
        } else {
            $("#error_usuario_roles").text("");
        }

        if (isValid) {
            var datos = new FormData();
            datos.append("edit_id_usuario_roles", idUsuario);
            datos.append("edit_usuario_roles", JSON.stringify(rolesSeleccionados));
 
            $.ajax({
                url: "ajax/UsuarioRoles.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    console.log(respuesta);

                    var res = JSON.parse(respuesta);

                    if (res === "ok") {
                        $("#form_update_usuario_roles")[0].reset();
                        $("#modal_editar_usuario_rol").modal("hide");

                        Swal.fire({
                            title: "¡Correcto!",
                            text: "Los roles del usuario han sido actualizados.",
                            icon: "success",
                        });
                        mostrarUsuariosConRoles();
                    } else {
                        console.error("Error al actualizar los roles.");
                        Swal.fire({
                            title: "Error",
                            text: "Hubo un problema al actualizar los roles.",
                            icon: "error",
                        });
                    }
                },
                error: function () {
                    console.error("Error en la solicitud AJAX.");
                    Swal.fire({
                        title: "Error",
                        text: "Hubo un problema al procesar la solicitud.",
                        icon: "error",
                    });
                }
            });
        }
    });


    /*=============================================
      USUARIO ROLES
      =============================================*/
    $("#tabla_usuario_roles").on("click", ".btnEliminarUsuarioRol", function (e) {

        e.preventDefault();
        let deleteIdUsuarioRol = $(this).attr("idUsuarioRol");
        const datos = new FormData();
        datos.append("deleteIdUsuarioRol", deleteIdUsuarioRol);

        Swal.fire({
            title: "¿Está seguro de borrar?",
            text: "¡Si no lo está puede cancelar la accíón!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0084FF",
            cancelButtonColor: "#F1666D",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, borrar!",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "ajax/UsuarioRoles.ajax.php",
                    method: "POST",
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (respuesta) {
                        var res = JSON.parse(respuesta);

                        if (res === "ok") {

                            Swal.fire({
                                title: "¡Eliminado!",
                                text: "Datos eliminado con éxito",
                                icon: "success",
                            });

                            mostrarUsuariosConRoles();

                        } else {

                            console.error("Error al eliminar los datos");

                        }
                    }
                });

            }
        });
    }
    );

    /* =====================================
    MOSTRAR USUARIOS Y ROLES
    ===================================== */
    mostrarUsuariosConRoles();

});
