<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de permisos para los usuarios</h4>
                <h6>Administrar permisos</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_permisos_usuario"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar permisos</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_usuario_permisos">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Modulos</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_usuario_permisos">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NUEVO PERMISOS -->
<div class="modal fade" id="modal_nuevo_permisos_usuario" tabindex="-1" aria-labelledby="modal_nuevo_permisos_usuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear permisos para el usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>

            <form enctype="multipart/form-data" id="form_rol_modulo_accion" class="p-4">
                <div class="modal-body">
                    <?php
                    $item = null;
                    $valor = null;
                    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                    $roles = ControladorRol::ctrMostrarRoles($item, $valor);
                    $modulos = ControladorModulos::ctrMostrarModulos($item, $modulos);
                    $acciones = ControladorAccion::ctrMostrarAcciones($item, $valor);
                    ?>

                    <!-- Sección de Usuarios y Roles -->
                    <p class="mb-3 fw-bold">Asignar Usuario y Rol</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_usuario_permiso" class="form-label">Seleccione el usuario (<span class="text-danger">*</span>)</label>
                                <select name="id_usuario_permiso" id="id_usuario_permiso" class="select" required>
                                    <option selected disabled>Seleccione</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario["id_usuario"] ?>"><?php echo $usuario["nombre_usuario"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_rol_permiso" class="form-label">Seleccione el rol (<span class="text-danger">*</span>)</label>
                                <select name="id_rol_permiso" id="id_rol_permiso" class="select">
                                    <option selected disabled>Seleccione</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol["id_rol"] ?>"><?php echo $rol["nombre_rol"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Seleccionar Todos -->
                    <div class="form-group mt-4">
                        <div class="d-flex align-items-center">
                            <label for="select_all" class="form-label h5 me-3">Seleccionar todos</label>
                            <div class="form-check">
                                <input type="checkbox" id="select_all" class="form-check-input">
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Módulos y Acciones -->
                    <p class="mt-4 fw-bold mb-3">Asignar Módulos y Acciones</p>
                    <div class="row">
                        <?php foreach ($modulos as $modulo): ?>
                            <div class="col-md-12">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <!-- Checkbox del Módulo -->
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="id_modulo_<?php echo $modulo["id_modulo"]; ?>" value="<?php echo $modulo["id_modulo"]; ?>">
                                                    <label class="form-check-label fw-bold text-dark" for="id_modulo_<?php echo $modulo["id_modulo"]; ?>">
                                                        <?php echo $modulo["modulo"] == 'gastos_ingresos' ? 'Gastos/Ingresos extras' : $modulo["modulo"]; ?>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Acciones Relacionadas -->
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <?php foreach ($acciones as $accion): ?>
                                                        <div class="col-md-4 mb-1">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="accion_<?php echo $accion["id_accion"]; ?>" value="<?php echo $accion["id_accion"]; ?>">
                                                                <label class="form-check-label" for="accion_<?php echo $accion["id_accion"]; ?>">
                                                                    <?php echo $accion["accion"]; ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="text-end mt-4">
                    <button type="button" id="btn_guardar_rol_modulo_accion" class="btn btn-primary mx-2">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER PERMISOS -->
<div class="modal fade" id="modal_ver_usuario_permisos" tabindex="-1" aria-labelledby="modal_ver_usuario_permisos_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permisos del usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Usuario</label>
                            <input type="text" id="usuario_ver" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Rol</label>
                            <input type="text" id="rol_ver" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold">Módulos y Acciones Permitidas</h6>
                    <div id="modulos_acciones_ver" class="mt-3">
                        <!-- Aquí se cargarán dinámicamente los módulos y acciones -->
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>