$(document).ready(function () {

  //SELECCIONAR LA FECHA AUTOMATICAMENTE
  function setDateToToday(inputId) {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; 
    $(`#${inputId}`).val(formattedDate);
  }
  setDateToToday('fecha_egreso');

  //RELOJ AUTOMATICO
  function actualizarReloj() {
    const ahora = new Date();
    let horas = ahora.getHours();
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const segundos = String(ahora.getSeconds()).padStart(2, '0');
    const ampm = horas >= 12 ? 'PM' : 'AM';
    horas = horas % 12;
    horas = horas ? horas : 12; 

    const horaFormateada = `${String(horas).padStart(2, '0')}:${minutos}:${segundos} ${ampm}`;

    $('#hora_egreso').val(horaFormateada);
  }
  setInterval(actualizarReloj, 1000);
  actualizarReloj();

  //SELECION DE TIPO DE PAGO YAPE O EFECTIVO 
  function tipoPago() {

    var paymentMethodLinks = document.querySelectorAll("a.paymentmethod");
    paymentMethodLinks.forEach(function (link) {
      link.addEventListener("click", function () {
        var radioButton = this.querySelector(".tipo_pago_egreso");
        if (!radioButton.checked) {
          radioButton.checked = true;
        }
      });
    });
  }
  tipoPago();

  //MOSTRAR PRODUCTOS PARA LA COMPRA
  function mostrarProductos() {
    $.ajax({
      url: "ajax/Producto.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (productos) {
        const tbody = $("#data_productos_detalle");

        // Limpiar contenido previo del tbody
        tbody.empty();

        // Generar las filas dinámicamente
        productos.forEach((producto) => {
          const imagen = producto.imagen_producto.substring(3);
          const fila = `
                    <tr>
                        <td class="text-center">
                            <a href="#" class="hover_img_a btnAddProducto" idProductoAdd="${producto.id_producto}">
                                <img class="hover_img" src="${imagen}" alt="${producto.imagen_producto}">
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

        // Inicializar o actualizar DataTable
        $("#tabla_add_producto").DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los productos:", error);
      },
    });
  }

  //MOSTRAR ESTILOS DE BOTONES DEL STOCK DE PRODUCTOS
  function getButtonStyles(stock) {
    if (stock > 20) {
      return "background-color: #28C76F; color: white; border: none;";
    } else if (stock >= 10 && stock <= 20) {
      return "background-color: #FF9F43; color: white; border: none;";
    } else {
      return "background-color: #FF4D4D; color: white; border: none;";
    }
  }

  //MOMSTRAR NUMERO DE SERIE Y VENTA AUTOMATICAMENTE
  function mostrarSerieNumero() {
    $.ajax({
      url: "ajax/SerieNumero.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {

        if (respuesta == "" || respuesta == null) {
          $("#serie_comprobante").val("T0001");
          $("#num_comprobante").val("0001");
        }

        respuesta.forEach((data) => {
          var serie = parseInt(data.serie_comprobante.match(/\d+/)[0]);
          var numero = parseInt(data.num_comprobante.match(/\d+/)[0]);

          // Sumar 1 a los números
          serie += 1;
          numero += 1;

          // Formatear serie con ceros a la izquierda
          var seriComprobante = "T" + serie.toString().padStart(4, "0");
          // Formatear número con ceros a la izquierda, calculando la longitud dinámicamente
          var numeroComprobante = numero.toString().padStart(data.num_comprobante.length, "0");

          $("#serie_comprobante").val(seriComprobante);
          $("#num_comprobante").val(numeroComprobante);

        });
      },
      error: function (xhr, status, error) {
        console.error(xhr);
        console.error(status);
        console.error(error);
      },
    });
  }

  //AGREGAR PRODUCTOS A LA TABLA DETALLE DE LA COMPRA
  $("#tabla_add_producto").on("click", ".btnAddProducto", function (e) {
    e.preventDefault();

    var idProductoAdd = $(this).attr("idProductoAdd");

    var datos = new FormData();
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
        respuesta.imagen_producto = respuesta.imagen_producto.substring(3);

        var nuevaFila = `
            <tr>
                <input type="hidden" class="id_producto_egreso" value="${respuesta.id_producto}">
                <th class="text-center align-middle d-none d-md-table-cell">
                    <a href="#" class="me-3 confirm-text btnEliminarAddProducto" idAddProducto="${respuesta.id_producto}" fotoUsuario="${respuesta.imagen_producto}">
                        <i class="fa fa-trash fa-lg" style="color: #F1666D"></i>
                    </a>
                </th>
                <td>
                    <img src="${respuesta.imagen_producto}" alt="Imagen de un pollo" width="80">
                </td>
                <td>${respuesta.nombre_producto}</td>
                <td>
                    <input type="number" class="form-control form-control-sm numero_javas" value="0">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm numero_aves" value="0">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm peso_promedio" value="0.00">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm peso_bruto" readonly value="0.00">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm peso_tara" value="0.00" style="width: 60px;">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm peso_merma" value="0.00" style="width: 60px;">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm peso_neto" readonly value="0.00">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm precio_compra" value="0.00">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm precio_venta" value="0.00">
                </td>
                <td class="text-end">
                    <span style="font-weight: bold;">S/</span>
                    <input type="text" class="form-control form-control-sm precio_sub_total" value="0.00" readonly style="width: 100px; display: inline-block; text-align: right; font-weight: bold;">
                </td>
            </tr>`;

        $("#detalle_egreso_producto").append(nuevaFila);

        // Agregar el evento 'focus' para borrar el valor predeterminado
        $("input").on("focus", function () {
          if ($(this).val() === "0" || $(this).val() === "0.00") {
            $(this).val(""); // Borra el valor cuando se hace focus
          }
        });

        // Agregar evento para calcular el subtotal al cambiar la cantidad_aves o peso_promedio
        $(".numero_aves, .peso_promedio, .peso_tara, .peso_merma, .precio_compra, #impuesto_egreso").on("input", function () {
          var fila = $(this).closest("tr");

          var numero_aves = parseFloat(fila.find(".numero_aves").val());
          var peso_promedio = parseFloat(fila.find(".peso_promedio").val());
          var peso_tara = parseFloat(fila.find(".peso_tara").val());
          var peso_merma = parseFloat(fila.find(".peso_merma").val());
          var precio_compra = parseFloat(fila.find(".precio_compra").val());
          let impuesto_egreso = $("#impuesto_egreso").val();

          // Verificar si numero_aves o peso_promedio son NaN y asignar 0 en su lugar
          if (isNaN(numero_aves)) {
            numero_aves = 0;
          }
          if (isNaN(peso_promedio)) {
            peso_promedio = 0;
          }
          if (isNaN(peso_tara)) {
            peso_tara = 0;
          }
          if (isNaN(peso_merma)) {
            peso_merma = 0;
          }
          if (isNaN(precio_compra)) {
            precio_compra = 0;
          }
          if (isNaN(impuesto_egreso)) {
            impuesto_egreso = 0;
          }

          // Calcular peso_bruto
          var peso_bruto = peso_promedio * numero_aves;
          let peso_neto = peso_bruto - peso_tara - peso_merma;
          let precio_sub_total = peso_neto * precio_compra;
          // Formatear peso_bruto
          var format_peso_bruto = formateoPrecio(peso_bruto.toFixed(2));
          var format_peso_neto = formateoPrecio(peso_neto.toFixed(2));
          var format_precio_sub_total = formateoPrecio(precio_sub_total.toFixed(2));

          // Actualizar el valor de peso_bruto en el input
          fila.find(".peso_bruto").val(format_peso_bruto);
          fila.find(".peso_neto").val(format_peso_neto);
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

  //ELIMINAR EL PRODUCTO AGREGADO A LA TABLA DETALLE
  $(document).on("click", ".btnEliminarAddProducto", function (e) {
    e.preventDefault();

    var idProductoEliminar = $(this).attr("idAddProducto");

    // Encuentra la fila que corresponde al producto a eliminar y elimínala
    $("#detalle_egreso_producto")
      .find("tr")
      .each(function () {
        var idProducto = $(this)
          .find(".btnEliminarAddProducto")
          .attr("idAddProducto");

        if (idProducto == idProductoEliminar) {
          $(this).remove();

          // Una vez eliminada la fila, recalcular el total
          calcularTotal();

          return false; // Termina el bucle una vez que se ha encontrado y eliminado la fila
        }
      });
  });

  //FORMATEO DE PRECIOS
  function formateoPrecio(numero) {
    return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  //CALCULAR EL TOTAL
  function calcularTotal(impuesto) {
    var subtotalTotal = 0;

    // Recorrer todas las filas para sumar los subtotales
    $("#detalle_egreso_producto tr").each(function () {
      var precio_sub_total = $(this).find(".precio_sub_total").val();

      // Eliminar las comas antes de convertir a número
      precio_sub_total = precio_sub_total.replace(/,/g, '');

      var subtotal = parseFloat(precio_sub_total);

      // Si subtotal no es un número válido, asignar 0
      if (isNaN(subtotal)) {
        subtotal = 0;
      }
      subtotalTotal += subtotal;
    });

    if (isNaN(impuesto)) {
      impuesto = 0;
    }

    let igv = 0;
    // Calcular el impuesto
    igv = subtotalTotal * (impuesto / 100);

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
    $("#subtotal_egreso").text(subtotalFormateado);
    $("#igv_egreso").text(igvFormateado);
    $("#total_precio_egreso").text(totalFormateado);
  }

  //CREAR COMPRA
  $("#btn_crear_compra").click(function (e) {
    e.preventDefault();

    let isValid = true;

    let id_usuario_egreso = $("#id_usuario_egreso").val();
    let id_proveedor_egreso = $("#id_proveedor_egreso").val();
    let fecha_egreso = $("#fecha_egreso").val();
    let hora_egreso = $("#hora_egreso").val();
    let tipo_comprobante_egreso = $("#tipo_comprobante_egreso").val();
    let serie_comprobante = $("#serie_comprobante").val();
    let num_comprobante = $("#num_comprobante").val();
    let impuesto_egreso = $("#impuesto_egreso").val();

    // Validar la categoria
    if (id_proveedor_egreso == "" || id_proveedor_egreso == null) {
      $("#error_egreso_proveedor")
        .html("Por favor, selecione el proveedor")
        .addClass("text-danger");

      isValid = false;
    } else {
      $("#error_egreso_proveedor").html("").removeClass("text-danger");
    }

    // Validar el nombre del producto
    if (tipo_comprobante_egreso == "" || tipo_comprobante_egreso == null) {
      $("#error_compra_comprobante")
        .html("Por favor, ingrese el tipo de comprobante")
        .addClass("text-danger");

      isValid = false;
    } else {
      $("#error_compra_comprobante").html("").removeClass("text-danger");
    }

    // Array para almacenar los valores de los productos
    var valoresProductos = [];

    // Iterar sobre cada fila de producto
    $("#detalle_egreso_producto tr").each(function () {

      var fila = $(this);

      var idProductoEgreso = fila.find(".id_producto_egreso").val();
      var numero_javas = fila.find(".numero_javas").val();
      var numero_aves = fila.find(".numero_aves").val();
      var peso_promedio = fila.find(".peso_promedio").val();
      var peso_bruto = fila.find(".peso_bruto").val();
      var peso_tara = fila.find(".peso_tara").val();
      var peso_merma = fila.find(".peso_merma").val();
      var peso_neto = fila.find(".peso_neto").val();
      var precio_compra = fila.find(".precio_compra").val();
      var precio_venta = fila.find(".precio_venta").val();

      var producto = {
        idProductoEgreso: idProductoEgreso,
        numero_javas: numero_javas,
        numero_aves: numero_aves,
        peso_promedio: peso_promedio,
        peso_bruto: peso_bruto,
        peso_tara: peso_tara,
        peso_merma: peso_merma,
        peso_neto: peso_neto,
        precio_compra: precio_compra,
        precio_venta: precio_venta
      };

      valoresProductos.push(producto);
    });

    let productoAddEgreso = JSON.stringify(valoresProductos);
    let subtotal = $("#subtotal_egreso").text().replace(/,/g, "");
    let igv = $("#igv_egreso").text().replace(/,/g, "");
    let total = $("#total_precio_egreso").text().replace(/,/g, "");
    let tipo_pago = $("input[name='forma_pago']:checked").val();
    var estado_pago;

    if (tipo_pago == "contado") {
      estado_pago = "completado";
    } else {
      estado_pago = "pendiente";
    }

    var pago_tipo = $("input[name='pago_tipo']:checked").val();
    var pago_e_y;

    if (pago_tipo == "efectivo") {
      pago_e_y = "efectivo";
    } else if (pago_tipo == "yape") {
      pago_e_y = "yape";
    } else {
      pago_e_y = ""
    }

    if (isValid) {
      var datos = new FormData();

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

      $.ajax({
        url: "ajax/Compra.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {

          var res = JSON.parse(respuesta);

          if (res.estado === "ok") {
            $("#form_compra_producto")[0].reset();
            $("#detalle_egreso_producto").empty();
            $("#subtotal_egreso").text("00.00");
            $("#igv_egreso").text("00.00");
            $("#total_precio_egreso").text("00.00");

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

                // Redirigir según el tipo de comprobante
                if (res.tipo_documento === 'factura') {
                  var ventana = window.open('extensiones/factura/factura.php?id_egreso=' + res.id_egreso, '_blank');
                  ventana.onload = function () {
                    ventana.print();
                  };
                } else if (res.tipo_documento === 'boleta') {
                  var ventana = window.open('extensiones/boleta/boleta.php?id_egreso=' + res.id_egreso, '_blank');
                  ventana.onload = function () {
                    ventana.print();
                  };
                } else if (res.tipo_documento === 'ticket') {
                  var ventana = window.open('extensiones/ticket/ticket.php?id_egreso=' + res.id_egreso, '_blank');
                  ventana.onload = function () {
                    ventana.print();
                  };
                }


              } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                  title: "¡Descargando!",
                  text: "Su comprobante se está descargando.",
                  icon: "success",
                });

                // Lógica para descargar el comprobante (dependiendo del tipo de documento)
                if (res.tipo_documento === 'factura') {
                  window.location.href = 'extensiones/factura/factura.php?id_egreso=' + res.id_egreso + '&accion=descargar';
                } else if (res.tipo_documento === 'boleta') {
                  window.location.href = 'extensiones/boleta/boleta.php?id_egreso=' + res.id_egreso + '&accion=descargar';
                } else if (res.tipo_documento === 'ticket') {
                  window.location.href = 'extensiones/ticket/ticket.php?id_egreso=' + res.id_egreso + '&accion=descargar';
                }

              } else {
                // Opción para enviar por WhatsApp o correo
                Swal.fire({
                  title: "¿Cómo desea enviar el comprobante?",
                  text: "Seleccione una opción.",
                  icon: "info",
                  showCancelButton: true,
                  cancelButtonText: "WhatsApp",
                  confirmButtonText: "Correo",
                }).then((sendResult) => {
                  if (sendResult.isConfirmed) {
                    Swal.fire({
                      title: "¡Enviando por correo!",
                      text: "Su comprobante se está enviando por correo.",
                      icon: "success",
                    });

                    // Lógica para enviar el comprobante por correo (puedes usar un enlace para enviar el comprobante por correo)
                    // Aquí puedes agregar la lógica para enviar por correo, utilizando una API o método de backend.

                  } else {
                    Swal.fire({
                      title: "¡Enviando por WhatsApp!",
                      text: "Su comprobante se está enviando por WhatsApp.",
                      icon: "success",
                    });

                    // Lógica para enviar por WhatsApp (también puedes usar un enlace para esto)
                    // Aquí puedes agregar la lógica para enviar por WhatsApp, usando el número de teléfono y el comprobante.
                  }
                });
              }
            });

            mostrarProductos();
            mostrarSerieNumero();

          } else {
            Swal.fire({
              title: "¡Error!",
              text: res.mensaje,
              icon: "error",
            });
          }
        },
        error: function (xhr, status, error) {
          console.error(xhr);
          console.error(status);
          console.error(error);
        },
      });

    }
  });

  //LIMPIAR LOS MODALES
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
  mostrarProductos();
  mostrarSerieNumero();
});
