$("#btn_aplicar_filtros_compras").on("click", function (e) {
    e.preventDefault();
    let filtro_usuario_compra = $("#filtro_usuario_compra").val();
    let filtro_fecha_desde_compra = $("#filtro_fecha_desde_compra").val();
    let filtro_fecha_hasta_compra = $("#filtro_fecha_hasta_compra").val();
    let filtro_tipo_comprobante_compra = $("#filtro_tipo_comprobante_compra").val();
    let filtro_estado_pago_compra = $("#filtro_estado_pago_compra").val();
    let filtro_total_compra_min = $("#filtro_total_compra_min").val();
    let filtro_total_compra_max = $("#filtro_total_compra_max").val();


    const datos = new FormData();
    datos.append("filtro_usuario_compra", filtro_usuario_compra);
    datos.append("filtro_fecha_desde_compra", filtro_fecha_desde_compra);
    datos.append("filtro_fecha_hasta_compra", filtro_fecha_hasta_compra);
    datos.append("filtro_tipo_comprobante_compra", filtro_tipo_comprobante_compra);
    datos.append("filtro_estado_pago_compra", filtro_estado_pago_compra);
    datos.append("filtro_total_compra_min", filtro_total_compra_min);
    datos.append("filtro_total_compra_max", filtro_total_compra_max);

    mostrarProductos(datos);
    $('#tabla_compras_reporte').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarProductos(datos) {
    $.ajax({
        url: "ajax/Reporte.compra.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (compras) {
 
            let tbody = $("#data_compras_reporte");
            tbody.empty();

            compras.forEach(function (producto, index) {
                // Reemplazar valores vacíos
                let nombre_usuario = producto.nombre_usuario || '<span class="text-secondary">Sin categoría</span>';
                let razon_social = producto.razon_social || '<span class="text-secondary">Sin proveedor</span>';
                let fecha_egre = producto.fecha_egre || '<span class="text-secondary">Sin fecha</span>';
                let comprobante = producto.tipo_comprobante || '<span class="text-secondary">Sin comprobante</span>';
                let serie = producto.serie_comprobante || '<span class="text-secondary">Sin serie</span>';
                let numero = producto.num_comprobante || '<span class="text-secondary">Sin número</span>';
                let total_compra = producto.total_compra || '<span class="text-secondary">0.00</span>';
                let total_pago = producto.total_pago || '<span class="text-secondary">0.00</span>';
                let estado = producto.estado_pago === 'completado' ? 'Pagado' : 'Pendiente';

                // Generar fila
                let fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${razon_social}</td>
                        <td>${nombre_usuario}</td>
                        <td>${fecha_egre}</td>
                        <td>${comprobante}</td>
                        <td>${serie}-${numero}</td>
                        <td>${total_compra}</td>
                        <td>${total_pago}</td>
                        <td>${estado}</td>
                    </tr>
                `;
                tbody.append(fila);
            });
            $('#tabla_compras_reporte').DataTable();
        },
        error: function (xhr, status, error) {
            console.error("Error al recuperar los productos:", error);
            console.log(xhr);
            console.log(status);
        },
    });
}

let datos = new FormData();
mostrarProductos(datos);

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
$("#seccion_compras_reporte").on("click", ".reporte_compras_pdf", (e) => {
    e.preventDefault();

    // Obtenemos los valores de los filtros actuales
    const filtro_usuario_compra = $("#filtro_usuario_compra").val() || "";
    const filtro_fecha_desde_compra = $("#filtro_fecha_desde_compra").val() || "";
    const filtro_fecha_hasta_compra = $("#filtro_fecha_hasta_compra").val() || "";
    const filtro_tipo_comprobante_compra = $("#filtro_tipo_comprobante_compra").val() || "";
    const filtro_estado_pago_compra = $("#filtro_estado_pago_compra").val() || "";
    const filtro_total_compra_min = $("#filtro_total_compra_min").val() || "";
    const filtro_total_compra_max = $("#filtro_total_compra_max").val() || "";

    // Construir la URL con los filtros como parámetros
    const url = `extensiones/reportes/compras.php?` +
        `filtro_usuario_compra=${encodeURIComponent(filtro_usuario_compra)}` +
        `&filtro_fecha_desde_compra=${encodeURIComponent(filtro_fecha_desde_compra)}` +
        `&filtro_fecha_hasta_compra=${encodeURIComponent(filtro_fecha_hasta_compra)}` +
        `&filtro_tipo_comprobante_compra=${encodeURIComponent(filtro_tipo_comprobante_compra)}` +
        `&filtro_estado_pago_compra=${encodeURIComponent(filtro_estado_pago_compra)}` +
        `&filtro_total_compra_min=${encodeURIComponent(filtro_total_compra_min)}` +
        `&filtro_total_compra_max=${encodeURIComponent(filtro_total_compra_max)}`;

    // Abrir el reporte PDF con los filtros aplicados
    window.open(url, "_blank");

    // Limpiar los campos de filtro después de abrir el reporte
    $("#filtro_usuario_compra").val("");
    $("#filtro_fecha_desde_compra").val("");
    $("#filtro_fecha_hasta_compra").val("");
    $("#filtro_tipo_comprobante_compra").val("");
    $("#filtro_estado_pago_compra").val("");
    $("#filtro_total_compra_min").val("");
    $("#filtro_total_compra_max").val("");
});
