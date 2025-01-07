<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Configuración del sistema</h4>
                <h6>Administrar comprobantes</h6>
            </div>
            <div class="page-btn" id="btn_agregar_configuracion_sistema">
                    <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#modal_nuevo_configuracion_sistema"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Crear configuración</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="text-center my-4">
                    <h3>¡Aviso!</h3>
                    <h4>Una vez configurado inicie sesión nuevamente 😊</h4>
                </div>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_configuracion_sistema">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nombre tienda</th>
                                <th>icon pestaña</th>
                                <th>Imagen menu lateral (Escritorio)</th>
                                <th>Imagen menu lateral (Movil)</th>
                                <th>Imagen fondo login</th>
                                <th>Imagen icono de login</th>
                                <th>Fecha</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_configuracion_sistema">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ===========================================
MODAL NUEVA CONFIGURACION
=========================================== -->
<div class="modal fade" id="modal_nuevo_configuracion_sistema" tabindex="-1" aria-labelledby="modal_nuevo_configuracion_sistemaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-cogs text-warning"></i> Configurar Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form enctype="multipart/form-data" id="form_nuevo_configuracion_sistema">
                <div class="modal-body">
                    <!-- Instrucciones de medidas de imágenes -->
                    <p class="text-muted">
                        <small>Las imágenes deben tener las siguientes medidas recomendadas:</small><br>
                        <strong>Icono de pestaña:</strong> 32x32 px<br>
                        <strong>Imagen Sidebar:</strong> 250x250 px<br>
                        <strong>Imagen Sidebar (Mínima):</strong> 100x100 px<br>
                        <strong>Imagen Login:</strong> 400x300 px<br>
                        <strong>Icono Login:</strong> 64x64 px
                    </p>

                    <div class="row">
                        <!-- Primera columna -->
                        <div class="col-md-6">
                            <!-- Campo Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label"><i class="fa fa-tag text-primary"></i> Nombre del negocio o de la tienda</label>
                                <input type="text" class="form-control" id="nombre_sis" name="nombre" placeholder="Nombre del sistema" required>
                            </div>

                            <!-- Campo Icono de pestaña -->
                            <div class="mb-3">
                                <label for="icon_pestana" class="form-label"><i class="fa fa-window-maximize text-success"></i> Icono de pestaña del boscador (Google)</label>
                                <input type="file" class="form-control" id="icon_pestana_sis" name="icon_pestana" accept="image/*" required onchange="previewImage(event, 'preview_icon_pestana')">
                                <img id="preview_icon_pestana" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                            </div>

                            <!-- Campo Imagen Sidebar -->
                            <div class="mb-3">
                                <label for="img_sidebar" class="form-label"><i class="fa fa-bars text-danger"></i> Imagen para el menu lateral (Modo escritorio)</label>
                                <input type="file" class="form-control" id="img_sidebar_sis" name="img_sidebar" accept="image/*" required onchange="previewImage(event, 'preview_img_sidebar')">
                                <img id="preview_img_sidebar" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                            </div>
                        </div>

                        <!-- Segunda columna -->
                        <div class="col-md-6">
                            <!-- Campo Imagen Sidebar Mínima -->
                            <div class="mb-3">
                                <label for="img_sidebar_min" class="form-label"><i class="fa fa-compress text-info"></i> Imagen para el menu lateral movil (para el modo movil)</label>
                                <input type="file" class="form-control" id="img_sidebar_min" name="img_sidebar_min" accept="image/*" required onchange="previewImage(event, 'preview_img_sidebar_min')">
                                <img id="preview_img_sidebar_min" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                            </div>

                            <!-- Campo Imagen Login -->
                            <div class="mb-3">
                                <label for="img_login" class="form-label"><i class="fa fa-user-circle text-secondary"></i> Imagen para el fondo de login</label>
                                <input type="file" class="form-control" id="img_login" name="img_login" accept="image/*" required onchange="previewImage(event, 'preview_img_login')">
                                <img id="preview_img_login" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                            </div>

                            <!-- Campo Icono Login -->
                            <div class="mb-3">
                                <label for="icon_login" class="form-label"><i class="fa fa-key text-success"></i> Icono Login de inicio de sesión</label>
                                <input type="file" class="form-control" id="icon_login" name="icon_login" accept="image/*" required onchange="previewImage(event, 'preview_icon_login')">
                                <img id="preview_icon_login" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="text-end mx-4 mb-2">
                    <button type="submit" id="btn_guardar_configuracion_sistema" class="btn btn-primary mx-2">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- ===========================================
MODAL EDIT CONFIGURACION
=========================================== -->
<div class="modal fade" id="modal_editar_configuracion_sistema" tabindex="-1" aria-labelledby="modal_editar_configuracion_sistema_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-cogs text-warning"></i> Configurar Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <form enctype="multipart/form-data" id="form_edit_configuracion_sistema">
                <div class="modal-body">
                    <!-- Instrucciones de medidas de imágenes -->
                    <p class="text-muted">
                        <small>Las imágenes deben tener las siguientes medidas recomendadas:</small><br>
                        <strong>Icono de pestaña:</strong> 32x32 px<br>
                        <strong>Imagen Sidebar:</strong> 250x250 px<br>
                        <strong>Imagen Sidebar (Mínima):</strong> 100x100 px<br>
                        <strong>Imagen Login:</strong> 400x300 px<br>
                        <strong>Icono Login:</strong> 64x64 px
                    </p>
                    <input type="hidden" id="edit_id_configuracion_sistema">
                    <div class="row">
                        <!-- Primera columna -->
                        <div class="col-md-6">
                            <!-- Campo Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label"><i class="fa fa-tag text-primary"></i> Nombre del negocio</label>
                                <input type="text" class="form-control" id="edit_nombre_sis" name="nombre" placeholder="Nombre del sistema" required>
                            </div>

                            <!-- Campo Icono de pestaña -->
                            <div class="mb-3">
                                <label for="icon_pestana" class="form-label"><i class="fa fa-window-maximize text-success"></i> Icono de pestaña de boscador (Google)</label>
                                <input type="file" class="form-control" id="edit_icon_pestana_sis" name="icon_pestana" accept="image/*" required onchange="previewImage(event, 'edit_preview_icon_pestana')">
                                <img id="edit_preview_icon_pestana" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                                <input type="hidden" id="actual_icon_pestana_sis">
                            </div>

                            <!-- Campo Imagen Sidebar -->
                            <div class="mb-3">
                                <label for="img_sidebar" class="form-label"><i class="fa fa-bars text-danger"></i> Imagen Sidebar (Modo escritorio)</label>
                                <input type="file" class="form-control" id="edit_img_sidebar_sis" name="img_sidebar" accept="image/*" required onchange="previewImage(event, 'edit_preview_img_sidebar')">
                                <img id="edit_preview_img_sidebar" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                                <input type="hidden" id="actual_img_sidebar_sis">
                            </div>
                        </div>

                        <!-- Segunda columna -->
                        <div class="col-md-6">
                            <!-- Campo Imagen Sidebar Mínima -->
                            <div class="mb-3">
                                <label for="img_sidebar_min" class="form-label"><i class="fa fa-compress text-info"></i> Imagen Sidebar (para el modo movil)</label>
                                <input type="file" class="form-control" id="edit_img_sidebar_min" name="img_sidebar_min" accept="image/*" required onchange="previewImage(event, 'edit_preview_img_sidebar_min')">
                                <img id="edit_preview_img_sidebar_min" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                                <input type="hidden" id="actual_img_sidebar_min">
                            </div>

                            <!-- Campo Imagen Login -->
                            <div class="mb-3">
                                <label for="img_login" class="form-label"><i class="fa fa-user-circle text-secondary"></i> Imagen para el fondo de login</label>
                                <input type="file" class="form-control" id="edit_img_login" name="img_login" accept="image/*" required onchange="previewImage(event, 'edit_preview_img_login')">
                                <img id="edit_preview_img_login" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                                <input type="hidden" id="actual_img_login">
                            </div>

                            <!-- Campo Icono Login -->
                            <div class="mb-3">
                                <label for="icon_login" class="form-label"><i class="fa fa-key text-success"></i> Icono Login de inicio de sesión</label>
                                <input type="file" class="form-control" id="edit_icon_login" name="icon_login" accept="image/*" required onchange="previewImage(event, 'edit_preview_icon_login')">
                                <img id="edit_preview_icon_login" src="" alt="Vista previa" class="img-thumbnail mt-2" style="display: none; max-height: 150px;">
                                <input type="hidden" id="actual_icon_login">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="text-end mx-4 mb-2">
                    <button type="submit" id="btn_update_configuracion_sistema" class="btn btn-primary mx-2">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- JavaScript para Vista Previa -->
<script>
    function previewImage(event, previewId) {
        const input = event.target;
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }
</script>
