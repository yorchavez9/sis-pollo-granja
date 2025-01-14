$(document).ready(function () {

  /* ===========================================
    SELECIONADO LA FECHA AUTOMÁTICO
    =========================================== */
    // Obtener la fecha actual en formato YYYY-MM-DD
    const today = new Date().toISOString().split('T')[0];
    
    // Asignar la fecha actual a los campos de apertura y cierre
    $("#fecha_apertura_caja").val(today);
    $("#fecha_cierre_caja").val(today); // Puedes ajustar esto según tu lógica

  /* ===========================================
    GUARDAR CAJA GENRAL APERTURA
    =========================================== */
  $("#btn_guardar_apertura_caja").click(function (e) {
    e.preventDefault();
    let isValid = true;
    let id_usuario_caja = $("#id_usuario_caja").val();
    let monto_inicial_caja = $("#monto_inicial_caja").val();
    let fecha_apertura_caja = $("#fecha_apertura_caja").val();
    let fecha_cierre_caja = $("#fecha_cierre_caja").val();

    // Limpia los errores previos
    $("#error_monto_inicial_caja").text("");
    $("#fecha_apertura_caja").removeClass("is-invalid");
    $("#fecha_cierre_caja").removeClass("is-invalid");

    // Validación del monto inicial
    if (!monto_inicial_caja || parseFloat(monto_inicial_caja) <= 0) {
      $("#error_monto_inicial_caja").text(
        "El monto inicial debe ser un número positivo."
      );
      $("#monto_inicial_caja").addClass("is-invalid");
      isValid = false;
    }

    // Validación de fecha de apertura
    if (!fecha_apertura_caja) {
      $("#fecha_apertura_caja").addClass("is-invalid");
      isValid = false;
    }

    // Validación de fecha de cierre
    if (!fecha_cierre_caja) {
      $("#fecha_cierre_caja").addClass("is-invalid");
      isValid = false;
    }

    if (isValid) {
      var datos = new FormData();
      datos.append("id_usuario_caja", id_usuario_caja);
      datos.append("monto_inicial_caja", monto_inicial_caja);
      datos.append("fecha_apertura_caja", fecha_apertura_caja);
      datos.append("fecha_cierre_caja", fecha_cierre_caja);
      $.ajax({
        url: "ajax/Caja.general.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          var res = JSON.parse(respuesta);
          if (res.status === true) {
            $("#form_nuevo_apertura_caja")[0].reset();
            $("#modal_nuevo_apertura_caja").modal("hide");
            Swal.fire({
              title: "¡Correcto!",
              text: res.message,
              icon: "success",
            });
            mostrarCajaGeneral();
            mostrarCajaGeneralApertura();
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
    MOSTRANDO CAJA GENRAL APERTURA
    =========================== */
  function mostrarCajaGeneral() {
    $.ajax({
      url: "ajax/Caja.general.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {
        var tbody = $("#data_list_caja");
        tbody.empty();
        respuesta.forEach(function (item, index) {
          var fila = "";
          if (item.estado === "cerrado") {
            fila = `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${
                                  item.fecha_cierre
                                }</td>
                                <td class="text-center">USD ${
                                  item.ingresos
                                }</td>
                                <td class="text-center">USD ${item.egresos}</td>
                                <td class="text-center">USD ${
                                  item.monto_inicial
                                }</td>
                                <td class="text-center">USD ${
                                  item.monto_final
                                }</td>
                            </tr>
                        `;
          }
          tbody.append(fila);
        });

        // Inicializar DataTables después de cargar los datos
        $("#tabla_apertura_cierre_caja").DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los proveedores:", error);
      },
    });
  }

  /* ===========================
    MOSTRANDO CAJA GENRAL APERTURA
    =========================== */
  function mostrarCajaGeneralApertura() {
    $.ajax({
      url: "ajax/Caja.general.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {
        if (respuesta && respuesta.length > 0) {
          let datosCaja = {
            id_movimiento: "",
            id_usuario: "",
            egresos: 0.0,
            ingresos: 0.0,
            monto_inicial: 0.0,
            monto_final: 0.0,
            fecha_cierre: "",
          };

          // Buscar la caja abierta
          respuesta.forEach(function (item) {
            if (item.estado === "abierto") {
              datosCaja = {
                id_movimiento: item.id_movimiento,
                id_usuario: item.id_usuario,
                ingresos: parseFloat(item.ingresos) || 0.0,
                egresos: parseFloat(item.egresos) || 0.0,
                monto_inicial: parseFloat(item.monto_inicial) || 0.0,
                monto_final: parseFloat(item.monto_final) || 0.0,
                fecha_cierre: item.fecha_cierre,
              };

              // Cálculos de caja
              const ingresos_con_saldo =
                datosCaja.ingresos + datosCaja.monto_inicial;
              const caja_total = ingresos_con_saldo - datosCaja.egresos;

              // Actualizar la interfaz con los montos formateados
              $("#total_ingresos_caja").text(datosCaja.ingresos.toFixed(2));
              $("#total_egresos_caja").text(datosCaja.egresos.toFixed(2));
              $("#total_saldo_inicial_caja").text(
                datosCaja.monto_inicial.toFixed(2)
              );
              $("#monto_totol_caja").text(caja_total.toFixed(2));
            }
          });
          let flag = false;

          const fechaActual = new Date().toLocaleDateString("en-CA", {
            timeZone: "America/Lima",
          });
          const fechaCierre = datosCaja.fecha_cierre.trim().split(" ")[0];
          if (fechaCierre === fechaActual) {
            setInterval(() => {
              const now = new Date();
              const hour = now.getHours();
              const minute = now.getMinutes();
              if (hour === 0 && minute === 0 && !flag) {
                flag = true;
                guardarAperturaCaja(
                  datosCaja.id_movimiento,
                  datosCaja.id_usuario,
                  datosCaja.ingresos,
                  datosCaja.egresos,
                  datosCaja.monto_inicial,
                  datosCaja.monto_final
                );
              }
            }, 1000);
          } else {
            console.log("La fecha de cierre no es hoy.");
          }
        } else {
          console.log("No hay datos disponibles.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al obtener datos de caja:", error);
      },
    });
  }

  /* ===========================
    GUARDAR CIERRE DE CAJA
    =========================== */
  function guardarAperturaCaja(
    id_movimiento,
    id_usuario,
    ingresos,
    egresos,
    monto_inicial,
    monto_final
  ) {
    const monto_final_calculado = monto_inicial + ingresos - egresos;

    const datosCaja = {
      id_movimiento_update: id_movimiento, // ID del movimiento a actualizar
      id_usuario_update: id_usuario,
      tipo_movimiento_update: "cierre",
      egresos_update: egresos, // Egresos acumulados del día
      ingresos_update: ingresos, // Ingresos acumulados del día
      monto_inicial_update: monto_inicial,
      monto_final_update: monto_final_calculado,
      fecha_cierre_update: new Date()
        .toISOString()
        .slice(0, 19)
        .replace("T", " "),
      estado_update: "cerrado",
    };

    $.ajax({
      url: "ajax/Caja.general.ajax.php",
      type: "POST",
      data: datosCaja,
      dataType: "json",
      success: function (respuesta) {
        if (respuesta.status === true) {
          $("#total_ingresos_caja").text("0.00");
          $("#total_egresos_caja").text("0.00");
          $("#total_saldo_inicial_caja").text("0.00");
          $("#monto_totol_caja").text("0.00");
          mostrarCajaGeneral();
        } else {
          console.log(respuesta.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
      },
    });
  }

  /* =====================================
    MOSTRANDO CAJA GENRAL APERTURA
    ===================================== */
  mostrarCajaGeneral();
  mostrarCajaGeneralApertura();
});
