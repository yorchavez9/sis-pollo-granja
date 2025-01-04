

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
                            ${
                                cotizacion.estado === 0
                                ? `<button class="btn btn-sm rounded btnActivar bg-success text-white" idProducto="${cotizacion.id_producto}" estadoProducto="0"> Enviado</button>`
                                : cotizacion.estado === 1
                                ? `<button class="btn btn-sm rounded btnActivar bg-warning text-dark" idProducto="${cotizacion.id_producto}" estadoProducto="1">Pendiente</button>`
                                : cotizacion.estado === 2
                                ? `<button class="btn btn-sm rounded btnActivar bg-info text-white" idProducto="${cotizacion.id_producto}" estadoProducto="2">Completado</button>`
                                : `<button class="btn btn-sm rounded btnActivar bg-danger text-white" idProducto="${cotizacion.id_producto}" estadoProducto="3">Desactivado</button>`
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
    var idProducto = $(this).attr("idProducto");
    var estadoProducto = parseInt($(this).attr("estadoProducto")); // Convertir a entero

    var nuevoEstado;
    var nuevoTexto;
    var nuevaClase;

    // Definir el siguiente estado
    if (estadoProducto === 0) {
        nuevoEstado = 1;
        nuevoTexto = "Pendiente";
        nuevaClase = "bg-warning text-dark";
    } else if (estadoProducto === 1) {
        nuevoEstado = 2;
        nuevoTexto = "Completado";
        nuevaClase = "bg-info text-white";
    } else if (estadoProducto === 2) {
        nuevoEstado = 0;
        nuevoTexto = "Enviado";
        nuevaClase = "bg-success text-white";
    } else {
        nuevoEstado = 3;
        nuevoTexto = "Desactivado";
        nuevaClase = "bg-danger text-white";
    }

    // Enviar el nuevo estado por AJAX
    var datos = new FormData();
    datos.append("activarId", idProducto);
    datos.append("activarProducto", nuevoEstado);

    $.ajax({
        url: "ajax/Producto.ajax.php",
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
        .removeClass("bg-success bg-warning bg-info bg-danger text-white text-dark") // Quitar todas las clases previas
        .addClass(nuevaClase) // Agregar la nueva clase
        .html(nuevoTexto); // Cambiar el texto
    $(this).attr("estadoProducto", nuevoEstado); // Actualizar el atributo
});



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
