/* ===========================
MOSTRANDO REPORTE DE TRABAJADORES
=========================== */
function mostrarReporteTrabjadores() {
    $.ajax({
        url: "ajax/Reporte.trabajador.ajax.php",
        type: "GET",
        dataType: "json",
        success: function (trabajadores) {
            let tbody = $("#data_trabajadores_reporte");
            tbody.empty();
            trabajadores.forEach(function (trabajador, index) {
                // Reemplazar valores vacíos
                let nombre = trabajador.nombre || '<span class="text-secondary">No tiene</span>';
                let numeroDoc = trabajador.num_documento || '<span class="text-secondary">No tiene</span>';
                let telefono = trabajador.telefono || '<span class="text-secondary">No tiene</span>';
                let correo = trabajador.correo || '<span class="text-secondary">No tiene</span>';
                let tipo_pago = trabajador.tipo_pago || '<span class="text-secondary">No tiene</span>';
                let num_cuenta = trabajador.num_cuenta || '<span class="text-secondary">No tiene</span>';
                let estado = trabajador.estado_trabajador != 0 ? 'Activo' : 'Inactivo';

                // Generar fila
                let fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${nombre}</td>
                            <td>${numeroDoc}</td>
                            <td>${telefono}</td>
                            <td>${correo}</td>
                            <td>${tipo_pago}</td>
                            <td>${num_cuenta}</td>
                            <td>${estado}</td>

                        </tr>
                    `;
                tbody.append(fila);

            });
            $('#tabla_trabajadores_reporte').DataTable();
        },
        error: function (xhr, status, error) {
            console.error("Error al recuperar los trabajadors:", error);
        },
    });
}

mostrarReporteTrabjadores();

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
$("#seccion_trabajadores_reporte").on("click", ".reporte_trabajadores_pdf", (e) => {
    e.preventDefault();
    const url = "extensiones/reportes/trabajadores.php";
    window.open(url, "_blank");
});
