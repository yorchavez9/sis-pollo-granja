<div class="page-wrapper" id="seccion_asistencia_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de pago trabajadores</h4>
                <h6>Genere su reporte de pagos de trabajadores</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_asistencia_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">

                    <div class="col-md-3 mb-2">
                        <label for="filtro_trabajador">Selecione el trabajador</label>
                        <?php
                        $item = null;
                        $valor = null;
                        $trabajadores = ControladorTrabajador::ctrMostrarTrabajadores($item, $valor);
                        ?>
                        <select name="filtro_trabajador_asistencia" id="filtro_trabajador_asistencia" class="js-example-basic-single select2 ">
                            <option value="">Todos</option>
                            <?php
                            foreach ($trabajadores as $key => $value) {
                            ?>
                                <option value="<?php echo $value["id_trabajador"] ?>"><?php echo $value["nombre"] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Estado -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_estado" class="form-label text-center">Estado</label>
                        <select id="filtro_estado_asistencia" class="select">
                            <option value="">Todos</option>
                            <option value="Presente">Presente</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Falta">Falta</option>
                        </select>
                    </div>

                    <!-- Fecha de Vencimiento Desde -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_desde" class="form-label text-center">Fecha desde</label>
                        <input type="date" id="filtro_fecha_desde_asistencia" class="form-control">
                    </div>

                    <!-- Fecha de Vencimiento Hasta -->
                    <div class="col-md-2 mb-2">
                        <label for="filtro_fecha_hasta" class="form-label text-center">Fecha hasta</label>
                        <input type="date" id="filtro_fecha_hasta_asistencia" class="form-control">
                    </div>

                    <!-- Botón Aplicar Filtros -->
                    <div class="col-md-2 d-flex align-items-end mb-2">
                        <button id="btn_aplicar_filtros_asistencia" class="btn btn-primary w-100">
                            Aplicar Filtros
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
<script>
    // Inicializar Select2 en todos los elementos
    $('.js-example-basic-single').select2({
        placeholder: "Select an option",
        allowClear: true,
    });
</script>
