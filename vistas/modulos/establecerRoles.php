<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de roles establecidos</h4>
                <h6>Administrar roles establecidos</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modalNuevoCategoria"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar roles al usuario</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_categoria">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Usuario</th>
                                <th>Roles</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="dataCategorias">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL ESTABLECER NUEVOS ROLES -->
<div class="modal fade" id="modalNuevoCategoria" tabindex="-1" aria-labelledby="modalNuevoCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Roles a Usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_usuario_roles">
                <div class="modal-body">

                    <!-- SELECCIONA UN USUARIO -->
                    <div class="form-group mb-3">
                        <label for="id_usuario_roles" class="form-label">Selecciona un Usuario</label>
                        <select id="id_usuario_roles" class="js-example-basic-single select2 form-control" style="width: 100%;">
                            <option value="1">Usuario 1</option>
                            <option value="2">Usuario 2</option>
                            <option value="3">Usuario 3</option>
                            <option value="4">Usuario 4</option>
                        </select>
                        <small id="error_usuario_roles" class="text-danger"></small>
                    </div>

                    <!-- SELECCIONA LOS ROLES -->
                    <div class="form-group mb-3">
                        <label for="id_roles" class="form-label">Selecciona Roles</label>
                        <select id="id_roles" class="form-control select2" multiple="multiple" style="width: 100%;">
                            <option value="admin">Administrador</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Visualizador</option>
                            <option value="guest">Invitado</option>
                        </select>
                        <small id="error_roles" class="text-danger"></small>
                    </div>

                </div>

                <!-- BOTONES -->
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_usuario_roles" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
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

<script>
    $(document).ready(function() {
        // Inicializar Select2 en todos los elementos
        $('.js-example-basic-single').select2({
            placeholder: "Select an option",
            allowClear: true,
        });

        // Reinicializar al abrir el modal
        $('#modalNuevoCategoria').on('shown.bs.modal', function() {
            $(this).find('.js-example-basic-single').select2({
                placeholder: "Select an option",
                dropdownParent: $('#modalNuevoCategoria')
            });
        });
    });
</script>
