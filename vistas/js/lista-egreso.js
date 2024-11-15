$(document).ready(function () {

  //FORMATEAR LOS PRECIOS 
  function formateoPrecio(numero) {
    return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  //GUARDAR PRODUCTO
  $("#btn_guardar_producto").click(function () {

    var isValid = true;

    var id_categoria_P = $("#id_categoria_P").val();
    var codigo_producto = $("#codigo_producto").val();
    var nombre_producto = $("#nombre_producto").val();
    var stock_producto = $("#stock_producto").val();
    var fecha_vencimiento = $("#fecha_vencimiento").val();
    var descripcion_producto = $("#descripcion_producto").val();
    var imagen_producto = $("#imagen_producto").get(0).files[0];



    // Validar la categoria
    if (id_categoria_P == "" || id_categoria_P == null) {
      $("#error_id_categoria_p")
        .html("Por favor, selecione la cateogría")
        .addClass("text-danger");

      isValid = false;
    } else {
      $("#error_id_categoria_p").html("").removeClass("text-danger");
    }

    // Validar el codigo de producto
    if (codigo_producto == "") {
      $("#error_codigo_p")
        .html("Por favor, ingrese el código del producto")
        .addClass("text-danger");
      isValid = false;
    } else {
      $("#error_codigo_p").html("").removeClass("text-danger");
    }

    // Validar el nombre del producto
    if (nombre_producto == "") {
      $("#error_nombre_p")
        .html("Por favor, ingrese el stock")
        .addClass("text-danger");
      isValid = false;
    } else {
      $("#error_nombre_p").html("").removeClass("text-danger");
    }

    // Validar el stock del producto
    if (stock_producto === "" || stock_producto === null || isNaN(stock_producto) || parseInt(stock_producto) !== parseFloat(stock_producto) || parseInt(stock_producto) <= 0) {
      $("#error_stock_p")
        .html("Por favor, ingrese un número entero positivo para el stock")
        .addClass("text-danger");
      isValid = false;
    } else {
      $("#error_stock_p").html("").removeClass("text-danger");
    }

    // Si el formulario es válido, envíalo
    if (isValid) {
      var datos = new FormData();
      datos.append("id_categoria_P", id_categoria_P);
      datos.append("codigo_producto", codigo_producto);
      datos.append("nombre_producto", nombre_producto);
      datos.append("stock_producto", stock_producto);
      datos.append("fecha_vencimiento", fecha_vencimiento);
      datos.append("descripcion_producto", descripcion_producto);
      datos.append("imagen_producto", imagen_producto);




      $.ajax({
        url: "ajax/Producto.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
          var res = JSON.parse(respuesta);

          if (res.estado === "ok") {

            $("#form_nuevo_producto")[0].reset();

            $(".vistaPreviaImagenProducto").attr("src", "");

            $("#modalNuevoProducto").modal("hide");

            Swal.fire({
              title: "¡Correcto!",
              text: res.mensaje,
              icon: "success",
            });

            mostrarEgresos();

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

  //MOSTRANDO EN LA TABLA LOS EGRESO O CAMPRAS 
  function mostrarEgresos() {

    $.ajax({
      url: "ajax/Lista.compra.ajax.php",
      type: "GET",
      dataType: "json",
      success: function (egresos) {

        var tbody = $("#data_lista_egresos");
        tbody.empty();

        // Inicializamos un conjunto vacío para almacenar los id_egreso ya procesados
        var egresosProcesados = new Set();

        egresos.forEach(function (egreso, index) {
          // Verificar si el id_egreso ya ha sido procesado
          if (!egresosProcesados.has(egreso.id_egreso)) {

            var restantePago = (egreso.total_compra - egreso.total_pago).toFixed(2);
            let fechaOriginal = egreso.fecha_egre;
            let partesFecha = fechaOriginal.split("-"); // Dividir la fecha en año, mes y día
            let fechaFormateada = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
            let totalCompra = formateoPrecio(egreso.total_compra);
            let formateadoPagoRestante = formateoPrecio(restantePago);

            var fila = `
                  <tr>
                      <td>${index + 1}</td>
                      <td>${fechaFormateada}</td>
                      <td>${egreso.razon_social}</td>
                      <td>${egreso.serie_comprobante}</td>
                      <td>${egreso.num_comprobante}</td>
                      <td>${egreso.tipo_pago}</td>
                      <td>S/ ${totalCompra}</td>
                      <td>S/ ${formateadoPagoRestante}</td>
                      <td class="text-center">
                          ${restantePago == '0.00' ? '<button class="btn btn-sm rounded" style="background: #28C76F; color:white;">Completado</button>'
                            : '<button class="btn btn-sm rounded" style="background: #FF4D4D; color:white;">Pendiente</button>'
                          }
                      </td>
                      
                      <td class="text-center">

                          <a href="#" class="me-3 btnPagarCompra" idEgreso="${egreso.id_egreso}" totalCompraEgreso=${totalCompra} pagoRestanteEgreso=${formateadoPagoRestante} data-bs-toggle="modal" data-bs-target="#modalPagarCompra">
                              <i class="fas fa-money-bill-alt fa-lg" style="color: #28C76F"></i>
                          </a>
                          <a href="#" class="me-3 btnVerProducto" idEgreso="${egreso.id_egreso}" data-bs-toggle="modal" data-bs-target="#modalVerProducto">
                              <i class="fa fa-print fa-lg" style="color: #0084FF"></i>
                          </a>
                          <a href="#" class="me-3 btnVerProducto" idEgreso="${egreso.id_egreso}" data-bs-toggle="modal" data-bs-target="#modalVerProducto">
                              <i class="fa fa-download fa-lg" style="color: #28C76F"></i>
                          </a>
                          <a href="#" class="me-3 confirm-text btnEliminarCompra" idEgreso="${egreso.id_egreso}" imagenProducto="${egreso.imagen_producto}">
                              <i class="fa fa-trash fa-lg" style="color: #FF4D4D"></i>
                          </a>
                      </td>
                  </tr>`;

            // Agregar la fila al tbody
            tbody.append(fila);

            // Agregar el id_egreso al conjunto de egresos procesados
            egresosProcesados.add(egreso.id_egreso);
          }
        });

        // Inicializar DataTables después de cargar los datos
        $('#tabla_lista_agreso').DataTable();
      },
      error: function (xhr, status, error) {
        console.error("Error al recuperar los usuarios:", error.mensaje);
      },
    });
  }

  //MOSTRAR DEUDA A COMPRAR
  $("#data_lista_egresos").on("click", ".btnPagarCompra", function (e) {

    e.preventDefault();

    let idEgreso = $(this).attr("idEgreso");
    let totalCompraEgreso = $(this).attr("totalCompraEgreso");
    let pagoRestanteEgreso = $(this).attr("pagoRestanteEgreso");

    $("#id_egreso_pagar").val(idEgreso);
    $("#total_compra_show").text("S/ " + totalCompraEgreso);
    $("#total_restante_show").text("S/ " + pagoRestanteEgreso);

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

      var datos = new FormData();
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
