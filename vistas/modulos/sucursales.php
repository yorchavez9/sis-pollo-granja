<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de sucursales</h4>
                <h6>Administrar sucursal</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_sucursal"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar sucursal</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_sucursal">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_sucursal">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL NUEVO CATEGORIA -->
<div class="modal fade" id="modal_nuevo_sucursal" tabindex="-1" aria-labelledby="modal_nuevo_sucursal_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear sucursal</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_sucursal">
                <div class="modal-body">
                    <!-- INGRESO TIPO DE PROVEEDOR -->
                    <div class="form-group">
                        <label for="nombre_sucursal" class="form-label">Ingrese el nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre_sucursal" id="nombre_sucursal" placeholder="Ingresa el nombre">
                        <small id="error_nombre_sucursal"></small>
                    </div>
                    <!-- INGRESO LA DIRECCION -->
                    <div class="form-group">
                        <label for="direccion" class="form-label">Ingrese la dirección (<span class="text-danger">*</span>)</label>
                        <input type="text" name="direccion_sucursal" id="direccion_sucursal" placeholder="Ingresa la dirección">
                        <small id="error_direccion_sucursal"></small>
                    </div>
                    <!-- INGRESO DEL  TELEFONO -->
                    <div class="form-group">
                        <label for="telefono" class="form-label">Ingrese el teléfono (<span class="text-danger">*</span>)</label>
                        <input type="text" name="telefono_sucursal" id="telefono_sucursal" placeholder="Ingresa el teléfono">
                        <small id="error_telefono_sucursal"></small>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_sucursal" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL NUEVO CATEGORIA -->
<div class="modal fade" id="modal_editar_sucursal" tabindex="-1" aria-labelledby="modal_editar_sucursal_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar sucursal</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_sucursal">
                <div class="modal-body">
                    <!-- ID DEL SUCURSAL -->
                    <input type="hidden" name="edit_id_sucursal" id="edit_id_sucursal">
                    <!-- INGRESO TIPO DE PROVEEDOR -->
                    <div class="form-group">
                        <label for="nombre_sucursal" class="form-label">Ingrese el nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" name="edit_nombre_sucursal" id="edit_nombre_sucursal" placeholder="Ingresa el nombre">
                        <small id="error_edit_nombre_sucursal"></small>
                    </div>
                    <!-- INGRESO LA DIRECCION -->
                    <div class="form-group">
                        <label for="edit_direccion" class="form-label">Ingrese la dirección (<span class="text-danger">*</span>)</label>
                        <input type="text" name="edit_direccion_sucursal" id="edit_direccion_sucursal" placeholder="Ingresa la dirección">
                        <small id="error_edit_direccion_sucursal"></small>
                    </div>
                    <!-- INGRESO DEL  TELEFONO -->
                    <div class="form-group">
                        <label for="edit_telefono" class="form-label">Ingrese el teléfono (<span class="text-danger">*</span>)</label>
                        <input type="text" name="edit_telefono_sucursal" id="edit_telefono_sucursal" placeholder="Ingresa el teléfono">
                        <small id="error_edit_telefono_sucursal"></small>
                    </div>
                </div>
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_sucursal" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
