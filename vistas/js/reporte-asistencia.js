$(document).ready(function() {
    // Inicializar Select2
    $('.js-example-basic-single').select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: '100%'
    });

    // Inicializar DataTable con configuración básica
    var table = $('#tabla_asistencia_reporte').DataTable({
        "responsive": true,
        "autoWidth": false,
        "processing": true,
        "serverSide": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "paging": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    // Función para formatear la fecha
    function formatFecha(fecha) {
        if (!fecha) return '<span class="text-secondary">No aplica</span>';
        try {
            const opciones = { year: 'numeric', month: '2-digit', day: '2-digit' };
            return new Date(fecha).toLocaleDateString('es-PE', opciones);
        } catch (e) {
            console.error("Error al formatear fecha:", e);
            return fecha; // Devuelve la fecha original si hay error
        }
    }

    // Función para formatear la hora en AM/PM
    function formatHora(hora) {
        if (!hora) return '<span class="text-secondary">No aplica</span>';
        try {
            const [hour, minute] = hora.split(':');
            const date = new Date();
            date.setHours(hour, minute);
            return date.toLocaleTimeString('es-PE', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
            });
        } catch (e) {
            console.error("Error al formatear hora:", e);
            return hora; // Devuelve la hora original si hay error
        }
    }

    // Función para cargar datos en la tabla
    function cargarDatosEnTabla(datos) {
        // Limpiar la tabla
        table.clear().draw();
        
        // Verificar si hay datos
        if (!datos || !Array.isArray(datos)) {
            console.error("Datos no válidos recibidos:", datos);
            Swal.fire({
                title: "Error",
                text: "No se recibieron datos válidos del servidor",
                icon: "error"
            });
            return;
        }

        // Procesar cada registro
        datos.forEach(function(asistencia, index) {
            // Validar y formatear los datos
            const nombre = asistencia.nombre || asistencia.nombre_completo || asistencia.nombre_trabajador || '<span class="text-secondary">Sin nombre</span>';
            const fecha_asistencia = formatFecha(asistencia.fecha_asistencia || asistencia.fecha);
            const hora_entrada = formatHora(asistencia.hora_entrada || asistencia.entrada);
            const hora_salida = formatHora(asistencia.hora_salida || asistencia.salida);
            const observaciones = asistencia.observaciones || '<span class="text-secondary">Ninguno</span>';

            // Determinar el estado y su clase CSS
            let estadoClass, estadoTexto;
            switch (asistencia.estado) {
                case 'Presente':
                    estadoClass = 'badge bg-success';
                    estadoTexto = 'Presente';
                    break;
                case 'Tarde':
                    estadoClass = 'badge bg-warning text-dark';
                    estadoTexto = 'Tarde';
                    break;
                case 'Falta':
                    estadoClass = 'badge bg-danger';
                    estadoTexto = 'Falta';
                    break;
                default:
                    estadoClass = 'badge bg-secondary';
                    estadoTexto = asistencia.estado || 'No registrado';
                    break;
            }

            // Agregar fila a DataTable
            table.row.add([
                index + 1,
                nombre,
                fecha_asistencia,
                hora_entrada,
                hora_salida,
                `<span class="${estadoClass}">${estadoTexto}</span>`,
                observaciones,
                `<div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-info btnVerDetalle mx-1" data-id="${asistencia.id_asistencia || asistencia.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning btnEditarAsistencia mx-1" data-id="${asistencia.id_asistencia || asistencia.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>`
            ]);
        });

        // Dibujar todas las filas al final
        table.draw();
    }

    // Función para obtener y mostrar asistencias
    function mostrarAsistenciaReporte(datos) {
        $.ajax({
            url: "ajax/Reporte.asistencia.ajax.php",
            type: "POST",
            data: datos,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend: function() {
                // Mostrar carga
                $('#tabla_asistencia_reporte_wrapper').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
            },
            complete: function() {
                // Ocultar carga
                $('.overlay').remove();
            },
            success: function(response) {
                // Verificar si la respuesta es válida
                if (!response) {
                    console.error("Respuesta vacía recibida del servidor");
                    Swal.fire({
                        title: "Error",
                        text: "No se recibieron datos del servidor",
                        icon: "error"
                    });
                    return;
                }

                // Verificar si hay error en la respuesta
                if (response.error) {
                    console.error("Error del servidor:", response.error);
                    Swal.fire({
                        title: "Error",
                        text: response.error,
                        icon: "error"
                    });
                    return;
                }

                // Si todo está bien, cargar los datos
                /* console.log("Datos recibidos:", response); */
                cargarDatosEnTabla(response);
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", status, error);
                Swal.fire({
                    title: "Error",
                    text: "No se pudieron cargar los datos de asistencia: " + error,
                    icon: "error"
                });
            }
        });
    }

    // Aplicar filtros
    $("#btn_aplicar_filtros_asistencia").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();

        let filtro_trabajador_asistencia = $("#filtro_trabajador_asistencia").val();
        let filtro_estado_asistencia = $("#filtro_estado_asistencia").val();
        let filtro_fecha_desde_asistencia = $("#filtro_fecha_desde_asistencia").val();
        let filtro_fecha_hasta_asistencia = $("#filtro_fecha_hasta_asistencia").val();

        const datos = new FormData();
        if (filtro_trabajador_asistencia) datos.append("filtro_trabajador_asistencia", filtro_trabajador_asistencia);
        if (filtro_estado_asistencia) datos.append("filtro_estado_asistencia", filtro_estado_asistencia);
        if (filtro_fecha_desde_asistencia) datos.append("filtro_fecha_desde_asistencia", filtro_fecha_desde_asistencia);
        if (filtro_fecha_hasta_asistencia) datos.append("filtro_fecha_hasta_asistencia", filtro_fecha_hasta_asistencia);

        mostrarAsistenciaReporte(datos);
    });

    // Limpiar filtros
    $("#btn_limpiar_filtros_asistencia").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $("#filtro_trabajador_asistencia").val('').trigger('change');
        $("#filtro_estado_asistencia").val('');
        $("#filtro_fecha_desde_asistencia").val('');
        $("#filtro_fecha_hasta_asistencia").val('');
        
        // Cargar todos los datos sin filtros
        mostrarAsistenciaReporte(new FormData());
    });

    // Descargar reporte PDF
    $(document).on("click", ".reporte_asistencia_pdf", function(e) {
        e.preventDefault();
        e.stopPropagation();

        let filtro_trabajador_asistencia = $("#filtro_trabajador_asistencia").val();
        let filtro_estado_asistencia = $("#filtro_estado_asistencia").val();
        let filtro_fecha_desde_asistencia = $("#filtro_fecha_desde_asistencia").val();
        let filtro_fecha_hasta_asistencia = $("#filtro_fecha_hasta_asistencia").val();

        const params = new URLSearchParams();
        if (filtro_trabajador_asistencia) params.append("filtro_trabajador_asistencia", filtro_trabajador_asistencia);
        if (filtro_estado_asistencia) params.append("filtro_estado_asistencia", filtro_estado_asistencia);
        if (filtro_fecha_desde_asistencia) params.append("filtro_fecha_desde_asistencia", filtro_fecha_desde_asistencia);
        if (filtro_fecha_hasta_asistencia) params.append("filtro_fecha_hasta_asistencia", filtro_fecha_hasta_asistencia);

        window.open(`extensiones/reportes/asistencias.php?${params.toString()}`, "_blank");
    });

    // Ver detalle de asistencia
    $('#tabla_asistencia_reporte').on('click', '.btnVerDetalle', function() {
        var idAsistencia = $(this).data('id');
        
        $.ajax({
            url: 'ajax/Asistencia.ajax.php',
            type: 'POST',
            data: { 
                action: 'detalle', 
                id_asistencia: idAsistencia 
            },
            beforeSend: function() {
                $('#modalDetalleAsistencia .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            },
            success: function(response) {
                $('#contenidoDetalleAsistencia').html(response);
                $('#modalDetalleAsistencia').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar detalle:", error);
                $('#contenidoDetalleAsistencia').html('<div class="alert alert-danger">Error al cargar los detalles</div>');
                $('#modalDetalleAsistencia').modal('show');
            }
        });
    });

    // Imprimir detalle
    $('#btnImprimirDetalle').click(function() {
        var contenido = $('#contenidoDetalleAsistencia').html();
        var ventana = window.open('', '_blank');
        ventana.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Detalle de Asistencia</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .titulo { text-align: center; margin-bottom: 20px; }
                    .detalle { margin-bottom: 15px; }
                    .label { font-weight: bold; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    table, th, td { border: 1px solid #ddd; }
                    th, td { padding: 8px; text-align: left; }
                    .badge { padding: 5px 10px; border-radius: 3px; font-weight: bold; }
                    .bg-success { background-color: #28a745!important; color: white; }
                    .bg-warning { background-color: #ffc107!important; color: black; }
                    .bg-danger { background-color: #dc3545!important; color: white; }
                    .bg-secondary { background-color: #6c757d!important; color: white; }
                </style>
            </head>
            <body>
                ${contenido}
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                            window.close();
                        }, 500);
                    };
                <\/script>
            </body>
            </html>
        `);
        ventana.document.close();
    });

    // Cargar datos iniciales al cargar la página
    mostrarAsistenciaReporte(new FormData());
});