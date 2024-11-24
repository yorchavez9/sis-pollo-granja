/* ===========================
MOSTRANDO CLIENTES
=========================== */
function mostrarClientes() {
    $.ajax({
        url: "ajax/Reporte.clientes.ajax.php",
        type: "GET",
        dataType: "json",
        success: function (clientes) {
            console.log(clientes);
            return;
            let tbody = $("#data_clientes_reporte");
            tbody.empty();
            clientes.forEach(function (cliente, index) {
                if (cliente.tipo_persona == "cliente") {
                    // Reemplazar valores vacíos con "No tiene" y agregar clase para opacidad
                    var razonSocial = cliente.razon_social ? cliente.razon_social : '<span class="text-secondary">No tiene</span>';
                    var nombreDoc = cliente.nombre_doc ? cliente.nombre_doc : '<span class="text-secondary">No tiene</span>';
                    var numeroDoc = cliente.numero_documento ? cliente.numero_documento : '<span class="text-secondary">No tiene</span>';
                    var direccion = cliente.direccion ? cliente.direccion : '<span class="text-secondary">No tiene</span>';
                    var telefono = cliente.telefono ? cliente.telefono : '<span class="text-secondary">No tiene</span>';
                    var email = cliente.email ? cliente.email : '<span class="text-secondary">No tiene</span>';
                    var fila = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${razonSocial}</td>
                            <td class="text-center">
                                <span>${nombreDoc}:</span><br>
                                <span>${numeroDoc}</span>
                            </td>
                            <td>${direccion}</td>
                            <td>${telefono}</td>
                            <td>${email}</td>
                            <td>
                                ${cliente.estado_persona != 0 ? '<button class="btn bg-lightgreen badges btn-sm rounded btnActivar" idCliente="' + cliente.id_persona + '" estadoCliente="0">Activado</button>' : '<button class="btn bg-lightred badges btn-sm rounded btnActivar" idCliente="' + cliente.id_persona + '" estadoCliente="1">Desactivado</button>'}
                            </td>
                            <td class="text-center">
                                <a href="#" class="me-3 btnEditarCliente" idCliente="${cliente.id_persona}" data-bs-toggle="modal" data-bs-target="#modalEditarCliente">
                                    <i class="text-warning fas fa-edit fa-lg"></i>
                                </a>
                                <a href="#" class="me-3 btnVerCliente" idVerCliente="${cliente.id_persona}" data-bs-toggle="modal" data-bs-target="#modalVerCliente">
                                    <i class="text-primary fa fa-eye fa-lg"></i>
                                </a>
                                <a href="#" class="me-3 confirm-text btnEliminarCliente" idCliente="${cliente.id_persona}">
                                    <i class="text-danger fa fa-trash fa-lg"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.append(fila);
                }
            });

            // Inicializar DataTables después de cargar los datos
            $('#tabla_clientes_reporte').DataTable();
        },
        error: function (xhr, status, error) {
            console.error("Error al recuperar los proveedores:", error);
        },
    });
}
mostrarClientes();
