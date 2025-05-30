
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

$("#btn_mostrar_reporte_IE").on("click", function(e){
    e.preventDefault();

    let id_usuario_reporte = $("#id_usuario_ingreso_egreso_reporte").val();
    let tipo_reporte = $("#estado_ingreso_egreso_reporte").val();
    let fecha_desde_reporte = $("#fecha_desde_ingreso_egreso_reporte").val();
    let fecha_hasta_reporte = $("#fecha_hasta_ingreso_egreso_reporte").val();

    const datos = new FormData();
    datos.append("id_usuario_reporte", id_usuario_reporte);
    datos.append("tipo_reporte", tipo_reporte);
    datos.append("fecha_desde_reporte", fecha_desde_reporte);
    datos.append("fecha_hasta_reporte", fecha_hasta_reporte);

    mostrarIngresoEgreso(datos);
    $('#tabla_ingreso_egreso_reporte').DataTable();
})

/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarIngresoEgreso(datos) {
    $.ajax({
        url: "ajax/Reporte.egreso.ingreso.ajax.php",
        type: "POST",
        data: datos,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            let tbody = $("#data_ingreso_egreso_reporte");
            tbody.empty();

            response.forEach(function (data, index) {
                // Generar fila
                let fila = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${data.fecha}</td>
                        <td>
                          ${data.tipo === 'ingreso' 
                            ? `<span class="p-1 rounded" style="background: #28C76F; color: white">${data.tipo}</span>` 
                            : `<span class="p-1 rounded" style="background: #F5215C; color: white">${data.tipo}</span>`}
                        </td>
                        <td>${data.concepto}</td>
                        <td>S/ ${data.monto}</td>
                        <td>${data.detalles}</td>
                    </tr>
                `;
                tbody.append(fila);
            });
            $('#tabla_ingreso_egreso_reporte').DataTable();
        },
        error: function (xhr, status, error) {
            console.error("Error al recuperar los productos:", error);
            console.log(xhr);
            console.log(status);
        },
    });
}

let datos = new FormData();
mostrarIngresoEgreso(datos);

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
 $("#seccion_ingreso_egreso_reporte").on("click", ".reporte_ingreso_egreso_extra_pdf", (e) => {
    e.preventDefault();

    // Obtenemos los valores de los filtros actuales
    let id_usuario_reporte = $("#id_usuario_ingreso_egreso_reporte").val();
    let tipo_reporte = $("#estado_ingreso_egreso_reporte").val();
    let fecha_desde_reporte = $("#fecha_desde_ingreso_egreso_reporte").val();
    let fecha_hasta_reporte = $("#fecha_hasta_ingreso_egreso_reporte").val();

    // Construir la URL con los filtros como parámetros
    const url = `extensiones/reportes/ingreso_egreso_extra.php?` +
        `id_usuario_reporte=${id_usuario_reporte}&` +
        `tipo_reporte=${tipo_reporte}&` +
        `fecha_desde_reporte=${fecha_desde_reporte}&` +
        `fecha_hasta_reporte=${fecha_hasta_reporte}`;

    // Abrir el reporte PDF con los filtros aplicados
    window.open(url, "_blank");
});

// Llenar select de usuarios con datos obtenidos del servidor
const llenarSelectUsuarios = (selector) => {
    const select = $(selector);
    select.empty();
    select.append('<option value="" disabled selected>Seleccionar usuario</option>');

    $.ajax({
        url: "ajax/Usuario.ajax.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status && response.data.length > 0) {
                response.data.forEach(usuario => {
                    select.append(`<option value="${usuario.id_usuario}">${usuario.nombre_usuario}</option>`);
                });
            } else {
                console.warn("No hay usuarios disponibles.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener usuarios:", error);
        }
    });
};

llenarSelectUsuarios("#filtro_usuario_compra");
