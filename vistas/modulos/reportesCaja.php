<div class="page-wrapper" id="seccion_ingreso_egreso_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title d-flex align-items-center">
                <h3 class="d-flex align-items-center" style="font-size: 1.2rem;">
                    <img src="vistas/assets/img/icons/reporting-svgrepo-com.svg" width="40" alt="" class="me-2">
                    Reporte de ingreso y egreso extras
                </h3>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_ingreso_egreso_extra_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">

                    <!-- ID DEL USUARIO -->
                    <div class="col-md-3">
                        <label for="filtro_categoria" class="form-label">Usuarios</label>
                        <?php
                        $item = null;
                        $valor = null;
                        $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                        ?>
                        <select id="id_usuario_ingreso_egreso_reporte" class="select">
                            <option value="">Todas</option>
                            <?php
                            foreach ($usuarios as $key => $value) {
                            ?>
                                <option value="<?php echo $value["id_usuario"] ?>"><?php echo $value["nombre_usuario"] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <!-- TIPO DE MOVIMIENTO INGRESO O EGRESO EXTRA -->
                    <div class="col-md-3">
                        <label for="estado_ingreso_egreso_reporte" class="form-label">Tipo</label>
                        <select id="estado_ingreso_egreso_reporte" class="select">
                            <option value="">Todos</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div class="col-md-3">
                        <label for="fecha_desde_ingreso_egreso_reporte" class="form-label">Fecha Desde</label>
                        <input type="date" id="fecha_desde_ingreso_egreso_reporte" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="fecha_hasta_ingreso_egreso_reporte" class="form-label">Fecha Hasta</label>
                        <input type="date" id="fecha_hasta_ingreso_egreso_reporte" class="form-control">
                    </div>
                </div>
                <div class="row my-3 justify-content-center">
                    <!-- Botón Aplicar Filtros -->
                    <div class="col-md-3 text-center">
                        <button id="btn_mostrar_reporte_IE" class="btn btn-primary w-100">Aplicar Filtros</button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_ingreso_egreso_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody id="data_ingreso_egreso_reporte">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>