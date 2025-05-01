
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
          document.getElementById("error_moneda").textContent = "";
        }
      } catch (error) {
      /*  console.error("Error al actualizar la tasa:", error); */
      }
    }

    setInterval(updateRate, 60 * 60 * 1000);





/*=============================================
 CALCULO DE IGV AUTOMATICAMENTE
 =============================================*/
$(document).ready(() => {
  const manejarIGV = () => {
    const checkbox = $("#igv_checkbox");
    const igVenta = $("#igv_venta");
    const valorIGV = checkbox.is(":checked") ? 18 : 0;
    igVenta.val(valorIGV);
    calcularTotal(valorIGV);
  };
  $("#igv_checkbox").change(manejarIGV);
  manejarIGV();
});

/*=============================================
 RELOJ AUTOMATICO PARA LA VENTA
 =============================================*/
const actualizarReloj = () => {
  const ahora = new Date();
  let horas = ahora.getHours();
  const minutos = String(ahora.getMinutes()).padStart(2, "0");
  const segundos = String(ahora.getSeconds()).padStart(2, "0");
  const ampm = horas >= 12 ? "PM" : "AM";
  horas = horas % 12 || 12;
  const horaFormateada = `${String(horas).padStart(
    2,
    "0"
  )}:${minutos}:${segundos} ${ampm}`;
  $("#hora_venta").val(horaFormateada);
};
setInterval(actualizarReloj, 1000);
actualizarReloj();

function showSection() {
  $(".seccion_lista_venta").on("click", function () {
    $("#ventas_lista").show();
    $("#pos_venta").hide();
    $("#ver_pos_venta").hide();
    $("#edit_pos_venta").hide();
    $("#edit_detalle_cotizacion_producto").empty();
    $("#ver_detalle_cotizacion_producto").empty();
  });
}

/*=============================================
 FUNCION PARA MOSTRAR LA SERIE Y NUMERO AUTOMATICO
 =============================================*/
function mostrarSerieNumero() {
  function actualizarCampos() {
    var selectedOption = $("#comprobante_venta option:selected");
    var tipoComprobante = selectedOption.val();
    var seriePrefijo = selectedOption.attr("seriePrefijo");
    var folioInicial = selectedOption.attr("folioInicial");

    if (!tipoComprobante || tipoComprobante === "") {
      console.warn("No se ha seleccionado ningún tipo de comprobante.");
      $("#serie_venta").val("");
      $("#numero_venta").val("");
      return;
    }

    $("#serie_venta").val(seriePrefijo);
    $("#numero_venta").val(folioInicial);
    enviarDatos({
      tipo_comprobante: tipoComprobante,
      serie_prefijo: seriePrefijo,
      folio_inicial: folioInicial,
    });
  }

  function enviarDatos(datos) {
    $.ajax({
      url: "ajax/Serie.numero.venta.ajax.php",
      type: "POST",
      data: datos,
      success: function (respuesta) {
        try {
          let data = JSON.parse(respuesta);
          if (data) {
            $("#numero_venta").val(data);
          } else {
            console.warn(
              "No se encontraron datos para el tipo de comprobante seleccionado."
            );
            $("#numero_venta").val("");
          }
        } catch (e) {
          console.error("Error al parsear JSON:", e);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error AJAX:", status, error);
      },
    });
  }

  $("#comprobante_venta").change(actualizarCampos);
  actualizarCampos();
}

$("#comprobante_venta").change(function () {
  var tipoComprobante = $(this).val();
  mostrarSerieNumero(tipoComprobante);
});
showSection();

/*=============================================
LIMPIAR EL INPUT DEL INPUESTO AL HACER FOCUS
=============================================*/
function handleInputFocusAndBlur(inputSelector, defaultValue) {
  $(inputSelector).on("focus", function () {
    if ($(this).val() === defaultValue) {
      $(this).val("");
    }
  });

  $(inputSelector).on("blur", function () {
    if ($(this).val() === "") {
      $(this).val(defaultValue);
    }
  });
}
handleInputFocusAndBlur("#igv_venta", "0");

/*=============================================
SELECIONANDO EL TIPO DE PAGO
=============================================*/
// Función para manejar el cambio de tipo de pago
const manejarTipoPago = () => {
  const tipoPago = $("input[name='forma_pago_v']:checked").val(); // Obtén el valor del tipo de pago seleccionado

  if (tipoPago === "contado") {
    $("#pago_cuota_venta").hide(); // Oculta el campo de pago de cuota
  } else {
    $("#pago_cuota_venta").show(); // Muestra el campo de pago de cuota
  }
};

// Escuchar cambios en los inputs de tipo de pago
$("input[name='forma_pago_v']").change(manejarTipoPago);

// Ejecutar al cargar la página para ajustar según el valor inicial
manejarTipoPago();

/*=============================================
SELECION DE FECHA AUTOMATICO
=============================================*/
function setDateToToday(inputId) {
  let today = new Date();
  let formattedDate = today.toISOString().split("T")[0];
  $(`#${inputId}`).val(formattedDate);
}
setDateToToday("fecha_venta");

/*=============================================
MOSTRANDO TAABLE DE PRODUCTOS PARA VENTA
=============================================*/

async function mostrarProductoVenta() {
  await updateRate();
  $.ajax({
    url: "ajax/Producto.ajax.php",
    type: "GET",
    dataType: "json",
    success: function (productos) {
      var tbody = $("#data_productos_detalle_venta");
      tbody.empty();
      productos.forEach(function (producto) {
        // Asegurarse de que la imagen existe y tiene un valor válido
        if (producto.imagen_producto) {
          producto.imagen_producto = producto.imagen_producto.substring(3);
        } else {
          producto.imagen_producto = "vistas/img/productos/default.png"; // Ruta a la imagen predeterminada
        }
        let precioBolivares = currentRate > 0 ? (producto.precio_producto * currentRate).toFixed(2) : "N/A";
        var fila = `
                        <tr>
                            <td class="text-center">
                                <a href="#" id="btnAddProductoVenta" class="hover_img_a btnAddProductoVenta" idProductoAdd="${
                                  producto.id_producto
                                }" stockProducto="${producto.stock_producto}">
                                    <img class="hover_img" src="${
                                      producto.imagen_producto
                                    }" alt="${producto.nombre_producto}">
                                </a>
                            </td>
                            <td>${producto.nombre_categoria}</td>
                            <td class="fw-bold">
                                <div>S/ ${producto.precio_producto}</div>
                                <div>USD ${precioBolivares}</div>
                            </td>
                            <td>${producto.nombre_producto}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm" style="${getButtonStyles(
                                  producto.stock_producto
                                )}">
                                    ${producto.stock_producto}
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
      $("#tabla_add_producto_cotizacion").DataTable();
    },
    error: function (xhr, status, error) {
      console.error("Error al recuperar los usuarios:", error.mensaje);
    },
  });
}

/*=============================================
FORMATEO DE PRECIOS DE LA VENTA
=============================================*/
function formateoPrecio(numero) {
  return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/*=============================================
AGREGANDO EL PRODUCTO AL DETALLE VENTA
=============================================*/
$("#tabla_add_producto_cotizacion").on(
  "click",
  ".btnAddProductoVenta",
  function (e) {
    e.preventDefault();

    let idProductoAdd = $(this).attr("idProductoAdd");
    let stockProducto = $(this).attr("stockProducto");

    if (stockProducto <= 0) {
      Swal.fire({
        title: "¡Alerta!",
        text: "¡El stock de este producto se agotado!",
        icon: "error",
      });
      return;
    } else if (stockProducto > 0 && stockProducto < 10) {
      Swal.fire({
        title: "¡Aviso!",
        text: "¡El stock de este producto se está agotando!",
        icon: "warning",
      });
    }

    let datos = new FormData();
    datos.append("idProductoAdd", idProductoAdd);

    $.ajax({
      url: "ajax/ventas.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        if (respuesta.imagen_producto) {
          respuesta.imagen_producto = respuesta.imagen_producto.substring(3);
        } else {
          respuesta.imagen_producto = "vistas/img/productos/default.png";
        }

        // Crear una nueva fila
        let nuevaFila = `
                            <tr>
                            <input type="hidden" class="id_producto_venta" value="${respuesta.id_producto}">
                            <th class="text-center align-middle d-none d-md-table-cell">
                                <a href="#" class="me-3 confirm-text btnEliminarAddProductoVenta" idAddProducto="${respuesta.id_producto}">
                                <i class="fa fa-trash fa-lg" style="color: #F1666D"></i>
                                </a>
                            </th>
                            <td><img src="${respuesta.imagen_producto}" alt="Imagen de un pollo" width="50"></td>
                            <td>${respuesta.nombre_producto}</td>
                            <td><input type="number" class="form-control form-control-sm numero_javas_v" value="0" min="0" style="width: 50px;"></td>
                            <td><input type="number" class="form-control form-control-sm numero_aves_v" value="0" min="0"></td>
                            <td><input type="number" class="form-control form-control-sm peso_bruto_v" value="0.00" min="0" step="0.01"></td>
                            <td><input type="number" class="form-control form-control-sm peso_tara_v" value="0.00" min="0" step="0.01"></td>
                            <td><input type="number" class="form-control form-control-sm peso_merma_v" value="0.00" min="0" step="0.01"></td>
                            <td><input type="number" class="form-control form-control-sm peso_promedio_v" value="0.00" min="0" readonly step="0.01"></td>
                            <td><input type="number" class="form-control form-control-sm peso_neto_v" value="0.00" min="0" readonly step="0.01"></td>
                            <td><input type="number" class="form-control form-control-sm precio_venta" value="${respuesta.precio_producto}" min="0" step="0.01"></td>
                            <td class="text-end">
                                <span style="font-weight: bold;">S/</span>
                                <input type="text" class="form-control form-control-sm precio_sub_total_v" value="0.00" readonly style="width: 100px; display: inline-block; text-align: right; font-weight: bold;">
                            </td>
                            </tr>`;

        $("#detalle_cotizacion_producto").append(nuevaFila);

        // Limpiar valores predeterminados al hacer focus
        $("input").on("focus", function () {
          if (
            !$(this).hasClass("peso_promedio_v") &&
            !$(this).hasClass("peso_neto_v") &&
            !$(this).hasClass("precio_sub_total_v")
          ) {
            if ($(this).val() === "0" || $(this).val() === "0.00") {
              $(this).val("");
            }
          }
        });

        // Aplicar blur para restablecer valores predeterminados solo en inputs tipo text o number
        $("input[type='text'], input[type='number']").on("blur", function () {
          if (
            !$(this).hasClass("peso_promedio_v") &&
            !$(this).hasClass("peso_neto_v") &&
            !$(this).hasClass("precio_sub_total_v")
          ) {
            if ($(this).val().trim() === "") {
              $(this).val("0.00"); // Valor predeterminado
            }
          }
        });

        $("input[type='number']").on("input", function () {
          let value = parseFloat($(this).val());
          if (value < 0) $(this).val(0);
        });

        $(
          ".numero_aves_v, .peso_promedio_v, .peso_bruto_v, .peso_tara_v, .peso_merma_v, .peso_neto_v, .precio_venta, #igv_venta"
        ).on("input", function () {
          const fila = $(this).closest("tr");

          // Obtener valores de los campos
          let numero_aves = parseFloat(fila.find(".numero_aves_v").val()) || 0;
          let peso_bruto = parseFloat(fila.find(".peso_bruto_v").val()) || 0;
          let peso_tara = parseFloat(fila.find(".peso_tara_v").val()) || 0;
          let peso_merma = parseFloat(fila.find(".peso_merma_v").val()) || 0;
          let precio_venta = parseFloat(fila.find(".precio_venta").val()) || 0;
          let igv_venta = parseFloat($("#igv_venta").val()) || 0;

          // Variables para cálculos
          let peso_neto_f = 0;
          let peso_promedio_f = 0;
          let precio_sub_total_f = 0;

          // Validar y calcular según peso_bruto
          if (peso_bruto === 0 || peso_bruto === 0.0) {
            precio_sub_total_f = numero_aves * precio_venta;
          } else {
            // Calcular peso_neto, peso_promedio y precio_sub_total
            peso_neto_f = peso_bruto - peso_tara + peso_merma;
            peso_promedio_f = peso_neto_f / numero_aves;
            precio_sub_total_f = peso_neto_f * precio_venta;
          }

          // Formatear los valores para mostrar
          const format_peso_promedio = formateoPrecio(
            peso_promedio_f.toFixed(2)
          );
          const format_peso_neto = formateoPrecio(peso_neto_f.toFixed(2));
          const format_precio_sub_total = formateoPrecio(
            precio_sub_total_f.toFixed(2)
          );

          // Actualizar los inputs
          fila.find(".peso_promedio_v").val(format_peso_promedio);
          fila.find(".peso_neto_v").val(format_peso_neto);
          fila.find(".precio_sub_total_v").val(format_precio_sub_total);

          // Calcular total general
          calcularTotal(igv_venta);
        });
      },
      error: function (err) {
        console.error(err);
      },
    });

    // Llamada inicial
    calcularTotal();
  }
);

/*=============================================
CALCULAR EL TOTAL DE LA VENTA
=============================================*/
async function calcularTotal(igv_venta) {
  await updateRate();
  let subtotalTotal = 0;

  $("#detalle_cotizacion_producto tr").each(function () {
    let precio_sub_total_v = $(this)
      .find(".precio_sub_total_v")
      .val()
      .replace(/,/g, "");
    let subtotal = parseFloat(precio_sub_total_v) || 0;
    subtotalTotal += subtotal;
  });

  igv_venta = isNaN(igv_venta) ? 0 : igv_venta;
  let igv = subtotalTotal * (igv_venta / 100);
  let total = subtotalTotal + igv;
  total = isNaN(total) ? 0 : total;
  var precioBolivares = currentRate > 0 ? (total * currentRate).toFixed(2) : "N/A";
  $("#subtotal_venta").text(formateoPrecio(subtotalTotal.toFixed(2)));
  $("#igv_venta_show").text(formateoPrecio(igv.toFixed(2)));
  $("#total_precio_venta").text(formateoPrecio(total.toFixed(2)));
  $("#total_precio_venta_ves").text(formateoPrecio(precioBolivares));
}

/*=============================================
ELIMINANDO EL PRODUCTO AGREGADO AL DETALLE VENT.
=============================================*/
$(document).on("click", ".btnEliminarAddProductoVenta", function (e) {
  e.preventDefault();
  var idProductoEliminar = $(this).attr("idAddProducto");
  // Encuentra la fila que corresponde al producto a eliminar y elimínala
  $("#detalle_cotizacion_producto")
    .find("tr")
    .each(function () {
      var idProducto = $(this)
        .find(".btnEliminarAddProductoVenta")
        .attr("idAddProducto");
      if (idProducto == idProductoEliminar) {
        $(this).remove();
        // Una vez eliminada la fila, recalcular el total
        calcularTotal();
        return false; // Termina el bucle una vez que se ha encontrado y eliminado la fila
      }
    });
});

/*=============================================
 MOSTRANDO Y ESCONDIENDO EL TIPO DE PAGO 
 =============================================*/
$(".tipo_pago_venta").on("click", function () {
  let valor = $(this).val();
  if (valor == "credito") {
    $("#venta_al_contado").hide();
  } else {
    $("#venta_al_contado").show();
  }
});

/*=============================================
 CREAR COTIZACION
 =============================================*/
$("#btn_crear_cotizacion").click(function (e) {
  e.preventDefault();
  if (validarFormulario()) {
    enviarCotizacion();
  }
});

/*=============================================
 VALIDAR EL FORMULARIO DE LA COTIZACION
 =============================================*/
function validarFormulario() {
  let isValid = true;
  const campos = {
    id_cliente_venta: "cliente",
    comprobante_venta: "comprobante",
  };

  for (let [id, nombre] of Object.entries(campos)) {
    const valor = $(`#${id}`).val();
    if (!valor) {
      $(`#error_${nombre}_venta`)
        .html(`Por favor, seleccione el ${nombre}`)
        .addClass("text-danger");
      isValid = false;
    } else {
      $(`#error_${nombre}_venta`).html("").removeClass("text-danger");
    }
  }

  return isValid;
}

/*=============================================
 CAPTURANDO DATOS DEL PRODUCTO
 =============================================*/
function recolectarDatosProductos() {
  const productos = [];
  $("#detalle_cotizacion_producto tr").each(function () {
    productos.push({
      id_producto_venta: $(this).find(".id_producto_venta").val(),
      numero_javas: $(this).find(".numero_javas_v").val(),
      numero_aves: $(this).find(".numero_aves_v").val(),
      peso_promedio: $(this).find(".peso_promedio_v").val(),
      peso_bruto: $(this).find(".peso_bruto_v").val(),
      peso_tara: $(this).find(".peso_tara_v").val(),
      peso_merma: $(this).find(".peso_merma_v").val(),
      peso_neto: $(this).find(".peso_neto_v").val(),
      precio_venta: $(this).find(".precio_venta").val(),
    });
  });
  return JSON.stringify(productos);
}

/*=============================================
 ENVIAR COTIZACION A AJAX
 =============================================*/
function enviarCotizacion() {
  const datos = new FormData();
  const tipo_pago = $("input[name='forma_pago_v']:checked").val();

  const camposFormulario = {
    id_usuario_cotizacion: $("#id_usuario_cotizacion").val(),
    id_cliente_venta: $("#id_cliente_venta").val(),
    fecha_venta: $("#fecha_venta").val(),
    hora_venta: $("#hora_venta").val(),
    comprobante_venta: $("#comprobante_venta").val(),
    serie_cotizacion: $("#serie_venta").val(),
    validez_contizacion: $("#validez_contizacion").val(),
    igv_venta: $("#igv_venta").val(),
    productoAddVenta: recolectarDatosProductos(),
    subtotal: $("#subtotal_venta").text().replace(/,/g, ""),
    igv: $("#igv_venta_show").text().replace(/,/g, ""),
    total: $("#total_precio_venta").text().replace(/,/g, ""),
    tipo_pago: tipo_pago,
    estado_pago: tipo_pago === "contado" ? "completado" : "pendiente",
    metodos_pago_venta: $("#metodos_pago_venta").val(),
  };

  for (let [key, value] of Object.entries(camposFormulario)) {
    datos.append(key, value);
  }

  $.ajax({
    url: "ajax/Cotizacion.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    success: function (respuesta) {
      manejarRespuestaCotizacion(JSON.parse(respuesta));
    },
    error: function (xhr, status, error) {
      console.error(xhr, status, error);
      mostrarError("Error al procesar la solicitud");
    },
  });
}

/*=============================================
 RESPUESTA DE LA COTIZACION
 =============================================*/
function manejarRespuestaCotizacion(res) {
  limpiarFormulario();

  if (!res.status) {
    mostrarError(res.message);
    return;
  }
  const urlDocumento = `extensiones/${res.tipo_comprobante}/${res.tipo_comprobante}_c.php?id_cotizacion=${res.id_cotizacion}`;
  mostrarOpcionesComprobante(res);

  // Realizar una solicitud HTTP (como un GET)
  fetch(urlDocumento)
    .then((response) => response.text())
    .then((data) => {
      // Manejar la respuesta aquí si es necesario
      console.log(data);
    })
    .catch((error) => {
      console.error("Error al hacer la solicitud:", error);
    });
}

/*=============================================
 LIMPIAR LOS FORMULARIOS
 =============================================*/
function limpiarFormulario() {
  $("#form_contizacion_venta")[0].reset();
  $("#detalle_cotizacion_producto").empty();
  $("#subtotal_venta").text("00.00");
  $("#igv_venta_show").text("00.00");
  $("#total_precio_venta").text("00.00");
  $("#total_precio_venta_ves").text("00.00");
  setDateToToday("fecha_venta");
  mostrarProductoVenta();
}

/*=============================================
 MOSTRAR LAS OPCIONES DE LOS COMPROBANTES
 =============================================*/
function mostrarOpcionesComprobante(res) {
  Swal.fire({
    title: res.message,
    text: "¿Qué desea hacer con el comprobante?",
    icon: "success",
    showCancelButton: true,
    confirmButtonColor: "#28C76F",
    cancelButtonColor: "#F52E2F",
    confirmButtonText: "Imprimir",
    cancelButtonText: "Descargar",
    footer: `
            <div class="mt-3">
                <button type="button" id="btnWhatsApp" class="btn me-3" style="background: #28C76F; color: white">
                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                </button>
                <button type="button" id="btnEmail" class="btn btn-primary">
                    <i class="fas fa-envelope me-2"></i>Enviar por Correo
                </button>
            </div>
        `,
    didRender: () => {
      document.getElementById("btnWhatsApp").addEventListener("click", () => {
        enviarWhatsApp(res.tipo_comprobante, res.id_cotizacion, res.telefono);
      });
      document.getElementById("btnEmail").addEventListener("click", () => {
        enviarCorreo(res.tipo_comprobante, res.id_cotizacion, res.email);
      });
    },
  }).then((result) => {
    if (result.isConfirmed) {
      imprimirComprobante(res);
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      descargarComprobante(res);
    }
  });
}

/*=============================================
 IMPRIMIR COMPROBANTE
 =============================================*/
function imprimirComprobante(res) {
  const urlDocumento = `extensiones/${res.tipo_comprobante}/${res.tipo_comprobante}/cotizacion/${res.tipo_comprobante}_c_${res.id_cotizacion}.pdf`;
  const ventana = window.open(urlDocumento, "_blank");
  ventana.onload = () => {
    ventana.print();
    mostrarExito("Impreso con éxito", "Su comprobante se ha impreso.");
  };
}

/*=============================================
 DESCARGAR COMPROBANTE
 =============================================*/
function descargarComprobante(res) {
  const enlace = document.createElement("a");
  enlace.href = `extensiones/${res.tipo_comprobante}/${res.tipo_comprobante}/cotizacion/${res.tipo_comprobante}_c_${res.id_cotizacion}.pdf`;
  enlace.download = `${res.tipo_comprobante}_c_${res.id_cotizacion}.pdf`;
  enlace.click();
  mostrarExito("Descargado con éxito", "Su comprobante se descargó.");
}

/*=============================================
 ENVIAR PRO WHATSAPP EL COMPROBANTE
 =============================================*/
async function enviarWhatsApp(documento, id_cotizacion, num_telefono) {
  try {
    // Obtener datos de la cotización
    const response = await fetch(
      `ajax/Lista.cotizacion.ajax.php?id_cotizacion_whatsapp=${id_cotizacion}`
    );
    const cotizacion = await response.json();

    // Verificar si la cotización se obtuvo correctamente
    if (!cotizacion.id_cotizacion) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo obtener la cotización",
      });
      return;
    }

    // Pedir el número de teléfono para WhatsApp
    const { value: telefono } = await Swal.fire({
      title: "Ingrese el número de WhatsApp",
      input: "text", // Tipo de entrada correcto
      inputLabel: "Incluya el código de país (Ej: 51912345678)",
      inputValue: num_telefono, // Valor predeterminado
      inputValidator: (value) => {
        if (!value) return "Debe ingresar un número";
        if (!/^\d+$/.test(value)) return "Solo se permiten números";
        if (value.length < 10) return "Número inválido";
      },
    });

    if (telefono) {
      // Asegurarse de que la URL del documento sea correcta
      const documentoUrl = `${window.location.origin}/extensiones/${documento}/${documento}_c.php?id_cotizacion=${id_cotizacion}`;
      const mensaje = encodeURIComponent(
        `*COTIZACIÓN #${cotizacion.id_cotizacion}*\n\n` +
          `📅 Fecha: ${cotizacion.fecha_cotizacion}\n` +
          `💰 Total: USD. ${cotizacion.total_cotizacion}\n\n` +
          `Adjunto el comprobante: ${documentoUrl}` // Adjuntar el enlace al PDF
      );

      // Abrir el enlace de WhatsApp con el mensaje
      window.open(
        `https://api.whatsapp.com/send?phone=${telefono}&text=${mensaje}`
      );
    }
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se pudo enviar el mensaje",
    });
  }
}

/*=============================================
 ENVIAR POR CORREO EL COMPROBANTE
 =============================================*/
async function enviarCorreo(documento, id_cotizacion, correo) {
  const { value: email } = await Swal.fire({
    title: "Ingrese el correo electrónico",
    input: "email", // Tipo de entrada correcta
    inputLabel: "Correo del destinatario",
    inputValue: correo, // Valor predeterminado
    inputValidator: (value) => {
      if (!value) return "Debe ingresar un correo";
      if (!/\S+@\S+\.\S+/.test(value)) return "Correo inválido";
    },
  });

  if (email) {
    let timerInterval;
    Swal.fire({
      title: "Enviando al correo...",
      html: "Por favor, espere...",
      timer: 0,
      timerProgressBar: true,
      didOpen: () => {
        Swal.showLoading();
        const timer = Swal.getPopup().querySelector("b");
        timerInterval = setInterval(() => {
          timer.textContent = `${Swal.getTimerLeft()}`;
        }, 100);
      },
      willClose: () => {
        clearInterval(timerInterval);
      },
    });

    try {
      const formData = new FormData();
      formData.append("documento", documento);
      formData.append("id_cotizacion", id_cotizacion);
      formData.append("email", email);
      const response = await fetch("ajax/Email.cotizacion.ajax.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      Swal.close();

      if (data.success) {
        mostrarExito("¡Enviado con éxito!", "Comprobante enviado por correo");
      } else {
        mostrarError("Error al enviar", data.message || "Error en el envío");
      }
    } catch (error) {
      console.error("Error:", error);
      Swal.close();
      mostrarError("Error", "No se pudo enviar el correo");
    }
  }
}

/*=============================================
 MENSJAE Y ALERTAS DE EXITO
 =============================================*/
function mostrarExito(titulo, texto) {
  Swal.fire({
    title: titulo,
    text: texto,
    icon: "success",
  });
}

/*=============================================
 MENSAJE Y ALERTAS DE ERROR
 =============================================*/
function mostrarError(titulo, texto = "") {
  Swal.fire({
    title: titulo,
    text: texto,
    icon: "error",
  });
}

/*=============================================
LIMPINADO LOS MODALES
=============================================*/
function limpiarModales() {
  $(".btn_modal_ver_close_usuario").click(function () {
    $("#mostrar_data_roles").text("");
  });
  $(".btn_modal_editar_close_usuario").click(function () {
    $("#formEditUsuario")[0].reset();
  });
  // Cuando el mouse entra en la imagen
  $(".hover_img").mouseenter(function () {
    $(this).css("transform", "scale(1.2)"); // Agranda la imagen
  });

  // Cuando el mouse sale de la imagen
  $(".hover_img").mouseleave(function () {
    $(this).css("transform", "scale(1)"); // Restaura el tamaño original
  });
}

/*=============================================
MOSTRAR CLIENTES EN EL SELECT
=============================================*/
function mostrarClientesSelect() {
  $.ajax({
    url: "ajax/Cliente.ajax.php",
    method: "GET",
    dataType: "json",
    success: function (respuesta) {
      $("#id_cliente_venta").empty();
      $("#id_cliente_venta").append(
        '<option value="">Seleccione un cliente</option>'
      );
      respuesta.forEach((cliente) => {
        $("#id_cliente_venta").append(
            `<option value="${cliente.id_persona}">
                ${cliente.razon_social}
                ${cliente.correo ? `<i class="fas fa-envelope" title="Tiene correo"></i>` : `<i class="fas fa-envelope-slash" title="No tiene correo"></i>`}
                ${cliente.telefono ? `<i class="fas fa-phone" title="Tiene teléfono"></i>` : `<i class="fas fa-phone-slash" title="No tiene teléfono"></i>`}
            </option>`
        );
    });
    
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar los clientes:", error);
    },
  });
}
mostrarClientesSelect();

/*=============================================
MOSTRAR SERIE Y NUMERO DEL COMPROBANTE
=============================================*/

limpiarModales();
mostrarProductoVenta();

export { mostrarClientesSelect };
