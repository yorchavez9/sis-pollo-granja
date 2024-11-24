<div class="page-wrapper" id="seccion_roles_reporte">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Reporte de roles</h4>
                <h6>Genere su reporte de roles</h6>
            </div>
            <div class="page-btn">
                <a href="#" class="btn btn-added reporte_roles_pdf"><i class="fas fa-file-alt me-2"></i>Generar reporte</a>
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
                                <a class="reporte_roles_pdf" data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="vistas/assets/img/icons/pdf.svg" alt="img"></a>
                            </li>
                            <li>
                                <a class="reporte_roles_excel" data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="vistas/assets/img/icons/excel.svg" alt="img"></a>
                            </li>
                            <li>
                                <a class="reporte_roles_printer" data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="vistas/assets/img/icons/printer.svg" alt="img"></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_roles_reporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Rol</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody id="data_roles_reporte">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
