$(document).ready(function () {

  /* =====================================
 FORMATEAR FECHA Y HORA HUMANA
 ===================================== */
  function formatearFechaHumana(fechaString) {
    const fecha = new Date(fechaString);
    
    // Formatear fecha
    const fechaFormateada = fecha.toLocaleDateString("es-PE", {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      timeZone: "America/Lima"
    });
    
    // Formatear hora
    const horaFormateada = fecha.toLocaleTimeString("es-PE", {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true,
      timeZone: "America/Lima"
    });
    
    return `${fechaFormateada} - ${horaFormateada}`;
  }

  /* =====================================
 CONVERTIR DE DOLARES A 
 ===================================== */
  let currentRate = 0;

  async function getExchangeRate() {
    try {
      const response = await fetch('https://api.exchangerate-api.com/v4/latest/PEN');
      const data = await response.json();
      return data.rates.USD;
    } catch (error) {
      console.error('Error obteniendo tasas', error);
      try {
        const response = await fetch('https://open.er-api.com/v6/latest/PEN');
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
        currentRate = rate; // Asigna la tasa de cambio al valor global
        document.getElementById("error_moneda").textContent = "";
      }
    } catch (error) {
      /*  console.error("Error al actualizar la tasa:", error); */
    }
  }

  setInterval(updateRate, 60 * 60 * 1000);

  // Función para cambiar la moneda
  async function cambiarModena() {
    await updateRate();
    let valor_apertura_caja = $("#monto_inicial_caja").val();
    let precioBolivares = currentRate > 0 && !isNaN(parseFloat(valor_apertura_caja)) && valor_apertura_caja !== ""
      ? (parseFloat(valor_apertura_caja) * currentRate).toFixed(2)
      : "0.00";
    $("#value_valor_bolivares_caja").text("USD " + precioBolivares);
  }

  $("#monto_inicial_caja").on("input", function () {
    cambiarModena();
  });

  cambiarModena();


  /* ===========================================
    SELECIONADO LA FECHA AUTOMÁTICO
    =========================================== */
  // Obtener la fecha actual en formato YYYY-MM-DD
  const today = new Date().toISOString().split("T")[0];

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
                          <td class="text-center">${formatearFechaHumana(item.fecha_cierre)}</td>
                          <td class="text-center">S/ ${item.ingresos}</td>
                          <td class="text-center">S/ ${item.egresos}</td>
                          <td class="text-center">S/ ${item.monto_inicial}</td>
                          <td class="text-center">S/ ${item.monto_final}</td>
                          <td class="text-center">
                              <div class="btn-group">
                                  <button class="btn btn-sm btn-info btnReabrirCaja" 
                                          data-id="${item.id_movimiento}"
                                          title="Reabrir esta caja">
                                      <i class="fas fa-lock-open"></i>
                                  </button>
                              </div>
                          </td>
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


  // Evento para reabrir caja cerrada
  // Evento para reabrir caja cerrada
$(document).on("click", ".btnReabrirCaja", function() {
    const idCaja = $(this).data("id");
    
    Swal.fire({
        title: '¿Reabrir esta caja?',
        text: "Esta acción cambiará el estado de la caja a 'abierto'",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, reabrir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "ajax/Caja.general.ajax.php",
                type: "POST",
                data: {
                    id_caja_update: idCaja,
                    estado_update: "abierto",
                    action: "reabrir_caja"
                },
                dataType: "json",
                success: function(respuesta) {
                    if (respuesta.status) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: respuesta.message,
                            icon: 'success'
                        });
                        mostrarCajaGeneral();
                        mostrarCajaGeneralApertura();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: respuesta.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
    });
});

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

          const fechaActual = new Date().toLocaleDateString("en-CA", { timeZone: "America/Lima", });
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
            /* console.log("La fecha de cierre no es hoy."); */
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
    MOSTRANDO CAJA GENRAL APERTURA
    =========================== */
  function mostrarResumenVentaCaja() {
    $.ajax({
      url: "ajax/Resumen.ventas.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (response) {
        var tbody = $("#data_resumen_venta_productos");
        tbody.empty();
        response.forEach(function (data, index) {
          var fila = `
              <tr>
                  <td>${index + 1}</td>
                  <td>${data.nombre_producto}</td>
                  <td class="text-center">S/ ${data.total_vendido}</td>
                  <td class="text-center">S/ ${data.ganancia_por_unidad}</td>
                  <td class="text-center">S/ ${data.ganancia_total}</td>
              </tr>
          `;
          tbody.append(fila);
        });

        // Inicializar DataTables después de cargar los datos
        $('#tabla_resumen_venta_productos').DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al obtener datos de caja:", error);
        console.log(xhr);
        console.log(status);
      },
    });
  }
  mostrarResumenVentaCaja();
  /* ===================================
    CIERRE DE CAJA MANUALMENTE
  =================================== */
  function cierreCajaGeneralAperturaManual() {
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
            }
          });

          // Validar la fecha de cierre y realizar el cierre
          const fechaActual = new Date().toLocaleDateString("en-CA", { timeZone: "America/Lima" });
          const fechaCierre = datosCaja.fecha_cierre.trim().split(" ")[0];

          if (fechaCierre === fechaActual) {
            guardarAperturaCaja(
              datosCaja.id_movimiento,
              datosCaja.id_usuario,
              datosCaja.ingresos,
              datosCaja.egresos,
              datosCaja.monto_inicial,
              datosCaja.monto_final
            );
            console.log("La caja ha sido cerrada exitosamente.");
          } else {
            console.log("La fecha de cierre no corresponde al día actual.");
          }
        } else {
          console.log("No hay datos disponibles para cerrar la caja.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al obtener datos de caja:", error);
      },
    });
  }

  $("#btn_cerrar_caja_del_dia").on("click", function (e) {
    e.preventDefault(); // Evitar comportamiento predeterminado
    cierreCajaGeneralAperturaManual(); // Llamar a la función de cierre
  });




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
      fecha_cierre_update: (() => {
        // Obtener UTC y restar 5 horas para Perú (UTC-5)
        const utc = new Date();
        const peruTime = new Date(utc.getTime() - (5 * 60 * 60 * 1000));
        
        const year = peruTime.getUTCFullYear();
        const month = String(peruTime.getUTCMonth() + 1).padStart(2, '0');
        const day = String(peruTime.getUTCDate()).padStart(2, '0');
        const hours = String(peruTime.getUTCHours()).padStart(2, '0');
        const minutes = String(peruTime.getUTCMinutes()).padStart(2, '0');
        const seconds = String(peruTime.getUTCSeconds()).padStart(2, '0');
        
        const fechaFormateada = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        console.log("Hora UTC actual:", utc.toISOString());
        console.log("Hora de Perú calculada:", fechaFormateada);
        return fechaFormateada;
      })(),
      estado_update: "cerrado",
    };

    console.log("=== DATOS COMPLETOS PARA CERRAR CAJA ===");
    console.log("ID Movimiento:", id_movimiento);
    console.log("ID Usuario:", id_usuario);
    console.log("Egresos:", egresos);
    console.log("Ingresos:", ingresos);
    console.log("Monto inicial:", monto_inicial);
    console.log("Monto final calculado:", monto_final_calculado);
    console.log("Objeto completo datosCaja:", datosCaja);
    console.log("==========================================");

    $.ajax({
      url: "ajax/Caja.general.ajax.php",
      type: "POST",
      data: datosCaja,
      dataType: "json",
      success: function (respuesta) {
        console.log("=== RESPUESTA DEL SERVIDOR ===");
        console.log("Respuesta completa:", respuesta);
        console.log("===============================");
        
        if (respuesta.status === true) {
          console.log("✅ Caja cerrada exitosamente");
          $("#total_ingresos_caja").text("0.00");
          $("#total_egresos_caja").text("0.00");
          $("#total_saldo_inicial_caja").text("0.00");
          $("#monto_totol_caja").text("0.00");
          mostrarCajaGeneral();
        } else {
          console.log("❌ Error al cerrar caja:", respuesta.message);
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
