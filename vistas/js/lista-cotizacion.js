/*=============================================
TRAER EL ULTIMO NUMERO DE VENTA
=============================================*/
function ultimoNumeroVenta() {
    $.ajax({
        url: "ajax/Lista.venta.ajax.php",
        type: "GET",
        dataType: "json",
        success: function (respuesta) {
            const primerRegistro = respuesta[0];
            localStorage.setItem('numero_comprobante', parseInt(primerRegistro.num_comprobante) + 1);
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener el número de venta:", error);
        }
    });
}

ultimoNumeroVenta();


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
                if (!cotizacionesProcesados.has(cotizacion.id_cotizacion)) {
                    let totalCotizacion = formateoPrecio(cotizacion.total_cotizacion);
                    const fechaActual = new Date();
                    const fechaValidez = new Date();
                    fechaValidez.setDate(fechaActual.getDate() + cotizacion.validez);
                    const fechaEstaVencida = fechaActual >= fechaValidez;
                    var fila = `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${cotizacion.razon_social}</td>
                        <td>${cotizacion.tipo_comprobante_sn}</td>
                        <td class="${fechaEstaVencida ? 'text-danger' : ''}">
                            ${cotizacion.validez == 1 ? cotizacion.validez + ' dia' : cotizacion.validez + ' dias'}
                        </td>
                        <td>S/ ${totalCotizacion}</td>
                        <td>${cotizacion.fecha_cotizacion}</td>
                        <td>${cotizacion.hora_cotizacion}</td>
                        <td class="text-center">
                            ${
                                cotizacion.estado === 0
                                ? `<button class="btn btn-sm rounded btnActivar btn-enviado" idCotizacion="${cotizacion.id_cotizacion}" estadoCotizacion="0">Enviado</button>`
                                : cotizacion.estado === 1
                                ? `<button class="btn btn-sm rounded btnActivar btn-pendiente" idCotizacion="${cotizacion.id_cotizacion}" estadoCotizacion="1">Pendiente</button>`
                                : cotizacion.estado === 2
                                ? `<button class="btn btn-sm rounded btnActivar btn-completado" idCotizacion="${cotizacion.id_cotizacion}" estadoCotizacion="2">Completado</button>`
                                : `<button class="btn btn-sm rounded btnActivar btn-desactivado" idCotizacion="${cotizacion.id_cotizacion}" estadoCotizacion="3">Desactivado</button>`
                            }
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm dropdown-toggle text-white" type="button" id="dropdownMenuButton" style="background: #FF9F43" data-bs-toggle="dropdown" aria-expanded="false">
                                    Acciones
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    ${cotizacion.estado === 0 || cotizacion.estado === 1 ? `` 
                                    : `<li>
                                        <a href = "#" class="dropdown-item btnGenerarVenta" idCotizacion = "${cotizacion.id_cotizacion}" >
                                        <i class="fa fa-cart-plus fa-lg me-2" style="color: #1B2850"></i> Generar venta
                                                </a>
                                    </li >`}
                                   
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

/*=============================================
ESTADO DE LA COTIZACION
=============================================*/
$("#tabla_lista_cotizaciones").on("click", ".btnActivar", function () {
    var idCotizacion = $(this).attr("idCotizacion");
    var estadoCotizacion = parseInt($(this).attr("estadoCotizacion"));

    if (estadoCotizacion === 2) {
        Swal.fire({
            title: "¡Completado!",
            text: "La cotización ya ha sido completado",
            icon: "warning",
        });
        return;
    }

    var nuevoEstado;
    var nuevoTexto;
    var nuevaClase;

    if (estadoCotizacion === 0) {
        nuevoEstado = 1;
        nuevoTexto = "Pendiente";
        nuevaClase = "btn-pendiente";
    } else if (estadoCotizacion === 1) {
        nuevoEstado = 2;
        nuevoTexto = "Completado";
        nuevaClase = "btn-completado";
    } else {
        nuevoEstado = 0;
        nuevoTexto = "Enviado";
        nuevaClase = "btn-enviado";
    }

    // Enviar el nuevo estado por AJAX
    var datos = new FormData();
    datos.append("activarId", idCotizacion);
    datos.append("activarCotizacion", nuevoEstado);

    $.ajax({
        url: "ajax/Lista.cotizacion.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            console.log(respuesta);
        },
    });

    // Actualizar la interfaz del botón
    $(this)
        .removeClass("btn-enviado btn-pendiente btn-completado btn-desactivado") // Quitar todas las clases previas
        .addClass(nuevaClase) // Agregar la nueva clase
        .html(nuevoTexto); // Cambiar el texto
    $(this).attr("estadoCotizacion", nuevoEstado); // Actualizar el atributo
});

/*=============================================
GENERAR VENTA A PARTIR DE LA COTIZACION
=============================================*/
$("#data_lista_cotizacion").on("click", ".btnGenerarVenta", function(e){
    e.preventDefault();
    let idCotizacion = $(this).attr("idCotizacion");
    const datos = new FormData();
    datos.append("idCotizacionDatos", idCotizacion)
    $.ajax({
        url: "ajax/Lista.cotizacion.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            if(respuesta && Array.isArray(respuesta) && respuesta.length > 1){
                let cotizacion = respuesta[0];
                let detalles = respuesta[1];
                let productoAddVenta = JSON.stringify(detalles);
                let numero_venta = localStorage.getItem('numero_comprobante');

                var estado_pago = "";
                if (cotizacion.tipo_pago == "contado") {
                    estado_pago = "completado";
                } else {
                    estado_pago = "pendiente";
                }
                let metodos_pago_venta = "pago_efectivo";

                const datos = new FormData();
                datos.append("id_usuario_venta", cotizacion.id_usuario);
                datos.append("id_cliente_venta", cotizacion.id_persona);
                datos.append("fecha_venta", cotizacion.fecha_cotizacion);
                datos.append("hora_venta", cotizacion.hora_cotizacion);
                datos.append("comprobante_venta", cotizacion.id_serie_num);
                datos.append("serie_venta", cotizacion.serie_cotizacion);
                datos.append("numero_venta", numero_venta);
                datos.append("igv_venta", cotizacion.igv_venta);
                datos.append("productoAddVenta", productoAddVenta);
                datos.append("subtotal", cotizacion.sub_total);
                datos.append("igv", cotizacion.igv_total);
                datos.append("total", cotizacion.total_cotizacion);
                datos.append("tipo_pago", cotizacion.tipo_pago);
                datos.append("estado_pago", estado_pago);
                datos.append("metodos_pago_venta", metodos_pago_venta);
                datos.append("pago_cuota_venta", cotizacion.pago_cuota_venta);
                datos.append("recibo_de_pago_venta", cotizacion.recibo_de_pago_venta);
                datos.append("serie_de_pago_venta", cotizacion.serie_de_pago_venta);


                $.ajax({
                    url: "ajax/ventas.ajax.php",
                    method: "POST",
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (respuesta) {
                        const res = JSON.parse(respuesta);
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
                    },
                });
            }
        }
    })
})

/*=============================================
ELIMINAR VENTA
=============================================*/
$("#data_lista_cotizacion").on("click", ".btnEliminarCotizacion", function (e) {
    e.preventDefault();
    let idCotizacionDelete = $(this).attr("idCotizacionDelete");
    let datos = new FormData();
    datos.append("idCotizacionDelete", idCotizacionDelete);
    Swal.fire({
        title: "¿Está seguro de borrar la cotización?",
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
                            text: "La cotización ha sido eliminada",
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
MOSTRAR VENTAS
=========================================== */
mostrarCotizaciones();
