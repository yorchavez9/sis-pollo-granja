$(document).ready(function () {

    /* ===========================================
    GUARDAR CONFIGURACION CORREO
    =========================================== */
    $("#btn_guardar_correo_config").click(function (e) {

        e.preventDefault();
        var isValid = true;

        let id_usuario_config_correo = $("#id_usuario_config_correo").val();
        let smtp_correo = $("#smtp_correo").val();
        let usuario_correo_config = $("#usuario_correo_config").val();
        let password_correo_config = $("#password_correo_config").val();
        let puerto_correo = $("#puerto_correo").val();
        let correo_remitente = $("#correo_remitente").val();
        let nombre_remitente = $("#nombre_remitente").val();

        // Validar el campo SMTP
        if (smtp_correo === "") {
            $("#error_smtp_correo").text("El servidor SMTP es obligatorio.").css("color", "red");
            isValid = false;
        } else {
            $("#error_smtp_correo").text("");
        }

        // Validar el campo Usuario SMTP
        if (usuario_correo_config === "") {
            $("#error_usuario_correo_config").text("El usuario SMTP es obligatorio.").css("color", "red");
            isValid = false;
        } else {
            $("#error_usuario_correo_config").text("");
        }

        // Validar el campo Contraseña SMTP
        if (password_correo_config === "") {
            $("#error_password_correo_config").text("La contraseña SMTP es obligatoria.").css("color", "red");
            isValid = false;
        } else {
            $("#error_password_correo_config").text("");
        }

        // Validar el campo Puerto TCP
        if (puerto_correo === "") {
            $("#error_puerto_correo").text("El puerto TCP es obligatorio.").css("color", "red");
            isValid = false;
        } else {
            $("#error_puerto_correo").text("");
        }

        // Validar el campo Correo Remitente
        if (correo_remitente === "") {
            $("#error_correo_remitente").text("El correo del remitente es obligatorio.").css("color", "red");
            isValid = false;
        } else {
            $("#error_correo_remitente").text("");
        }

        // Validar el campo Nombre Remitente
        if (nombre_remitente === "") {
            $("#error_nombre_remitente").text("El nombre del remitente es obligatorio.").css("color", "red");
            isValid = false;
        } else {
            $("#error_nombre_remitente").text("");
        }

        if (isValid) {
            const datos = new FormData();
            datos.append("id_usuario", id_usuario_config_correo);
            datos.append("smtp", smtp_correo);
            datos.append("usuario", usuario_correo_config);
            datos.append("password", password_correo_config);
            datos.append("puerto", puerto_correo);
            datos.append("correo_remitente", correo_remitente);
            datos.append("nombre_remitente", nombre_remitente);

            $.ajax({
                url: "ajax/Correo.config.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    var res = JSON.parse(respuesta);
                    if (res.status === true) {
                        $("#form_nuevo_correo_config")[0].reset();
                        $("#modal_nuevo_configuracion_correo").modal("hide");
                        Swal.fire({
                            title: "¡Correcto!",
                            text: res.message,
                            icon: "success",
                        });
                        mostrarConfigCorreo();
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: res.message,
                            icon: "error",
                        });
                    }
                },
            });
        }
    });

    /* ===========================
    MOSTRANDO CONFIGURACION CORREO
    =========================== */
    function mostrarConfigCorreo() {
        $.ajax({
            url: "ajax/Correo.config.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (configuraciones) {
                let count = configuraciones.length;
                if (count > 0) {
                    $("#btn_agregar_configuracion_correo").hide();
                } else {
                    $("#btn_agregar_configuracion_correo").show();
                }

                let tbody = $("#data_correo_config");
                tbody.empty();
                configuraciones.forEach(function (config, index) {
                    var fila = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${config.smtp}</td>
                                    <td>${config.usuario}</td>
                                    <td>${config.password}</td>
                                    <td>${config.puerto}</td>
                                    <td>${config.correo_remitente}</td>
                                    <td>${config.nombre_remitente}</td>
                                    <td class="text-center">
                                        <a href="#" class="me-3 btnEditarCorreoConfig" idCorreoConfig="${config.id}" data-bs-toggle="modal" data-bs-target="#modal_editar_correo_config">
                                            <i class="text-warning fas fa-edit fa-lg"></i>
                                        </a>
                                        <a href="#" class="me-3 confirm-text btnEliminarCorreoConfig" idCorreoConfig="${config.id}">
                                            <i class="text-danger fa fa-trash fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                            `;
                    tbody.append(fila);
                });

                // Inicializar DataTables después de cargar los datos
                $('#tabla_correo_config').DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los proveedores:", error);
            },
        });
    }

    /*=============================================
    EDITAR LA CONFIGURACION DEL CORREO
    =============================================*/
    $("#tabla_correo_config").on("click", ".btnEditarCorreoConfig", function (e) {
        e.preventDefault();
        var idCorreoConfig = $(this).attr("idCorreoConfig");
        var datos = new FormData();
        datos.append("idCorreoConfig", idCorreoConfig);

        $.ajax({
            url: "ajax/Correo.config.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                $("#id_config_correo_edit").val(respuesta["id"]);
                $("#id_usuario_config_correo_edit").val(respuesta["id_usuario"]);
                $("#smtp_correo_edit").val(respuesta["smtp"]);
                $("#usuario_correo_config_edit").val(respuesta["usuario"]);
                $("#password_correo_config_edit").val(respuesta["password"]);
                $("#puerto_correo_edit").val(respuesta["puerto"]);
                $("#correo_remitente_edit").val(respuesta["correo_remitente"]);
                $("#nombre_remitente_edit").val(respuesta["nombre_remitente"]);
            },
        });
    });

    /*===========================================
    ACTUALIZAR LA CONFIGURACION DEL CORREO
    =========================================== */
    $("#btn_update_correo_config").click(function (e) {
        e.preventDefault();
        let isValid = true;
        let edit_id_correo_config = $("#id_config_correo_edit").val();
        let id_usuario_edit = $("#id_usuario_config_correo_edit").val();
        let smtp_edit = $("#smtp_correo_edit").val();
        let usuario_edit = $("#usuario_correo_config_edit").val();
        let password_edit = $("#password_correo_config_edit").val();
        let puerto_edit = $("#puerto_correo_edit").val();
        let correo_remitente_edit = $("#correo_remitente_edit").val();
        let nombre_remitente_edit = $("#nombre_remitente_edit").val();
        
        // Validar cada campo
        if (smtp_edit === "") {
            $("#error_smtp_correo_edit").text("El servidor SMTP es obligatorio.");
            isValid = false;
        } else {
            $("#error_smtp_correo_edit").text("");
        }

        if (usuario_edit === "") {
            $("#error_usuario_correo_config_edit").text("El usuario SMTP es obligatorio.");
            isValid = false;
        } else {
            $("#error_usuario_correo_config_edit").text("");
        }

        if (password_edit === "") {
            $("#error_password_correo_config_edit").text("La contraseña SMTP es obligatoria.");
            isValid = false;
        } else {
            $("#error_password_correo_config_edit").text("");
        }

        if (puerto_edit === "") {
            $("#error_puerto_correo_edit").text("El puerto TCP es obligatorio.");
            isValid = false;
        } else {
            $("#error_puerto_correo_edit").text("");
        }

        if (correo_remitente_edit === "") {
            $("#error_correo_remitente_edit").text("El correo del remitente es obligatorio.");
            isValid = false;
        } else {
            $("#error_correo_remitente_edit").text("");
        }

        if (nombre_remitente_edit === "") {
            $("#error_nombre_remitente_edit").text("El nombre del remitente es obligatorio.");
            isValid = false;
        } else {
            $("#error_nombre_remitente_edit").text("");
        }

        if (isValid) {
            const datos = new FormData();
            datos.append("edit_id_correo_config", edit_id_correo_config);
            datos.append("id_usuario_edit", id_usuario_edit);
            datos.append("smtp_edit", smtp_edit);
            datos.append("usuario_edit", usuario_edit);
            datos.append("password_edit", password_edit);
            datos.append("puerto_edit", puerto_edit);
            datos.append("correo_remitente_edit", correo_remitente_edit);
            datos.append("nombre_remitente_edit", nombre_remitente_edit);

            $.ajax({
                url: "ajax/Correo.config.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    var res = JSON.parse(respuesta);

                    if (res.status === true) {
                        $("#form_udpate_correo_config")[0].reset();
                        $("#modal_editar_correo_config").modal("hide");

                        Swal.fire({
                            title: "¡Correcto!",
                            text: res.message,
                            icon: "success",
                        });
                        mostrarConfigCorreo();
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: res.message,
                            icon: "error",
                        });
                    }
                }
            });
        }
    });

    /*=============================================
      ELIMINAR LA CONFIGURACION DEL CORREO
      =============================================*/
    $("#tabla_correo_config").on("click", ".btnEliminarCorreoConfig", function (e) {
        e.preventDefault();
        let idCorreoConfigDelete = $(this).attr("idCorreoConfig");
        const datos = new FormData();
        datos.append("idCorreoConfigDelete", idCorreoConfigDelete);
        Swal.fire({
            title: "¿Está seguro de borrar la configuración?",
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
                    url: "ajax/Correo.config.ajax.php",
                    method: "POST",
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (respuesta) {
                        var res = JSON.parse(respuesta);
                        if (res.status === true) {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text: res.message,
                                icon: "success",
                            });
                            mostrarConfigCorreo();
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: res.message,
                                icon: "error",
                            });
                        }
                    }
                });
            }
        });
    });

    /* =====================================
    MOSTRANDO CONFIGURACION
    ===================================== */
    mostrarConfigCorreo();

});
