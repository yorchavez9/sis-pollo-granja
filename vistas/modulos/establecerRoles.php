<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de roles establecidos</h4>
                <h6>Administrar roles establecidos</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_usuario_rol"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar roles al usuario</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_usuario_roles">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Usuario</th>
                                <th>Roles</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_usuario_roles">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL ESTABLECER NUEVOS ROLES -->
<div class="modal fade" id="modal_nuevo_usuario_rol" tabindex="-1" aria-labelledby="modal_nuevo_usuario_rolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Roles a Usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_usuario_roles">
                <div class="modal-body">

                    <?php
                    $item = null;
                    $valor = null;
                    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                    ?>
                    <!-- SELECCIONA UN USUARIO -->
                    <div class="form-group mb-3">
                        <label for="id_usuario_roles" class="form-label">Selecciona un Usuario</label>
                        <select id="id_usuario_roles" class="js-example-basic-single select2 select" style="width: 100%;">
                            <option selected disabled>Selecione el usuario</option>
                            <?php
                            foreach ($usuarios as $key => $usuario) {
                                if ($usuario["estado_usuario"] == 1) {
                            ?>
                                    <option value="<?php echo $usuario["id_usuario"] ?>"><?php echo $usuario["nombre_usuario"] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <small id="error_usuario_roles" class="text-danger"></small>
                    </div>

                    <!-- SELECIONAR EL ROL -->
                    <div class="form-group mb-3">
                        <?php
                        $item = null;
                        $valor = null;
                        $roles = ControladorRol::ctrMostrarRoles($item, $valor);
                        ?>
                        <label for="id_rol" class="form-label fw-bold">Selecione el rol</label>
                        <select name="id_rol_select" id="id_rol_select" class="select">
                            <option disabled selected>Selecione</option>
                            <?php
                            foreach ($roles as $key => $rol) {
                            ?>
                                <option value="<?php echo $rol["id_rol"] ?>"><?php echo $rol["nombre_rol"] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <!-- SELECCIONA LOS MODULOS -->
                    <div class="form-group mb-3">
                        <label for="id_roles" class="form-label fw-bold">Selecciona Roles</label>
                        <div class="card p-3 shadow-sm">
                            <?php
                            $item = null;
                            $valor = null;
                            $roles = ControladorRol::ctrMostrarRoles($item, $valor);
                            foreach ($roles as $key => $rol) {
                            ?>
                                <div class="form-check mb-2">
                                    <input type="checkbox" id="role_<?php echo $rol["id_rol"] ?>" class="form-check-input usuario_roles" value="<?php echo $rol["id_rol"] ?>">
                                    <label for="role_<?php echo $rol["id_rol"] ?>" class="form-check-label"><?php echo $rol["nombre_rol"] ?></label>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
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


<!-- MODAL EDITAR NUEVOS ROLES -->
<div class="modal fade" id="modal_editar_usuario_rol" tabindex="-1" aria-labelledby="modal_editar_usuario_rolLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Roles a Usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_update_usuario_roles">
                <div class="modal-body">

                    <?php
                    $item = null;
                    $valor = null;
                    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                    ?>
                    <!-- SELECCIONA UN USUARIO -->
                    <div class="form-group mb-3">
                        <label for="edit_id_usuario_roles" class="form-label">Selecciona un Usuario</label>
                        <select id="edit_id_usuario_roles" class="js-example-basic-single select2 select" style="width: 100%;">
                            <option selected disabled>Selecione el usuario</option>
                            <?php
                            foreach ($usuarios as $key => $usuario) {
                                if ($usuario["estado_usuario"] == 1) {
                            ?>
                                    <option value="<?php echo $usuario["id_usuario"] ?>"><?php echo $usuario["nombre_usuario"] ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <small id="error_usuario_roles" class="text-danger"></small>
                    </div>

                    <!-- SELECCIONA LOS ROLES -->
                    <div class="form-group mb-3">
                        <label for="id_roles" class="form-label fw-bold">Selecciona Roles</label>
                        <div class="card p-3 shadow-sm">
                            <?php
                            $item = null;
                            $valor = null;
                            $roles = ControladorRol::ctrMostrarRoles($item, $valor);
                            foreach ($roles as $key => $rol) {
                            ?>
                                <div class="form-check mb-2">
                                    <input type="checkbox" id="role_<?php echo $rol["id_rol"] ?>" class="form-check-input edit_usuario_roles" value="<?php echo $rol["id_rol"] ?>">
                                    <label for="role_<?php echo $rol["id_rol"] ?>" class="form-check-label"><?php echo $rol["nombre_rol"] ?></label>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>



                </div>

                <!-- BOTONES -->
                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_usuario_roles" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
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
        $('#modal_nuevo_usuario_rol').on('shown.bs.modal', function() {
            $(this).find('.js-example-basic-single').select2({
                placeholder: "Select an option",
                dropdownParent: $('#modal_nuevo_usuario_rol')
            });
        });

        // Inicializar Select2 en todos los elementos
        $('.js-example-basic-single').select2({
            placeholder: "Select an option",
            allowClear: true,
        });

        // Reinicializar al abrir el modal
        $('#modal_editar_usuario_rol').on('shown.bs.modal', function() {
            $(this).find('.js-example-basic-single').select2({
                placeholder: "Select an option",
                dropdownParent: $('#modal_editar_usuario_rol')
            });
        });

    });
</script>