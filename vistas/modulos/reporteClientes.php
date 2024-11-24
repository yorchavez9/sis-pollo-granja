<div class="page-wrapper" id="seccion_clientes_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de Clientes</h4>
                <h6>Genere su reporte de clientes con filtros dinámicos</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_clientes_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtro de búsqueda -->
                <div class="table-top">
                    <div class="search-set">
                        <div class="row">
                            <!-- Filtros de consulta -->
                            <div class="col-md-3">
                                <label for="search_nombre_cliente">Nombre</label>
                                <input type="text" id="search_nombre_cliente" class="form-control" placeholder="Buscar por nombre">
                            </div>
                            <div class="col-md-3">
                                <label for="search_estado_deuda">Estado de Deuda</label>
                                <select id="search_estado_deuda" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="Pagado">Pagado</option>
                                    <option value="Pendiente">Pendiente</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search_tipo_venta">Tipo de Venta</label>
                                <select id="search_tipo_venta" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="Contado">Contado</option>
                                    <option value="Crédito">Crédito</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search_compras_realizadas">Compras Realizadas</label>
                                <input type="number" id="search_compras_realizadas" class="form-control" placeholder="Cantidad de compras">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" id="btn_filtrar">Filtrar</button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de reportes -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_clientes_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Deuda Actual</th>
                                <th>Pagos Realizados</th>
                                <th>Compras Realizadas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="data_clientes_reporte">
                            <!-- Los datos del cliente se generarán aquí -->
                        </tbody>
                    </table>
                </div>

                <!-- Modal de detalle de cliente -->
                <div class="modal fade" id="clienteDetalleModal" tabindex="-1" aria-labelledby="clienteDetalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="clienteDetalleModalLabel">Detalle de Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Información General:</h6>
                                <p><strong>Nombre:</strong> <span id="detalle_cliente_nombre"></span></p>
                                <p><strong>Teléfono:</strong> <span id="detalle_cliente_telefono"></span></p>
                                <p><strong>Correo:</strong> <span id="detalle_cliente_correo"></span></p>
                                <p><strong>Estado:</strong> <span id="detalle_cliente_estado"></span></p>

                                <hr>

                                <h6>Ventas:</h6>
                                <p><strong>Total al Contado:</strong> <span id="detalle_cliente_venta_contado"></span></p>
                                <p><strong>Total al Crédito:</strong> <span id="detalle_cliente_venta_credito"></span></p>
                                <p><strong>Deuda Actual:</strong> <span id="detalle_cliente_deuda"></span></p>
                                <p><strong>Pagos Realizados:</strong> <span id="detalle_cliente_pagos"></span></p>

                                <hr>

                                <h6>Compras Realizadas:</h6>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle_cliente_compras">
                                        <!-- Lista de compras realizadas se llenará aquí -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Lógica para manejar la filtración de los clientes según los inputs
    document.getElementById('btn_filtrar').addEventListener('click', function() {
        let nombre = document.getElementById('search_nombre_cliente').value;
        let estadoDeuda = document.getElementById('search_estado_deuda').value;
        let tipoVenta = document.getElementById('search_tipo_venta').value;
        let comprasRealizadas = document.getElementById('search_compras_realizadas').value;

        // Aquí se realizaría la llamada al servidor para filtrar los datos según los valores
        // Por ejemplo, usando AJAX o fetch para obtener los datos filtrados

        let queryString = `nombre=${nombre}&estadoDeuda=${estadoDeuda}&tipoVenta=${tipoVenta}&comprasRealizadas=${comprasRealizadas}`;

        // Simulación de actualización de la tabla con los resultados filtrados
        fetch(`/api/clientes?${queryString}`)
            .then(response => response.json())
            .then(data => {
                let tableBody = document.getElementById('data_clientes_reporte');
                tableBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos

                data.forEach((cliente, index) => {
                    let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${cliente.nombre}</td>
                            <td>${cliente.ventaContado}</td>
                            <td>${cliente.ventaCredito}</td>
                            <td>${cliente.deuda}</td>
                            <td>${cliente.pagosRealizados}</td>
                            <td>${cliente.comprasRealizadas}</td>
                            <td>${cliente.estado}</td>
                            <td>
                                <button class="btn btn-info" onclick="verDetalleCliente(${cliente.id})">Ver Detalle</button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            });
    });

    // Función para ver el detalle del cliente
    function verDetalleCliente(id) {
        // Aquí se cargarían los datos detallados del cliente
        fetch(`/api/cliente/${id}`)
            .then(response => response.json())
            .then(cliente => {
                document.getElementById('detalle_cliente_nombre').innerText = cliente.nombre;
                document.getElementById('detalle_cliente_telefono').innerText = cliente.telefono;
                document.getElementById('detalle_cliente_correo').innerText = cliente.correo;
                document.getElementById('detalle_cliente_estado').innerText = cliente.estado;
                document.getElementById('detalle_cliente_venta_contado').innerText = cliente.ventaContado;
                document.getElementById('detalle_cliente_venta_credito').innerText = cliente.ventaCredito;
                document.getElementById('detalle_cliente_deuda').innerText = cliente.deuda;
                document.getElementById('detalle_cliente_pagos').innerText = cliente.pagosRealizados;

                // Mostrar compras
                let comprasBody = document.getElementById('detalle_cliente_compras');
                comprasBody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos

                cliente.compras.forEach(compra => {
                    let row = `
                        <tr>
                            <td>${compra.fecha}</td>
                            <td>${compra.producto}</td>
                            <td>${compra.monto}</td>
                        </tr>
                    `;
                    comprasBody.innerHTML += row;
                });

                // Mostrar modal
                $('#clienteDetalleModal').modal('show');
            });
    }
</script>
