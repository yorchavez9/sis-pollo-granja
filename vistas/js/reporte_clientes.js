/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarClientes() {
    $.ajax({
        url: "ajax/Reporte.clientes.ajax.php",
        type: "GET",
        dataType: "json",
        success: function (clientes) {
            let tbody = $("#data_clientes_reporte");
            tbody.empty();

            clientes.forEach(function (cliente, index) {
                if (cliente.tipo_persona === "cliente") {
                    // Reemplazar valores vacíos
                    let razonSocial = cliente.razon_social || '<span class="text-secondary">No tiene</span>';
                    let tipoDoc = cliente.nombre_doc || '<span class="text-secondary">No tiene</span>';
                    let numeroDoc = cliente.numero_documento || '<span class="text-secondary">No tiene</span>';
                    let telefono = cliente.telefono || '<span class="text-secondary">No tiene</span>';
                    let direccion = cliente.direccion || '<span class="text-secondary">No tiene</span>';
                    let ciudad = cliente.ciudad || '<span class="text-secondary">No tiene</span>';
                    let codigoPostal = cliente.codigo_postal || '<span class="text-secondary">No tiene</span>';
                    let estado = cliente.estado_persona != 0 ? 'Activo' : 'Inactivo';

                    // Generar fila
                    let fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${razonSocial}</td>
                            <td class="text-center">
                                <span>${tipoDoc}:</span><br>
                                <span>${numeroDoc}</span>
                            </td>
                            <td>${telefono}</td>
                            <td>${direccion}</td>
                            <td>${ciudad}</td>
                            <td>${codigoPostal}</td>
                            <td>${estado}</td>

                        </tr>
                    `;
                    tbody.append(fila);
                }
            });

            // Inicializar DataTable (destruir si ya existe)
            if ($.fn.DataTable.isDataTable('#tabla_clientes_reporte')) {
                $('#tabla_clientes_reporte').DataTable().destroy();
            }
            $('#tabla_clientes_reporte').DataTable();
        },
        error: function (xhr, status, error) {
            console.error("Error al recuperar los clientes:", error);
        },
    });
}

mostrarClientes();

/*=============================================
 DESCARGAR REPORTE
 =============================================*/
$("#seccion_clientes_reporte").on("click", ".reporte_clientes_pdf", (e) => {
    e.preventDefault();
    const url = "extensiones/reportes/clientes.php";
    window.open(url, "_blank");
});
