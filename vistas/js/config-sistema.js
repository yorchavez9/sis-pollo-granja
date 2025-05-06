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
    GUARDAR CONFIGURACION value
    =========================================== */
    function validarCampos() {
        let campos = [
            { id: "#nombre_sis", tipo: "texto", mensaje: "El nombre del sistema es obligatorio." },
            { id: "#icon_pestana_sis", tipo: "archivo", mensaje: "El icono de pestaña es obligatorio." },
            { id: "#img_sidebar_sis", tipo: "archivo", mensaje: "La imagen del sidebar es obligatoria." },
            { id: "#img_sidebar_min", tipo: "archivo", mensaje: "La imagen mínima del sidebar es obligatoria." },
            { id: "#img_login", tipo: "archivo", mensaje: "La imagen del login es obligatoria." },
            { id: "#icon_login", tipo: "archivo", mensaje: "El icono del login es obligatorio." }
        ];

        for (let campo of campos) {
            let valor = campo.tipo === "archivo" ? $(campo.id).get(0).files[0] : $(campo.id).val().trim();
            if (!valor) {
                Swal.fire({
                    title: "¡Atención!",
                    text: campo.mensaje,
                    icon: "warning",
                });
                $(campo.id).focus();
                return false;
            }
        }
        return true;
    }

    $("#btn_guardar_configuracion_sistema").click(function (e) {
        e.preventDefault();

        if (!validarCampos()) {
            return;
        }

        // Recoger los valores de los campos
        let nombre_sis = $("#nombre_sis").val();
        let icon_pestana_sis = $("#icon_pestana_sis").get(0).files[0];
        let img_sidebar_sis = $("#img_sidebar_sis").get(0).files[0];
        let img_sidebar_min = $("#img_sidebar_min").get(0).files[0];
        let img_login = $("#img_login").get(0).files[0];
        let icon_login = $("#icon_login").get(0).files[0];

        // Crear un objeto FormData para enviar los datos
        var datos = new FormData();
        datos.append("nombre_sis", nombre_sis);
        datos.append("icon_pestana_sis", icon_pestana_sis);
        datos.append("img_sidebar_sis", img_sidebar_sis);
        datos.append("img_sidebar_min", img_sidebar_min);
        datos.append("img_login", img_login);
        datos.append("icon_login", icon_login);
        // Realizar la solicitud AJAX
        $.ajax({
            url: "ajax/configuracion.sistema.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
     
                let res = JSON.parse(respuesta);

                if (res.estado === "ok") {
                    // Resetear el formulario y ocultar el modal
                    $("#form_nuevo_configuracion_sistema")[0].reset();
                    $("#modal_nuevo_configuracion_sistema").modal("hide");

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: "¡Correcto!",
                        text: res.mensaje,
                        icon: "success",
                    });

                    mostrarConfiguracionSistema();
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        title: "¡Error!",
                        text: res.mensaje,
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                // Manejar errores de la solicitud
                console.error(xhr, status, error);
            }
        });
    });


    /*=============================================
    MOSTRANDO CONFIGURACION value
    =============================================*/
    async function mostrarConfiguracionSistema() {
        let sesion = await obtenerSesion();
        if(!sesion) return;
        $.ajax({
            url: "ajax/configuracion.sistema.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
                let count = response.length;
                if (count > 0) {
                    $("#btn_agregar_configuracion_sistema").hide();
                } else {
                    $("#btn_agregar_configuracion_sistema").show();
                }
                
                var tbody = $("#data_configuracion_sistema");
                tbody.empty();
                function ajustarRutaImagen(imagen) {
                    return imagen ? imagen.substring(3) : null;
                }
                response.forEach(function (value, index) {
                    value.icon_pestana = ajustarRutaImagen(value.icon_pestana);
                    value.img_sidebar = ajustarRutaImagen(value.img_sidebar);
                    value.img_sidebar_min = ajustarRutaImagen(value.img_sidebar_min);
                    value.img_login = ajustarRutaImagen(value.img_login);
                    value.icon_login = ajustarRutaImagen(value.icon_login);

                    var fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${value.nombre}</td>
                        <td class="text-center">
                            <a href="#">
                                <img src="${value.icon_pestana}" alt="${value.nombre}">
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="#">
                                <img src="${value.img_sidebar}" alt="${value.nombre}">
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="#">
                                <img src="${value.img_sidebar_min}" alt="${value.nombre}">
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="#">
                                <img src="${value.img_login}" alt="${value.nombre}">
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="#">
                                <img src="${value.icon_login}" alt="${value.nombre}">
                            </a>
                        </td>
                        <td>${value.fecha}</td>
                        <td class="text-center">
                            ${sesion.permisos.configuracion && sesion.permisos.configuracion.acciones.includes("editar")?
                                ` <a href="#" class="me-3 btnEditarImagenSistema" idSistema="${value.id_img}" data-bs-toggle="modal" data-bs-target="#modal_editar_configuracion_sistema">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``} 
                           
                            ${sesion.permisos.configuracion && sesion.permisos.configuracion.acciones.includes("eliminar")?
                                `<a href="#" class="me-3 confirm-text btnEliminarImagenSistema" 
                            idSistema="${value.id_img}" 
                            url_icon_pestana="${value.icon_pestana}"
                            url_img_sidebar="${value.img_sidebar}"
                            url_img_sidebar_min="${value.img_sidebar_min}"
                            url_img_login="${value.img_login}"
                            url_icon_login="${value.icon_login}">
                                <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
                            </a>`:``} 
                            
                        </td>
                    </tr>`;
                    tbody.append(fila);
                });

                $('#tabla_configuracion_sistema').DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los usuarios:", error);
            },
        });
    }



    /*=============================================
    EDITAR CONFIGURACION DEL value
    =============================================*/
    $("#tabla_configuracion_sistema").on("click", ".btnEditarImagenSistema", function () {
        let idSistema = $(this).attr("idSistema");
        let datos = new FormData();
        datos.append("idSistema", idSistema);
        $.ajax({
            url: "ajax/configuracion.sistema.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                // Asignar valores a los campos de texto
                $("#edit_id_configuracion_sistema").val(respuesta["id_img"]);
                $("#edit_nombre_sis").val(respuesta["nombre"]);

                $("#actual_icon_pestana_sis").val(respuesta["icon_pestana"]);
                $("#actual_img_sidebar_sis").val(respuesta["img_sidebar"]);
                $("#actual_img_sidebar_min").val(respuesta["img_sidebar_min"]);
                $("#actual_img_login").val(respuesta["img_login"]);
                $("#actual_icon_login").val(respuesta["icon_login"]);

                // Generar rutas relativas (eliminar los primeros 3 caracteres, si corresponde)
                var icon_pestana = respuesta["icon_pestana"].substring(3);
                var img_sidebar = respuesta["img_sidebar"].substring(3);
                var img_sidebar_min = respuesta["img_sidebar_min"].substring(3);
                var img_login = respuesta["img_login"].substring(3);
                var icon_login = respuesta["icon_login"].substring(3);

                // Mostrar vistas previas
                if (respuesta["icon_pestana"] != "") {
                    $("#edit_preview_icon_pestana").attr("src", icon_pestana).show();
                }
                if (respuesta["img_sidebar"] != "") {
                    $("#edit_preview_img_sidebar").attr("src", img_sidebar).show();
                }
                if (respuesta["img_sidebar_min"] != "") {
                    $("#edit_preview_img_sidebar_min").attr("src", img_sidebar_min).show();
                }
                if (respuesta["img_login"] != "") {
                    $("#edit_preview_img_login").attr("src", img_login).show();
                }
                if (respuesta["icon_login"] != "") {
                    $("#edit_preview_icon_login").attr("src", icon_login).show();
                }
            }

        });
    });

    /*===========================================
    ACTUALIZAR EL PRODUCTO
    =========================================== */
    $("#btn_update_configuracion_sistema").click(function (e) {
        e.preventDefault();
        let edit_id_configuracion_sistema = $("#edit_id_configuracion_sistema").val();
        let edit_nombre_sis = $("#edit_nombre_sis").val();

        let edit_icon_pestana_sis = $("#edit_icon_pestana_sis").get(0).files[0];
        let actual_icon_pestana_sis = $("#actual_icon_pestana_sis").val();

        let edit_img_sidebar_sis = $("#edit_img_sidebar_sis").get(0).files[0];
        let actual_img_sidebar_sis = $("#actual_img_sidebar_sis").val();

        let edit_img_sidebar_min = $("#edit_img_sidebar_min").get(0).files[0];
        let actual_img_sidebar_min = $("#actual_img_sidebar_min").val();

        let edit_img_login = $("#edit_img_login").get(0).files[0];
        let actual_img_login = $("#actual_img_login").val();

        let edit_icon_login = $("#edit_icon_login").get(0).files[0];
        let actual_icon_login = $("#actual_icon_login").val();


        var datos = new FormData();
        datos.append("edit_id_configuracion_sistema", edit_id_configuracion_sistema);
        datos.append("edit_nombre_sis", edit_nombre_sis);

        datos.append("edit_icon_pestana_sis", edit_icon_pestana_sis);
        datos.append("actual_icon_pestana_sis", actual_icon_pestana_sis);

        datos.append("edit_img_sidebar_sis", edit_img_sidebar_sis);
        datos.append("actual_img_sidebar_sis", actual_img_sidebar_sis);

        datos.append("edit_img_sidebar_min", edit_img_sidebar_min);
        datos.append("actual_img_sidebar_min", actual_img_sidebar_min);

        datos.append("edit_img_login", edit_img_login);
        datos.append("actual_img_login", actual_img_login);

        datos.append("edit_icon_login", edit_icon_login);
        datos.append("actual_icon_login", actual_icon_login);
    
        $.ajax({
            url: "ajax/configuracion.sistema.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                var res = JSON.parse(respuesta);
                if (res === "ok") {
                    $("#form_edit_configuracion_sistema")[0].reset();
                    $("img").attr("src", "");
                    $("#modal_editar_configuracion_sistema").modal("hide");
                    Swal.fire({
                        title: "¡Correcto!",
                        text: "La configuración ha sido actualizado con éxito",
                        icon: "success",
                    });
                    mostrarConfiguracionSistema();
                } else {
                    console.error("Error al actualizar los datos");
                }
            }, error: function (xhr, status, error) {
                console.error("Error al recuperar los usuarios:", error);
                console.error(xhr);
                console.error(status);
            },
        });
    });

    /*=============================================
    ELIMINAR CONFIGURACION DEL value
    =============================================*/
    $("#tabla_configuracion_sistema").on("click", ".btnEliminarImagenSistema", function (e) {
        e.preventDefault();
        let idSistemaDelete = $(this).attr("idSistema");

        let url_icon_pestana = $(this).attr("url_icon_pestana");
        let delete_url_icon_pestana = "../" + url_icon_pestana;

        let url_img_sidebar = $(this).attr("url_img_sidebar");
        let delete_url_img_sidebar = "../" + url_img_sidebar;

        let url_img_sidebar_min = $(this).attr("url_img_sidebar_min");
        let delete_url_img_sidebar_min = "../" + url_img_sidebar_min;

        let url_img_login = $(this).attr("url_img_login");
        let delete_url_img_login = "../" + url_img_login;

        let url_icon_login = $(this).attr("url_icon_login");
        let delete_url_icon_login = "../" + url_icon_login;

        var datos = new FormData();
        datos.append("idSistemaDelete", idSistemaDelete);
        datos.append("delete_url_icon_pestana", delete_url_icon_pestana);
        datos.append("delete_url_img_sidebar", delete_url_img_sidebar);
        datos.append("delete_url_img_sidebar_min", delete_url_img_sidebar_min);
        datos.append("delete_url_img_login", delete_url_img_login);
        datos.append("delete_url_icon_login", delete_url_icon_login);

        Swal.fire({
            title: "¿Está seguro de borrar la configuración?",
            text: "¡Si no lo está puede cancelar la accíón!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#FF4D4D",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, borrar!",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "ajax/configuracion.sistema.ajax.php",
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
                                text: "La configuración ha sido eliminado",
                                icon: "success",
                            });
                            mostrarConfiguracionSistema();
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
    MSOTRANDO DATOS
    ===================================== */

    mostrarConfiguracionSistema();

});
