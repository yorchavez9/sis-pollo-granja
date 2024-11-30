<div class="page-wrapper" id="seccion_pago_trabajadores_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de pago trabajadores</h4>
                <h6>Genere su reporte de pagos de trabajadores</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_pago_trabajadores_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <!-- Estado -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_estado" class="form-label text-center">Estado</label>
                        <select id="filtro_estado_pago_t" class="select">
                            <option value="">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Desactivado</option>
                        </select>
                    </div>

                    <!-- Fecha de Vencimiento Desde -->
                    <div class="col-md-3 mb-2">
                        <label for="filtro_fecha_desde" class="form-label text-center">Fecha próximo pago (Desde)</label>
                        <input type="date" id="filtro_fecha_desde_pago_t" class="form-control">
                    </div>

                    <!-- Fecha de Vencimiento Hasta -->
                    <div class="col-md-3 mb-2">
                        <label for="filtro_fecha_hasta" class="form-label text-center">Fecha próximo pago (Hasta)</label>
                        <input type="date" id="filtro_fecha_hasta_pago_t" class="form-control">
                    </div>

                    <!-- Botón Aplicar Filtros -->
                    <div class="col-md-3 d-flex align-items-end mb-2">
                        <button id="btn_aplicar_filtros_pago_trabajadores" class="btn btn-primary w-100">
                            Aplicar Filtros
                        </button>
                    </div>
                </div>


                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_pago_trabajadores_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="data_pago_trabajadores_reporte">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
