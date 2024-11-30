$("#btn_aplicar_filtros_asistencia").on("click", function (e) {
    e.preventDefault();

    let filtro_trabajador_asistencia = $("#filtro_trabajador_asistencia").val();
    let filtro_estado_asistencia = $("#filtro_estado_asistencia").val();
    let filtro_fecha_desde_asistencia = $("#filtro_fecha_desde_asistencia").val();
    let filtro_fecha_hasta_asistencia = $("#filtro_fecha_hasta_asistencia").val();

    const datos = new FormData();
    datos.append("filtro_trabajador_asistencia", filtro_trabajador_asistencia);
    datos.append("filtro_estado_asistencia", filtro_estado_asistencia);
    datos.append("filtro_fecha_desde_asistencia", filtro_fecha_desde_asistencia);
    datos.append("filtro_fecha_hasta_asistencia", filtro_fecha_hasta_asistencia);

    mostrarAsistenciaReporte(datos);
    $('#tabla_asistencia_reporte').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarAsistenciaReporte(datos) {
    $.ajax({
        url: "ajax/Reporte.asistencia.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (asistencias) {
            let tbody = $("#data_asistencia_reporte");
            tbody.empty();

            // Función para formatear la fecha
            function formatFecha(fecha) {
                if (!fecha) return '<span class="text-secondary">No aplica</span>';
                const opciones = { year: 'numeric', month: '2-digit', day: '2-digit' };
                return new Date(fecha).toLocaleDateString('es-PE', opciones);
            }

            // Función para formatear la hora en AM/PM
            function formatHora(hora) {
                if (!hora) return '<span class="text-secondary">No aplica</span>';
                const [hour, minute] = hora.split(':');
                const date = new Date();
                date.setHours(hour, minute);
                return date.toLocaleTimeString('es-PE', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                });
            }

            asistencias.forEach(function (asistencia, index) {
                let nombre = asistencia.nombre || '<span class="text-secondary">Sin nombre</span>';
                let fecha_asistencia = formatFecha(asistencia.fecha_asistencia);
                let hora_entrada = formatHora(asistencia.hora_entrada);
                let hora_salida = formatHora(asistencia.hora_salida);

                // Asignación del estado con clase para color según el estado
                let estadoClass;
                let estadoTexto;

                switch (asistencia.estado) {
                    case 'Presente':
                        estadoClass = 'text-success'; // Verde
                        estadoTexto = 'Presente';
                        break;
                    case 'Tarde':
                        estadoClass = 'text-warning'; // Amarillo
                        estadoTexto = 'Tarde';
                        break;
                    case 'Falta':
                        estadoClass = 'text-danger'; // Rojo
                        estadoTexto = 'Falta';
                        break;
                    default:
                        estadoClass = 'text-secondary'; // Gris
                        estadoTexto = 'No aplica';
                        break;
                }

                // Asignación de observaciones
                let observaciones = asistencia.observaciones || '<span class="text-secondary">Ninguno</span>';

                // Generación de la fila
                let fila = `
            <tr>
                <td>${index + 1}</td>
                <td>${nombre}</td>
                <td>${fecha_asistencia}</td>
                <td>${hora_entrada}</td>
                <td>${hora_salida}</td>
                <td><span class="${estadoClass}">${estadoTexto}</span></td>
                <td>${observaciones}</td>
            </tr>
        `;
                tbody.append(fila);
            });

            $('#tabla_asistencia_reporte').DataTable();
        },


        error: function (xhr, status, error) {
            console.error("Error al recuperar los pagos:", error);
            console.log(xhr);
            console.log(status);
        },
    });
}

let datos = new FormData();
mostrarAsistenciaReporte(datos);

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
$("#seccion_asistencia_reporte").on("click", ".reporte_asistencia_pdf", (e) => {
    e.preventDefault();

    let filtro_trabajador_asistencia = $("#filtro_trabajador_asistencia").val();
    let filtro_estado_asistencia = $("#filtro_estado_asistencia").val();
    let filtro_fecha_desde_asistencia = $("#filtro_fecha_desde_asistencia").val();
    let filtro_fecha_hasta_asistencia = $("#filtro_fecha_hasta_asistencia").val();

    const url = `extensiones/reportes/asistencias.php?` +
        `filtro_trabajador_asistencia=${filtro_trabajador_asistencia}&` +
        `filtro_estado_asistencia=${filtro_estado_asistencia}&` +
        `filtro_fecha_desde_asistencia=${filtro_fecha_desde_asistencia}&` +
        `filtro_fecha_hasta_asistencia=${filtro_fecha_hasta_asistencia}`;

    window.open(url, "_blank");
});
