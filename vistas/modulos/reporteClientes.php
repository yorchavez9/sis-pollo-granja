<div class="page-wrapper" id="seccion_clientes_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de ventas por cliente</h4>
                <h6>Genere su reporte de clientes</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_clientes_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <!-- Filtros -->
                <div class="row mb-4">
                    <!-- Usuario -->
                    <div class="col-md-3 mb-2">
                        <label for="filtro_cliente" class="form-label">Selecione el cliente</label>
                        <select id="filtro_cliente_venta" class="select">
                            <option value="">Todos</option>
                            <!-- Aquí puedes cargar los usuarios dinámicamente desde la base de datos -->
                            <?php
                            $item = null;
                            $valor = null;
                            // Supongamos que tienes una función que obtiene los usuarios
                            $clientes = ControladorCliente::ctrMostrarCliente($item, $valor);
                            foreach ($clientes as $cliente) {
                            ?>
                                <option value="<?php echo $cliente['id_persona']; ?>"><?php echo $cliente['razon_social']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Fecha de Egreso Desde -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" id="filtro_fecha_desde_venta_cliente" class="form-control">
                    </div>

                    <!-- Fecha de Egreso Hasta -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" id="filtro_fecha_hasta_venta_cliente" class="form-control">
                    </div>

                    <!-- Tipo de Comprobante -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_tipo_comprobante" class="form-label">Tipo de venta</label>
                        <select id="filtro_tipo_venta_cliente" class="select">
                            <option value="contado">Contado</option>
                            <option value="credito">Crèdito</option>
                        </select>
                    </div>

                    <!-- Botón aplicar filtros -->
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <div class="w-100">
                            <button id="btn_filtro_venta_cliente" class="btn btn-primary w-100">Aplicar Filtros</button>
                        </div>
                    </div>
                </div>
                <!-- Tabla de reportes -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_clientes_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Proveedor</th>
                                <th>Usuario</th>
                                <th>Fecha </th>
                                <th>Tipo Comprobante</th>
                                <th>Serie y número</th>
                                <th>Total venta</th>
                                <th>Total Pago</th>
                                <th>Estado Pago</th>
                            </tr>
                        </thead>
                        <tbody id="data_clientes_reporte">
                            <!-- Los datos del cliente se generarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>