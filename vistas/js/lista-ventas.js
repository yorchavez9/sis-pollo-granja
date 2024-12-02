
/* =====================================
 VISTA PREVIA DE IMAGEN PRODUCTO
 ===================================== */
$("#comprobante_pago_historial").change(function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $(".vista_previa_comprobante_pago").attr("src", e.target.result);
      $(".vista_previa_comprobante_pago").show();
    };
    reader.readAsDataURL(file);
  }
});


/* =====================================
 VISTA PREVIA DEL COMPROBANTE EDITADO
 ===================================== */
$("#edit_comprobante_pago_historial").change(function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $(".edit_vista_previa_comprobante_pago").attr("src", e.target.result);
      $(".edit_vista_previa_comprobante_pago").show();
    };
    reader.readAsDataURL(file);
  }
});



/*=============================================
SELECCIONANDO LA SECCCION DE LA VENTA
=============================================*/
function showSection(){
    $(".seccion_lista_venta").on("click", function () {
      $("#ventas_lista").show();
      $("#pos_venta").hide();
      $("#ver_pos_venta").hide();
      $("#edit_pos_venta").hide();
      $("#edit_detalle_venta_producto").empty();
      $("#ver_detalle_venta_producto").empty();
    });
}

showSection();

/*=============================================
MOSTRANDO PRODUCTOS DE LA VENTA
=============================================*/
function mostrarProductoVenta() {
  $.ajax({
    url: "ajax/Producto.ajax.php",
    type: "GET",
    dataType: "json",
    success: function (productos) {
      var tbody = $("#data_edit_productos_detalle_venta");
      tbody.empty();
      productos.forEach(function (producto) {
        producto.imagen_producto = producto.imagen_producto.substring(3);
        var fila = `
                <tr>
                    <td class="text-center">
                        <a href="#" id="btnAddProductoVenta" class="hover_img_a btnAddEditProductoVenta" idProductoAdd="${historial_pago.id_producto}" stockProducto="${historial_pago.stock_producto}">
                            <img class="hover_img" src="${historial_pago.imagen_producto}" alt="${historial_pago.imagen_producto}">
                        </a>
                    </td>
                    <td>${historial_pago.nombre_categoria}</td>
                    <td class="fw-bold">S/ ${historial_pago.precio_producto}</td>
                    <td>${historial_pago.nombre_producto}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm" style="${getButtonStyles(producto.stock_producto)}">
                            ${historial_pago.stock_producto}
                        </button>
                    </td>
                </tr>`;

        function getButtonStyles(stock) {
          if (stock > 20) {
            return "background-color: #28C76F; color: white; border: none;";
          } else if (stock >= 10 && stock <= 20) {
            return "background-color: #FF9F43; color: white; border: none;";
          } else {
            return "background-color: #FF4D4D; color: white; border: none;";
          }
        }
        tbody.append(fila);
      });
      $("#tabla_edit_add_producto_venta").DataTable();
    },
    error: function (xhr, status, error) {
      console.error("Error al recuperar los usuarios:", error.mensaje);
    },
  });
}

/* ===========================================
FORMATEO DE PRECIOS
=========================================== */
function formateoPrecio(numero) {
  return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/* ===========================================
MOSTRANDO VENTAS
=========================================== */
function mostrarVentas() {
  $.ajax({
    url: "ajax/Lista.venta.ajax.php",
    type: "GET",
    dataType: "json",
    success: function (ventas) {
      let tbody = $("#data_lista_ventas");
      tbody.empty();
      var ventasProcesados = new Set();
      ventas.forEach(function (venta, index) {
        // Verificar si el id_egreso ya ha sido procesado
        if (!ventasProcesados.has(venta.id_venta)) {
          var restantePago = (venta.total_venta - venta.total_pago).toFixed(2);
          let fechaOriginal = venta.fecha_venta;
          let partesFecha = fechaOriginal.split("-"); // Dividir la fecha en año, mes y día
          let fechaFormateada = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
          let totalCompra = formateoPrecio(venta.total_venta);
          let formateadoPagoRestante = formateoPrecio(restantePago);
          var fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${venta.razon_social}</td>
                        <td>${venta.serie_prefijo}-${venta.num_comprobante}</td>
                        <td>${venta.tipo_pago}</td>
                        <td>S/ ${totalCompra}</td>
                        <td>S/ ${formateadoPagoRestante}</td>
                        <td>${fechaFormateada}</td>
                        <td class="text-center">
                            ${restantePago == "0.00"
                              ? '<button class="btn btn-sm rounded" style="background: #28C76F; color:white;">Completado</button>'
                              : '<button class="btn btn-sm rounded" style="background: #FF4D4D; color:white;">Pendiente</button>'
                            }
                        </td>
                        <td class="text-center">
                            <a href="#" class="me-3 btnPagarVenta" 
                            idVenta="${venta.id_venta}" 
                            totalCompraVenta="${totalCompra}" 
                            pagoRestanteVenta="${formateadoPagoRestante}" 
                            restantePago="${restantePago}"
                            tipoPago= "${venta.tipo_pago}">
                                <i class="fas fa-money-bill-alt fa-lg" style="color: #28C76F"></i>
                            </a>
                            <a href="#" class="me-3 btnHistorialPago" idVenta="${venta.id_venta}" data-bs-toggle="modal" data-bs-target="#modal_mostrar_historial_pago">
                                <i class="text-primary fas fa-history fa-lg"></i>
                            </a>
                            <a href="#" class="me-3 btnImprimirComprobanteV" idVenta="${venta.id_venta}" tipo_comprobante="${venta.tipo_comprobante_sn}">
                                <i class="fa fa-print fa-lg" style="color: #0084FF"></i>
                            </a>
                            <a href="#" class="me-3 btnDescargarComprobanteV" idVenta="${venta.id_venta}" tipo_comprobante="${venta.tipo_comprobante_sn}">
                                <i class="fa fa-download fa-lg" style="color: #28C76F"></i>
                            </a>
                            <a href="#" class="me-3 confirm-text btnEliminarVenta" idVentaDelete="${venta.id_venta}">
                                <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
                            </a>
                        </td>
                    </tr>`;

          // Agregar la fila al tbody
          tbody.append(fila);
          // Agregar el id_egreso al conjunto de egresos procesados
          ventasProcesados.add(venta.id_venta);
        }
      });
      // Inicializar DataTables después de cargar los datos
      $("#tabla_lista_ventas").DataTable();
    },
    error: function (xhr, status, error) {
      console.error(error);
      console.error(xhr);
      console.error(status);
    },
  });
}

/* ===========================================
MOSTRANDO HISTORIAL DE PAGO
=========================================== */
$("#data_lista_ventas").on("click", ".btnHistorialPago", function (e) {
  e.preventDefault();
  let id_venta_historial = $(this).attr("idVenta");
  cargarHistorialPago(id_venta_historial);
});

// Función para cargar el historial de pagos
function cargarHistorialPago(id_venta_historial) {
  const datos = new FormData();
  datos.append("id_venta_historial", id_venta_historial);
  $.ajax({
    url: "ajax/Historial.pago.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (historial_pagos) {
      let tbody = $("#data_historial_pago");
      tbody.empty();

      // Validar si el historial está vacío o no contiene datos
      if (!historial_pagos || historial_pagos.length === 0) {
        Swal.fire({
          title: "Sin datos",
          text: "No se encontraron pagos en el historial para esta venta.",
          icon: "info",
        });
        return;
      }

      // Recorrer los datos del historial de pagos
      historial_pagos.forEach(function (historial_pago, index) {
        let comprobanteImagen =
          historial_pago.comprobante_imagen && historial_pago.comprobante_imagen.trim() !== ""
            ? historial_pago.comprobante_imagen.substring(3)
            : null;

        let numeroSerie =
          historial_pago.numero_serie_pago && historial_pago.numero_serie_pago.trim() !== ""
            ? historial_pago.numero_serie_pago
            : "Sin serie";

        var fila = `
          <tr>
            <td>${index + 1}</td>
            <td>${historial_pago.fecha_registro}</td>
            <td>${historial_pago.forma_pago}</td>
            <td>S/ ${historial_pago.monto_pago}</td>
            <td class="text-center">
              <div>
                ${comprobanteImagen
            ? `<a href="javascript:void(0);" class="product-img"><img src="${comprobanteImagen}" alt="Comprobante"></a>`
            : `<small>Sin comprobante</small>`}
              </div>
              <div>
                <small>${numeroSerie}</small>
              </div>
            </td>
            <td class="text-center">
              <a href="#" class="me-3 btnEditarHistorialPago" idPago="${historial_pago.id_pago}" idVenta="${historial_pago.id_venta}" data-bs-toggle="modal" data-bs-target="#modal_editar_historial_pago">
                <i class="text-warning fas fa-edit fa-lg"></i>
              </a>
              <a href="#" class="me-3 confirm-text btn_print_pago_historial" idPago="${historial_pago.id_pago}" idVenta="${historial_pago.id_venta}"">
                <i class="fa fa-print fa-lg" style="color: #0084FF"></i>
              </a>
              <a href="#" class="me-3 confirm-text btnEliminarHistorialPago" idPago="${historial_pago.id_pago}" idVenta="${historial_pago.id_venta}" imagenHistorialPago="${comprobanteImagen}">
                <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
              </a>
            </td>
          </tr>`;
        tbody.append(fila);
      });

      $("#tabla_historial_pago").DataTable();
    },
    error: function () {
      Swal.fire({
        title: "Error",
        text: "Ocurrió un problema al obtener el historial de pagos.",
        icon: "error",
      });
    },
  });
}


/*=============================================
ELIMINAR HISTORIAL PAGO
=============================================*/
$("#data_historial_pago").on("click", ".btnEliminarHistorialPago", function (e) {
  e.preventDefault();
  let id_delete_pago_historial = $(this).attr("idPago");
  var id_venta_historial = $(this).attr("idVenta");
  let url_imagen_historial_pago = $(this).attr("imagenHistorialPago");
  const datos = new FormData();
  datos.append("id_delete_pago_historial", id_delete_pago_historial);
  datos.append("url_imagen_historial_pago", url_imagen_historial_pago);

  Swal.fire({
    title: "¿Está seguro de borrar el pago?",
    text: "¡Si no lo está puede cancelar la acción!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    cancelButtonText: "Cancelar",
    confirmButtonText: "Si, borrar!",
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        url: "ajax/Historial.pago.ajax.php",
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
              text: "El pago se eliminó con éxito",
              icon: "success",
            }).then(() => {
              if (id_venta_historial) {
                console.log("Refrescando historial para la venta ID:", id_venta_historial);
                cargarHistorialPago(id_venta_historial);
              }
            });
            $("#tabla_historial_pago").DataTable();
          } else {
            console.error("Error al eliminar los datos");
          }
        }

      });
    }
  });
});


/*=============================================
IMPRIMIR TICKET
=============================================*/
$("#data_lista_ventas").on("click", ".btnImprimirComprobanteV", function (e) {
  e.preventDefault();
  console.log("Imprimiendo comprobante");
  var idVenta = $(this).attr("idVenta");
  var documento = $(this).attr("tipo_comprobante");
  const urlDocumento = `extensiones/${documento}/${documento}_v.php?id_venta=${idVenta}`;
  const ventana = window.open(urlDocumento, '_blank');
  ventana.onload = () => ventana.print();
});

/*=============================================
DESCARGAR TICKET
=============================================*/
$("#data_lista_ventas").on("click", ".btnDescargarComprobanteV", function (e) {
  e.preventDefault();
  console.log("Descargando comprobante");
  var idVenta = $(this).attr("idVenta");
  var documento = $(this).attr("tipo_comprobante");
  window.location.href = `extensiones/${documento}/${documento}_v.php?id_venta=${idVenta}&accion=descargar`;
});

/* ===========================================
AGREGANDO PRODUCTO PARA EDITAR
=========================================== */
$("#data_edit_productos_detalle_venta").on("click", ".btnAddEditProductoVenta", function (e) {
  e.preventDefault();
  var id_producto_edit = $(this).attr("idProductoAdd");
  var sotck_producto_edit = $(this).attr("stockProducto");
  if (sotck_producto_edit <= 0) {
    Swal.fire({
      title: "¡Alerta!",
      text: "¡El stock de este producto se agotado!",
      icon: "error",
    });
    return;
  } else if (sotck_producto_edit > 0 && sotck_producto_edit < 10) {
    Swal.fire({
      title: "¡Aviso!",
      text: "¡El stock de este producto se está agotando!",
      icon: "warning",
    });
    
  }

  var datos = new FormData();
  datos.append("id_producto_edit", id_producto_edit);
  $.ajax({
    url: "ajax/Lista.venta.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      respuesta.imagen_producto = respuesta.imagen_producto.substring(3);
      var nuevaFila = `
                      <tr>
                          <input type="hidden" class="edit_id_producto_venta" value="${respuesta.id_producto}">
                          <th class="text-center align-middle d-none d-md-table-cell">
                              <a href="#" class="me-3 confirm-text btnEliminarAddProductoVentaEdit" idAddProducto="${respuesta.id_producto}"">
                                  <i class="fa fa-trash fa-lg" style="color: #F1666D"></i>
                              </a>
                          </th>
                          <td>
                              <img src="${respuesta.imagen_producto}" alt="Imagen de un pollo" width="50">
                          </td>
                          <td>${respuesta.nombre_producto}</td>
                          <td>
                              <input type="number" class="form-control form-control-sm edit_cantidad_u_v" value="0">
                          </td>
                          <td>
                              <input type="number" class="form-control form-control-sm edit_cantidad_kg_v" value="0">
                          </td>
                          <td>
                              <input type="number" class="form-control form-control-sm edit_precio_venta" value="${respuesta.precio_producto}">
                          </td>
                          <td style="text-align: right;">
                              <p class="price">S/ <span class="edit_precio_sub_total_venta">0.00</span></p>
                          </td>
                          
                      </tr>`;

      $("#edit_detalle_venta_producto").append(nuevaFila);
      calcularSubTotal();
    },
    error: function (err) {
      console.error(err);
    },
  });
  calcularTotal();
  $(document).ready(function () {
    calcularTotal();
  });
});

/* ===========================================
CALCULAR EL SUB TOTAL CON ACCION DE INPUT
=========================================== */
function calcularSubTotal() {
  $(".edit_cantidad_kg_v, .edit_precio_venta").on("input", function () {
    var fila = $(this).closest("tr");
    var cantidad_kg = parseFloat(fila.find(".edit_cantidad_kg_v").val());
    var precio_compra = parseFloat(fila.find(".edit_precio_venta").val());
    if (isNaN(cantidad_kg)) {
      cantidad_kg = 0;
    }
    if (isNaN(precio_compra)) {
      precio_compra = 0;
    }
    var subtotal = cantidad_kg * precio_compra;
    var formateadoSubTotal = formateoPrecio(subtotal.toFixed(2));
    fila.find(".edit_precio_sub_total_venta").text(formateadoSubTotal);
    // Calcular y mostrar el total
    calcularTotal();
  });
}

/* ===========================================
MOSTRAR Y OCULAR EL TIPO DE PAGO
=========================================== */
$(".tipo_pago_venta").on("click", function () {
  let valor = $(this).val();
  if (valor == "credito") {
    $("#edit_venta_al_contado").hide();
  } else {
    $("#edit_venta_al_contado").show();
  }
});

/*=============================================
ELIMINAR VENTA
=============================================*/

$("#data_lista_ventas").on("click", ".btnEliminarVenta", function(e) {

  e.preventDefault();
  
  var ventaIdDelete = $(this).attr("idVentaDelete");

  var datos = new FormData();

  datos.append("ventaIdDelete", ventaIdDelete);

  Swal.fire({
      title: "¿Está seguro de borrar la venta?",
      text: "¡Si no lo está puede cancelar la acción!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar",
      confirmButtonText: "Si, borrar!",
  }).then(function(result) {

      if (result.value) {

          $.ajax({
              url: "ajax/Lista.venta.ajax.php",
              method: "POST",
              data: datos,
              cache: false,
              contentType: false,
              processData: false,
              success: function(respuesta) {

                  var res = JSON.parse(respuesta);
                  if (res === "ok") {

                      Swal.fire({
                          title: "¡Eliminado!",
                          text: "La venta ha sido eliminada",
                          icon: "success",
                      });

                      mostrarVentas();
                  } else {

                      console.error("Error al eliminar los datos");
                  }
              }
          });
      }
  });
});


/* ===========================================
CALCULAR EL TOTAL DE LA VENTA
=========================================== */

function calcularTotal() {

  var subtotalTotal = 0;

  var impuesto = parseFloat($("#edit_igv_venta").val());

  // Recorrer todas las filas para sumar los subtotales

  $("#edit_detalle_venta_producto tr").each(function () {

    var subtotalString = $(this)
      .find(".edit_precio_sub_total_venta")
      .text()
      .replace("S/ ", "")
      .replace(",", "");

    var subtotal = parseFloat(subtotalString);

    // Si subtotal no es un número válido, asignar 0

    if (isNaN(subtotal)) {

      subtotal = 0;

    }

    subtotalTotal += subtotal;

  });

  // Calcular el impuesto

  var igv = subtotalTotal * (impuesto / 100);

  // Calcular el total

  var total = subtotalTotal + igv;

  // Verificar si el resultado es NaN y mostrar "0.00" en su lugar

  if (isNaN(total)) {

    total = 0;

  }

  // Formatear los resultados

  var subtotalFormateado = formateoPrecio(subtotalTotal.toFixed(2));

  var igvFormateado = formateoPrecio(igv.toFixed(2));

  var totalFormateado = formateoPrecio(total.toFixed(2));

  // Mostrar los resultados en el HTML

  $("#edit_subtotal_venta").text(subtotalFormateado);

  $("#edit_igv_venta_show").text(igvFormateado);

  $("#edit_total_precio_venta").text(totalFormateado);
}

/* ===========================================
ELIMINAR EL PRODUCTO AGREGADO DE LA LISTA
=========================================== */

$("#data_lista_ventas").on("click", ".btnPagarVenta", function (e) {
  e.preventDefault();

  // Función para limpiar comas y convertir el número a flotante
  const parseNumber = (numStr) => {
    if (!numStr) return 0; // Manejar valores nulos o indefinidos
    return parseFloat(numStr.replace(/,/g, "")); // Reemplazar comas y convertir a número
  };

  // Función para formatear un número en formato con comas
  const formatNumber = (num) => {
    return num.toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  };

  // Obtención de atributos de la venta
  const idVenta = $(this).attr("idVenta");
  const totalCompraVenta = parseNumber($(this).attr("totalCompraVenta"));
  const pagoRestanteVenta = parseNumber($(this).attr("pagoRestanteVenta"));
  const restantePago = parseNumber($(this).attr("restantePago"));
  const tipoPago = $(this).attr("tipoPago");

  // Validación si la venta no tiene deudas
  if (restantePago <= 0) {
    Swal.fire({
      title: "¡Aviso!",
      text: "Esta venta no tiene deudas pendientes.",
      icon: "warning",
    });
    return; // Salir si no hay deudas pendientes
  }

  // Validaciones de los valores
  if (!idVenta || isNaN(totalCompraVenta) || isNaN(pagoRestanteVenta)) {
    Swal.fire({
      title: "¡Error!",
      text: "Información de la venta incompleta o inválida.",
      icon: "error",
    });
    return; // Salir si la información no es válida
  }

  // Asignar valores a los elementos del formulario
  $("#id_venta_pagar").val(idVenta);
  $("#tipo_pago_historial_venta").val(tipoPago || ""); // Tipo de pago por defecto vacío si no existe
  $("#total_venta_pagar").text(`S/ ${formatNumber(totalCompraVenta)}`);
  $("#pago_restante_pagar").text(`S/ ${formatNumber(pagoRestanteVenta)}`);

  // Mostrar el modal
  $("#modalPagarVenta").modal("show");
});

/* ===========================================
MOSTRAR DEUDA A PAGAR
=========================================== */
$("#data_lista_ventas").on("click", ".btnPagarVenta", function (e) {
  e.preventDefault();

  // Función para limpiar comas y convertir el número a flotante
  const parseNumber = (numStr) => {
    if (!numStr) return 0; // Manejar valores nulos o indefinidos
    return parseFloat(numStr.replace(/,/g, "")); // Reemplazar comas y convertir a número
  };

  // Obtención de atributos de la venta
  const idVenta = $(this).attr("idVenta");
  const totalCompraVenta = parseNumber($(this).attr("totalCompraVenta"));
  const pagoRestanteVenta = parseNumber($(this).attr("pagoRestanteVenta"));
  const restantePago = parseNumber($(this).attr("restantePago"));
  const tipoPago = $(this).attr("tipoPago");

  // Validación si la venta no tiene deudas
  if (restantePago <= 0) {
    Swal.fire({
      title: "¡Aviso!",
      text: "Esta venta no tiene deudas pendientes.",
      icon: "warning",
    });
    return; // Salir si no hay deudas pendientes
  }

  // Validaciones de los valores
  if (!idVenta || isNaN(totalCompraVenta) || isNaN(pagoRestanteVenta)) {
    Swal.fire({
      title: "¡Error!",
      text: "Información de la venta incompleta o inválida.",
      icon: "error",
    });
    return; // Salir si la información no es válida
  }

  // Asignar valores a los elementos del formulario
  $("#id_venta_pagar").val(idVenta);
  $("#tipo_pago_historial_venta").val(tipoPago || ""); // Tipo de pago por defecto vacío si no existe
  $("#total_venta_pagar").text(`S/ ${totalCompraVenta.toFixed(2)}`);
  $("#pago_restante_pagar").text(`S/ ${pagoRestanteVenta.toFixed(2)}`);

  // Mostrar el modal
  $("#modalPagarVenta").modal("show");
});


/* ===========================================
PAGAR DEUDA VENTA
=========================================== */
$("#btn_pagar_deuda_venta").click(function (e) {

  // Variables del formulario
  const idVentaPagar = $("#id_venta_pagar").val();
  const metodoPago = $("#metodos_pago_venta_historial").val();
  const comprobantePago = $("#comprobante_pago_historial").get(0).files[0];
  const serieNumeroPago = $("#serie_numero_pago_historial").val();
  const montoPagar = parseFloat($("#monto_pagar_venta").val());

  // Bandera para verificar si el formulario es válido
  let isValid = true;

  // Validaciones de los campos
  if (!idVentaPagar) {
    isValid = false;
    Swal.fire("Error", "El ID de la venta no es válido.", "error");
    return;
  }

  if (!metodoPago || metodoPago == null || metodoPago == '') {
    isValid = false;
    $("#metodos_pago_venta_historial").addClass("is-invalid");
    $("#error_metodos_pago_venta_historial").text("Debe seleccionar un método de pago.");
  } else {
    $("#metodos_pago_venta_historial").removeClass("is-invalid");
    $("#error_metodos_pago_venta_historial").text("");
  }

  if (!montoPagar || montoPagar <= 0) {
    isValid = false;
    $("#monto_pagar_venta").addClass("is-invalid");
    $("#error_monto_pagar_venta").text("El monto a pagar debe ser mayor a 0.");
  } else {
    $("#monto_pagar_venta").removeClass("is-invalid");
    $("#error_monto_pagar_venta").text("");
  }

  if (!isValid) return;

  // Crear objeto FormData con los datos
  const datos = new FormData();
  datos.append("id_venta_pagar", idVentaPagar);
  datos.append("metodos_pago_venta_historial", metodoPago);
  datos.append("comprobante_pago_historial", comprobantePago);
  datos.append("serie_numero_pago_historial", serieNumeroPago);
  datos.append("monto_pagar_venta", montoPagar);

  // Enviar datos mediante AJAX
  $.ajax({
    url: "ajax/Historial.pago.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    success: function (respuesta) {
      try {
        const res = JSON.parse(respuesta);
        console.log(res);
        if (res.estado === "ok") {
          // Restablecer formulario y cerrar modal
          $("#frm_pagar_deuda_venta")[0].reset();
          $("#modalPagarVenta").modal("hide");
          Swal.fire("Éxito", res.message, "success");

          // Mostrar alerta y preguntar acción
          Swal.fire({
            title: "¿Qué desea hacer con el comprobante?",
            text: "Seleccione una opción.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28C76F",
            cancelButtonColor: "#F52E2F",
            confirmButtonText: "Imprimir",
            cancelButtonText: "Descargar",
            footer: '<a href="#">Enviar por WhatsApp o correo</a>',
          }).then((result) => {
            const id_pago = res.data.id_pago;  // ID del pago
            const id_venta = idVentaPagar;  // ID de la venta

            if (result.isConfirmed) {
              Swal.fire({
                title: "¡Imprimiendo!",
                text: "Su comprobante se está imprimiendo.",
                icon: "success",
              });
              // Construir la URL para imprimir el comprobante
              const urlDocumento = `extensiones/ticket/pago.php?id_pago=${id_pago}&id_venta=${id_venta}`;
              const ventana = window.open(urlDocumento, '_blank');

              ventana.onload = () => ventana.print();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              Swal.fire({
                title: "¡Descargando!",
                text: "Su comprobante se está descargando.",
                icon: "success",
              });
              // Construir la URL para descargar el comprobante
              window.location.href = `extensiones/ticket/pago.php?id_pago=${id_pago}&id_venta=${id_venta}&accion=descargar`;
            } else {
              Swal.fire({
                title: "¿Cómo desea enviar el comprobante?",
                text: "Seleccione una opción.",
                icon: "info",
                showCancelButton: true,
                cancelButtonText: "WhatsApp",
                confirmButtonText: "Correo",
              }).then((sendResult) => {
                const mensaje = sendResult.isConfirmed ? "¡Enviando por correo!" : "¡Enviando por WhatsApp!";
                Swal.fire({
                  title: mensaje,
                  text: `Su comprobante se está enviando por ${sendResult.isConfirmed ? "correo" : "WhatsApp"}.`,
                  icon: "success",
                });
              });
            }
          });
        
        } else if (res.estado == "warning"){
          Swal.fire("Aviso!", res.message, "warning");
        }else{
          Swal.fire("Error!", res.message, "error");
        }

        mostrarVentas();
      } catch (error) {
        Swal.fire("Error", "Respuesta del servidor inválida.", "error");
      }
    },


    error: function () {
      Swal.fire("Error", "No se pudo conectar al servidor.", "error");
    },
  });
});

/*=============================================
 EDITAR PAGO HISTORIAL
 =============================================*/
$("#tabla_historial_pago").on("click", ".btnEditarHistorialPago", function (e) {
  e.preventDefault();
  var idPago = $(this).attr("idPago");
  var idVenta = $(this).attr("idVenta");
  var datos = new FormData();
  datos.append("idPago", idPago);
  datos.append("idVenta", idVenta);
  $.ajax({
    url: "ajax/Historial.pago.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      $("#edit_id_venta_pagar").val(respuesta["id_venta"]);
      $("#edit_edit_pago_historial").val(respuesta["id_pago"]);
      $("#edit_metodos_pago_venta_historial").val(respuesta["forma_pago"]);
      $("#actual_comprobante_pago_historial").val(respuesta["comprobante_imagen"]);
      $("#edit_serie_numero_pago_historial").val(respuesta["numero_serie_pago"]);
      $("#edit_monto_pagar_venta").val(respuesta["monto_pago"]);
      $("#edit_monto_actual_pago").val(respuesta["monto_pago"]);

      if (respuesta["comprobante_imagen"] && respuesta["comprobante_imagen"] !== "") {
        let img_comprobante = respuesta["comprobante_imagen"].substring(3);
        $(".edit_vista_previa_comprobante_pago").attr("src", img_comprobante);
      } else {
        $(".edit_vista_previa_comprobante_pago").attr(
          "src",
          "vistas/img/comprobantes/default.png"
        );
      }
    },
  });
});

/*===========================================
ACTUALIZAR PAGO HISTORIAL
=========================================== */
$("#btn_update_pagar_deuda_venta").click(function (e) {
  e.preventDefault();
  var isValid = true;
  let edit_id_venta_pagar = $("#edit_id_venta_pagar").val();
  let edit_edit_pago_historial = $("#edit_edit_pago_historial").val();
  let edit_metodos_pago_venta_historial = $("#edit_metodos_pago_venta_historial").val();
  let edit_serie_numero_pago_historial = $("#edit_serie_numero_pago_historial").val();
  let edit_comprobante_pago_historial = $("#edit_comprobante_pago_historial").get(0).files[0];
  let actual_comprobante_pago_historial = $("#actual_comprobante_pago_historial").val();
  let edit_monto_actual_pago = $("#edit_monto_actual_pago").val();
  let edit_monto_pagar_venta = $("#edit_monto_pagar_venta").val();

  // Validar la categoria
  if (edit_metodos_pago_venta_historial == "" || edit_metodos_pago_venta_historial == null) {
    $("#edit_error_metodos_pago_venta")
      .html("Por favor, selecione la cateogría")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#edit_error_metodos_pago_venta").html("").removeClass("text-danger");
  }

  // Validar el stock del producto
  if (edit_monto_pagar_venta === "" || edit_monto_pagar_venta === null) {
    $("#edit_error_monto_pagar_venta")
      .html("Por favor, ingrese un numero válido")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#edit_error_monto_pagar_venta").html("").removeClass("text-danger");
  }

  if (isValid) {
    var datos = new FormData();
    datos.append("edit_id_venta_pagar", edit_id_venta_pagar);
    datos.append("edit_edit_pago_historial", edit_edit_pago_historial);
    datos.append("edit_metodos_pago_venta_historial", edit_metodos_pago_venta_historial);
    datos.append("edit_serie_numero_pago_historial", edit_serie_numero_pago_historial);
    datos.append("edit_comprobante_pago_historial", edit_comprobante_pago_historial);
    datos.append("actual_comprobante_pago_historial", actual_comprobante_pago_historial);
    datos.append("edit_monto_actual_pago", edit_monto_actual_pago);
    datos.append("edit_monto_pagar_venta", edit_monto_pagar_venta);
    $.ajax({
      url: "ajax/Historial.pago.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        var res = JSON.parse(respuesta);
        if (res.estado === "ok") {
          $("#edit_frm_pagar_deuda_venta")[0].reset();
          $(".edit_vista_previa_comprobante_pago").attr("src", "");
          $("#modal_editar_historial_pago").modal("hide");
          Swal.fire({
            title: "¡Correcto!",
            text: res.message,
            icon: "success",
          });
          mostrarVentas();
        } else {
          Swal.fire({
            title: "!Error!",
            text: res.message,
            icon: "error",
          });
        }
      }, error: function (xhr, status, error) {
        console.error("Error al recuperar los usuarios:", error);
        console.error(xhr);
        console.error(status);
      },
    });
  }
});

/*===========================================
IMPRIMIR PAGO HISTORIAL
=========================================== */
$("#tabla_historial_pago").on("click", ".btn_print_pago_historial", function (e){
  e.preventDefault();
  let idPago = $(this).attr("idPago");
  let idVenta = $(this).attr("idVenta");
  const urlDocumento = `extensiones/ticket/pago.php?id_pago=${idPago}&id_venta=${idVenta}`;
  const ventana = window.open(urlDocumento, '_blank');

  ventana.onload = () => ventana.print();
});

/* ===========================================
MOSTRAR VENTAS
=========================================== */
mostrarVentas();
/* ===========================================
CALCULAR EL TOTAL DE LA VENTA
=========================================== */
calcularTotal();
/* ===========================================
EXPORTANDO VENTA
=========================================== */
export {
  mostrarVentas
};
