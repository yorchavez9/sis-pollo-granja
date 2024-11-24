<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de series y números de comprobantes</h4>
                <h6>Administrar series y números</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_config_serie_numero"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar serie y número</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_serie_num">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Tipo de comprobante</th>
                                <th>Serie o profijo</th>
                                <th>Folio inicial</th>
                                <th>Folio final</th>
                                <th>Fecha</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_serie_num">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL NUEVO SERIE Y NUMERO -->
<div class="modal fade" id="modal_config_serie_numero" tabindex="-1" aria-labelledby="modal_config_serie_numeroLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear categoría</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_serie_num">
                <div class="modal-body">

                    <!-- INGRESO TIPO DE PROVEEDOR -->
                    <div class="form-group">
                        <label for="nombre_categoria" class="form-label">Ingrese el nombre (<span class="text-danger">*</span>)</label>
                        <select name="tipo_comprobante" id="tipo_comprobante" class="select">
                            <option disabled selected>Selecione un comprobante</option>
                            <option value="boleta">Boleta</option>
                            <option value="factura">Factura</option>
                            <option value="ticket">Ticket</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serie" class="form-label">Serie o prefijo</label>
                                <input type="text" name="serie_prefijo" id="serie_prefijo" placeholder="Serie o prefijo">
                                <small id="error_serie_prefijo"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serie" class="form-label">Folio inicial</label>
                                <input type="text" name="folio_inicial" id="folio_inicial" placeholder="Folio inicial">
                                <small id="error_folio_inicial"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serie" class="form-label">Folio final</label>
                                <input type="text" name="folio_final" id="folio_final" placeholder="Folio final">
                                <small id="error_folio_final"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_serie_num" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
