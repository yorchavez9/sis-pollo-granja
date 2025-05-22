<div class="page-wrapper" id="seccion_asistencia_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de asistencia de trabajadores</h4>
                <h6>Genere su reporte de asistencias</h6>
            </div>
            <?php
            if (isset($permisos["reporte_asistencia"]) && in_array("crear", $permisos["reporte_asistencia"]["acciones"])) {
            ?>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_asistencia_pdf"><i class="fas fa-file-pdf me-2"></i>Generar PDF</a>
            </div>
            <?php
            }
            ?>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <label for="filtro_trabajador_asistencia">Seleccione el trabajador</label>
                        <?php
                        $item = null;
                        $valor = null;
                        $trabajadores = ControladorTrabajador::ctrMostrarTrabajadores($item, $valor);
                        ?>
                        <select name="filtro_trabajador_asistencia" id="filtro_trabajador_asistencia" class="js-example-basic-single select2">
                            <option value="">Todos</option>
                            <?php foreach ($trabajadores as $trabajador): ?>
                                <option value="<?php echo $trabajador["id_trabajador"] ?>"><?php echo $trabajador["nombre"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Estado -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_estado_asistencia" class="form-label">Estado</label>
                        <select id="filtro_estado_asistencia" class="form-select">
                            <option value="">Todos</option>
                            <option value="Presente">Presente</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Falta">Falta</option>
                        </select>
                    </div>

                    <!-- Fecha Desde -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_desde_asistencia" class="form-label">Fecha desde</label>
                        <input type="date" id="filtro_fecha_desde_asistencia" class="form-control">
                    </div>

                    <!-- Fecha Hasta -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_hasta_asistencia" class="form-label">Fecha hasta</label>
                        <input type="date" id="filtro_fecha_hasta_asistencia" class="form-control">
                    </div>

                    <!-- Botón Aplicar Filtros -->
                    <div class="col-md-2 d-flex align-items-end mb-2">
                        <button id="btn_aplicar_filtros_asistencia" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                    
                    <!-- Botón Limpiar Filtros -->
                    <div class="col-md-1 d-flex align-items-end mb-2">
                        <button id="btn_limpiar_filtros_asistencia" class="btn btn-secondary w-100">
                            <i class="fas fa-broom me-2"></i>Limpiar
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_asistencia_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Fecha</th>
                                <th>Hora entrada</th>
                                <th>Hora salida</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="data_asistencia_reporte">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="modalDetalleAsistencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Asistencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleAsistencia">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnImprimirDetalle">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar Select2
    $('.js-example-basic-single').select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: '100%'
    });
</script>