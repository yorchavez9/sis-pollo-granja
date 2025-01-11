<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title d-flex align-items-center">
                <h3 class="d-flex align-items-center" style="font-size: 1.5rem;">
                    <img src="vistas/assets/img/icons/dollars.svg" width="40" alt="" class="me-2">
                    Arqueo caja
                </h3>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_arqueo"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Nuevo arqueo</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-path">
                            <a class="btn btn-filter" id="filter_search">
                                <img src="vistas/assets/img/icons/filter.svg" alt="img">
                                <span><img src="vistas/assets/img/icons/closes.svg" alt="img"></span>
                            </a>
                        </div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="vistas/assets/img/icons/search-white.svg" alt="img">
                            </a>
                        </div>
                    </div>
                    <div class="wordset">
                        <ul>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="vistas/assets/img/icons/pdf.svg" alt="img"></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="vistas/assets/img/icons/excel.svg" alt="img"></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="vistas/assets/img/icons/printer.svg" alt="img"></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_arqueo_caja">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Monto sistema</th>
                                <th>Monto físico</th>
                                <th>diferencia</th>
                                <th>Observaciones</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_arqueo_caja">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL NUEVO ARQUEO CAJA -->
<div class="modal fade" id="modal_nuevo_arqueo" tabindex="-1" aria-labelledby="modal_nuevo_arqueoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear nuevo arqueo (Cuadrar el dinero)</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_arqueo_caja">
                <div class="modal-body">

                    <!-- Campos ocultos -->
                    <input type="hidden" id="id_movimiento_arqueo_caja">
                    <input type="hidden" id="id_usuario_arqueo_caja" value="<?php echo $_SESSION["id_usuario"] ?>">

                    <!-- Selección de la fecha -->
                    <div class="form-group mb-4">
                        <label for="fecha_arqueo_caja" class="form-label">
                            <strong>Seleccione la fecha</strong> (<span class="text-danger">*</span>)
                        </label>
                        <input type="date" name="fecha_arqueo_caja" id="fecha_arqueo_caja" class="form-control" placeholder="Fecha">
                        <small id="error_fecha_arqueo_caja" class="text-danger"></small>
                    </div>

                    <!-- Monto del sistema -->
                    <div class="form-group mb-4">
                        <label for="monto_sistema_arqueo_caja" class="form-label"><strong>Monto del sistema (USD):</strong></label>
                        <div class="d-flex align-items-center">
                            <input type="number" id="monto_sistema_arqueo_caja" class="form-control" style="max-width: 200px;" value="0.00" min="0" placeholder="Monto sistema">
                            <h5 class="m-0 text-primary mx-3" id="value_monto_sistema_arqueo_caja">0.00 VES</h5>
                        </div>
                        <span class="text-danger" id="error_value_monto_sistema_arqueo_caja"></span>
                    </div>

                    <!-- Monto físico -->
                    <div class="form-group mb-4">
                        <label for="monto_fisico_arqueo_caja" class="form-label"><strong>Monto físico y otros (USD):</strong></label>
                        <div class="d-flex align-items-center">
                            <input type="number" id="monto_fisico_arqueo_caja" class="form-control" style="max-width: 200px;" value="0.00" min="0" placeholder="Monto físico">
                            <h5 class="m-0 text-success mx-3" id="value_monto_fisico_arqueo_caja">0.00 VES</h5>
                        </div>
                        <span class="text-danger" id="error_value_monto_fisico_arqueo_caja"></span>
                    </div>

                    <!-- Monto diferencia -->
                    <div class="form-group mb-4">
                        <label for="monto_diferencia_arqueo_caja" class="form-label"><strong>Monto diferencia (USD):</strong></label>
                        <div class="d-flex align-items-center">
                            <input type="number" id="monto_diferencia_arqueo_caja" class="form-control" style="max-width: 200px;" value="0.00" min="0" readonly disabled placeholder="Monto diferencia">
                            <h5 class="m-0 text-success mx-3" id="value_monto_diferencia_arqueo_caja">0.00 VES</h5>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group mb-4">
                        <label for="observaciones_arqueo_caja" class="form-label"><strong>Observaciones:</strong></label>
                        <textarea id="observaciones_arqueo_caja" class="form-control" rows="3" placeholder="Ingrese observaciones aquí..."></textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_arqueo_caja" class="btn btn-primary mx-2">
                        <i class="fa fa-save me-1"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- MODAL NUEVO ARQUEO CAJA -->
<div class="modal fade" id="modal_editar_arqueo_caja" tabindex="-1" aria-labelledby="modal_editar_arqueo_caja" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar arqueo (Cuadrar el dinero)</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_editar_arqueo_caja">
                <div class="modal-body">

                    <!-- Campos ocultos -->
                    <input type="hidden" id="edit_id_arqueo_caja">
                    <input type="hidden" id="edit_id_movimiento_arqueo_caja">
                    <input type="hidden" id="edit_id_usuario_arqueo_caja" value="<?php echo $_SESSION["id_usuario"] ?>">

                    <!-- Selección de la fecha -->
                    <div class="form-group mb-4">
                        <label for="edit_fecha_arqueo_caja" class="form-label">
                            <strong>Seleccione la fecha</strong> (<span class="text-danger">*</span>)
                        </label>
                        <input type="date" name="edit_fecha_arqueo_caja" id="edit_fecha_arqueo_caja" class="form-control" placeholder="Fecha">
                        <small id="error_edit_fecha_arqueo_caja" class="text-danger"></small>
                    </div>

                    <!-- Monto del sistema -->
                    <div class="form-group mb-4">
                        <label for="edit_monto_sistema_arqueo_caja" class="form-label"><strong>Monto del sistema (USD):</strong></label>
                        <div class="d-flex align-items-center">
                            <input type="number" id="edit_monto_sistema_arqueo_caja" class="form-control" style="max-width: 200px;" value="0.00" min="0" placeholder="Monto sistema">
                            <h5 class="m-0 text-primary mx-3" id="value_monto_sistema_arqueo_caja_edit">0.00 VES</h5>
                        </div>
                        <span class="text-danger" id="error_edit_monto_sistema_arqueo_caja"></span>
                    </div>

                    <!-- Monto físico -->
                    <div class="form-group mb-4">
                        <label for="edit_monto_fisico_arqueo_caja" class="form-label"><strong>Monto físico y otros (USD):</strong></label>
                        <div class="d-flex align-items-center">
                            <input type="number" id="edit_monto_fisico_arqueo_caja" class="form-control" style="max-width: 200px;" value="0.00" min="0" placeholder="Monto físico">
                            <h5 class="m-0 text-success mx-3" id="value_edit_monto_fisico_arqueo_caja">0.00 VES</h5>
                        </div>
                        <span class="text-danger" id="error_edit_monto_fisico_arqueo_caja"></span>
                    </div>

                    <!-- Monto diferencia -->
                    <div class="form-group mb-4">
                        <label for="edit_monto_diferencia_arqueo_caja" class="form-label"><strong>Monto diferencia (USD):</strong></label>
                        <div class="d-flex align-items-center">
                            <input type="number" id="edit_monto_diferencia_arqueo_caja" class="form-control" style="max-width: 200px;" value="0.00" min="0" readonly disabled placeholder="Monto diferencia">
                            <h5 class="m-0 text-success mx-3" id="value_edit_monto_diferencia_arqueo_caja">0.00 VES</h5>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group mb-4">
                        <label for="edit_observaciones_arqueo_caja" class="form-label"><strong>Observaciones:</strong></label>
                        <textarea id="edit_observaciones_arqueo_caja" class="form-control" rows="3" placeholder="Ingrese observaciones aquí..."></textarea>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_arqueo_caja" class="btn btn-primary mx-2">
                        <div class="d-flex align-items-center">
                            <img src="vistas/assets/img/icons/update.svg" alt="img" width="20" class="me-2"> Actualizar
                        </div>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    /* Swal.fire({
        title: "¡Aviso Importante!",
        text: "Recuerda realizar el arqueo de caja al finalizar el día o una vez completadas todas las ventas del día. Este proceso asegura que los registros financieros coincidan correctamente.",
        icon: "warning",
    }); */
</script>