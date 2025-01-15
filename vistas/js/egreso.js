$(document).ready(function () {

  function mostrarIdMovimientoCaja() {
    $.ajax({
      url: "ajax/Caja.general.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {
        console.log(respuesta);
        if (respuesta && respuesta.length > 0) {
          let encontrado = false;
          respuesta.forEach(function (item) {
            if (item.estado === "abierto") {
              $("#id_movimiento_caja_compra").val(item.id_movimiento);
              encontrado = true;
            }else{
              Swal.fire({
                title: "¡Aviso!",
                text: "Aperture la caja del día.",
                icon: "warning",
              });
            }
          });

        } else {
          $.ajax({
            url: "ajax/Sesion.usuario.ajax.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
              console.log(response);
              if(response.estado === "success") {
                Swal.fire({
                  title: "¡Aviso!",
                  text: "Aperture la caja del día. caso contrario no podrá realizar la compra y venta",
                  icon: "warning",
                });
              }
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
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const data = await response.json();
        return data.rates.VES;
      } catch (error) {
        console.error('Error obteniendo tasas', error);
        try {
          const response = await fetch('https://open.er-api.com/v6/latest/USD');
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
          currentRate = rate; // Asigna la tasa de cambio al valor global
          document.getElementById("error_moneda").textContent = "";
        }
      } catch (error) {
      /*  console.error("Error al actualizar la tasa:", error); */
      }
    }

    setInterval(updateRate, 60 * 60 * 1000);



  // SELECCIONAR LA FECHA AUTOMATICAMENTE
  const setDateToToday = (inputId) => {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0];
    $(`#${inputId}`).val(formattedDate);
  };
  setDateToToday('fecha_egreso');

  // RELOJ AUTOMATICO
  const actualizarReloj = () => {
    const ahora = new Date();
    let horas = ahora.getHours();
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const segundos = String(ahora.getSeconds()).padStart(2, '0');
    const ampm = horas >= 12 ? 'PM' : 'AM';
    horas = horas % 12 || 12;
    const horaFormateada = `${String(horas).padStart(2, '0')}:${minutos}:${segundos} ${ampm}`;
    $('#hora_egreso').val(horaFormateada);
  };
  setInterval(actualizarReloj, 1000);
  actualizarReloj();

  // SELECCION DE TIPO DE PAGO YAPE O EFECTIVO 
  const tipoPago = () => {
    const paymentMethodLinks = document.querySelectorAll("a.paymentmethod");

    paymentMethodLinks.forEach(link => {
      link.addEventListener("click", () => {
        // Buscar el radio button dentro del enlace
        const radioButton = link.querySelector(".tipo_pago_egreso");

        // Verificar si el radio button existe
        if (radioButton) {
          // Alternar el estado del radio button
          radioButton.checked = !radioButton.checked;
        } else {
          console.warn("No se encontró el radio button con la clase .tipo_pago_egreso en el enlace.");
        }
      });
    });
  };

  tipoPago();

  // MOSTRAR PRODUCTOS PARA LA COMPRA
  const mostrarProductos = () => {
    $.ajax({
      url: "ajax/Producto.ajax.php",
      type: "GET",
      dataType: "json",
      success: (productos) => {
        const tbody = $("#data_productos_detalle");
        tbody.empty();
        productos.forEach((producto) => {
          // Asegurarse de que la imagen existe y tiene un valor válido
          if (producto.imagen_producto) {
            producto.imagen_producto = producto.imagen_producto.substring(3);
          } else {
            producto.imagen_producto = "vistas/img/productos/default.png"; // Ruta a la imagen predeterminada
          }

          const fila = `
          <tr>
            <td class="text-center">
              <a href="#" class="hover_img_a btnAddProducto" idProductoAdd="${producto.id_producto}">
                <img class="hover_img" src="${producto.imagen_producto}" alt="${producto.imagen_producto}">
              </a>
            </td>
            <td>${producto.nombre_categoria}</td>
            <td>${producto.nombre_producto}</td>
            <td class="text-center">
              <button type="button" class="btn btn-sm" style="${getButtonStyles(producto.stock_producto)}">
                ${producto.stock_producto}
              </button>
            </td>
          </tr>`;
          tbody.append(fila);
        });
        $("#tabla_add_producto").DataTable();
      },
      error: (xhr, status, error) => {
        console.error("Error al recuperar los productos:", error);
      },
    });
  };

  // MOSTRAR ESTILOS DE BOTONES DEL STOCK DE PRODUCTOS
  const getButtonStyles = (stock) => {
    const styles = {
      high: "background-color: #28C76F; color: white; border: none;",
      medium: "background-color: #FF9F43; color: white; border: none;",
      low: "background-color: #FF4D4D; color: white; border: none;"
    };
    if (stock > 20) {
      return styles.high;
    } else if (stock >= 10) {
      return styles.medium;
    } else {
      return styles.low;
    }
  };


  // Agregar productos a la tabla detalle de la compra
  $("#tabla_add_producto").on("click touchstart", ".btnAddProducto", function (e) {
    e.preventDefault();

    const idProductoAdd = $(this).attr("idProductoAdd");
    const datos = new FormData();
    datos.append("idProductoAdd", idProductoAdd);

    $.ajax({
      url: "ajax/Compra.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        // Ajustar la ruta de la imagen
        
        if(respuesta.imagen_producto){
          respuesta.imagen_producto = respuesta.imagen_producto.substring(3);
        }else{
          respuesta.imagen_producto = "vistas/img/productos/default.png"; // Ruta a la imagen predeterminada
        }

        // Crear nueva fila con datos del producto
        const nuevaFila = `
        <tr>
          <input type="hidden" class="id_producto_egreso" value="${respuesta.id_producto}">
          <th class="text-center align-middle d-none d-md-table-cell">
            <a href="#" class="me-3 confirm-text btnEliminarAddProducto" idAddProducto="${respuesta.id_producto}" fotoUsuario="${respuesta.imagen_producto}">
              <i class="fa fa-trash fa-lg" style="color: #F1666D"></i>
            </a>
          </th>
          <td><img src="${respuesta.imagen_producto}" alt="Imagen de un producto" width="80"></td>
          <td>${respuesta.nombre_producto}</td>
          <td><input type="number" class="form-control form-control-sm numero_javas" value="0" min="0"></td>
          <td><input type="number" class="form-control form-control-sm numero_aves" value="0" min="0"></td>
          <td><input type="number" class="form-control form-control-sm peso_promedio" readonly value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_bruto" value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_tara" value="0.00" style="width: 60px;" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_merma" value="0.00" style="width: 60px;" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm peso_neto" readonly value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm precio_compra" value="0.00" min="0" step="0.01"></td>
          <td><input type="number" class="form-control form-control-sm precio_venta" value="0.00" min="0" step="0.01"></td>
          <td class="text-end">
            <span style="font-weight: bold;">USD</span>
            <input type="text" class="form-control form-control-sm precio_sub_total" value="0.00" readonly style="width: 100px; display: inline-block; text-align: right; font-weight: bold;">
          </td>
        </tr>`;

        $("#detalle_egreso_producto").append(nuevaFila);

        // Evento para limpiar valores predeterminados al hacer focus
        $("input").on("focus", function () {
          // Excluir los campos específicos por su clase
          if (
            !$(this).hasClass("peso_promedio") &&
            !$(this).hasClass("peso_neto") &&
            !$(this).hasClass("precio_sub_total")
          ) {
            if ($(this).val() === "0" || $(this).val() === "0.00") {
              $(this).val(""); // Borra el valor cuando se hace focus
            }
          }
        });

        // Evento para validar que no se ingresen valores negativos
        $("input[type='number']").on("input", function () {
          let value = parseFloat($(this).val());
          if (value < 0) {
            $(this).val(0); // Establece el valor a 0 si es negativo
          }
        });

        // Evento para calcular el subtotal al cambiar cantidad_aves o peso_promedio
        $(".numero_aves, .peso_promedio, .peso_bruto, .peso_tara, .peso_merma, .peso_neto, .precio_compra, #impuesto_egreso").on("input", function () {
          const fila = $(this).closest("tr");

          let numero_aves = parseFloat(fila.find(".numero_aves").val()) || 0;
          let peso_bruto = parseFloat(fila.find(".peso_bruto").val()) || 0.00;
          let peso_tara = parseFloat(fila.find(".peso_tara").val()) || 0;
          let peso_merma = parseFloat(fila.find(".peso_merma").val()) || 0;
          let precio_compra = parseFloat(fila.find(".precio_compra").val()) || 0;
          let impuesto_egreso = parseFloat($("#impuesto_egreso").val()) || 0;

          // Variables para cálculos
          let peso_neto_f = 0;
          let peso_promedio_f = 0;
          let precio_sub_total_f = 0;
          
          if(peso_bruto === 0 || peso_bruto == 0.00){
            precio_sub_total_f = numero_aves * precio_compra;
          }else{
            // Calcular peso_bruto, peso_neto y precio_sub_total
            peso_neto_f = peso_bruto - peso_tara - peso_merma;
            peso_promedio_f = peso_neto_f / numero_aves;
            precio_sub_total_f = peso_neto_f * precio_compra;
          }

          // Formatear los valores
          const format_peso_promedio = formateoPrecio(peso_promedio_f.toFixed(2));
          const format_peso_neto = formateoPrecio(peso_neto_f.toFixed(2));
          const format_precio_sub_total = formateoPrecio(precio_sub_total_f.toFixed(2));


          // Actualizar los inputs con los valores calculados
          fila.find(".peso_promedio").val(format_peso_promedio);
          fila.find(".peso_neto").val(format_peso_neto);
          fila.find(".precio_sub_total").val(format_precio_sub_total);

          // Calcular y mostrar el total
          calcularTotal(impuesto_egreso);
        });
      },
    });

    // Llamada inicial para calcular el total
    calcularTotal();

    $(document).ready(function () {
      calcularTotal();
    });
  });

  // ELIMINAR EL PRODUCTO AGREGADO A LA TABLA DETALLE
  $(document).on("click touchstart", ".btnEliminarAddProducto", function (e) {
    e.preventDefault();
    let idProductoEliminar = $(this).attr("idAddProducto");
    $("#detalle_egreso_producto").find("tr").each(function () {
      let idProducto = $(this).find(".btnEliminarAddProducto").attr("idAddProducto");
      if (idProducto === idProductoEliminar) {
        $(this).remove();
        calcularTotal();
        return false;
      }
    });
  });

  //FORMATEO DE PRECIOS
  function formateoPrecio(numero) {
    return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  //CALCULAR EL TOTAL DE CADA DETALLE PRODUCTO
  async function calcularTotal(impuesto) {
    await updateRate();
    let subtotalTotal = 0;
    $("#detalle_egreso_producto tr").each(function () {
      let precio_sub_total = $(this).find(".precio_sub_total").val().replace(/,/g, '');
      let subtotal = parseFloat(precio_sub_total) || 0;
      subtotalTotal += subtotal;
    });
    impuesto = isNaN(impuesto) ? 0 : impuesto;
    let igv = subtotalTotal * (impuesto / 100);
    let total = subtotalTotal + igv;
    total = isNaN(total) ? 0 : total;
    var precioBolivares = currentRate > 0 ? (total * currentRate).toFixed(2) : "N/A";

    $("#subtotal_egreso").text(formateoPrecio(subtotalTotal.toFixed(2)));
    $("#igv_egreso").text(formateoPrecio(igv.toFixed(2)));
    $("#total_precio_egreso").text(formateoPrecio(total.toFixed(2)));
    $("#total_precio_egreso_ves").text(formateoPrecio(precioBolivares));
    
  }


  // FUNCION PARA CONFIGURAR EL VALOR POR DEFECTO EN LOS INPUTS AL HACER FOCUS Y BLUR
  function configurarInputConValorPorDefecto(selector, valorPorDefecto) {
    let input = $(selector);
    input.val(valorPorDefecto);
    input.on("focus", function () {
      if ($(this).val() === valorPorDefecto) $(this).val("");
    });
    input.on("blur", function () {
      if ($(this).val().trim() === "") $(this).val(valorPorDefecto);
    });
  }

  configurarInputConValorPorDefecto("#impuesto_egreso", "0.00");


  // CREAR COMPRA
  $("#btn_crear_compra").click((e) => {
    e.preventDefault();
    let isValid = true;

    // Validación de campos
    const id_movimiento_caja_compra = $("#id_movimiento_caja_compra").val();
    const id_usuario_egreso = $("#id_usuario_egreso").val();
    const id_proveedor_egreso = $("#id_proveedor_egreso").val();
    const fecha_egreso = $("#fecha_egreso").val();
    const hora_egreso = $("#hora_egreso").val();
    const tipo_comprobante_egreso = $("#tipo_comprobante_egreso").val();
    const serie_comprobante = $("#serie_comprobante").val();
    const num_comprobante = $("#num_comprobante").val();
    const impuesto_egreso = $("#impuesto_egreso").val();

    // Si falta el proveedor
    if (!id_proveedor_egreso) {
      $("#error_egreso_proveedor").html("Por favor, seleccione el proveedor").addClass("text-danger");
      isValid = false;
    } else {
      $("#error_egreso_proveedor").html("").removeClass("text-danger");
    }

    if (!fecha_egreso){
      $("#error_egreso_fecha").html("Por favor, seleccione el proveedor").addClass("text-danger");
      isValid = false;
    }else{
      $("#error_egreso_fecha").html("").removeClass("text-danger");
    }

    // Si falta el tipo de comprobante
    if (!tipo_comprobante_egreso) {
      $("#error_compra_comprobante").html("Por favor, selecione el tipo de comprobante").addClass("text-danger");
      isValid = false;
    } else {
      $("#error_compra_comprobante").html("").removeClass("text-danger");
    }


    // Capturar los productos de la compra
    const valoresProductos = [];
    $("#detalle_egreso_producto tr").each(function () {
      const fila = $(this);
      const producto = {
        idProductoEgreso: fila.find(".id_producto_egreso").val(),
        numero_javas: fila.find(".numero_javas").val(),
        numero_aves: fila.find(".numero_aves").val(),
        peso_promedio: fila.find(".peso_promedio").val(),
        peso_bruto: fila.find(".peso_bruto").val(),
        peso_tara: fila.find(".peso_tara").val(),
        peso_merma: fila.find(".peso_merma").val(),
        peso_neto: fila.find(".peso_neto").val(),
        precio_compra: fila.find(".precio_compra").val(),
        precio_venta: fila.find(".precio_venta").val()
      };
      valoresProductos.push(producto);
    });
    const productoAddEgreso = JSON.stringify(valoresProductos);

    // Datos para la compra
    const subtotal = $("#subtotal_egreso").text().replace(/,/g, "");
    const igv = $("#igv_egreso").text().replace(/,/g, "");
    const total = $("#total_precio_egreso").text().replace(/,/g, "");
    const tipo_pago = $("input[name='forma_pago']:checked").val();
    const estado_pago = tipo_pago === "contado" ? "completado" : "pendiente";
    const pago_tipo = $("input[name='pago_tipo']:checked").val();
    const pago_e_y = pago_tipo === "efectivo" ? "efectivo" : (pago_tipo === "yape" ? "yape" : "");
    const tipo_movimiento = "egreso";

    // Si la validación es correcta
    if (isValid) {
      const datos = new FormData();
      datos.append("id_movimiento_caja_compra", id_movimiento_caja_compra);
      datos.append("tipo_movimiento", tipo_movimiento);
      datos.append("id_proveedor_egreso", id_proveedor_egreso);
      datos.append("id_usuario_egreso", id_usuario_egreso);
      datos.append("fecha_egreso", fecha_egreso);
      datos.append("hora_egreso", hora_egreso);
      datos.append("tipo_comprobante_egreso", tipo_comprobante_egreso);
      datos.append("serie_comprobante", serie_comprobante);
      datos.append("num_comprobante", num_comprobante);
      datos.append("impuesto_egreso", impuesto_egreso);
      datos.append("productoAddEgreso", productoAddEgreso);
      datos.append("subtotal", subtotal);
      datos.append("igv", igv);
      datos.append("total", total);
      datos.append("tipo_pago", tipo_pago);
      datos.append("estado_pago", estado_pago);
      datos.append("pago_e_y", pago_e_y);

      // Enviar datos mediante AJAX
      $.ajax({
        url: "ajax/Compra.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: (respuesta) => {
 
          const res = JSON.parse(respuesta);

          // Verificar respuesta de la API
          if (res.estado === "ok") {
            // Limpiar formulario y tabla
            $("#form_compra_producto")[0].reset();
            $("#detalle_egreso_producto").empty();
            $("#subtotal_egreso").text("00.00");
            $("#igv_egreso").text("00.00");
            $("#total_precio_egreso").text("00.00");
            $("#total_precio_egreso_ves").text("00.00");
            setDateToToday('fecha_egreso');

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
              footer: '<h2 style="font-size: 50px">👍</h2>',
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

            // Llamar funciones adicionales
            setDateToToday('fecha_egreso');
            mostrarProductos();
            mostrarIdMovimientoCaja();
          } else {
            Swal.fire({
              title: "¡Error!",
              text: res.mensaje,
              icon: "error",
            });
          }
        },
        error: (xhr, status, error) => {
          console.error(xhr, status, error);
        },
      });
    }
  });

  const limpiarModales = () => {
    $(".btn_modal_ver_close_usuario").click(() => $("#mostrar_data_roles").text(""));
    $(".btn_modal_editar_close_usuario").click(() => $("#formEditUsuario")[0].reset());
    $(".hover_img").mouseenter(function () {
      $(this).css("transform", "scale(1.2)");
    }).mouseleave(function () {
      $(this).css("transform", "scale(1)");
    });
  };

  limpiarModales();
  mostrarProductos();
});
