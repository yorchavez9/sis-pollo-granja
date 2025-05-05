
import { mostrarVentas } from "./lista-ventas.js";

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

function mostrarIdMovimientoCaja() {
  $.ajax({
    url: "ajax/Verificar.estado.caja.ajax.php",
    type: "GET",
    dataType: "json",
    success: function (respuesta) {
      if (respuesta && respuesta.length > 0) {
        let encontrado = false;
        respuesta.forEach(function (item) {
          if (item.estado === "abierto") {
            $("#id_movimiento_caja_venta").val(item.id_movimiento);
            encontrado = true;
          }
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al recuperar los proveedores:", error);
    },
  });
}

mostrarIdMovimientoCaja();



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
  const minutos = String(ahora.getMinutes()).padStart(2, '0');
  const segundos = String(ahora.getSeconds()).padStart(2, '0');
  const ampm = horas >= 12 ? 'PM' : 'AM';
  horas = horas % 12 || 12;
  const horaFormateada = `${String(horas).padStart(2, '0')}:${minutos}:${segundos} ${ampm}`;
  $('#hora_venta').val(horaFormateada);
};
setInterval(actualizarReloj, 1000);
actualizarReloj();

function showSection() {
  $(".seccion_lista_venta").on("click", function () {
    $("#ventas_lista").show();
    $("#pos_venta").hide();
    $("#ver_pos_venta").hide();
    $("#edit_pos_venta").hide();
    $("#edit_detalle_venta_producto").empty();
    $("#ver_detalle_venta_producto").empty();
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
      folio_inicial: folioInicial
    });
  }

  function enviarDatos(datos) {
    $.ajax({
      url: "ajax/Serie.numero.venta.ajax.php",
      type: "POST",
      data: datos,
      success: function (respuesta) {
        console.log(respuesta);
        try {
          let data = JSON.parse(respuesta);
          if (data) {
            $("#numero_venta").val(data);
          } else {
            console.warn("No se encontraron datos para el tipo de comprobante seleccionado.");
            $("#numero_venta").val("");
          }
        } catch (e) {
          /* console.error("Error al parsear JSON:", e); */
        }
      },
      error: function (xhr, status, error) {
        console.error("Error AJAX:", status, error);
      }
    });
  }

  $("#comprobante_venta").change(actualizarCampos);
  actualizarCampos();
}

$('#comprobante_venta').change(function () {
  var tipoComprobante = $(this).val();
  mostrarSerieNumero(tipoComprobante);
});

showSection();

/*=============================================
LIMPIAR EL INPUT DEL INPUESTO AL HACER FOCUS
=============================================*/
function handleInputFocusAndBlur(inputSelector, defaultValue) {
  $(inputSelector).on('focus', function () {
    if ($(this).val() === defaultValue) {
      $(this).val('');
    }
  });

  $(inputSelector).on('blur', function () {
    if ($(this).val() === '') {
      $(this).val(defaultValue);
    }
  });
}
handleInputFocusAndBlur('#igv_venta', '0');


/*=============================================
SELECION DE FECHA AUTOMATICO
=============================================*/
function setDateToToday(inputId) {
  let today = new Date();
  let formattedDate = today.toISOString().split('T')[0];
  $(`#${inputId}`).val(formattedDate);
}
setDateToToday('fecha_venta');


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
                  <a href="#" id="btnAddProductoVenta" class="hover_img_a btnAddProductoVenta" idProductoAdd="${producto.id_producto}" stockProducto="${producto.stock_producto}">
                      <img class="hover_img" src="${producto.imagen_producto}" alt="${producto.nombre_producto}">
                  </a>
              </td>
              <td>${producto.nombre_categoria}</td>
              <td class="fw-bold">
                <div>S/ ${producto.precio_producto}</div>
                <div>USD ${precioBolivares}</div>
              </td>
              <td>${producto.nombre_producto}</td>
              <td class="text-center">
                  <button type="button" class="btn btn-sm" style="${getButtonStyles(producto.stock_producto)}">
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
      $("#tabla_add_producto_venta").DataTable();
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
$("#tabla_add_producto_venta").on("click touchstart", ".btnAddProductoVenta", function (e) {
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
      if(respuesta.imagen_producto){
        respuesta.imagen_producto = respuesta.imagen_producto.substring(3);
      }else{
        respuesta.imagen_producto = "vistas/img/productos/default.png";
      }
      
      // Crear una nueva fila
      let nuevaFila = `
        <tr>
          <input type="hidden" class="id_producto" value="${respuesta.id_producto}">
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

      $("#detalle_venta_producto").append(nuevaFila);

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



      $(".numero_aves_v, .peso_promedio_v, .peso_bruto_v, .peso_tara_v, .peso_merma_v, .peso_neto_v, .precio_venta, #igv_venta").on("input", function () {
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
        if (peso_bruto === 0 || peso_bruto === 0.00) {
          precio_sub_total_f = numero_aves * precio_venta;
        } else {
          // Calcular peso_neto, peso_promedio y precio_sub_total
          peso_neto_f = peso_bruto - peso_tara + peso_merma;
          peso_promedio_f = peso_neto_f / numero_aves;
          precio_sub_total_f = peso_neto_f * precio_venta;
        }

        // Formatear los valores para mostrar
        const format_peso_promedio = formateoPrecio(peso_promedio_f.toFixed(2));
        const format_peso_neto = formateoPrecio(peso_neto_f.toFixed(2));
        const format_precio_sub_total = formateoPrecio(precio_sub_total_f.toFixed(2));


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
});

/*=============================================
CALCULAR EL TOTAL DE LA VENTA
=============================================*/

async function calcularTotal(igv_venta) {

  await updateRate();

  let subtotalTotal = 0;

  $("#detalle_venta_producto tr").each(function () {
    let precio_sub_total_v = $(this).find(".precio_sub_total_v").val().replace(/,/g, '');
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
$(document).on("click touchstart", ".btnEliminarAddProductoVenta", function (e) {
  e.preventDefault();
  var idProductoEliminar = $(this).attr("idAddProducto");

  // Encuentra la fila que corresponde al producto a eliminar y elimínala
  $("#detalle_venta_producto")
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



// CREAR VENTA
$("#btn_crear_nueva_venta").click(function (e) {
  e.preventDefault();
  let isValid = true;
  let id_movimiento_caja_venta = $("#id_movimiento_caja_venta").val();
  let id_usuario_venta = $("#id_usuario_venta").val();
  let id_cliente_venta = $("#id_cliente_venta").val();
  let fecha_venta = $("#fecha_venta").val();
  let hora_venta = $("#hora_venta").val();
  let comprobante_venta = $("#comprobante_venta").val();
  let serie_venta = $("#serie_venta").val();
  let numero_venta = $("#numero_venta").val();
  let igv_venta = $("#igv_venta").val();

  if (id_cliente_venta == "" || id_cliente_venta == null) {
    $("#error_cliente_venta")
      .html("Por favor, selecione el cliente")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_cliente_venta").html("").removeClass("text-danger");
  }

  if (comprobante_venta == "" || comprobante_venta == null) {
    $("#error_comprobante_venta")
      .html("Por favor, selecione el comprobante")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_comprobante_venta").html("").removeClass("text-danger");
  }

  if (serie_venta == "" || serie_venta == null) {
    $("#error_serie_venta")
      .html("Por favor, ingrese la serie")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_serie_venta").html("").removeClass("text-danger");
  }
  if (numero_venta == "" || numero_venta == null) {
    $("#error_numero_venta")
      .html("Por favor, ingrese el número")
      .addClass("text-danger");
    isValid = false;
  } else {
    $("#error_numero_venta").html("").removeClass("text-danger");
  }

  const valoresProductos = [];

  $("#detalle_venta_producto tr").each(function () {
    const fila = $(this);
    const producto = {
      id_producto: fila.find(".id_producto").val(),
      numero_javas: fila.find(".numero_javas_v").val(),
      numero_aves: fila.find(".numero_aves_v").val(),
      peso_promedio: fila.find(".peso_promedio_v").val(),
      peso_bruto: fila.find(".peso_bruto_v").val(),
      peso_tara: fila.find(".peso_tara_v").val(),
      peso_merma: fila.find(".peso_merma_v").val(),
      peso_neto: fila.find(".peso_neto_v").val(),
      precio_venta: fila.find(".precio_venta").val()
    };
    valoresProductos.push(producto);

  });

  const productoAddVenta = JSON.stringify(valoresProductos);

  //Datos para la venta
  const subtotal = $("#subtotal_venta").text().replace(/,/g, "");
  const igv = $("#igv_venta_show").text().replace(/,/g, "");
  const total = $("#total_precio_venta").text().replace(/,/g, "");
  
  let tipo_pago = $("input[name='forma_pago_v']:checked").val();
  let metodos_pago_venta = $("#metodos_pago_venta").val();
  let pago_cuota_venta = $("#pago_cuota_venta").val();
  let recibo_de_pago_venta = $("#recibo_de_pago_venta").get(0).files[0];
  let serie_de_pago_venta = $("#serie_de_pago_venta").val();

  // Verificar si algún campo está vacío o no seleccionado
  if (!tipo_pago || !metodos_pago_venta || !pago_cuota_venta) {
    Swal.fire({
      icon: "warning",
      title: "Campos obligatorios",
      text: "Selecione el tipo de pago, el método de pago y el monto del pago",
      confirmButtonText: "Entendido",
    });

    isValid = false;
  }

  var estado_pago;
  if (tipo_pago == "contado") {
    estado_pago = "completado";
  } else {
    estado_pago = "pendiente";
  }
  let tipo_movimiento = "ingreso";
 
  if (isValid) {

    const datos = new FormData();
    datos.append("id_movimiento_caja_venta", id_movimiento_caja_venta);
    datos.append("tipo_movimiento", tipo_movimiento);
    datos.append("id_usuario_venta", id_usuario_venta);
    datos.append("id_cliente_venta", id_cliente_venta);
    datos.append("fecha_venta", fecha_venta);
    datos.append("hora_venta", hora_venta);
    datos.append("comprobante_venta", comprobante_venta);
    datos.append("serie_venta", serie_venta);
    datos.append("numero_venta", numero_venta);
    datos.append("igv_venta", igv_venta);
    datos.append("productoAddVenta", productoAddVenta);
    datos.append("subtotal", subtotal);
    datos.append("igv", igv);
    datos.append("total", total);
    datos.append("tipo_pago", tipo_pago);
    datos.append("estado_pago", estado_pago);
    datos.append("metodos_pago_venta", metodos_pago_venta);
    datos.append("pago_cuota_venta", pago_cuota_venta);
    datos.append("recibo_de_pago_venta", recibo_de_pago_venta);
    datos.append("serie_de_pago_venta", serie_de_pago_venta);
    
    $.ajax({
      url: "ajax/ventas.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (respuesta) {
        const res = JSON.parse(respuesta);
        $("#form_venta_producto")[0].reset();
        $("#detalle_venta_producto").empty();
        $("#subtotal_venta").text("00.00");
        $("#igv_venta_show").text("00.00");
        $("#total_precio_venta").text("00.00");
        $("#total_precio_venta_ves").text("00.00");
        setDateToToday('fecha_venta');
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
          if (result.isConfirmed) {
            Swal.fire({
              title: "¡Imprimiendo!",
              text: "Su comprobante se está imprimiendo.",
              icon: "success",
            });
            const documento = res.tipo_comprobante;
            const id_venta = res.id_venta;
            const urlDocumento = `extensiones/${documento}/${documento}_v.php?id_venta=${id_venta}`;
            const ventana = window.open(urlDocumento, '_blank');
            ventana.onload = () => ventana.print();
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
              title: "¡Descargando!",
              text: "Su comprobante se está descargando.",
              icon: "success",
            });
            const documento = res.tipo_comprobante;
            window.location.href = `extensiones/${documento}/${documento}_v.php?id_venta=${res.id_venta}&accion=descargar`;
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
        setDateToToday('fecha_venta');
        mostrarProductoVenta();
        mostrarSerieNumero('ticket');
        mostrarVentas();
        mostrarIdMovimientoCaja();
      },
      error: function (xhr, status, error) {
        console.error(xhr);
        console.error(status);
        console.error(error);
      },
    });
  }

});

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
      $("#id_cliente_venta").append('<option value="">Seleccione un cliente</option>');
      respuesta.forEach((cliente) => {
        $("#id_cliente_venta").append(`<option value="${cliente.id_persona}">${cliente.razon_social}</option>`);
      });
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar los clientes:", error);
    }
  });
}
mostrarClientesSelect();

/*=============================================
MOSTRAR SERIE Y NUMERO DEL COMPROBANTE
=============================================*/

limpiarModales();
mostrarProductoVenta();


export {
  mostrarClientesSelect
};
