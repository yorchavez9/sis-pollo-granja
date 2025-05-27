<div class="page-wrapper" id="seccion_clientes_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de ventas por cliente</h4>
                <h6>Genere reportes detallados de ventas por cliente</h6>
            </div>
            <?php if (isset($permisos["reporte_clientes"]) && in_array("crear", $permisos["reporte_clientes"]["acciones"])): ?>
            <div class="page-btn">
                <button class="btn btn-primary reporte_clientes_pdf">
                    <i class="fas fa-file-pdf me-2"></i>Generar PDF
                </button>
                <button class="btn btn-success ms-2 reporte_clientes_excel">
                    <i class="fas fa-file-excel me-2"></i>Exportar Excel
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros Avanzados -->
                <div class="row mb-4 g-3">
                    <!-- Cliente -->
                    <div class="col-md-2">
                        <label for="filtro_cliente_venta" class="form-label">Cliente</label>
                        <select id="filtro_cliente_venta" class="form-select select2">
                            <option value="">Seleccione</option>
                            <?php
                            $clientes = ControladorCliente::ctrMostrarCliente(null, null);
                            foreach ($clientes as $cliente):
                            ?>
                            <option value="<?= $cliente['id_persona'] ?>">
                                <?= htmlspecialchars($cliente['razon_social']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Fechas -->
                    <div class="col-md-2">
                        <label for="filtro_fecha_desde_venta_cliente" class="form-label">Fecha Desde</label>
                        <input type="date" id="filtro_fecha_desde_venta_cliente" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label for="filtro_fecha_hasta_venta_cliente" class="form-label">Fecha Hasta</label>
                        <input type="date" id="filtro_fecha_hasta_venta_cliente" class="form-control">
                    </div>

                    <!-- Tipo de Venta -->
                    <div class="col-md-2">
                        <label for="filtro_tipo_venta_cliente" class="form-label">Tipo de Venta</label>
                        <select id="filtro_tipo_venta_cliente" class="form-select">
                            <option value="">Todos</option>
                            <option value="contado">Contado</option>
                            <option value="credito">Crédito</option>
                        </select>
                    </div>

                    <!-- Estado de Pago -->
                    <div class="col-md-2">
                        <label for="filtro_estado_pago" class="form-label">Estado de Pago</label>
                        <select id="filtro_estado_pago" class="form-select">
                            <option value="">Todos</option>
                            <option value="completado">Pagado</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-1 d-flex align-items-end">
                        <button id="btn_filtro_venta_cliente" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button id="btn_refrescar_venta_cliente" class="btn btn-secondary w-100" onclick="location.reload();">
                            <i class="fas fa-sync-alt me-1"></i> Refrescar
                        </button>
                    </div>
                </div>

                <!-- Resumen Estadístico -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light-primary">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Total Ventas</h6>
                                        <h4 class="mb-0" id="total-ventas">S/ 0.00</h4>
                                        <small class="text-muted" id="total-ventas-usd">USD 0.00</small>
                                    </div>
                                    <div class="avatar bg-primary">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-success">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Total Pagado</h6>
                                        <h4 class="mb-0" id="total-pagado">S/ 0.00</h4>
                                        <small class="text-muted" id="total-pagado-usd">USD 0.00</small>
                                    </div>
                                    <div class="avatar bg-success">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-warning">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Saldo Pendiente</h6>
                                        <h4 class="mb-0" id="saldo-pendiente">S/ 0.00</h4>
                                        <small class="text-muted" id="saldo-pendiente-usd">USD 0.00</small>
                                    </div>
                                    <div class="avatar bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-info">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Total Ventas</h6>
                                        <h4 class="mb-0" id="total-registros">0</h4>
                                        <small class="text-muted">Registros encontrados</small>
                                    </div>
                                    <div class="avatar bg-info">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de reportes -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped" style="width:100%" id="tabla_clientes_reporte">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">N°</th>
                                <th width="15%">Cliente</th>
                                <th width="10%">Usuario</th>
                                <th width="10%">Fecha</th>
                                <th width="10%">Comprobante</th>
                                <th width="10%">Serie/Número</th>
                                <th width="10%">Total Venta</th>
                                <th width="10%">Total Pago</th>
                                <th width="10%">Saldo</th>
                                <th width="10%">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="data_clientes_reporte">
                            <!-- Los datos se cargarán via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>