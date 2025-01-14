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
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Fecha</th>
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


<!-- MODAL NUEVO CATEGORIA -->
<div class="modal fade" id="modal_nuevo_permisos_usuario" tabindex="-1" aria-labelledby="modal_nuevo_permisos_usuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear permisos para el usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_categoria">
                <div class="modal-body">
                    <?php
                    $item = null;
                    $valor = null;
                    $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                    $roles = ControladorRol::ctrMostrarRoles($item, $valor);
                    $modulos = ControladorModulos::ctrMostrarModulos($item, $modulos);
                    ?>

                    <!-- SECCION DE USUARIOS Y ROLES -->
                    <div class="row col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_usuario_permiso">Selecione el usuario (<span class="text-danger">*</span>)</label>
                                <select name="id_usuario_permiso" id="id_usuario_permiso" class="select">
                                    <option selected disabled>Selecione</option>
                                    <?php
                                    foreach ($usuarios as $key => $usuario) {
                                    ?>
                                        <option value="<?php echo $usuario["id_usuario"] ?>"><?php echo $usuario["nombre_usuario"] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_rol_permiso">Selecione el rol (<span class="text-danger">*</span>)</label>
                                <select name="id_rol_permiso" id="id_rol_permiso" class="select">
                                    <option selected disabled>Selecione</option>
                                    <?php
                                    foreach ($roles as $key => $rol) {
                                    ?>
                                        <option value="<?php echo $rol["id_rol"] ?>"><?php echo $rol["nombre_rol"] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <?php
                            foreach ($modulos as $key => $modulo) {
                            ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="id_<?php echo $modulo["id_modulo"]; ?>"
                                            value="id_<?php echo $modulo["id_modulo"]; ?>">
                                        <label
                                            class="form-check-label"
                                            for="id_<?php echo $modulo["id_modulo"]; ?>">
                                            <?php echo $modulo["modulo"]; ?>
                                        </label>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>


                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_categoria" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>