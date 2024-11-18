
import { mostrarVentas } from "./lista-ventas.js";

$(document).ready(function () {

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
  function mostrarSerieNumero(tipoComprobante) {
    $.ajax({
      url: "ajax/Serie.numero.venta.ajax.php",
      type: "POST",
      data: { tipoComprobante },
      success: function (respuesta) {
        try {
          if (respuesta.trim() !== "") {
            let data = JSON.parse(respuesta);
            $('#serie_venta').val(data[0].serie_comprobante);
            $('#numero_venta').val(data[0].num_comprobante);
          }
        } catch (e) {
          console.error("Error al parsear JSON:", e);
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr, status, error);
      }
    });
  }
  mostrarSerieNumero('ticket');
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
  SELECION DE TIPO DE PAGO (EFECYIVO O YAPE)
  =============================================*/
  function tipoPagoYE() {
    // Obtener todos los elementos <a> con la clase "paymentmethod"
    var paymentMethodLinks = document.querySelectorAll("a.tipo_pago_e_y");

    // Iterar sobre cada elemento <a>
    paymentMethodLinks.forEach(function (link) {
      // Añadir un evento de clic a cada elemento <a>
      link.addEventListener("click", function () {
        // Obtener el radio button dentro del elemento <a> actual
        var radioButton = this.querySelector(".tipo_pago_venta");

        // Verificar si el radio button existe
        if (radioButton) {
          // Alternar el estado del radio button
          radioButton.checked = !radioButton.checked;
        } else {
          console.warn("Radio button no encontrado dentro de la etiqueta <a>");
        }
      });
    });
  }


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

  function mostrarProductoVenta() {

    $.ajax({
      url: "ajax/Producto.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (productos) {
        var tbody = $("#data_productos_detalle_venta");
        tbody.empty();
        productos.forEach(function (producto) {
          producto.imagen_producto = producto.imagen_producto.substring(3);
          var fila = `
                  <tr>
                      <td class="text-center">
                          <a href="#" id="btnAddProductoVenta" class="hover_img_a btnAddProductoVenta" idProductoAdd="${producto.id_producto}" stockProducto="${producto.stock_producto}">
                              <img class="hover_img" src="${producto.imagen_producto}" alt="${producto.imagen_producto}">
                          </a>
                      </td>
                      <td>${producto.nombre_categoria}</td>
                      <td class="fw-bold">S/ ${producto.precio_producto}</td>
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
  $("#tabla_add_producto_venta").on("click", ".btnAddProductoVenta", function (e) {
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
        respuesta.imagen_producto = respuesta.imagen_producto.substring(3);

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
          <td><input type="number" class="form-control form-control-sm peso_promedio_v" value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_bruto_v" value="0.00" min="0" readonly step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_tara_v" value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_merma_v" value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_neto_v" value="0.00" min="0" readonly step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm precio_venta" value="${respuesta.precio_producto}" min="0" step="0.01"></td>
          <td class="text-end">
            <span style="font-weight: bold;">S/</span>
            <input type="text" class="form-control form-control-sm precio_sub_total_v" value="0.00" readonly style="width: 100px; display: inline-block; text-align: right; font-weight: bold;">
          </td>
        </tr>`;

        $("#detalle_venta_producto").append(nuevaFila);

        // Eventos para inputs
        $("input[type='number']").on("focus", function () {
          if ($(this).val() === "0" || $(this).val() === "0.00") {
            $(this).val("");
          }
        });

        $("input[type='number']").on("input", function () {
          let value = parseFloat($(this).val());
          if (value < 0) $(this).val(0);
        });

        $(".numero_aves_v, .peso_promedio_v, .peso_tara_v, .peso_merma_v, .precio_venta, #igv_venta").on("input", function () {
          const fila = $(this).closest("tr");

          let numero_aves = parseFloat(fila.find(".numero_aves_v").val()) || 0;
          let peso_promedio = parseFloat(fila.find(".peso_promedio_v").val()) || 0;
          let peso_tara = parseFloat(fila.find(".peso_tara_v").val()) || 0;
          let peso_merma = parseFloat(fila.find(".peso_merma_v").val()) || 0;
          let precio_venta = parseFloat(fila.find(".precio_venta").val()) || 0;
          let igv_venta = parseFloat($("#igv_venta").val()) || 0;

          // Calcular valores
          const peso_bruto = peso_promedio * numero_aves;
          const peso_neto = peso_bruto - peso_tara - peso_merma;
          const precio_sub_total = peso_neto * precio_venta;

          // Actualizar los inputs
          fila.find(".peso_bruto_v").val(peso_bruto.toFixed(2));
          fila.find(".peso_neto_v").val(peso_neto.toFixed(2));
          fila.find(".precio_sub_total_v").val(precio_sub_total.toFixed(2));

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

  function calcularTotal(igv_venta) {
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
    $("#subtotal_venta").text(formateoPrecio(subtotalTotal.toFixed(2)));
    $("#igv_venta_show").text(formateoPrecio(igv.toFixed(2)));
    $("#total_precio_venta").text(formateoPrecio(total.toFixed(2)));
  }



  /*=============================================
  ELIMINANDO EL PRODUCTO AGREGADO AL DETALLE VENT.
  =============================================*/
  $(document).on("click", ".btnEliminarAddProductoVenta", function (e) {
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

  // CREAR VENTA
  $("#btn_crear_nueva_venta").click(function (e) {
    e.preventDefault();
    let isValid = true;
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

    const valoresProductos = [];

    $("#detalle_venta_producto tr").each(function () {
      const fila = $(this);
      const producto = {
        id_producto_venta: fila.find(".id_producto_venta").val(),
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
    const tipo_pago = $("input[name='forma_pago_v']:checked").val();

    var estado_pago;
    if (tipo_pago == "contado") {
      estado_pago = "completado";
    } else {
      estado_pago = "pendiente";
    }
    var pago_tipo = $("input[name='pago_tipo_v']:checked").val();
    var pago_e_y;
    if (pago_tipo == "efectivo") {
      pago_e_y = "efectivo";
    } else {
      pago_e_y = "yape";
    }
    if (isValid) {

      const datos = new FormData();
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
      datos.append("pago_e_y", pago_e_y);

      $.ajax({
        url: "ajax/ventas.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          console.log(respuesta);
          return;
          $("#form_venta_producto")[0].reset();
          $("#detalle_venta_producto").empty();
          $("#subtotal_venta").text("00.00");
          $("#igv_venta_show").text("00.00");
          $("#total_precio_venta").text("00.00");
          
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
              const documento = res.tipo_documento;
              const id_egreso = res.id_egreso;
              const urlDocumento = `extensiones/${documento}/${documento}.php?id_egreso=${id_egreso}`;
              const ventana = window.open(urlDocumento, '_blank');
              ventana.onload = () => ventana.print();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              Swal.fire({
                title: "¡Descargando!",
                text: "Su comprobante se está descargando.",
                icon: "success",
              });
              const documento = res.tipo_documento;
              window.location.href = `extensiones/${documento}/${documento}.php?id_egreso=${res.id_egreso}&accion=descargar`;
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

          mostrarProductoVenta();
          mostrarSerieNumero('ticket');
          mostrarVentas();
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

  limpiarModales();
  mostrarProductoVenta();
  tipoPagoYE();
});
