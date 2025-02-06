/* =====================================
 CONVERTIR DE DOLARES A 
 ===================================== */
let currentRate = 0;

async function getExchangeRate() {
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

$("#btn_filtro_venta_cliente").on("click", function (e) {
    e.preventDefault();
    let id_cliente = $("#filtro_cliente_venta").val();
    let fecha_desde = $("#filtro_fecha_desde_venta_cliente").val();
    let fecha_hasta = $("#filtro_fecha_hasta_venta_cliente").val();
    let tipo_venta = $("#filtro_tipo_venta_cliente").val();

    const datos = new FormData();
    datos.append("id_cliente", id_cliente);
    datos.append("fecha_desde", fecha_desde);
    datos.append("fecha_hasta", fecha_hasta);
    datos.append("tipo_venta", tipo_venta);
    mostrarClienteVenta(datos);
    $('#tabla_clientes_reporte').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
async function mostrarClienteVenta(datos) {
    await updateRate();
    $.ajax({
        url: "ajax/Reporte.clientes.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (ventas) { 
            let tbody = $("#data_clientes_reporte");
            tbody.empty();

            ventas.forEach(function (producto, index) {
                // Reemplazar valores vacíos
                let nombre_usuario = producto.nombre_usuario || '<span class="text-secondary">Sin categoría</span>';
                let razon_social = producto.razon_social || '<span class="text-secondary">Sin proveedor</span>';
                let fecha_venta = producto.fecha_venta || '<span class="text-secondary">Sin fecha</span>';
                let comprobante = producto.tipo_comprobante_sn || '<span class="text-secondary">Sin comprobante</span>';
                let serie = producto.serie_prefijo || '<span class="text-secondary">Sin serie</span>';
                let numero = producto.num_comprobante || '<span class="text-secondary">Sin número</span>';
                let total_venta = producto.total_venta || '<span class="text-secondary">0.00</span>';
                let total_pago = producto.total_pago || '<span class="text-secondary">0.00</span>';

                // Determinar el estado con color
                let estado =
                    producto.estado_pago === 'completado'
                        ? `<span class="text-success">Pagado</span>`
                        : `<span class="text-danger">Pendiente</span>`;
                var venta_total = currentRate > 0 ? (producto.total_venta * currentRate).toFixed(2) : "N/A";
                var venta_total_pago = currentRate > 0 ? (producto.total_pago * currentRate).toFixed(2) : "N/A";
                // Generar fila
                let fila = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${razon_social}</td>
                                <td>${nombre_usuario}</td>
                                <td>${fecha_venta}</td>
                                <td>${comprobante}</td>
                                <td>${serie}-${numero}</td>
                                <td>
                                    <div>S/ ${total_venta}</div>
                                    <div>USD ${venta_total}</div>
                                </td>
                                <td>
                                    <div>S/ ${total_pago}</div>
                                    <div>USD ${venta_total_pago}</div>
                                </td>
                                <td>${estado}</td>
                            </tr>
                        `;
                tbody.append(fila);
            });

            // Inicializar DataTable
            $('#tabla_clientes_reporte').DataTable();
        },

        error: function (xhr, status, error) {
            console.error("Error al recuperar los productos:", error);
            console.log(xhr);
            console.log(status);
        },
    });
}

let datos = new FormData();
mostrarClienteVenta(datos);

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
 $("#seccion_clientes_reporte").on("click", ".reporte_clientes_pdf", (e) => {
    e.preventDefault();

    let id_cliente = $("#filtro_cliente_venta").val();
    let fecha_desde = $("#filtro_fecha_desde_venta_cliente").val();
    let fecha_hasta = $("#filtro_fecha_hasta_venta_cliente").val();
    let tipo_venta = $("#filtro_tipo_venta_cliente").val();

    // Construir la URL con los filtros como parámetros
    const url = `extensiones/reportes/clientes.php?` +
        `id_cliente=${encodeURIComponent(id_cliente)}` +
        `&fecha_desde=${encodeURIComponent(fecha_desde)}` +
        `&fecha_hasta=${encodeURIComponent(fecha_hasta)}` +
        `&tipo_venta=${encodeURIComponent(tipo_venta)}`;

    // Abrir el reporte PDF con los filtros aplicados
    window.open(url, "_blank");

    // Refrescar la página para restablecer los filtros
    location.reload();
});
