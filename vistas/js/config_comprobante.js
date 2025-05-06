$(document).ready(function () {

    /* ===========================================
    GUARDAR CONFIGURACION TICKET
    =========================================== */
    $("#btn_guardar_serie_num").click(function (e) {
        e.preventDefault();

        // Obtener los valores de los campos
        let tipo_comprobante = $("#tipo_comprobante").val();
        let serie_prefijo = $("#serie_prefijo").val();
        let folio_inicial = $("#folio_inicial").val();
        let folio_final = $("#folio_final").val();

        // Limpiar mensajes de error anteriores
        $("#error_serie_prefijo").text("");
        $("#error_folio_inicial").text("");
        $("#error_folio_final").text("");

        let valid = true;

        // Validaciones
        if (tipo_comprobante === "" || tipo_comprobante == null) {
            $("#error_tipo_comprobante").text("El prefijo de la serie es obligatorio.");
            valid = false;
            valid = false;
        }

        if (serie_prefijo === "") {
            $("#error_serie_prefijo").text("El prefijo de la serie es obligatorio.");
            valid = false;
        }

        if (folio_inicial === "" || isNaN(folio_inicial) || parseInt(folio_inicial) <= 0) {
            $("#error_folio_inicial").text("El folio inicial debe ser un número mayor a 0.");
            valid = false;
        }

        if (folio_final === "" || isNaN(folio_final) || parseInt(folio_final) <= 0) {
            $("#error_folio_final").text("El folio final debe ser un número mayor a 0.");
            valid = false;
        }

        // Comprobar que el folio final sea mayor o igual al folio inicial
        if (parseInt(folio_final) < parseInt(folio_inicial)) {
            $("#error_folio_final").text("El folio final no puede ser menor que el folio inicial.");
            valid = false;
        }

        if (!valid) {
            return; // Detener el flujo si hay errores
        }

        // Si todo es válido, proceder con el envío
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
                if (res === "ok") {
                    $("#form_nuevo_serie_num")[0].reset();
                    $("#modal_config_serie_numero").modal("hide");

                    Swal.fire({
                        title: "¡Correcto!",
                        text: res.mensaje,
                        icon: "success",
                    });

                    mostrarConfiguracionSerieNum();
                } else if (res == "error_tipo_comprobante_existente") {
                    Swal.fire({
                        title: "Aviso!",
                        text: "El tipo de comprobante ya existe.",
                        icon: "warning",
                    });
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
    function mostrarConfiguracionSerieNum() {
        $.ajax({
            url: "ajax/Configuracion.num.serie.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (respuestas) {
                let tbody = $("#data_serie_num");
                tbody.empty();
                respuestas.forEach(function (value, index) {
                    var fila = `
                          <tr>
                              <td>${index + 1}</td>
                              <td>${value.tipo_comprobante_sn.toUpperCase()}</td>
                              <td>${value.serie_prefijo}</td>
                              <td>${value.folio_inicial}</td>
                              <td>${value.folio_final}</td>
                              <td>${value.fecha_sn}</td>
                              <td class="text-center">
                                  <a href="#" class="me-3 btnEditarSerieNumero" idSerieNumero="${value.id_serie_num}" data-bs-toggle="modal" data-bs-target="#modal_editar_serie_numero">
                                      <i class="text-warning fas fa-edit fa-lg"></i>
                                  </a>
                                  <a href="#" class="me-3 confirm-text btnEliminarSerieNumero" idSerieNumero="${value.id_serie_num}">
                                      <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
                                  </a>
                              </td>

                          </tr>`;


                    // Agregar la fila al tbody
                    tbody.append(fila);
                });
                // Inicializar DataTables después de cargar los datos
                $('#tabla_serie_num').DataTable();
            },
            error: function (xhr, status, error) {

                console.error("Error al recuperar los usuarios:", error.mensaje);

            },

        });

    }

    /*=============================================
    EDITAR CONFIGURACION DEL TICKET
    =============================================*/
    $("#tabla_serie_num").on("click", ".btnEditarSerieNumero", function (e) {
        e.preventDefault();
        let idSerieNumero = $(this).attr("idSerieNumero");
        let datos = new FormData();
        datos.append("idSerieNumero", idSerieNumero);
        $.ajax({
            url: "ajax/Configuracion.num.serie.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                // Asignar valores a los campos de formulario
                $("#edit_id_serie_num").val(respuesta["id_serie_num"]);
                $("#edit_serie_prefijo").val(respuesta["serie_prefijo"]);
                $("#edit_folio_inicial").val(respuesta["folio_inicial"]);
                $("#edit_folio_final").val(respuesta["folio_final"]);
                $("#edit_tipo_comprobante").val(respuesta["tipo_comprobante_sn"]).trigger("change");
            },

        });
    });

    /*===========================================
    ACTUALIZAR EL PRODUCTO
    =========================================== */
    $("#btn_update_serie_num").click(function (e) {
        e.preventDefault();
        let edit_id_serie_num = $("#edit_id_serie_num").val();
        let edit_tipo_comprobante = $("#edit_tipo_comprobante").val();
        let edit_serie_prefijo = $("#edit_serie_prefijo").val();
        let edit_folio_inicial = $("#edit_folio_inicial").val();
        let edit_folio_final = $("#edit_folio_final").val();

        var datos = new FormData();
        datos.append("edit_id_serie_num", edit_id_serie_num);
        datos.append("edit_tipo_comprobante", edit_tipo_comprobante);
        datos.append("edit_serie_prefijo", edit_serie_prefijo);
        datos.append("edit_folio_inicial", edit_folio_inicial);
        datos.append("edit_folio_final", edit_folio_final);
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
                    $("#form_update_serie_num")[0].reset();
                    $("#modal_editar_serie_numero").modal("hide");
                    Swal.fire({
                        title: "¡Correcto!",
                        text: "La configuración ha sido actualizado con éxito",
                        icon: "success",
                    });
                    mostrarConfiguracionSerieNum();
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
    $("#tabla_serie_num").on("click", ".btnEliminarSerieNumero", function (e) {
        e.preventDefault();
        let DeleteidSerieNumero = $(this).attr("idSerieNumero");
        const datos = new FormData();
        datos.append("DeleteidSerieNumero", DeleteidSerieNumero);
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
                        let res = JSON.parse(respuesta);
                        if (res === "ok") {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text: "La configuración ha sido eliminado",
                                icon: "success",
                            });
                            mostrarConfiguracionSerieNum();
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

    mostrarConfiguracionSerieNum();


});
