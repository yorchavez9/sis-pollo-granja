$(document).ready(function () {
    
    async function obtenerSesion() {
        try {
            const response = await fetch('ajax/sesion.ajax.php?action=sesion', {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });
            
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            return data.status === false ? null : data;
            
        } catch (error) {
            console.error('Error al obtener sesión:', error);
            return null;
        }
    }

    /* ===========================================
    GUARDAR SUCURSAL
    =========================================== */
    $("#btn_guardar_rol").click(function (e) {
        e.preventDefault();

        // Obtener valores de los campos
        const nombreRol = $("#nombre_rol").val().trim();
        const descripcionRol = $("#descripcion_rol").val().trim();
        let isValid = true;

        // Función para mostrar mensajes de error
        const mostrarError = (selector, mensaje) => {
            $(selector).html(mensaje).addClass("text-danger");
        };

        // Función para limpiar mensajes de error
        const limpiarError = (selector) => {
            $(selector).html("").removeClass("text-danger");
        };

        // Validar el nombre del rol
        if (!nombreRol) {
            mostrarError("#error_nombre_rol", "Por favor, ingrese el nombre");
            isValid = false;
        } else if (!/^[a-zA-ZñÑáéíóúÁÉÍÓÚ0-9\s,.-]+$/.test(nombreRol)) {
            mostrarError("#error_nombre_rol", "El nombre no puede contener caracteres no permitidos");
            isValid = false;
        } else {
            limpiarError("#error_nombre_rol");
        }

        // Validar la descripción del rol
        if (descripcionRol && !/^[a-zA-ZñÑáéíóúÁÉÍÓÚ0-9\s,.\-#]+$/.test(descripcionRol)) {
            mostrarError("#error_descripcion_rol", "La descripción contiene caracteres no permitidos");
            isValid = false;
        } else {
            limpiarError("#error_descripcion_rol");
        }


        // Si el formulario es válido, enviar los datos
        if (isValid) {
            const datos = new FormData();
            datos.append("nombre_rol", nombreRol);
            datos.append("descripcion_rol", descripcionRol);
            $.ajax({
                url: "ajax/Rol.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    try {
                        const res = JSON.parse(respuesta);
                        if (res === "ok") {
                            // Resetear el formulario y cerrar el modal
                            $("#form_nuevo_rol")[0].reset();
                            $("#modal_nuevo_rol").modal("hide");

                            // Mostrar alerta de éxito
                            Swal.fire({
                                title: "¡Correcto!",
                                text: "Los datos se guardaron con éxito",
                                icon: "success",
                            });

                            // Actualizar lista de roles
                            mostrarRoles();
                        } else {
                            console.error("Error al procesar la respuesta: ", res);
                        }
                    } catch (error) {
                        console.error("Error al parsear la respuesta: ", error);
                    }
                },
                error: function (error) {
                    console.error("Error en la solicitud AJAX: ", error);
                },
            });
        }
    });

    /* ===========================================
    MOSTRAR ROLES
    =========================================== */
    async function mostrarRoles() {
        let sesion = await obtenerSesion();
        if (sesion === null) {
            window.location.href = "inicio";
            return;
        }
        $.ajax({
            url: "ajax/Rol.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (roles) {
                var tbody = $("#data_rol");
                tbody.empty();
                roles.forEach(function (rol, index) {
                    var fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${rol.nombre_rol}</td>
                        <td>${rol.descripcion}</td>
                        <td class="text-center">
                            ${sesion.permisos.roles && sesion.permisos.roles.acciones.includes("editar")?
                                `<a href="#" class="me-3 btnEditarRol" idRol="${rol.id_rol}" data-bs-toggle="modal" data-bs-target="#modal_editar_rol">
                                    <i class="text-warning fas fa-edit fa-lg"></i>
                                </a>`:``}
                            
                            ${sesion.permisos.roles && sesion.permisos.roles.acciones.includes("eliminar")?
                                `<a href="#" class="me-3 confirm-text btnEliminarRol" idRol="${rol.id_rol}">
                                    <i class="text-danger fa fa-trash fa-lg"></i>
                                </a>`:``}
                            
                        </td>
                    </tr>
                `;
                    tbody.append(fila);
                });

                // Inicializar DataTables después de cargar los datos
                $('#tabla_rol').DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar roles:", error);
                console.log(xhr);
                console.log(status);
            },
        });
    }

    /* =====================================
    MOSTRANDO DATOS
    ===================================== */
    mostrarRoles();

    /*=============================================
    EDITAR EL SUCURSAL
    =============================================*/
    $("#tabla_rol").on("click", ".btnEditarRol", function () {
        let idRol = $(this).attr("idRol");
        let datos = new FormData();
        datos.append("idRol", idRol);
        $.ajax({
            url: "ajax/Rol.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                $("#edit_id_rol").val(respuesta["id_rol"]);
                $("#edit_nombre_rol").val(respuesta["nombre"]);
                $("#edit_descripcion_rol").val(respuesta["descripcion"]);

            },
        });
    });

    /*===========================================
    ACTUALIZAR CATEGORIA
    =========================================== */
    $("#btn_update_rol").click(function (e) {

        e.preventDefault();
        let isValid = true;
        let edit_id_rol = $("#edit_id_rol").val();
        let nombre_rol = $("#edit_nombre_rol").val();
        let descripcion_rol = $("#edit_descripcion_rol").val();

        const mostrarError = (selector, mensaje) => {
            $(selector).html(mensaje).addClass("text-danger");
        };

        // Función para limpiar mensajes de error
        const limpiarError = (selector) => {
            $(selector).html("").removeClass("text-danger");
        };

        // Validar el nombre del rol
        if (nombre_rol === "") {
            $("#error_edit_nombre_rol")
                .html("Por favor, ingrese el nombre")
                .addClass("text-danger");
            isValid = false;
        } else if (!/^[a-zA-ZñÑáéíóúÁÉÍÓÚ0-9\s,.-]+$/.test(nombre_rol)) { // Letras, "ñ", tildes, números y caracteres permitidos
            $("#error_edit_nombre_rol")
                .html("El nombre no puede contener caracteres no permitidos")
                .addClass("text-danger");
            isValid = false;
        } else {
            $("#error_edit_nombre_rol").html("").removeClass("text-danger");
        }

        // Validar la descripción del rol
        if (descripcion_rol && !/^[a-zA-ZñÑáéíóúÁÉÍÓÚ0-9\s,.\-#]+$/.test(descripcion_rol)) { // Letras, "ñ", tildes, números y caracteres permitidos
            mostrarError("#edit_descripcion_rol", "La descripción contiene caracteres no permitidos");
            isValid = false;
        } else {
            limpiarError("#edit_descripcion_rol");
        }


        if (isValid) {
            var datos = new FormData();
            datos.append("edit_id_rol", edit_id_rol);
            datos.append("edit_nombre_rol", nombre_rol);
            datos.append("edit_descripcion_rol", descripcion_rol);
            $.ajax({
                url: "ajax/Rol.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    console.log(respuesta);
                    let res = JSON.parse(respuesta);
                    if (res === "ok") {
                        $("#form_update_rol")[0].reset();
                        $("#modal_editar_rol").modal("hide");
                        Swal.fire({
                            title: "¡Correcto!",
                            text: "Datos actualizados con éxito",
                            icon: "success",
                        });
                        mostrarRoles();
                    } else {
                        console.error("Error al cargar los datos.");
                    }
                }
            });
        }
    });

    /*=============================================
      ELIMINAR SUCURSAL
      =============================================*/
    $("#tabla_rol").on("click", ".btnEliminarRol", function (e) {
        e.preventDefault();
        var delete_id_rol = $(this).attr("idRol");
        var datos = new FormData();
        datos.append("delete_id_rol", delete_id_rol);
        Swal.fire({
            title: "¿Está seguro de borrar el rol?",
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
                    url: "ajax/Rol.ajax.php",
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
                                text: "Se eliminó con éxito",
                                icon: "success",
                            });
                            mostrarRoles();
                        } else {
                            console.error("Error al eliminar los datos");
                        }
                    }
                });
            }
        });
    });


    /* ===========================================
    MOSTRAR ROLES REPORTE
    =========================================== */
    function mostrarRolesReporte() {
        $.ajax({
            url: "ajax/Rol.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (roles) {
                var tbody = $("#data_roles_reporte");
                tbody.empty();
                roles.forEach(function (rol, index) {
                    var fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${rol.nombre_rol}</td>
                        <td>${rol.descripcion}</td>
                    </tr>
                `;
                    tbody.append(fila);
                });

                // Inicializar DataTables después de cargar los datos
                $('#tabla_roles_reporte').DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los proveedores:", error);
            },
        });
    }

    /* =====================================
    MOSTRANDO ROLES REPORTE
    ===================================== */
    mostrarRolesReporte();


    /*=============================================
    DESCARGAR REPORTE
    =============================================*/
    $("#seccion_roles_reporte").on("click", ".reporte_roles_pdf", (e) => {
        e.preventDefault();
        const url = "extensiones/reportes/roles.php";
        window.open(url, "_blank");
    });


    /*=============================================
    IMPRIMIR REPORTE
    =============================================*/
    $("#seccion_roles_reporte").on("click", ".reporte_roles_printer", (e) => {
        e.preventDefault();
        const url = "extensiones/reportes/roles.php";
        const newWindow = window.open(url, "_blank");

        // Esperar a que se cargue la página antes de imprimir
        newWindow.onload = () => {
            newWindow.print();
        };
    });

});
