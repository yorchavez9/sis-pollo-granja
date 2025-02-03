<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Gatos e Ingresos extras</h4>
                <h6>Administrar gastos e ingresos</h6>
            </div>
            <div class="page-btn d-flex justify-content-start">
                <a href="cajaGeneral" id="btn_mostrar_apertura_caja" class="btn btn-added me-2">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Aperturar la caja
                </a>
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_ingreso_gatos">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar Gatos o Ingresos
                </a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_ingresos_egresos">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Detalles</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_ingresos_egresos">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL AGREGAR NUEVO  -->
<div class="modal fade" id="modal_nuevo_ingreso_gatos" tabindex="-1" aria-labelledby="modal_nuevo_ingreso_gatosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Añade nuevos ingresos/egresos</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_ingreso_egreso">
                <div class="modal-body">

                    <!-- ID USUARIO Y MOVIMIENTO CAJA -->
                    <input type="hidden" id="id_usuario_ingreso_egreso" value="<?php echo $_SESSION["id_usuario"] ?>">
                    <input type="hidden" id="id_movimiento_caja_ingreso_egreso" value="">

                    <!-- INGRESO TIPO DE PROVEEDOR -->
                    <div class="form-group">
                        <label for="nombre_categoria" class="form-label">Selecione el tipo movimiento (<span class="text-danger">*</span>)</label>
                        <select name="tipo_ingreso_egreso_caja" id="tipo_ingreso_egreso_caja" class="select">
                            <option selected disabled>Seleccione</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                        <small class="text-danger" id="error_tipo_ingreso_egreso_caja"></small>
                    </div>

                    <!-- INGRESO DE CONCEPTO O NATURALEZA -->
                    <div class="form-group">
                        <label class="form-label">Concepto o naturaleza del pago (<span class="text-danger">*</span>)</label>
                        <input type="text" name="naturaleza_concepto_pago" id="naturaleza_concepto_pago" placeholder="Concepto">
                        <small class="text-danger" id="error_naturaleza_concepto_pago"></small>
                    </div>

                    <!-- INGRESO DEL MONTO -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="monto_ingreso_egreso" class="form-label mr-2">Precio (PEN) (<span class="text-danger">*</span>)</label>
                            <div class="d-flex align-items-center">
                                <input type="number" id="monto_ingreso_egreso" style="max-width: 200px;" value="0.00" min="0" class="form-control mr-3">
                                <div class="text-center mx-3">
                                    <h5 class="m-0" id="value_monto_ingreso_egrso">USD 0.00</h5>
                                </div>
                            </div>
                            <span class="text-danger" id="error_monto_ingreso_egreso"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="detalle_ingreso_egreso" class="form-label">Detalles</label>
                        <textarea id="detalle_ingreso_egreso" name="detalle_ingreso_egreso" class="form-control" placeholder="Detalles"></textarea>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_ingreso_egreso" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL AGREGAR NUEVO  -->
<div class="modal fade" id="modal_editar_gatos_ingresos_caja" tabindex="-1" aria-labelledby="modal_editar_gatos_ingresos_caja_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar ingresos/egresos</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_ingreso_egreso">
                <div class="modal-body">

                    <!-- ID USUARIO Y MOVIMIENTO CAJA -->
                    <input type="hidden" id="edit_id_gatos_caja">
                    <input type="hidden" id="edit_id_movimiento_caja_ingreso_egreso">

                    <!-- INGRESO TIPO DE PROVEEDOR -->
                    <div class="form-group">
                        <label for="nombre_categoria" class="form-label">Selecione el tipo movimiento (<span class="text-danger">*</span>)</label>
                        <select name="edit_tipo_ingreso_egreso_caja" id="edit_tipo_ingreso_egreso_caja" class="select">
                            <option selected disabled>Seleccione</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                        <small class="text-danger" id="error_edit_tipo_ingreso_egreso_caja"></small>
                    </div>

                    <!-- INGRESO DE CONCEPTO O NATURALEZA -->
                    <div class="form-group">
                        <label class="form-label">Concepto o naturaleza del pago (<span class="text-danger">*</span>)</label>
                        <input type="text" name="edit_naturaleza_concepto_pago" id="edit_naturaleza_concepto_pago" placeholder="Concepto">
                        <small class="text-danger" id="error_edit_naturaleza_concepto_pago"></small>
                    </div>

                    <!-- INGRESO DEL MONTO -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="edit_monto_ingreso_egreso" class="form-label mr-2">Precio (PEN) (<span class="text-danger">*</span>)</label>
                            <div class="d-flex align-items-center">
                                <input type="number" id="edit_monto_ingreso_egreso" style="max-width: 200px;" value="0.00" min="0" class="form-control mr-3">
                                <div class="text-center mx-3">
                                    <h5 class="m-0" id="value_monto_ingreso_egreso_edit">USD 0.00</h5>
                                </div>
                            </div>
                            <span class="text-danger" id="error_edit_monto_ingreso_egreso"></span>
                        </div>
                    </div>
                    <input type="hidden" id="edit_monto_ingreso_egreso_actual" >

                    <div class="form-group">
                        <label for="edit_detalle_ingreso_egreso" class="form-label">Detalles</label>
                        <textarea id="edit_detalle_ingreso_egreso" name="edit_detalle_ingreso_egreso" class="form-control" placeholder="Detalles"></textarea>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_ingreso_egreso" class="btn btn-primary mx-2 align-items-center">
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