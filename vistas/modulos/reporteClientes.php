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


                <!-- Tabla de reportes -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_clientes_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre/Razón Social</th>
                                <th>Documento</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Ciudad</th>
                                <th>Código Postal</th>
                                <th>Estado</th>
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
