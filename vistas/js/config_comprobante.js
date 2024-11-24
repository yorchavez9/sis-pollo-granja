$(document).ready(function () {

    /* ===========================================
    GUARDAR CONFIGURACION TICKET
    =========================================== */
    $("#btn_guardar_serie_num").click(function (e) {
        e.preventDefault();
        let tipo_comprobante = $("#tipo_comprobante").val();
        let serie_prefijo = $("#serie_prefijo").val();
        let folio_inicial = $("#folio_inicial").val();
        let folio_final = $("#folio_final").val();

        var datos = new FormData();
        datos.append("tipo_comprobante", tipo_comprobante);
        datos.append("serie_prefijo", serie_prefijo);
        datos.append("folio_inicial", folio_inicial);
        datos.append("folio_final", folio_final);
       
        $.ajax({
            url: "ajax/Configuracion.num.serie.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {


                let res = JSON.parse(respuesta);

                if (res.estado === "ok") {

                    $("#form_nuevo_configuracion_ticket")[0].reset();

                    $("#vista_previa_logo_ticket").attr("src", "");

                    $("#modalNuevoConfiguracionTicket").modal("hide");

                    Swal.fire({
                        title: "¡Correcto!",
                        text: res.mensaje,
                        icon: "success",
                    });

                    mostrarConfiguracionTicket();

                } else {
                    Swal.fire({
                        title: "¡Error!",
                        text: res.mensaje,
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr);
                console.error(status);
                console.error(error);
            },
        });




    });

    /*=============================================
    MOSTRANDO CONFIGURACION TICKET
    =============================================*/
    function mostrarConfiguracionTicket() {
        $.ajax({
            url: "ajax/Configuracion.num.serie.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (tickets) {



                var tbody = $("#data_configuracion_ticket");

                tbody.empty();

                tickets.forEach(function (ticket, index) {

                    if (ticket.logo != null) {
                        ticket.logo = ticket.logo.substring(3);
                    }

                    var fila = `
                          <tr>
                              <td>${index + 1}</td>

                              <td>${ticket.nombre_empresa}</td>

                              <td>${ticket.ruc}</td>
                              <td>${ticket.telefono}</td>

                              <td>${ticket.correo}</td>

                              <td>${ticket.direccion}</td>

                              <td class="text-center">

                                  <a href="javascript:void(0);" class="product-img">
                                      <img src="${ticket.logo}" alt="${ticket.nombre_empresa}">
                                  </a>

                              </td>

                              <td>${ticket.mensaje}</td>

                              <td>${ticket.fecha_config_ticket}</td>

                              <td class="text-center">

                                  <a href="#" class="me-3 btnEditarTicket" idTicket="${ticket.id_config_ticket}" data-bs-toggle="modal" data-bs-target="#modalEditarConfiguracionTicket">
                                      <i class="text-warning fas fa-edit fa-lg"></i>
                                  </a>

                                  <a href="#" class="me-3 btn_descargar_ticket_prueba" idTicket="${ticket.id_config_ticket}" data-bs-toggle="modal">
                                      <i class="fa fa-download fa-lg" style="color: #28C76F"></i>
                                  </a>

                                  <a href="#" class="me-3 confirm-text btnEliminarTicket" idTicket="${ticket.id_config_ticket}" imagenTicket="${ticket.logo}">
                                      <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
                                  </a>

                              </td>

                          </tr>`;


                    // Agregar la fila al tbody
                    tbody.append(fila);
                });
                // Inicializar DataTables después de cargar los datos
                $('#tabla_configuracion_ticket').DataTable();
            },
            error: function (xhr, status, error) {

                console.error("Error al recuperar los usuarios:", error.mensaje);

            },

        });

    }


    /*=============================================
    EDITAR CONFIGURACION DEL TICKET
    =============================================*/
    $("#tabla_configuracion_ticket").on("click", ".btnEditarTicket", function () {

        let idTicket = $(this).attr("idTicket");


        let datos = new FormData();

        datos.append("idTicket", idTicket);

        $.ajax({
            url: "ajax/Configuracion.num.serie.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {


                $("#edit_id_config_ticket").val(respuesta["id_config_ticket"]);
                $("#edit_nombre_empresa_ticket").val(respuesta["nombre_empresa"]);
                $("#edit_ruc_ticket").val(respuesta["ruc"]);
                $("#edit_telefono_ticket").val(respuesta["telefono"]);
                $("#edit_correo_ticket").val(respuesta["correo"]);
                $("#edit_direccion_ticket").val(respuesta["direccion"]);
                $("#edit_foto_actual_ticket").val(respuesta["logo"]);
                $("#edit_mensaje_ticket").val(respuesta["mensaje"]);


                var imagenTicket = respuesta["logo"].substring(3);

                if (respuesta["logo"] != "") {
                    $("#edit_vista_previa_logo_ticket").attr("src", imagenTicket);
                } else {
                    $("#edit_vista_previa_logo_ticket").attr("src", "vistas/img/usuarios/default/anonymous.png"
                    );
                }


            },
        });
    });

    /*===========================================
    ACTUALIZAR EL PRODUCTO
    =========================================== */
    $("#btn_update_configuracion_ticket").click(function (e) {

        e.preventDefault();

        let edit_id_config_ticket = $("#edit_id_config_ticket").val();
        let edit_nombre_empresa_ticket = $("#edit_nombre_empresa_ticket").val();
        let edit_ruc_ticket = $("#edit_ruc_ticket").val();
        let edit_telefono_ticket = $("#edit_telefono_ticket").val();
        let edit_correo_ticket = $("#edit_correo_ticket").val();
        let edit_direccion_ticket = $("#edit_direccion_ticket").val();
        let edit_logo_ticket = $("#edit_logo_ticket").get(0).files[0];
        let edit_foto_actual_ticket = $("#edit_foto_actual_ticket").val();
        let edit_mensaje_ticket = $("#edit_mensaje_ticket").val();


        var datos = new FormData();
        datos.append("edit_id_config_ticket", edit_id_config_ticket);
        datos.append("edit_nombre_empresa_ticket", edit_nombre_empresa_ticket);
        datos.append("edit_ruc_ticket", edit_ruc_ticket);
        datos.append("edit_telefono_ticket", edit_telefono_ticket);
        datos.append("edit_correo_ticket", edit_correo_ticket);
        datos.append("edit_direccion_ticket", edit_direccion_ticket);
        datos.append("edit_logo_ticket", edit_logo_ticket);
        datos.append("edit_foto_actual_ticket", edit_foto_actual_ticket);
        datos.append("edit_mensaje_ticket", edit_mensaje_ticket);

        $.ajax({
            url: "ajax/Configuracion.num.serie.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {

                var res = JSON.parse(respuesta);

                if (res === "ok") {

                    $("#form_edit_configuracion_ticket")[0].reset();

                    $("#edit_vista_previa_logo_ticket").attr("src", "");

                    $("#modalEditarConfiguracionTicket").modal("hide");

                    Swal.fire({
                        title: "¡Correcto!",
                        text: "La configuración ha sido actualizado con éxito",
                        icon: "success",
                    });

                    mostrarConfiguracionTicket();

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
    ELIMINAR CONFIGURACION DEL TICKET
    =============================================*/
    $("#tabla_configuracion_ticket").on("click", ".btnEliminarTicket", function (e) {

        e.preventDefault();


        let idTicketDelete = $(this).attr("idTicket");
        let imagenTicketDelete = $(this).attr("imagenTicket");
        let rutaDeleteImagen = "../" + imagenTicketDelete;


        var datos = new FormData();
        datos.append("idTicketDelete", idTicketDelete);
        datos.append("rutaDeleteImagen", rutaDeleteImagen);

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
                    url: "ajax/Configuracion.num.serie.ajax.php",
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

                            mostrarConfiguracionTicket();

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

    mostrarConfiguracionTicket();


    /*=============================================
    DESCARGAR REPORTE DE PROVEEDORES
    =============================================*/

    $("#tabla_configuracion_ticket").on("click", ".btn_descargar_ticket_prueba", function (e) {

        e.preventDefault();


        var idTicket = $(this).attr("idTicket");

        window.open("extensiones/ticket/ticketPrueba.php?idTicket=" + idTicket, "_blank");

    });



});
