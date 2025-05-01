async function obtenerSesion() {
    try {
        const response = await fetch('ajax/sesion.ajax.php?action=sesion', {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            credentials: 'include'
        });
        
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        
        const data = await response.json();
        return data.status === false ? null : data;
        
    } catch (error) {
        console.error('Error al obtener sesión:', error);
        return null;
    }
}

$("#btn_aplicar_filtros_pago_trabajadores").on("click", function (e) {
    e.preventDefault();
    let filtro_estado_pago_t = $("#filtro_estado_pago_t").val();
    let filtro_fecha_desde_pago_t = $("#filtro_fecha_desde_pago_t").val();
    let filtro_fecha_hasta_pago_t = $("#filtro_fecha_hasta_pago_t").val();

    const datos = new FormData();
    datos.append("filtro_estado_pago_t", filtro_estado_pago_t);
    datos.append("filtro_fecha_desde_pago_t", filtro_fecha_desde_pago_t);
    datos.append("filtro_fecha_hasta_pago_t", filtro_fecha_hasta_pago_t);

    mostrarPagosReporte(datos);
    $('#tabla_pago_trabajadores_reporte').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarPagosReporte(datos) {
    $.ajax({
        url: "ajax/Reporte.pago.trabajador.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (pagos) {
            let tbody = $("#data_pago_trabajadores_reporte");
            tbody.empty();

            pagos.forEach(function (pago, index) {
                let nombre = pago.nombre || '<span class="text-secondary">Sin nombre</span>';
                let monto = pago.monto_pago || 0; // Valor predeterminado 0 si no hay monto
                let fecha = pago.fecha_pago || '<span class="text-secondary">No aplica</span>';
                let estadoClass = pago.estado_pago === 1 ? 'text-success' : 'text-danger';
                let estado = `<span class="${estadoClass}">${pago.estado_pago === 1 ? 'Pagado' : 'Pendiente'}</span>`;

                // Formatear el monto con 2 decimales y separadores de miles
                monto = parseFloat(monto).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                let fila = `
            <tr>
                <td>${index + 1}</td>
                <td>${nombre}</td>
                <td><b>S/ ${monto}</b></td>
                <td>${fecha}</td>
                <td>${estado}</td>
            </tr>
        `;
                tbody.append(fila);
            });

            $('#tabla_pago_trabajadores_reporte').DataTable();
        },


        error: function (xhr, status, error) {
            console.error("Error al recuperar los pagos:", error);
            console.log(xhr);
            console.log(status);
        },
    });
}

let datos = new FormData();
mostrarPagosReporte(datos);

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
$("#seccion_pago_trabajadores_reporte").on("click", ".reporte_pago_trabajadores_pdf", (e) => {
    e.preventDefault();

    let filtro_estado_pago_t = $("#filtro_estado_pago_t").val();
    let filtro_fecha_desde_pago_t = $("#filtro_fecha_desde_pago_t").val();
    let filtro_fecha_hasta_pago_t = $("#filtro_fecha_hasta_pago_t").val();

    const url = `extensiones/reportes/pagos_trabajador.php?` +
        `filtro_estado_pago_t=${filtro_estado_pago_t}&` +
        `filtro_fecha_desde_pago_t=${filtro_fecha_desde_pago_t}&` +
        `filtro_fecha_hasta_pago_t=${filtro_fecha_hasta_pago_t}`;

    window.open(url, "_blank");
});
