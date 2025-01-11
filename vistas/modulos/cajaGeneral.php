<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title d-flex align-items-center">
                <h3 class="d-flex align-items-center" style="font-size: 1.5rem;">
                    <img src="vistas/assets/img/icons/caja-icon.svg" width="40" alt="" class="me-2">
                    Caja 
                </h3>
            </div>

            <div class="page-btn d-flex">
                <a href="#" style="border-radius: 10px; background: #1B2850; color: white" class="btn me-2">
                    <i class="fa fa-clock me-2"></i> Cerrar caja automáticamente a las 12pm
                </a>
                <a href="#" style="border-radius: 10px;" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_apertura_caja">
                    <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Aperturar caja
                </a>
            </div>
        </div>
        <div class="row col-md-12 g-4">
            <!-- Ingresos -->
            <div class="col">
                <div class="dash-widget dash1 p-4 text-center rounded">
                    <div class="dash-widgetimg mb-3">
                        <span><img src="vistas/assets/img/icons/dash2.svg" alt="img" class="img-fluid"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>USD <span class="counters" id="total_ingresos_caja">0.00</span></h5>
                        <h6>Total ingresos</h6>
                    </div>
                </div>
            </div>
            <!-- Egresos -->
            <div class="col">
                <div class="dash-widget p-4 text-center rounded">
                    <div class="dash-widgetimg mb-3">
                        <span><img src="vistas/assets/img/icons/dash1.svg" alt="img" class="img-fluid"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>USD <span class="counters" id="total_egresos_caja">0.00</span></h5>
                        <h6>Total egresos</h6>
                    </div>
                </div>
            </div>
            <!-- Saldo Inicial -->
            <div class="col">
                <div class="dash-widget dash3 p-4 text-center rounded">
                    <div class="dash-widgetimg mb-3">
                        <span><img src="vistas/assets/img/icons/cash-money-svgrepo-com.svg" width="30" alt="img"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>USD <span class="counters" id="total_saldo_inicial_caja">0.00</span></h5>
                        <h6>Saldo Inicial</h6>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="dash-widget dash3 p-4 text-center rounded">
                    <div class="dash-widgetimg mb-3">
                        <span><img src="vistas/assets/img/icons/box-caja.svg" alt="img" width="30" class="img-fluid"></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>USD <span class="counters" id="monto_totol_caja">0.00</span></h5>
                        <h6>Total en caja</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-top"></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_apertura_cierre_caja">
                        <thead>
                            <tr>
                                <th class="text-center">N°</th>
                                <th class="text-center">Fecha Cierre</th>
                                <th class="text-center">Ingreso</th>
                                <th class="text-center">Egresos</th>
                                <th class="text-center">Saldo Inicial</th>
                                <th class="text-center">total en caja Cerrada</th>
                            </tr>
                        </thead>
                        <tbody id="data_list_caja">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL NUEVO APERTURA CAJA -->
<div class="modal fade" id="modal_nuevo_apertura_caja" tabindex="-1" aria-labelledby="modal_nuevo_apertura_caja_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apertura tu caja</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_apertura_caja">
                <div class="modal-body">

                    <!-- ID DEL USUARIO -->
                    <div class="form-group">
                        <input type="hidden" name="id_usuario_caja" value="<?php echo $_SESSION["id_usuario"] ?>" id="id_usuario_caja">
                    </div>

                    <!-- MONTO INICIAL -->
                    <div class="form-group">
                        <label for="nombre_categoria" class="form-label">Monto Inicial USD (<span class="text-danger">*</span>)</label>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <input type="number" name="monto_inicial_caja" value="0.00" class="form-control" id="monto_inicial_caja" placeholder="Monto">
                            </div>
                            <div class="col-auto">
                                <h5>VES 329,234.00</h5>
                            </div>
                        </div>
                        <small class="text-danger" id="error_monto_inicial_caja"></small>
                    </div>

                    <!-- FECHAS DE APERTURA DE LA CAJA -->
                    <div class="row col-md-12">
                        <!-- FECHA DE APERTURA -->
                        <div class="form-group col-md-6">
                            <label for="descripcion_categoria" class="form-label">Fecha de apertura (<span class="text-danger">*</span>)</label>
                            <input type="date" name="fecha_apertura_caja" class="form-control" id="fecha_apertura_caja">
                        </div>

                        <!-- FECHA CIERRE -->
                        <div class="form-group col-md-6">
                            <label for="descripcion_categoria" class="form-label">Fecha de cierre (<span class="text-danger">*</span>)</label>
                            <input type="date" name="fecha_cierre_caja" class="form-control" id="fecha_cierre_caja">
                        </div>
                    </div>

                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_apertura_caja" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR CATEGORIA -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar categoría</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_actualizar_categoria">
                <div class="modal-body">

                    <!-- ID CATEGORIA -->
                    <input type="hidden" name="edit_id_categoria" id="edit_id_categoria">
                    <!-- INGRESO TIPO DE PROVEEDOR -->
                    <div class="form-group">
                        <label for="nombre_categoria" class="form-label">Ingrese el nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="edit_nombre_categoria" id="edit_nombre_categoria" placeholder="Ingresa el nombre">
                        <small id="edit_error_nombre_categoria"></small>
                    </div>

                    <!-- INGRESO DE RAZÓN SOCIAL -->
                    <div class="form-group">
                        <label class="form-label">Ingrese la descripción (<span class="text-danger">*</span>)</label>
                        <textarea name="edit_descripcion_categoria" id="edit_descripcion_categoria" cols="30" rows="10" placeholder="Ingrese la descripción"></textarea>
                        <small id="edit_error_descripcion_categoria"></small>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_actualizar_categoria" class="btn btn-primary mx-2"><i class="fas fa-sync"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>