<div class="page-wrapper" id="seccion_productos_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de productos</h4>
                <h6>Genere su reporte de los productos</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_productos_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <!-- Categoría -->
                    <div class="col-md-2">
                        <label for="filtro_categoria" class="form-label">Categoría</label>
                        <?php
                        $item = null;
                        $valor = null;
                        $categorias = ControladorCategoria::ctrMostrarCategoria($item, $valor);
                        ?>
                        <select id="filtro_categoria" class="select">
                            <option value="">Todas</option>
                            <?php
                            foreach ($categorias as $key => $value) {
                            ?>
                            <option value="<?php echo $value["id_categoria"]?>"><?php echo $value["nombre_categoria"]?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2">
                        <label for="filtro_estado" class="form-label">Estado</label>
                        <select id="filtro_estado" class="select">
                            <option value="">Todos</option>
                            <option value="1">Disponible</option>
                            <option value="0">Agotado</option>
                        </select>
                    </div>

                    <!-- Precio -->
                    <div class="col-md-2">
                        <label for="filtro_precio_min" class="form-label">Precio Mínimo</label>
                        <input type="number" id="filtro_precio_min" min="0" class="form-control" placeholder="USD">
                    </div>

                    <div class="col-md-2">
                        <label for="filtro_precio_max" class="form-label">Precio Máximo</label>
                        <input type="number" id="filtro_precio_max" min="0" class="form-control" placeholder="USD">
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div class="col-md-2">
                        <label for="filtro_fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" id="filtro_fecha_desde" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label for="filtro_fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" id="filtro_fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="row my-3 justify-content-center">
                    <!-- Botón Aplicar Filtros -->
                    <div class="col-md-3 text-center">
                        <button id="btn_aplicar_filtros" class="btn btn-primary w-100">Aplicar Filtros</button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_productos_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Categoría</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Fecha vencimiento</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="data_productos_reporte">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
