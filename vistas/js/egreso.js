$(document).ready(function () {

  seleccionFecha();
  
  function actualizarReloj() {
    var ahora = new Date();
    var horas = ahora.getHours();
    var minutos = String(ahora.getMinutes()).padStart(2, '0');  // Minutos con 2 dígitos
    var segundos = String(ahora.getSeconds()).padStart(2, '0');  // Segundos con 2 dígitos

    var ampm = horas >= 12 ? 'PM' : 'AM';  // Determina si es AM o PM
    horas = horas % 12;  // Convierte de 24h a 12h
    horas = horas ? horas : 12;  // El "0" de la medianoche se muestra como 12
    var horaFormateada = String(horas).padStart(2, '0') + ":" + minutos + ":" + segundos + " " + ampm;

    // Mostrar la hora en el campo de entrada
    document.getElementById('hora_egreso').value = horaFormateada;
  }

  // Actualizar la hora cada 1000 milisegundos (1 segundo)
  setInterval(actualizarReloj, 1000);

  // Inicializar la hora en el campo de texto cuando la página carga
  actualizarReloj();


  /* ===================================
  SELECCION DE TIPO DE PAGO
  =================================== */
  function tipoPago() {
    // Obtener todos los elementos <a> con la clase "paymentmethod"
    var paymentMethodLinks = document.querySelectorAll("a.paymentmethod");

    // Iterar sobre cada elemento <a>
    paymentMethodLinks.forEach(function (link) {
      // Añadir un evento de clic a cada elemento <a>
      link.addEventListener("click", function () {
        // Obtener el radio button dentro del elemento <a> actual
        var radioButton = this.querySelector(".tipo_pago_egreso");

        // Verificar si el radio button no está marcado
        if (!radioButton.checked) {
          // Marcar el radio button
          radioButton.checked = true;
        }
      });
    });
  }

  tipoPago();

  /*=============================================
  SELECION DE FECHA AUTOMATICO
  =============================================*/
  function seleccionFecha() {
    const fechaEgresoInput = document.getElementById("fecha_egreso");
    
    // Verificar si el elemento existe
    if (fechaEgresoInput !== null) {
        const today = new Date().toISOString().split("T")[0];
        fechaEgresoInput.value = today;
    } else {
        console.log("El  elemento con ID 'fecha_egreso' no existe.");
    }
}

  /*=============================================
  MOSTRAR PRODUCTOS
  =============================================*/
  function mostrarProductos() {
    $.ajax({
      url: "ajax/Producto.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (productos) {
        var tbody = $("#data_productos_detalle");

        tbody.empty();

        productos.forEach(function (producto, index) {
          producto.imagen_producto = producto.imagen_producto.substring(3);

          var fila = `
                      <tr>
                         
                          <td class="text-center">
                              <a href="#" id="btnAddProducto" class=" hover_img_a btnAddProducto" idProductoAdd="${
                                producto.id_producto
                              }">
                                  <img class="hover_img" src="${
                                    producto.imagen_producto
                                  }" alt="${producto.imagen_producto}">
                              </a>
                          </td>
                          <td>${producto.nombre_categoria}</td>
                          <td>${producto.nombre_producto}</td>
                          <td class="text-center"><button type="button" class="btn btn-sm" style="${getButtonStyles(
                            producto.stock_producto
                          )}">${producto.stock_producto}</button></td>

                      </tr>`;

          tbody.append(fila);
          $("#tabla_add_producto").DataTable();
        });
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuarios:", error.mensaje);
      },
    });
  }

  function getButtonStyles(stock) {
    if (stock > 20) {
      return "background-color: #28C76F; color: white; border: none;";
    } else if (stock >= 10 && stock <= 20) {
      return "background-color: #FF9F43; color: white; border: none;";
    } else {
      return "background-color: #FF4D4D; color: white; border: none;";
    }
  }

  /* ============================================
  MOSTRAR SERIE Y NUMERO DE VENTA 
  ============================================ */

  function mostrarSerieNumero() {
    $.ajax({
      url: "ajax/SerieNumero.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (respuesta) {

        if(respuesta == "" || respuesta == null){
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

  /* ====================================
  AGREGAR PRODUCTO A LA TABLA DETALLE
  ===================================== */

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


  /* =============================================
  ELIMINAR EL PRODUCTO AGREGADO DE LA LISTA
  ============================================= */

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

  /*============================================
  FORMATEAR LOS PRECIOS
  ============================================ */

  function formateoPrecio(numero) {
    return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

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


  /* ===========================================
  CREAR VENTA EGRESO
  =========================================== */
  $("#btn_crear_venta").click(function (e) {
    e.preventDefault();

    var isValid = true;

    var id_usuario_egreso = $("#id_usuario_egreso").val();
    var id_proveedor_egreso = $("#id_proveedor_egreso").val();
    var fecha_egreso = $("#fecha_egreso").val();
    var tipo_comprobante_egreso = $("#tipo_comprobante_egreso").val();
    var serie_comprobante = $("#serie_comprobante").val();
    var num_comprobante = $("#num_comprobante").val();
    var impuesto_egreso = $("#impuesto_egreso").val();

    // Validar la categoria
    if (id_proveedor_egreso == "" || id_proveedor_egreso == null) {
      $("#error_egreso_proveedor")
        .html("Por favor, selecione el proveedor")
        .addClass("text-danger");

      isValid = false;
    } else {
      $("#error_egreso_proveedor").html("").removeClass("text-danger");
    }

    // Validar el codigo de producto
    if (fecha_egreso == "") {
      $("#error_egreso_fecha")
        .html("Por favor, ingrese ingrese la fecha")
        .addClass("text-danger");

      isValid = false;
    } else {
      $("#error_egreso_fecha").html("").removeClass("text-danger");
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

      // Obtener los valores de cada campo en la fila
      var idProductoEgreso = fila.find(".id_producto_egreso").val();
      var cantidadU = fila.find(".cantidad_u").val();
      var cantidadKg = fila.find(".cantidad_kg").val();
      var precioCompra = fila.find(".precio_compra").val();
      var precioVenta = fila.find(".precio_venta").val();

      // Crear un objeto con los valores y agregarlo al array
      var producto = {
        idProductoEgreso: idProductoEgreso,
        cantidad_u: cantidadU,
        cantidad_kg: cantidadKg,
        precio_compra: precioCompra,
        precio_venta: precioVenta,
      };

      valoresProductos.push(producto);
    });

    var productoAddEgreso = JSON.stringify(valoresProductos);

    var subtotal = $("#subtotal_egreso").text().replace(/,/g, "");

    var igv = $("#igv_egreso").text().replace(/,/g, "");

    var total = $("#total_precio_egreso").text().replace(/,/g, "");

    // Captura el valor del tipo de pago (contado o crédito)
    var tipo_pago = $("input[name='forma_pago']:checked").val();

    // Variable para almacenar el estado
    var estado_pago;

    // Verifica el tipo de pago seleccionado
    if (tipo_pago == "contado") {
      estado_pago = "completado";
    } else {
      estado_pago = "pendiente";
    }

    // Captura el valor del tipo de pago (contado o crédito)
    var pago_tipo = $("input[name='pago_tipo']:checked").val();

    // Variable para almacenar el estado
    var pago_e_y;

    // Verifica el tipo de pago seleccionado
    if (pago_tipo == "efectivo") {
      pago_e_y = "efectivo";
    } else {
      pago_e_y = "yape";
    }

    if (isValid) {
      var datos = new FormData();

      datos.append("id_proveedor_egreso", id_proveedor_egreso);
      datos.append("id_usuario_egreso", id_usuario_egreso);
      datos.append("fecha_egreso", fecha_egreso);
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
              title: "¿Quiere imprimir comprobante?",
              text: "¡No podrás revertir esto!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#28C76F",
              cancelButtonColor: "#F52E2F",
              confirmButtonText: "¡Sí, imprimir!",
            }).then((result) => {
              if (result.isConfirmed) {
                Swal.fire({
                  title: "¡Imprimiendo!",
                  text: "Su comprobante se está imprimiento.",
                  icon: "success",
                });
              }
            });

            mostrarProductos();
            seleccionFecha();
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

  /* ==========================================
  LIMPIAR MODALES
  ========================================== */
  function limpiarModales(){
    
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

  /* =====================================
  MSOTRANDO DATOS
  ===================================== */
  mostrarProductos();
  seleccionFecha();
  mostrarSerieNumero();
});
