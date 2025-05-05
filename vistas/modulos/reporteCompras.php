<div class="page-wrapper" id="seccion_compras_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de compras</h4>
                <h6>Genere su reporte de compras</h6>
            </div>
            <?php
            if (isset($permisos["reporte_compras"]) && in_array("crear", $permisos["reporte_compras"]["acciones"])) {
            ?>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_compras_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
            <?php
            }
            ?>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <!-- Usuario -->
                    <div class="col-md-4 mb-2">
                        <label for="filtro_usuario" class="form-label">Usuario</label>
                        <select id="filtro_usuario_compra" class="select">
                           
                        </select>
                    </div>

                    <!-- Fecha de Egreso Desde -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" id="filtro_fecha_desde_compra" class="form-control">
                    </div>

                    <!-- Fecha de Egreso Hasta -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" id="filtro_fecha_hasta_compra" class="form-control">
                    </div>

                    <!-- Tipo de Comprobante -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_tipo_comprobante" class="form-label">Tipo de Comprobante</label>
                        <select id="filtro_tipo_comprobante_compra" class="select">
                            <option value="">Todos</option>
                            <option value="ticket">Ticket</option>
                            <option value="boleta">Boleta</option>
                            <option value="factura">Factura</option>
                            <!-- Puedes agregar más tipos de comprobantes aquí -->
                        </select>
                    </div>

                    <!-- Estado de Pago -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_estado_pago" class="form-label">Estado de Pago</label>
                        <select id="filtro_estado_pago_compra" class="select">
                            <option value="">Todos</option>
                            <option value="completado">Completado</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <!-- Total Compra Mínimo -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_total_compra_min" class="form-label">Total Compra Mínimo</label>
                        <input type="number" id="filtro_total_compra_min" min="0" class="form-control" placeholder="PEN">
                    </div>

                    <!-- Total Compra Máximo -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_total_compra_max" class="form-label">Total Compra Máximo</label>
                        <input type="number" id="filtro_total_compra_max" min="0" class="form-control" placeholder="PEN">
                    </div>
                </div>

                <div class="row my-3 justify-content-center">
                    <!-- Botón Aplicar Filtros -->
                    <div class="col-md-3 text-center">
                        <button id="btn_aplicar_filtros_compras" class="btn btn-primary w-100">Aplicar Filtros</button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_compras_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Proveedor</th>
                                <th>Usuario</th>
                                <th>Fecha </th>
                                <th>Tipo Comprobante</th>
                                <th>Serie y número</th>
                                <th>Total Compra</th>
                                <th>Total Pago</th>
                                <th>Estado Pago</th>
                            </tr>
                        </thead>
                        <tbody id="data_compras_reporte">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
