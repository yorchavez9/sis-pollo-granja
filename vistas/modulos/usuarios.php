<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de usuarios</h4>
                <h6>Administrar usuarios</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario"><img src="vistas/dist/assets/img/icons/plus.svg" alt="img" class="me-2">Agregar usuario</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-path">
                            <a class="btn btn-filter" id="filter_search">
                                <img src="vistas/dist/assets/img/icons/filter.svg" alt="img">
                                <span><img src="vistas/dist/assets/img/icons/closes.svg" alt="img"></span>
                            </a>
                        </div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="vistas/dist/assets/img/icons/search-white.svg" alt="img">
                            </a>
                        </div>
                    </div>
                    <div class="wordset">
                        <ul>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="vistas/dist/assets/img/icons/pdf.svg" alt="img"></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="vistas/dist/assets/img/icons/excel.svg" alt="img"></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="vistas/dist/assets/img/icons/printer.svg" alt="img"></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_usuarios">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Sucursal</th>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Telefono</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="dataUsuarios">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- MODAL NUEVO USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear nuevo usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="nuevoUsuario">
                <div class="modal-body">
                    <!-- INGRESO DEL SUCURSAL -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Seleccione la sucursal (<span class="text-danger">*</span>)</label>
                            <?php
                            $item = null;
                            $valor = null;
                            $sucursales = ControladorSucursal::ctrMostrarSucursales($item, $valor);
                            ?>
                            <select class="form-control select" id="id_sucursal">
                                <option disabled selected>Seleccione</option>
                                <?php foreach ($sucursales as $key => $value) {
                                    if ($value['estado'] == 1) {
                                ?>
                                        <option value="<?php echo $value["id_sucursal"] ?>"><?php echo $value["nombre_sucursal"] ?></option>
                                <?php }
                                } ?>
                            </select>
                            <small id="errorid_sucursal" class="text-danger"></small>
                        </div>
                        <div class="col-md-6">
                            <label>Ingrese el nombre completo (<span class="text-danger">*</span>)</label>
                            <input type="text" id="nombre_usuario" class="form-control" placeholder="Ingrese el nombre completo">
                            <small id="errornombre_usuario" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- INGRESO DE DIRECCION Y TELEFONO -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="telefono">Ingrese teléfono (<span class="text-danger">*</span>)</label>
                            <input type="text" id="telefono" class="form-control" placeholder="Ingrese el teléfono">
                            <small id="errorTelefonoUsuario" class="text-danger"></small>
                        </div>
                        <div class="col-md-6">
                            <label for="correo">Ingrese el correo electrónico (<span class="text-danger">*</span>)</label>
                            <input type="email" id="correo" class="form-control" placeholder="Ingrese el correo electrónico">
                            <small id="errorCorreoUsuario" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- INGRESO DE CORREO ELECTRONICO Y USUARIO -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="usuario">Ingrese el usuario (<span class="text-danger">*</span>)</label>
                            <input type="text" id="usuario" class="form-control" placeholder="Ingrese el usuario">
                            <small id="errorusuario" class="text-danger"></small>
                        </div>
                        <div class="col-md-6">
                            <label for="contrasena">Ingrese la contraseña (<span class="text-danger">*</span>)</label>
                            <div class="pass-group">
                                <input type="password" id="contrasena" class="form-control pass-input" placeholder="Ingrese la contraseña">
                                <span class="fas toggle-password fa-eye-slash"></span>
                                <small id="errorContrasena" class="text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <!-- INGRESO DE IMAGEN DEL USUARIO -->
                    <div class="form-group mb-4">
                        <label for="imagen_usuario">Seleccione una imagen</label>
                        <input type="file" class="form-control" id="imagen_usuario">
                        <div class="text-center mt-3">
                            <img src="" class="vistaPreviaImagenUsuario img-fluid rounded-circle" width="250" alt="">
                            <small id="errorImagenUsuario" class="text-danger"></small>
                        </div>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="guardar_usuario" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar usuario</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_editar_usuario">
                <div class="modal-body">

                    <!-- ID DEL USUARIO -->
                    <input type="hidden" id="edit_id_usuario">

                    <!-- INGRESO DEL SUCURSAL -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Seleccione la sucursal (<span class="text-danger">*</span>)</label>
                            <?php
                            $item = null;
                            $valor = null;
                            $sucursales = ControladorSucursal::ctrMostrarSucursales($item, $valor);
                            ?>
                            <select class="form-control select" id="edit_id_sucursal">
                                <option disabled selected>Seleccione</option>
                                <?php foreach ($sucursales as $key => $value) {
                                    if ($value['estado'] == 1) {
                                ?>
                                        <option value="<?php echo $value["id_sucursal"] ?>"><?php echo $value["nombre_sucursal"] ?></option>
                                <?php }
                                } ?>
                            </select>
                            <small id="error_edit_id_sucursal" class="text-danger"></small>
                        </div>
                        <div class="col-md-6">
                            <label>Ingrese el nombre completo (<span class="text-danger">*</span>)</label>
                            <input type="text" id="edit_nombre_usuario" class="form-control" placeholder="Ingrese el nombre completo">
                            <small id="error_edit_nombre_usuario" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- INGRESO DE DIRECCION Y TELEFONO -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="edit_telefono_usuario">Ingrese teléfono (<span class="text-danger">*</span>)</label>
                            <input type="text" id="edit_telefono_usuario" class="form-control" placeholder="Ingrese el teléfono">
                            <small id="error_edit_telefono_usuario" class="text-danger"></small>
                        </div>
                        <div class="col-md-6">
                            <label for="correo">Ingrese el correo electrónico (<span class="text-danger">*</span>)</label>
                            <input type="email" id="edit_correo_usuario" class="form-control" placeholder="Ingrese el correo electrónico">
                            <small id="error_edit_correo_usuario" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- INGRESO DE CORREO ELECTRONICO Y USUARIO -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="edit_usuario_usuario">Ingrese el usuario (<span class="text-danger">*</span>)</label>
                            <input type="text" id="edit_usuario_usuario" class="form-control" placeholder="Ingrese el usuario">
                            <small id="error_edit_usuario_usuario" class="text-danger"></small>
                        </div>
                        <div class="col-md-6">
                            <!--  mostrando contraseña vieja -->
                            <input type="hidden" id="edit_password_actual">
                            <label for="contrasena">Ingrese la contraseña (<span class="text-danger">*</span>)</label>
                            <div class="pass-group">
                                <input type="password" id="edit_new_password_usuario" class="form-control pass-input" placeholder="Ingrese la contraseña">
                                <span class="fas toggle-password fa-eye-slash"></span>
                                <small id="edit_new_password_usuario" class="text-danger"></small>
                            </div>
                        </div>

                    </div>

                    <!-- INGRESO DE IMAGEN DEL USUARIO -->
                    <div class="form-group mb-4">
                        <input type="hidden" id="edit_imagen_actual_usuario">
                        <label for="imagen_usuario">Seleccione una imagen</label>
                        <input type="file" class="form-control" id="edit_new_imagen_usuario" accept="image/*">
                        <div class="text-center mt-3">
                            <img src="" class="edit_vista_imagen_usuario img-fluid rounded-circle" width="250" alt="">
                            <small id="error_edit_new_imagen_usuario" class="text-danger"></small>
                        </div>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_update_usuario" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- MODAL VER   USUARIO -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-labelledby="modalVerUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del usuario</h5>
                <button type="button" class="close btn_modal_ver_close_usuario" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="formVerUsuario">
                <div class="modal-body">

                    <div class="row">
                        <!-- MOSTRANDO NOMBRE DE SUCURSAL -->
                        <div class="form-group col-md-6">
                            <label>
                                <i class="fas fa-store text-primary"></i> Registrado en el sucursal:
                            </label>
                            <p id="mostrar_nombre_sucursal"></p>
                        </div>

                        <!-- MOSTRANDO NOMBRE DEL USUARIO -->
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-user text-primary"></i> Nombre de usuario:</label>
                            <p id="mostrar_nombre_usuario"></p>
                        </div>
                    </div>

                    <div class="row">

                        <!-- MOSTRAR LA DIRECCION DEL USUARIO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion" class="form-label"><i class="fas fa-map-marker-alt text-warning"></i> Dirección:</label>
                                <p id="mostrar_direccion_usuario"></p>
                            </div>
                        </div>

                        <!-- MOSTRAR TELEFONO DE USUARIO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label"><i class="fas fa-phone text-info"></i> Teléfono:</label>
                                <P id="mostrar_telefono_usuario"></P>
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <!-- MOSTRAR CORREO USUARIO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correo" class="form-label"><i class="fas fa-envelope text-primary"></i> Correo:</label>
                                <p id="mostrar_correo_usuario"></p>
                            </div>
                        </div>

                        <!-- MOSTRAR USUARIO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario" class="form-label"><i class="fas fa-user-circle text-danger"></i> Usuario:</label>
                                <P id="mostrar_usuario"></P>
                            </div>
                        </div>
                    </div>


                    <!-- MOSTRAR IMAGEN DEL USUARIO -->
                    <div class="form-group">
                        <label for="imagen_usuario" class="form-label"><i class="fas fa-image text-success"></i> FOTO:</label>
                        <div class="text-center mt-3">
                            <img src="" class="mostrarFotoUsuario img img-fluid rounded-circle" width="250" alt="">
                        </div>
                    </div>


                    <!-- ROLES -->
                    <div class="form-group">
                        <h5 class="fw-bold mb-2"><i class="fas fa-users text-warning"></i> Roles:</h5>
                        <div id="mostrar_roles">
                            <!-- Aquí puedes mostrar los roles -->
                        </div>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" class="btn btn-secondary btn_modal_ver_close_usuario" data-bs-dismiss="modal"> Cerrar</button>
                </div>
            </form>

        </div>
    </div>
</div>
