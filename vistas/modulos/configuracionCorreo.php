<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Configuración del correo</h4>
                <h6>Administrar correo para en envio desde el sistema</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_configuracion_correo"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Ingresar datos</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_correo_config">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>SMTP</th>
                                <th>usuario del correo</th>
                                <th>Contraseña del SMTP (correo)</th>
                                <th>Puerto TCP</th>
                                <th>Correo del remitente</th>
                                <th>Nombre del remitente</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_correo_config">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- ===================================================
MODAL NUEVA CONFIGURACION DEL CORREO
=================================================== -->
<div class="modal fade" id="modal_nuevo_configuracion_correo" tabindex="-1" aria-labelledby="modal_nuevo_configuracion_correoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_correo_config">

                <div class="modal-body">

                    <div class="alert alert-warning alert-dismissible fade show position-relative" role="alert">
                        <!-- Botón de cierre con icono -->
                        <a href="#" data-bs-dismiss="alert" aria-label="Close" class="position-absolute top-0 end-0 me-2 mt-2 text-decoration-none">
                            <i class="fas fa-times"></i>
                        </a>
                        <!-- Título de la alerta -->
                        <h6 class="fw-bold text-center"><i class="fas fa-exclamation-triangle me-2"></i>¡Aviso!</h6>
                        <!-- Mensaje -->
                        <p>Los datos tienen que ser reales, caso contrario tendrá errores.</p>
                        <!-- Soporte con icono -->
                        <a href="tel:+51925602416" class="d-block">
                            <i class="fas fa-phone-alt me-2 text-primary"></i>Soporte: +51 925 602 416
                        </a>
                        <!-- Instrucciones con ícono de YouTube -->
                        <a href="https://www.youtube.com" class="d-block" target="_blank">
                            <i class="fab fa-youtube me-2 text-danger"></i>Ver instrucciones
                        </a>
                    </div>


                    <!-- INGRESO ID DEL USUARIO -->
                    <div class="form-group">
                        <input type="text" id="id_usuario_config_correo" value="<?php echo $_SESSION["id_usuario"] ?>">
                    </div>

                    <!-- INGRESO EL SMTP -->
                    <div class="form-group">
                        <label for="smtp_correo" class="form-label"> Servidor SMTP para enviar el correo (<span class="text-danger">*</span>)</label>
                        <input type="text" name="smtp_correo" id="smtp_correo" placeholder="smtp.proveedor.com">
                        <small id="error_smtp_correo"></small>
                    </div>
                    <!-- INGRESO  Nombre de usuario SMTP-->
                    <div class="form-group">
                        <label for="usuario_correo_config" class="form-label">Ingrese el usuario SMTP (<span class="text-danger">*</span>)</label>
                        <input type="text" name="usuario_correo_config" id="usuario_correo_config" placeholder="proveedor@proveedor.com">
                        <small id="error_usuario_correo_config"></small>
                    </div>
                    <!-- Contraseña SMTP -->
                    <div class="form-group">
                        <label for="password_correo_config" class="form-label">Contraseña SMTP (<span class="text-danger">*</span>)</label>
                        <input type="text" name="password_correo_config" id="password_correo_config" placeholder="Proveedor123">
                        <small id="error_password_correo_config"></small>
                    </div>
                    <!-- Puerto TCP para la conexión -->
                    <div class="form-group">
                        <label for="puerto_correo" class="form-label">Ingrese el puerto TCP (<span class="text-danger">*</span>)</label>
                        <input type="number" name="puerto_correo" id="puerto_correo" placeholder="465" class="form-control">
                        <small id="error_puerto_correo"></small>
                    </div>
                    <!-- Remitente del correo -->
                    <div class="form-group">
                        <label for="correo_remitente" class="form-label">Ingrese el correo del remitente o empresa (<span class="text-danger">*</span>)</label>
                        <input type="text" name="correo_remitente" id="correo_remitente" placeholder="proveedor@proveedor.com">
                        <small id="error_correo_remitente"></small>
                    </div>
                    <!-- Nombre del correo -->
                    <div class="form-group">
                        <label for="nombre_remitente" class="form-label">Nombre del remitente o empresa (<span class="text-danger">*</span>)</label>
                        <input type="text" name="nombre_remitente" id="nombre_remitente" placeholder="Proveedor o nombre del negocio">
                        <small id="error_nombre_remitente"></small>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_correo_config" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================================
MODAL EDITAR CONFIGURACION DEL CORREO
=================================================== -->
