/* =====================================
 CONVERSIÓN DE MONEDA Y TASA DE CAMBIO
 ===================================== */
let currentRate = 0;

async function getExchangeRate() {
    try {
        // Intentar con la API principal
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/PEN');
        if (!response.ok) throw new Error('Error en API principal');
        
        const data = await response.json();
        if (data.rates && data.rates.USD) {
            return data.rates.USD;
        }
        throw new Error('Tasa no disponible');
    } catch (error) {
        console.warn('Error con API principal, usando respaldo:', error);
        
        // Intentar con API de respaldo
        try {
            const backupResponse = await fetch('https://open.er-api.com/v6/latest/PEN');
            if (!backupResponse.ok) throw new Error('Error en API de respaldo');
            
            const backupData = await backupResponse.json();
            if (backupData.rates && backupData.rates.USD) {
                return backupData.rates.USD;
            }
            throw new Error('Tasa no disponible en respaldo');
        } catch (backupError) {
            console.error('Error con API de respaldo:', backupError);
            return null;
        }
    }
}

async function updateExchangeRate() {
    try {
        const rate = await getExchangeRate();
        if (rate) {
            currentRate = rate;
            console.log('Tasa de cambio actualizada:', currentRate);
        } else {
            console.warn('No se pudo obtener la tasa de cambio');
        }
    } catch (error) {
        console.error('Error al actualizar tasa de cambio:', error);
    }
}

// Actualizar tasa al cargar y cada hora
$(document).ready(function() {
    updateExchangeRate();
    setInterval(updateExchangeRate, 60 * 60 * 1000);
});

/* =====================================
 FUNCIONES PRINCIPALES
 ===================================== */

// Formatear números como moneda
function formatCurrency(amount, currency = 'S/') {
    return `${currency} ${parseFloat(amount).toFixed(2)}`;
}

// Mostrar resumen estadístico
function updateSummaryStats(data) {
    let totalVentas = 0;
    let totalPagado = 0;
    
    data.forEach(item => {
        totalVentas += parseFloat(item.total_venta) || 0;
        totalPagado += parseFloat(item.total_pago) || 0;
    });
    
    const saldoPendiente = totalVentas - totalPagado;
    
    // Actualizar UI
    $('#total-ventas').text(formatCurrency(totalVentas));
    $('#total-pagado').text(formatCurrency(totalPagado));
    $('#saldo-pendiente').text(formatCurrency(saldoPendiente));
    $('#total-registros').text(data.length);
    
    // Mostrar en dólares si hay tasa de cambio
    if (currentRate > 0) {
        $('#total-ventas-usd').text(`USD ${(totalVentas * currentRate).toFixed(2)}`);
        $('#total-pagado-usd').text(`USD ${(totalPagado * currentRate).toFixed(2)}`);
        $('#saldo-pendiente-usd').text(`USD ${(saldoPendiente * currentRate).toFixed(2)}`);
    }
}

// Inicializar DataTable
function initDataTable() {
    return $('#tabla_clientes_reporte').DataTable({
        dom: '<"top"Bf>rt<"bottom"lip><"clear">',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copiar',
                className: 'btn btn-secondary'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info'
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        autoWidth: false,
        order: [[3, 'desc']] // Ordenar por fecha descendente
    });
}

// Cargar datos de ventas por cliente
async function mostrarClienteVenta(datos) {
    try {
        // Mostrar loader
        $('#data_clientes_reporte').html('<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status"></div> Cargando datos...</td></tr>');
        
        // Obtener datos via AJAX
        const response = await $.ajax({
            url: "ajax/Reporte.clientes.ajax.php",
            type: "POST",
            data: datos,
            processData: false,
            contentType: false,
            dataType: "json"
        });
        
        // Limpiar tabla
        let tbody = $("#data_clientes_reporte");
        tbody.empty();
        
        // Procesar cada venta
        response.forEach((venta, index) => {
            // Formatear valores
            const nombreUsuario = venta.nombre_usuario || '<span class="text-muted">N/A</span>';
            const razonSocial = venta.razon_social || '<span class="text-muted">Cliente no especificado</span>';
            const fechaVenta = venta.fecha_venta ? new Date(venta.fecha_venta).toLocaleDateString() : 'N/A';
            const comprobante = venta.tipo_comprobante_sn || 'N/A';
            const serieNumero = `${venta.serie_prefijo || '000'}-${venta.num_comprobante || '000000'}`;
            const totalVenta = parseFloat(venta.total_venta) || 0;
            const totalPago = parseFloat(venta.total_pago) || 0;
            const saldo = totalVenta - totalPago;
            
            // Formatear montos en dólares si hay tasa de cambio
            let totalVentaUsd = '';
            let totalPagoUsd = '';
            let saldoUsd = '';
            
            if (currentRate > 0) {
                totalVentaUsd = `<div class="text-muted small">USD ${(totalVenta * currentRate).toFixed(2)}</div>`;
                totalPagoUsd = `<div class="text-muted small">USD ${(totalPago * currentRate).toFixed(2)}</div>`;
                saldoUsd = `<div class="text-muted small">USD ${(saldo * currentRate).toFixed(2)}</div>`;
            }
            
            // Determinar estado de pago
            let estadoPago = '';
            if (venta.estado_pago === 'completado') {
                estadoPago = '<span class="badge bg-success">Pagado</span>';
            } else if (saldo <= 0) {
                estadoPago = '<span class="badge bg-success">Pagado</span>';
            } else if (totalPago > 0) {
                estadoPago = '<span class="badge bg-warning">Parcial</span>';
            } else {
                estadoPago = '<span class="badge bg-danger">Pendiente</span>';
            }
            
            // Determinar clase para saldo
            const saldoClass = saldo > 0 ? 'text-danger' : 'text-success';
            
            // Crear fila
            const fila = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${razonSocial}</td>
                    <td>${nombreUsuario}</td>
                    <td>${fechaVenta}</td>
                    <td>${comprobante}</td>
                    <td>${serieNumero}</td>
                    <td>
                        <div>S/ ${totalVenta.toFixed(2)}</div>
                        ${totalVentaUsd}
                    </td>
                    <td>
                        <div>S/ ${totalPago.toFixed(2)}</div>
                        ${totalPagoUsd}
                    </td>
                    <td class="${saldoClass}">
                        <div>S/ ${saldo.toFixed(2)}</div>
                        ${saldoUsd}
                    </td>
                    <td>${estadoPago}</td>
                </tr>
            `;
            
            tbody.append(fila);
        });
        
        // Actualizar resumen estadístico
        updateSummaryStats(response);
        
        // Reinicializar DataTable si ya existe
        if ($.fn.DataTable.isDataTable('#tabla_clientes_reporte')) {
            $('#tabla_clientes_reporte').DataTable().destroy();
        }
        
        // Inicializar DataTable
        initDataTable();
        
    } catch (error) {
        console.error("Error al cargar datos:", error);
        tbody.html('<tr><td colspan="10" class="text-center text-danger">Error al cargar los datos. Por favor intente nuevamente.</td></tr>');
    }
}

/* =====================================
 EVENTOS
 ===================================== */

// Aplicar filtros
$("#btn_filtro_venta_cliente").on("click", async function(e) {
    e.preventDefault();
    
    const idCliente = $("#filtro_cliente_venta").val();
    const fechaDesde = $("#filtro_fecha_desde_venta_cliente").val();
    const fechaHasta = $("#filtro_fecha_hasta_venta_cliente").val();
    const tipoVenta = $("#filtro_tipo_venta_cliente").val();
    const estadoPago = $("#filtro_estado_pago").val();
    
    const datos = new FormData();
    datos.append("id_cliente", idCliente);
    datos.append("fecha_desde", fechaDesde);
    datos.append("fecha_hasta", fechaHasta);
    datos.append("tipo_venta", tipoVenta);
    datos.append("estado_pago", estadoPago);
    
    await mostrarClienteVenta(datos);
});

// Generar reporte PDF
$("#seccion_clientes_reporte").on("click", ".reporte_clientes_pdf", function(e) {
    e.preventDefault();
    
    const idCliente = $("#filtro_cliente_venta").val();
    const fechaDesde = $("#filtro_fecha_desde_venta_cliente").val();
    const fechaHasta = $("#filtro_fecha_hasta_venta_cliente").val();
    const tipoVenta = $("#filtro_tipo_venta_cliente").val();
    const estadoPago = $("#filtro_estado_pago").val();
    
    const url = `extensiones/reportes/clientes.php?` +
        `id_cliente=${encodeURIComponent(idCliente)}` +
        `&fecha_desde=${encodeURIComponent(fechaDesde)}` +
        `&fecha_hasta=${encodeURIComponent(fechaHasta)}` +
        `&tipo_venta=${encodeURIComponent(tipoVenta)}` +
        `&estado_pago=${encodeURIComponent(estadoPago)}` +
        `&tasa_cambio=${encodeURIComponent(currentRate)}`;
    
    window.open(url, "_blank");
});

// Generar reporte Excel
$("#seccion_clientes_reporte").on("click", ".reporte_clientes_excel", function(e) {
    e.preventDefault();
    
    const idCliente = $("#filtro_cliente_venta").val();
    const fechaDesde = $("#filtro_fecha_desde_venta_cliente").val();
    const fechaHasta = $("#filtro_fecha_hasta_venta_cliente").val();
    const tipoVenta = $("#filtro_tipo_venta_cliente").val();
    const estadoPago = $("#filtro_estado_pago").val();
    
    const url = `extensiones/reportes/clientes_excel.php?` +
        `id_cliente=${encodeURIComponent(idCliente)}` +
        `&fecha_desde=${encodeURIComponent(fechaDesde)}` +
        `&fecha_hasta=${encodeURIComponent(fechaHasta)}` +
        `&tipo_venta=${encodeURIComponent(tipoVenta)}` +
        `&estado_pago=${encodeURIComponent(estadoPago)}` +
        `&tasa_cambio=${encodeURIComponent(currentRate)}`;
    
    window.open(url, "_blank");
});

// Cargar datos iniciales
$(document).ready(function() {

    // Configurar datepicker
    $('.flatpickr').flatpickr({
        dateFormat: "Y-m-d",
        locale: "es"
    });
    
    // Cargar datos iniciales
    const datos = new FormData();
    mostrarClienteVenta(datos);
});