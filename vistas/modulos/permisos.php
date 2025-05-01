<?php
if (isset($_SESSION["permisos"])) {
    $permisos = $_SESSION["permisos"];
?>

    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Administración de Permisos</h4>
                    <h6>Asignar permisos a roles</h6>
                </div>
                <?php
                if (isset($permisos["permisos"]) && in_array("crear", $permisos["permisos"]["acciones"])) {
                ?>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_permiso">
                            <img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Nuevo Permiso
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_permisos">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Módulos con permisos</th>
                                    <th>Fecha asignación</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL NUEVO PERMISO -->
    <div class="modal fade" id="modal_nuevo_permiso" tabindex="-1" aria-labelledby="modal_nuevo_permiso_Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Nuevos Permisos</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form_nuevo_permiso">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Usuario -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Usuario (<span class="text-danger">*</span>)</label>
                                    <select name="id_usuario" id="id_usuario_permiso" class="js-example-basic-single select2">
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <small id="error_usuario_permiso"></small>
                                </div>
                            </div>
                            <!-- Rol -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Rol (<span class="text-danger">*</span>)</label>
                                    <select name="id_rol" id="id_rol_permiso" class="js-example-basic-single select2">
                                        <!-- Opciones dinámicas -->
                                    </select>
                                    <small id="error_rol_permiso"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Módulos y Acciones -->
                        <div class="form-group mt-4">
                            <label class="form-label">Seleccione módulos y acciones permitidas (<span class="text-danger">*</span>)</label>

                            <!-- Checkbox para seleccionar todos -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="select_all_modulos">
                                <label class="form-check-label fw-bold" for="select_all_modulos">
                                    Seleccionar todos los módulos
                                </label>
                            </div>

                            <div class="row" id="contenedor_modulos_acciones">
                                <!-- Módulos con sus acciones dinámicas -->
                            </div>
                            <small id="error_modulos_acciones"></small>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_guardar_permiso" class="btn btn-primary mx-2">
                            <i class="fa fa-save"></i> Guardar Permisos
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR PERMISO -->
    <div class="modal fade" id="modal_editar_permiso" tabindex="-1" aria-labelledby="modal_editar_permiso_Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Permisos</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <form id="form_editar_permiso">
                    <div class="modal-body">
                        <input type="hidden" name="id_permiso" id="edit_id_permiso">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="form-select"> Seleccione el usuario</label>
                                <select name="id_usuario" id="edit_id_usuario_permiso" class="js-example-basic-single select2">

                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="form-select"> Seleccione el rol</label>
                                <select name="id_rol" id="edit_rol_permiso" class="js-example-basic-single select2">

                                </select>
                            </div>
                        </div>


                        <!-- Módulos y Acciones -->
                        <div class="form-group mt-4">
                            <label class="form-label">Módulos y acciones permitidas (<span class="text-danger">*</span>)</label>

                            <!-- Checkbox para seleccionar todos -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="edit_select_all_modulos">
                                <label class="form-check-label fw-bold" for="edit_select_all_modulos">
                                    Seleccionar todos los módulos
                                </label>
                            </div>

                            <div class="row" id="edit_contenedor_modulos_acciones">
                                <!-- Módulos con sus acciones dinámicas -->
                            </div>
                            <small id="error_edit_modulos_acciones"></small>
                        </div>
                    </div>
                    <div class="text-end mx-4 mb-2">
                        <button type="button" id="btn_actualizar_permiso" class="btn btn-primary mx-2">
                            <i class="fa fa-save"></i> Actualizar Permisos
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL VER PERMISOS -->
    <div class="modal fade" id="modal_ver_permiso" tabindex="-1" aria-labelledby="modal_ver_permiso_Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Visualizar Permisos</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Usuario</label>
                            <input type="text" class="form-control" id="view_usuario" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Rol</label>
                            <input type="text" class="form-control" id="view_rol" readonly>
                        </div>
                    </div>

                    <!-- Módulos y Acciones -->
                    <div class="form-group mt-4">
                        <label class="form-label">Módulos y acciones permitidas</label>
                        <div class="row" id="view_contenedor_modulos_acciones">
                            <!-- Módulos con sus acciones dinámicas -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>