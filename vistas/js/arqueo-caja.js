$(document).ready(function () {

    /* =====================================
      CONVERTIR DE DOLARES A 
      ===================================== */
    let currentRate = 0;

    async function getExchangeRate() {
        try {
            const response = await fetch(
                "https://api.exchangerate-api.com/v4/latest/USD"
            );
            const data = await response.json();
            return data.rates.VES;
        } catch (error) {
            console.error("Error obteniendo tasas", error);
            try {
                const response = await fetch("https://open.er-api.com/v6/latest/USD");
                const data = await response.json();
                return data.rates.VES;
            } catch (error2) {
                console.log("Error en API de respaldo:", error2);
                return null;
            }
        }
    }

    async function updateRate() {
        try {
            const rate = await getExchangeRate();
            if (rate) {
                currentRate = rate;
            }
        } catch (error) { }
    }
    setInterval(updateRate, 60 * 60 * 1000);


    function calcularDiferencia() {
        const montoSistema = parseFloat($("#monto_sistema_arqueo_caja").val()) || 0;
        const montoFisico = parseFloat($("#monto_fisico_arqueo_caja").val()) || 0;
        const diferencia = parseFloat((montoSistema - montoFisico).toFixed(2));

        $("#monto_diferencia_arqueo_caja").val(diferencia);

        if (diferencia > 0) {
            $("#value_monto_diferencia_arqueo_caja").removeClass("text-success text-primary").addClass("text-danger");
        } else if (diferencia < 0) {
            $("#value_monto_diferencia_arqueo_caja").removeClass("text-danger text-primary").addClass("text-success");
        } else {
            $("#value_monto_diferencia_arqueo_caja").removeClass("text-danger text-success").addClass("text-success");
        }
    }
    function calcularDiferenciaEdit() {
        const montoSistema = parseFloat($("#edit_monto_sistema_arqueo_caja").val()) || 0;
        const montoFisico = parseFloat($("#edit_monto_fisico_arqueo_caja").val()) || 0;
        const diferencia = parseFloat((montoSistema - montoFisico).toFixed(2));

        $("#edit_monto_diferencia_arqueo_caja").val(diferencia);

        if (diferencia > 0) {
            $("#value_edit_monto_diferencia_arqueo_caja").removeClass("text-success text-primary").addClass("text-danger");
        } else if (diferencia < 0) {
            $("#value_edit_monto_diferencia_arqueo_caja").removeClass("text-danger text-primary").addClass("text-success");
        } else {
            $("#value_edit_monto_diferencia_arqueo_caja").removeClass("text-danger text-success").addClass("text-success");
        }
    }

    $("#monto_sistema_arqueo_caja, #monto_fisico_arqueo_caja").on("input", calcularDiferencia);
    $("#edit_monto_sistema_arqueo_caja, #edit_monto_fisico_arqueo_caja").on("input", calcularDiferenciaEdit);

    /* ===========================================
      MOSTRANDO LA FECHA AUTOMÁTICO
      =========================================== */
    function setFechaHoy() {
        const fechaHoy = new Date().toISOString().split('T')[0];
        $("#fecha_arqueo_caja").val(fechaHoy);
    }
    setFechaHoy();


    /* ===========================================
      MOSTRAR CAMBIO DE VALOR DE LA MONEDA
      =========================================== */
    function mostrarConversorMoneda() {
        $("#monto_ingreso_egreso, #monto_sistema_arqueo_caja, #monto_fisico_arqueo_caja, #monto_diferencia_arqueo_caja").on("input", async function () {
            await updateRate(); // Asegúrate de tener la tasa de conversión actualizada

            let montoSistema = parseFloat($("#monto_sistema_arqueo_caja").val()) || 0;
            let montoFisico = parseFloat($("#monto_fisico_arqueo_caja").val()) || 0;
            let montoDiferencia = parseFloat($("#monto_diferencia_arqueo_caja").val()) || 0;

            // Conversión a CAD
            let montoSistemaCAD = currentRate > 0 ? (montoSistema * currentRate).toFixed(2) + " VES" : "N/A";
            let montoFisicoCAD = currentRate > 0 ? (montoFisico * currentRate).toFixed(2) + " VES" : "N/A";
            let montoDiferenciaCAD = currentRate > 0 ? (montoDiferencia * currentRate).toFixed(2) + " VES" : "N/A";

            // Mostrar valores convertidos
            $("#value_monto_sistema_arqueo_caja").text(montoSistemaCAD);
            $("#value_monto_fisico_arqueo_caja").text(montoFisicoCAD);
            $("#value_monto_diferencia_arqueo_caja").text(montoDiferenciaCAD);
        });
    }
    function mostrarConversorMonedaEdit() {
        $("#edit_monto_sistema_arqueo_caja, #edit_monto_fisico_arqueo_caja, #edit_monto_diferencia_arqueo_caja").on("input", async function () {
            await updateRate(); // Asegúrate de tener la tasa de conversión actualizada

            let montoSistema = parseFloat($("#edit_monto_sistema_arqueo_caja").val()) || 0;
            let montoFisico = parseFloat($("#edit_monto_fisico_arqueo_caja").val()) || 0;
            let montoDiferencia = parseFloat($("#edit_monto_diferencia_arqueo_caja").val()) || 0;

            // Conversión a CAD
            let montoSistemaCAD = currentRate > 0 ? (montoSistema * currentRate).toFixed(2) + " VES" : "N/A";
            let montoFisicoCAD = currentRate > 0 ? (montoFisico * currentRate).toFixed(2) + " VES" : "N/A";
            let montoDiferenciaCAD = currentRate > 0 ? (montoDiferencia * currentRate).toFixed(2) + " VES" : "N/A";

            // Mostrar valores convertidos
            $("#value_monto_sistema_arqueo_caja_edit").text(montoSistemaCAD);
            $("#value_edit_monto_fisico_arqueo_caja").text(montoFisicoCAD);
            $("#value_edit_monto_diferencia_arqueo_caja").text(montoDiferenciaCAD);
        });
    }

    mostrarConversorMoneda();
    mostrarConversorMonedaEdit();

    /* ===========================
    MOSTRANDO ID DE LA CAJA
    =========================== */
    async function mostrandoMontoCajaActual() {
        await updateRate();
        $.ajax({
            url: "ajax/Caja.general.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (respuesta) {
                if (respuesta.length > 0) {
                    respuesta.forEach(function (item) {
                        if (item.estado === "abierto") {
                            let monto_final = parseFloat(item.monto_inicial) + parseFloat(item.ingresos) - parseFloat(item.egresos);
                            let montoSistemaVES = currentRate > 0 ? (monto_final * currentRate).toFixed(2) + " VES" : "N/A";
                            $("#monto_sistema_arqueo_caja").val(monto_final.toFixed(2));
                            $("#value_monto_sistema_arqueo_caja").text(montoSistemaVES);
                            $("#id_movimiento_arqueo_caja").val(item.id_movimiento);
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los proveedores:", error);
            },
        });
    }

    mostrandoMontoCajaActual();

    /* ===========================================
      GUARDAR ARQUEO CAJA
      =========================================== */
    $("#btn_guardar_arqueo_caja").click(function (e) {
        e.preventDefault();

        let isValid = true;
        let id_movimiento_caja = $("#id_movimiento_arqueo_caja").val();
        let id_usuario = $("#id_usuario_arqueo_caja").val();
        let fecha_arqueo = $("#fecha_arqueo_caja").val();
        let monto_sistema = $("#monto_sistema_arqueo_caja").val();
        let monto_fisico = $("#monto_fisico_arqueo_caja").val();
        let diferencia = $("#monto_diferencia_arqueo_caja").val();
        let observaciones = $("#observaciones_arqueo_caja").val();

        // Limpiar los errores previos
        $("#error_value_monto_sistema_arqueo_caja").text("");
        $("#error_value_monto_fisico_arqueo_caja").text("");


        if (monto_sistema <= 0) {
            isValid = false;
            $("#error_value_monto_sistema_arqueo_caja").text("El monto del sistema debe ser mayor a 0.");
        }

        if (monto_fisico <= 0) {
            isValid = false;
            $("#error_value_monto_fisico_arqueo_caja").text("El monto físico debe ser mayor a 0.");
        }

        if (isValid) {
            const datos = new FormData();

            datos.append("id_movimiento_caja", id_movimiento_caja);
            datos.append("id_usuario", id_usuario);
            datos.append("fecha_arqueo", fecha_arqueo);
            datos.append("monto_sistema", monto_sistema);
            datos.append("monto_fisico", monto_fisico);
            datos.append("diferencia", diferencia);
            datos.append("observaciones", observaciones);
            $.ajax({
                url: "ajax/Arqueo.caja.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    let res = JSON.parse(respuesta);
                    if (res.status === true) {
                        $("#form_nuevo_arqueo_caja")[0].reset();
                        $("#modal_nuevo_arqueo").modal("hide");
                        Swal.fire({
                            title: "¡Correcto!",
                            text: res.message,
                            icon: "success",
                        });

                        mostrarArqueoCaja();
                        mostrandoMontoCajaActual();
                        setFechaHoy();
                    } else {
                        Swal.fire({
                            title: "¡Aviso!",
                            text: res.message,
                            icon: "warning",
                        });
                    }
                },
            });
        }
    });

    /* ===========================
      MOSTRANDO  ARQUEO CAJA
      =========================== */
    function mostrarArqueoCaja() {
        $.ajax({
            url: "ajax/Arqueo.caja.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (respuestas) {
                let tbody = $("#data_arqueo_caja");
                tbody.empty();
                respuestas.forEach(function (data, index) {
                    let diferencia = parseFloat(data.monto_sistema) - parseFloat(data.monto_fisico);
                    var fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${data.fecha_arqueo}</td>
                            <td>USD ${data.monto_sistema}</td>
                            <td>USD ${data.monto_fisico}</td>
                            <td>USD ${diferencia}</td>
                            <td>${data.observaciones}</td>
                            <td class="text-center">
                                <a href="#" class="me-3 btnEditarArqueoCaja" idArqueoCaja="${data.id_arqueo}" data-bs-toggle="modal" data-bs-target="#modal_editar_arqueo_caja">
                                    <i class="text-warning fas fa-edit fa-lg"></i>
                                </a>
                                <a href="#" class="me-3 confirm-text btnEliminarArqueoCaja" idArqueoCaja="${data.id_arqueo}">
                                    <i class="text-danger fa fa-trash fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.append(fila);

                });

                $("#tabla_arqueo_caja").DataTable();
            },
            error: function (xhr, status, error) {
                console.error("Error al recuperar los proveedores:", error);
            },
        });
    }


    /*=============================================
      EDITAR EL  ARQUEO CAJA
      =============================================*/
    $("#tabla_arqueo_caja").on("click", ".btnEditarArqueoCaja", function () {
        var idArqueoCaja = $(this).attr("idArqueoCaja");
        var datos = new FormData();
        datos.append("idArqueoCaja", idArqueoCaja);
        $.ajax({
            url: "ajax/Arqueo.caja.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: async function (respuesta) {
                await updateRate();

                let fecha = new Date(respuesta["fecha_arqueo"]);
                let fechaFormateada = fecha.toISOString().split('T')[0];

                let monto_sistema_ves = currentRate > 0 ? (parseFloat(respuesta["monto_sistema"]) * currentRate).toFixed(2) + " VES" : "N/A";
                let monto_fisico_ves = currentRate > 0 ? (parseFloat(respuesta["monto_fisico"]) * currentRate).toFixed(2) + " VES" : "N/A";
                let monto_diferencia_ves = currentRate > 0 ? (parseFloat(respuesta["diferencia"]) * currentRate).toFixed(2) + " VES" : "N/A";

                $("#edit_id_arqueo_caja").val(respuesta["id_arqueo"]);
                $("#edit_id_movimiento_arqueo_caja").val(respuesta["id_movimiento_caja"]);
                $("#edit_id_usuario_arqueo_caja").val(respuesta["id_usuario"]);
                $("#edit_fecha_arqueo_caja").val(fechaFormateada);
                $("#edit_monto_sistema_arqueo_caja").val(respuesta["monto_sistema"]);
                $("#edit_monto_fisico_arqueo_caja").val(respuesta["monto_fisico"]);
                $("#edit_monto_diferencia_arqueo_caja").val(respuesta["diferencia"]);
                $("#edit_observaciones_arqueo_caja").val(respuesta["observaciones"]);


                $("#value_monto_sistema_arqueo_caja_edit").text(monto_sistema_ves);
                $("#value_edit_monto_fisico_arqueo_caja").text(monto_fisico_ves);
                $("#value_edit_monto_diferencia_arqueo_caja").text(monto_diferencia_ves);

            },
        });
    });

    /*===========================================
      ACTUALIZAR  ARQUEO CAJA
      =========================================== */
    $("#btn_update_arqueo_caja").click(function (e) {
        e.preventDefault();

        let isValid = true;

        let id_arqueo_edit = $("#edit_id_arqueo_caja").val();
        let id_movimiento_edit = $("#edit_id_movimiento_arqueo_caja").val();
        let id_usuario_edit = $("#edit_id_usuario_arqueo_caja").val();
        let fecha_arqueo_edit = $("#edit_fecha_arqueo_caja").val();
        let monto_sistema_edit = $("#edit_monto_sistema_arqueo_caja").val();
        let monto_fisico_edit = $("#edit_monto_fisico_arqueo_caja").val();
        let diferencia_edit = $("#edit_monto_diferencia_arqueo_caja").val();
        let observaciones_edit = $("#edit_observaciones_arqueo_caja").val();

        if (isValid) {
            var datos = new FormData();
            datos.append("id_arqueo_edit", id_arqueo_edit);
            datos.append("id_movimiento_edit", id_movimiento_edit);
            datos.append("id_usuario_edit", id_usuario_edit);
            datos.append("fecha_arqueo_edit", fecha_arqueo_edit);
            datos.append("monto_sistema_edit", monto_sistema_edit);
            datos.append("monto_fisico_edit", monto_fisico_edit);
            datos.append("diferencia_edit", diferencia_edit);
            datos.append("observaciones_edit", observaciones_edit);
            $.ajax({
                url: "ajax/Arqueo.caja.ajax.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    let res = JSON.parse(respuesta);
                    if (res.status === true) {
                        $("#form_editar_arqueo_caja")[0].reset();
                        $("#modal_editar_arqueo_caja").modal("hide");

                        Swal.fire({
                            title: "¡Correcto!",
                            text: res.message,
                            icon: "success",
                        });

                        mostrarArqueoCaja();
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

    /*=============================================
        ELIMINAR  ARQUEO CAJA
        =============================================*/
    $("#tabla_arqueo_caja").on("click", ".btnEliminarArqueoCaja", function (e) {
        e.preventDefault();
        let idArqueoCajaDelete = $(this).attr("idArqueoCaja");
        const datos = new FormData();
        datos.append("idArqueoCajaDelete", idArqueoCajaDelete);
        Swal.fire({
            title: "¿Está seguro de borrar el arqueo?",
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
                    url: "ajax/Arqueo.caja.ajax.php",
                    method: "POST",
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (respuesta) {
                        let res = JSON.parse(respuesta);
                        if (res.status === true) {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text: res.message,
                                icon: "success",
                            });

                            mostrarArqueoCaja();
                            mostrandoMontoCajaActual();
                            setFechaHoy();
                        } else {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text: res.message,
                                icon: "success",
                            });
                        }
                    },
                });
            }
        });
    });

    /* =====================================
      MOSTRANDO  ARQUEO CAJA
      ===================================== */
    mostrarArqueoCaja();
});
