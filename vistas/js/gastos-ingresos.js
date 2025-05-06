$(document).ready(function () {

  function formatCurrency(value) {
    if (!value) return "S/ 0.00";
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(value);
  }

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

  /* =====================================
    CONVERTIR DE DOLARES A 
    ===================================== */
  let currentRate = 0;

  async function getExchangeRate() {
    try {
      const response = await fetch(
        "https://api.exchangerate-api.com/v4/latest/PEN"
      );
      const data = await response.json();
      return data.rates.USD;
    } catch (error) {
      console.error("Error obteniendo tasas", error);
      try {
        const response = await fetch("https://open.er-api.com/v6/latest/PEN");
        const data = await response.json();
        return data.rates.USD;
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

  /* ===========================
      MOSTRANDO CONVERSOR MONEDA
      =========================== */
  function mostrarConversorMoneda() {
    $("#monto_ingreso_egreso").on("input", async function () {
      await updateRate();
      let monto = parseFloat($(this).val()) || 0;
      let precioBolivares =
        currentRate > 0 ? (monto * currentRate).toFixed(2) + " USD" : "N/A";
      $("#value_monto_ingreso_egrso").text(precioBolivares);
    });
  }
  mostrarConversorMoneda();

  function mostrarConversorMonedaEdit() {
    $("#edit_monto_ingreso_egreso").on("input", async function () {
      await updateRate();
      let monto = parseFloat($(this).val()) || 0;
      let precioBolivares =
        currentRate > 0 ? (monto * currentRate).toFixed(2) + " USD" : "N/A";
      $("#value_monto_ingreso_egreso_edit").text(precioBolivares);
    });
  }
  mostrarConversorMonedaEdit();

  /* ===========================
      MOSTRANDO ID DE LA CAJA
      =========================== */
  function mostrarIdMovimientoCaja() {
    $.ajax({
      url: "ajax/Caja.general.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {
        if (respuesta.length > 0) {
          let encontrado = false;
          respuesta.forEach(function (item) {
            if (item.estado === "abierto") {
              $("#id_movimiento_caja_ingreso_egreso").val(item.id_movimiento);
              $("#edit_id_movimiento_caja_ingreso_egreso").val(item.id_movimiento);
              encontrado = true;
            }
          });

          if (!encontrado) {
            $("#btn_mostrar_apertura_caja").prop("disabled", false).show();
          } else {
            $("#btn_mostrar_apertura_caja").prop("disabled", true).hide();
          }
        } else {
          $("#btn_mostrar_apertura_caja").prop("disabled", false).show();
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los proveedores:", error);
      },
    });
  }

  mostrarIdMovimientoCaja();

  /* ===========================================
      GUARDAR CATEGORIA
      =========================================== */
  $("#btn_guardar_ingreso_egreso").click(function (e) {
    e.preventDefault();
    let isValid = true;
    let id_usuario = $("#id_usuario_ingreso_egreso").val();
    let id_movimiento_caja = $("#id_movimiento_caja_ingreso_egreso").val();
    let tipo = $("#tipo_ingreso_egreso_caja").val();
    let concepto = $("#naturaleza_concepto_pago").val();
    let monto = $("#monto_ingreso_egreso").val();
    let detalles = $("#detalle_ingreso_egreso").val();

    // Validar si la caja está abierta
    if (!id_movimiento_caja || id_movimiento_caja === "") {
      Swal.fire({
        title: "¡Aviso!",
        text: "Por favor aperture la caja",
        icon: "warning",
      });
      isValid = false;
    }

    // Validar el tipo de movimiento
    if (!tipo || tipo === "") {
      $("#error_tipo_ingreso_egreso_caja").text("Este campo es obligatorio.");
      isValid = false;
    } else {
      $("#error_tipo_ingreso_egreso_caja").text(""); // Limpiar error
    }

    // Validar el concepto/naturaleza del pago
    if (!concepto || concepto === "") {
      $("#error_naturaleza_concepto_pago").text("Este campo es obligatorio.");
      isValid = false;
    } else {
      $("#error_naturaleza_concepto_pago").text(""); // Limpiar error
    }

    // Validar el monto
    if (!monto || monto <= 0) {
      $("#error_monto_ingreso_egreso").text("Por favor ingrese un monto válido.");
      isValid = false;
    } else {
      $("#error_monto_ingreso_egreso").text(""); // Limpiar error
    }


    // Si el formulario es válido, envíalo
    if (isValid) {
      const datos = new FormData();
      datos.append("id_usuario", id_usuario);
      datos.append("id_movimiento_caja", id_movimiento_caja);
      datos.append("tipo", tipo);
      datos.append("concepto", concepto);
      datos.append("monto", monto);
      datos.append("detalles", detalles);
      $.ajax({
        url: "ajax/Gastos.ingreso.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          let res = JSON.parse(respuesta);
          if (res.status === true) {
            $("#form_nuevo_ingreso_egreso")[0].reset();
            $("#modal_nuevo_ingreso_gatos").modal("hide");
            Swal.fire({
              title: "¡Correcto 😊!",
              text: res.message,
              icon: "success",
            });

            mostrarGatosIngresos();
            mostrarIdMovimientoCaja();
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
      MOSTRANDO INGRSOS O EGRESOS
      =========================== */
  async function mostrarGatosIngresos() {
    let sesion = await obtenerSesion();
    if(!sesion) return;
    await updateRate();
    $.ajax({
      url: "ajax/Gastos.ingreso.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {
        var tbody = $("#data_ingresos_egresos");
        tbody.empty();
        respuesta.forEach(function (data, index) {
          var precioBolivares = currentRate > 0 ? (data.monto * currentRate).toFixed(2) : "N/A";
          var fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${data.fecha}</td>
                        <td>
                          ${data.tipo === 'ingreso'
              ? `<span class="p-1 rounded" style="background: #28C76F; color: white">${data.tipo}</span>`
              : `<span class="p-1 rounded" style="background: #F5215C; color: white">${data.tipo}</span>`}
                        </td>
                        <td>${data.concepto}</td>
                        <td>
                            <div>S/ ${data.monto}</div>
                            <div>USD ${precioBolivares}</div>
                        </td>
                        <td>${data.detalles}</td>
                        <td class="text-center">
                            ${sesion.permisos.gastos_ingresos && sesion.permisos.gastos_ingresos.acciones.includes("editar")?
                              `<a href="#" class="me-3 btnEditarGastosIngresos" idGatosIngreso="${data.id_gasto}" montoIngresoGasto="${data.monto}" data-bs-toggle="modal" data-bs-target="#modal_editar_gatos_ingresos_caja">
                                <i class="text-warning fas fa-edit fa-lg"></i>
                            </a>`:``}
                            
                            ${sesion.permisos.gastos_ingresos && sesion.permisos.gastos_ingresos.acciones.includes("eliminar")?
                              `<a href="#" class="me-3 confirm-text btnEliminarGastosIngresos" 
                            idGatosIngreso="${data.id_gasto}" 
                            montoIngresoGasto="${data.monto}" 
                            tipoMovimiento="${data.tipo}" 
                            IdmovimientoCaja="${data.id_movimiento_caja}" ">
                                <i class="text-danger fa fa-trash fa-lg"></i>
                            </a>`:``}
                            
                        </td>
                    </tr>
                `;
          tbody.append(fila);
        });
        $("#tabla_ingresos_egresos").DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los gastos he ingreso:", error);
      },
    });
  }

  /*=============================================
    EDITAR EL GATOS O INGRESOS
    =============================================*/

  $("#tabla_ingresos_egresos").on("click", ".btnEditarGastosIngresos", function () {
    let idGatosIngreso = $(this).attr("idGatosIngreso");
    const datos = new FormData();
    datos.append("idGatosIngreso", idGatosIngreso);
    $.ajax({
      url: "ajax/Gastos.ingreso.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: async function (respuesta) {
        await updateRate();
        var precioBolivares = currentRate > 0 ? (respuesta["monto"] * currentRate).toFixed(2) : "N/A";
        $("#edit_id_gatos_caja").val(respuesta["id_gasto"]);
        $("#edit_tipo_ingreso_egreso_caja").val(respuesta["tipo"]).trigger('change');
        $("#edit_naturaleza_concepto_pago").val(respuesta["concepto"]);
        $("#edit_monto_ingreso_egreso").val(respuesta["monto"]);
        $("#edit_monto_ingreso_egreso_actual").val(respuesta["monto"]);
        $("#value_monto_ingreso_egreso_edit").text(precioBolivares + ' USD');
        $("#edit_detalle_ingreso_egreso").val(respuesta["detalles"]);
      },
    });
  });

  /*===========================================
      ACTUALIZAR INGRESO O INGRESO
      =========================================== */
  $("#btn_update_ingreso_egreso").click(function (e) {
    e.preventDefault();

    let isValid = true;
    let monto_edit_final = 0;
    let accion_monto = '';
    let id_gasto_edit = $("#edit_id_gatos_caja").val();
    let id_movimiento_caja_ingreso_egreso = $("#edit_id_movimiento_caja_ingreso_egreso").val();
    let tipo_edit = $("#edit_tipo_ingreso_egreso_caja").val();
    let concepto_edit = $("#edit_naturaleza_concepto_pago").val();
    let monto_edit = $("#edit_monto_ingreso_egreso").val();
    let monto_edit_actual = $("#edit_monto_ingreso_egreso_actual").val();

    if (monto_edit > monto_edit_actual) {
      monto_edit_final = monto_edit - monto_edit_actual;
      accion_monto = 'suma';
    } else if (monto_edit < monto_edit_actual) {
      monto_edit_final = monto_edit_actual - monto_edit;
      accion_monto = 'resta';
    }

    let detalles_edit = $("#edit_detalle_ingreso_egreso").val();

    if (isValid) {
      const datos = new FormData();
      datos.append("id_gasto_edit", id_gasto_edit);
      datos.append("id_movimiento_caja_ingreso_egreso", id_movimiento_caja_ingreso_egreso);
      datos.append("tipo_edit", tipo_edit);
      datos.append("concepto_edit", concepto_edit);
      datos.append("monto_edit", monto_edit);
      datos.append("monto_edit_final", monto_edit_final);
      datos.append("detalles_edit", detalles_edit);
      datos.append("accion_monto", accion_monto);
      datos.forEach(element => {
        console.log(element);
      });
      $.ajax({
        url: "ajax/Gastos.ingreso.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          let res = JSON.parse(respuesta);
          if (res.status === true) {
            $("#form_update_ingreso_egreso")[0].reset();
            $("#modal_editar_gatos_ingresos_caja").modal("hide");
            Swal.fire({
              title: "¡Correcto!",
              text: res.message,
              icon: "success",
            });
            mostrarGatosIngresos();
            mostrarIdMovimientoCaja();
          } else {
            Swal.fire({
              title: "¡Error!",
              text: res.message,
              icon: "error",
            });
          }
        },
      });
    }
  });

  /*=============================================
        ELIMINAR GATOS O INGRESOS
    =============================================*/
  $("#tabla_ingresos_egresos").on("click", ".btnEliminarGastosIngresos", function (e) {
    e.preventDefault();

    let IdmovimientoCaja = $(this).attr("IdmovimientoCaja");
    let idGatosIngresoDelete = $(this).attr("idGatosIngreso");
    let montoIngresoGastoDelete = $(this).attr("montoIngresoGasto");
    let tipoMovimiento = $(this).attr("tipoMovimiento");

    const datos = new FormData();
    datos.append("IdmovimientoCaja", IdmovimientoCaja);
    datos.append("idGatosIngresoDelete", idGatosIngresoDelete);
    datos.append("montoIngresoGastoDelete", montoIngresoGastoDelete);
    datos.append("tipoMovimiento", tipoMovimiento);

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
          url: "ajax/Gastos.ingreso.ajax.php",
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
              mostrarGatosIngresos();
              mostrarIdMovimientoCaja();
            } else {
              console.error("Error al eliminar los datos");
            }
          },
        });
      }
    });
  });

  /* =====================================
      MOSTRANDO DATOS
      ===================================== */
  mostrarGatosIngresos();
});
