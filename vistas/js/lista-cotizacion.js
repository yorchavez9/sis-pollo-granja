
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
function mostrarCotizaciones() {
    $.ajax({
        url: "ajax/Lista.cotizacion.ajax.php",
        type: "GET",
        dataType: "json",
        success: function (cotizaciones) {
            let tbody = $("#data_lista_cotizacion");
            tbody.empty();
            var cotizacionesProcesados = new Set();
            cotizaciones.forEach(function (cotizacion, index) {
                // Verificar si el id_egreso ya ha sido procesado
                if (!cotizacionesProcesados.has(cotizacion.id_cotizacion)) {
                    let totalCotizacion = formateoPrecio(cotizacion.total_cotizacion);
                    var fila = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${cotizacion.razon_social}</td>
                        <td>${cotizacion.tipo_comprobante_sn}</td>
                        <td>${cotizacion.validez == 1 ? cotizacion.validez + ' dia' : cotizacion.validez + ' dias'}</td>
                        <td>S/ ${totalCotizacion}</td>
                        <td>${cotizacion.fecha_cotizacion}</td>
                        <td>${cotizacion.hora_cotizacion}</td>
                       <td class="text-center">
                            ${cotizacion.estado === 0? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idProducto="' + cotizacion.id_producto + '" estadoProducto="0">Enviado</button>': cotizacion.estado === 2
                            ? '<button class="btn bg-lightyellow badges btn-sm rounded btnActivar" idProducto="' + cotizacion.id_producto + '" estadoProducto="2">Pendiente</button>': cotizacion.estado === 3
                            ? '<button class="btn bg-lightblue badges btn-sm rounded btnActivar" idProducto="' + cotizacion.id_producto + '" estadoProducto="3">Completado</button>': '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idProducto="' + cotizacion.id_producto + '" estadoProducto="1">Desactivado</button>'
                            }
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle text-white" type="button" id="dropdownMenuButton" style="background: #FF9F43" data-bs-toggle="dropdown" aria-expanded="false">
                                    Acciones
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a href="#" class="dropdown-item btnGenerarVenta" idCotizacion="${cotizacion.id_cotizacion}">
                                            <i class="fa fa-cart-plus fa-lg me-2" style="color: #1B2850"></i> Generar venta
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item btnEditarCotizacion" idCotizacion="${cotizacion.id_cotizacion}">
                                            <i class="fa fa-edit fa-lg me-2" style="color: #FF9F43"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item btnImprimirComprobanteC" idCotizacion="${cotizacion.id_cotizacion}" tipo_comprobante="${cotizacion.tipo_comprobante_sn}">
                                            <i class="fa fa-print fa-lg me-2" style="color: #0084FF"></i> Imprimir
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item btnDescargarComprobanteC" idCotizacion="${cotizacion.id_cotizacion}" tipo_comprobante="${cotizacion.tipo_comprobante_sn}">
                                            <i class="fa fa-download fa-lg me-2" style="color: #28C76F"></i> Descargar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item confirm-text btnEliminarCotizacion" idCotizacionDelete="${cotizacion.id_cotizacion}">
                                            <i class="fa fa-trash fa-lg me-2" style="color: #FF4D4D"></i> Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>

                    </tr>`;

                    tbody.append(fila);
                    cotizacionesProcesados.add(cotizacion.id_cotizacion);
                }
            });
            // Inicializar DataTables después de cargar los datos
            $("#tabla_lista_cotizaciones").DataTable();
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
$("#data_lista_cotizacion").on("click", ".btnHistorialPago", function (e) {
    e.preventDefault();
    let id_venta_historial = $(this).attr("idVenta");
    cargarHistorialPago(id_venta_historial);
});

/*=============================================
IMPRIMIR COMPROBANTE
=============================================*/
$("#data_lista_cotizacion").on("click", ".btnImprimirComprobanteC", function (e) {
    e.preventDefault();
    console.log("Imprimiendo comprobante");
    var idCotizacion = $(this).attr("idCotizacion");
    var documento = $(this).attr("tipo_comprobante");
    const urlDocumento = `extensiones/${documento}/${documento}_c.php?id_cotizacion=${idCotizacion}`;
    const ventana = window.open(urlDocumento, '_blank');
    ventana.onload = () => ventana.print();
});

/*=============================================
DESCARGAR COMPROBANTE
=============================================*/
$("#data_lista_cotizacion").on("click", ".btnDescargarComprobanteC", function (e) {
    e.preventDefault();
    console.log("Descargando comprobante");
    var idCotizacion = $(this).attr("idCotizacion");
    var documento = $(this).attr("tipo_comprobante");
    window.location.href = `extensiones/${documento}/${documento}_c.php?id_cotizacion=${idCotizacion}&accion=descargar`;
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
        url: "ajax/Lista.cotizacion.ajax.php",
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

$("#data_lista_cotizacion").on("click", ".btnEliminarVenta", function (e) {

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
    }).then(function (result) {

        if (result.value) {

            $.ajax({
                url: "ajax/Lista.cotizacion.ajax.php",
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
                            text: "La venta ha sido eliminada",
                            icon: "success",
                        });

                        mostrarCotizaciones();
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

$("#data_lista_cotizacion").on("click", ".btnPagarVenta", function (e) {
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
$("#data_lista_cotizacion").on("click", ".btnPagarVenta", function (e) {
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
    $("#total_venta_pagar").text(`S/ ${totalCompracotizacion.toFixed(2)}`);
    $("#pago_restante_pagar").text(`S/ ${pagoRestantecotizacion.toFixed(2)}`);

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

                } else if (res.estado == "warning") {
                    Swal.fire("Aviso!", res.message, "warning");
                } else {
                    Swal.fire("Error!", res.message, "error");
                }

                mostrarCotizaciones();
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
                    mostrarCotizaciones();
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
$("#tabla_historial_pago").on("click", ".btn_print_pago_historial", function (e) {
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
mostrarCotizaciones();
/* ===========================================
CALCULAR EL TOTAL DE LA VENTA
=========================================== */
calcularTotal();
/* ===========================================
EXPORTANDO VENTA
=========================================== */
export {
    mostrarCotizaciones
};
