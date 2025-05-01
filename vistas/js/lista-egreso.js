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

  /* =====================================
  CONVERTIR DE DOLARES A 
  ===================================== */
  let currentRate = 0;

  async function getExchangeRate(){
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
        document.getElementById("error_moneda_egreso").textContent = "";
      }
    } catch (error) {
     /*  console.error("Error al actualizar la tasa:", error); */
    }
  }

  setInterval(updateRate, 60 * 60 * 1000);

  //FORMATEAR LOS PRECIOS 
  function formateoPrecio(numero) {
    return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  //MOSTRANDO EN LA TABLA LOS EGRESO O CAMPRAS 
  async function mostrarEgresos() {
    await updateRate();
    $.ajax({
      url: "ajax/Lista.compra.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (egresos) {
        var tbody = $("#data_lista_egresos");
        tbody.empty();
        var egresosProcesados = new Set();
        egresos.forEach(function (egreso, index) {
          if (!egresosProcesados.has(egreso.id_egreso)) {
            var restantePago = (egreso.total_compra - egreso.total_pago).toFixed(2);
            let fechaOriginal = egreso.fecha_egre;
            let partesFecha = fechaOriginal.split("-");
            let fechaFormateada = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
            let totalCompra = formateoPrecio(egreso.total_compra);
            let formateadoPagoRestante = formateoPrecio(restantePago);
            var precioBolivares = currentRate > 0 ? (totalCompra * currentRate).toFixed(2) : "N/A";
            var precioBolivaresRes = currentRate > 0 ? (formateadoPagoRestante * currentRate).toFixed(2) : "N/A";
            var fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${fechaFormateada}</td>
                            <td>${egreso.razon_social}</td>
                            <td>${egreso.serie_comprobante}</td>
                            <td>${egreso.num_comprobante}</td>
                            <td>${egreso.tipo_pago}</td>
                            <td>
                              <div>S/ ${totalCompra}</div>
                              <div>USD ${precioBolivares}</div>
                              <span id="error_moneda_egreso"></span>
                            </td>
                            <td>
                            <div>S/ ${formateadoPagoRestante}</div>
                            <div>USD ${precioBolivaresRes}</div>
                            </td>
                            <td class="text-center">
                                ${restantePago == '0.00' ?
                '<button class="btn btn-sm rounded" style="background: #28C76F; color:white;">Completado</button>' :
                '<button class="btn btn-sm rounded" style="background: #FF4D4D; color:white;">Pendiente</button>'}
                            </td>
                            <td class="text-center">
                                <a href="#" class="me-3 btnPagarCompra" 
                                  idEgreso="${egreso.id_egreso}" 
                                  totalCompraEgreso="${totalCompra}" 
                                  pagoRestanteEgreso="${formateadoPagoRestante}" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#modalPagarCompra">
                                    <i class="fas fa-money-bill-alt fa-lg" style="color: #28C76F"></i>
                                </a>
                                <a href="#" 
                                  class="me-3 btnImprimir" 
                                  idEgreso="${egreso.id_egreso}" 
                                  tipoComprobante="${egreso.tipo_comprobante}">
                                    <i class="fa fa-print fa-lg" style="color: #0084FF"></i>
                                </a>
                                <a href="#" class="me-3 btnDownload" 
                                  idEgreso="${egreso.id_egreso}" tipoComprobante="${egreso.tipo_comprobante}">
                                    <i class="fa fa-download fa-lg" style="color: #28C76F"></i>
                                </a>
                                <a href="#" class="me-3 confirm-text btnEliminarCompra" 
                                  idEgreso="${egreso.id_egreso}" 
                                  imagenProducto="${egreso.imagen_producto}">
                                    <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
                                </a>
                            </td>
                        </tr>`;
            tbody.append(fila);
            egresosProcesados.add(egreso.id_egreso);
          }
        });
        $('#tabla_lista_agreso').DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuarios:", error.mensaje);
      },
    });
  }

  //BOTON PARRA IMPRIMIR LAS COMPRAS
  $(document).on('click', '.btnImprimir', function (e) {
    e.preventDefault();
    let idEgreso = $(this).attr('idEgreso');
    let tipoComprobante = $(this).attr('tipoComprobante');
    if (idEgreso && tipoComprobante == "ticket") {
      const url = `extensiones/ticket/ticket.php?id_egreso=${idEgreso}`;
      console.log(`Redirigiendo a: ${url}`);
      abrirEnNuevaPestana(url);
    } else if (idEgreso && tipoComprobante == "boleta") {
      const url = `extensiones/boleta/boleta.php?id_egreso=${idEgreso}`;
      console.log(`Redirigiendo a: ${url}`);
      abrirEnNuevaPestana(url);
    } else if (idEgreso && tipoComprobante == "factura") {
      const url = `extensiones/factura/factura.php?id_egreso=${idEgreso}`;
      console.log(`Redirigiendo a: ${url}`);
      abrirEnNuevaPestana(url);
    }
  });
  function abrirEnNuevaPestana(url) {
    const nuevaVentana = window.open(url, '_blank');
    nuevaVentana.onload = function () {
      nuevaVentana.print();
    };
  }

  // BOTON PARA DESCARGAR LAS COMPRAS
  $(document).on('click', '.btnDownload', function (e) {
    e.preventDefault();
    const idEgreso = $(this).attr('idEgreso');
    const tipoComprobante = $(this).attr('tipoComprobante');
    let url = '';
    if (idEgreso && tipoComprobante == "ticket") {
      url = `extensiones/ticket/ticket.php?id_egreso=${idEgreso}&accion=descargar`;
    } else if (idEgreso && tipoComprobante == "boleta") {
      url = `extensiones/boleta/boleta.php?id_egreso=${idEgreso}&accion=descargar`;
    } else if (idEgreso && tipoComprobante == "factura") {
      url = `extensiones/factura/factura.php?id_egreso=${idEgreso}&accion=descargar`;
    }
    window.location.href = url;
  });

  //MOSTRAR DEUDA A COMPRAR
  $("#data_lista_egresos").on("click", ".btnPagarCompra", async function (e) {
    e.preventDefault();
    await updateRate();
    let idEgreso = $(this).attr("idEgreso");
    let totalCompraEgreso = $(this).attr("totalCompraEgreso");
    let pagoRestanteEgreso = $(this).attr("pagoRestanteEgreso");
    let total_compra = currentRate > 0 ? (totalCompraEgreso * currentRate).toFixed(2) : "N/A";
    let pago_restante = currentRate > 0 ? (pagoRestanteEgreso * currentRate).toFixed(2) : "N/A";
    $("#id_egreso_pagar").val(idEgreso);
    $("#total_compra_show").text("S/ " + totalCompraEgreso);
    $("#total_compra_show_ves").text("USD " + total_compra);
    $("#total_restante_show").text("S/ " + pagoRestanteEgreso);
    $("#total_restante_show_ves").text("USD " + pago_restante);

  })

  //BOTON PAGAR DEUDA 
  $("#btn_pagar_deuda_egreso").click(function (e) {
    e.preventDefault();
    var isValid = true;
    var id_egreso_pagar = $("#id_egreso_pagar").val();
    var monto_pagar_compra = $("#monto_pagar_compra").val();
    var total_restante_texto = $("#total_restante_show").text();
    var numero_decimal = parseFloat(
      total_restante_texto.match(/-?\d+(\.\d+)?/)[0]
    );
    if (numero_decimal <= 0.0) {
      Swal.fire({
        title: "¡Aviso!",
        text: "No tiene deudas",
        icon: "warning",
      });
      $("#frm_pagar_deuda")[0].reset();
      $("#modalPagarCompra").modal("hide");
      return;
    }

    // Validar el tipo de documento
    if (monto_pagar_compra === "" || monto_pagar_compra == null) {
      $("#error_monto_pagar_egreso")
        .html("Por favor, ingrese el monto")
        .addClass("text-danger");
      isValid = false;
    } else if (isNaN(monto_pagar_compra)) {
      $("#error_monto_pagar_egreso")
        .html("El monto solo puede contener números")
        .addClass("text-danger");
      isValid = false;
    } else {
      $("#error_monto_pagar_egreso").html("").removeClass("text-danger");
    }

    // Si el formulario es válido, envíalo
    if (isValid) {
      const datos = new FormData();
      datos.append("id_egreso_pagar", id_egreso_pagar);
      datos.append("monto_pagar_compra", monto_pagar_compra);
      $.ajax({
        url: "ajax/Lista.compra.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          var res = JSON.parse(respuesta);
          if (res.estado === "ok") {
            Swal.fire({
              title: "¡Correcto!",
              text: res.mensaje,
              icon: "success",
            });
            $("#frm_pagar_deuda")[0].reset();
            $("#modalPagarCompra").modal("hide");
          } else {
            Swal.fire({
              title: res.estado,
              text: res.mensaje,
              icon: "error",
            });
          }
          mostrarEgresos();
        },
      });
    }
  });

  //ACTIVAR PRODUCTO 
  $("#tabla_productos").on("click", ".btnActivar", function () {
    var idProducto = $(this).attr("idProducto");
    var estadoProducto = $(this).attr("estadoProducto");
    var datos = new FormData();
    datos.append("activarId", idProducto);
    datos.append("activarProducto", estadoProducto);
    $.ajax({
      url: "ajax/Producto.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        if (window.matchMedia("(max-width:767px)").matches) {
          swal({
            title: "El producto ha sido actualizado",
            type: "success",
            confirmButtonText: "¡Cerrar!",
          }).then(function (result) {
            if (result.value) {
              window.location = "usuarios";
            }
          });
        }
      },
    });
    if (estadoProducto == 0) {
      $(this).removeClass("btn-success").addClass("btn-danger").css({
        "background-color": "#FF4D4D",
        "color": "white",
        "border": "none" // Quita el borde del botón
      }).html("Desactivado").attr("estadoProducto", 1);
    } else {
      $(this).addClass("btn-success").removeClass("btn-danger").css({
        "background-color": "#28C76F",
        "color": "white",
        "border": "none" // Quita el borde del botón
      }).html("Activado").attr("estadoProducto", 0);
    }
  });

  //ELIMINAR COMPRA 
  $("#tabla_lista_agreso").on("click", ".btnEliminarCompra", function (e) {
    e.preventDefault();
    let idEgresoDelete = $(this).attr("idEgreso");
    var datos = new FormData();
    datos.append("idEgresoDelete", idEgresoDelete);
    Swal.fire({
      title: "¿Está seguro de borrar la compra?",
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
          url: "ajax/Lista.compra.ajax.php",
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
                text: "El producto ha sido eliminado",
                icon: "success",
              });
              mostrarEgresos();
            } else {
              console.error("Error al eliminar los datos");
            }
          }
        });
      }
    });
  }
  );

  //LIMPIAR MODALES
  $(".btn_modal_ver_close_usuario").click(function () {
    $("#mostrar_data_roles").text('');
  });
  $(".btn_modal_editar_close_usuario").click(function () {
    $("#formEditUsuario")[0].reset();
  });

  //MOSTRAR COMPRAS O EGRESOS 
  mostrarEgresos();
});
