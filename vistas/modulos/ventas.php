<style>
    .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }

    #hora_venta {
        font-weight: bold;
    }

    .flex-container {
        display: flex;
        flex-direction: column;
    }

    .flex-container ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .flex-container li {
        display: flex;
        justify-content: space-between;
    }

    .price {
        font-weight: bold;
    }

    .total-value {
        font-size: 1.5rem;
        color: #7367F0;
    }

    .hover_img {
        width: 100px;
        /* Tamaño original de la imagen */
        height: auto;
        transition: all 0.3s ease;
        /* Transición suave */
    }

    /* Estilo de la imagen cuando se agranda */
    .hover_img:hover {
        transform: scale(1.2);
        /* Aumenta el tamaño en un 20% */
    }
</style>

<?php
$item = null;
$valor = null;
$serieNumeroComprobante = ControladorSerieNumero::ctrMostrarSerieNumero($item, $valor);
?>

<!-- SECCCION DE CREAR VENTA -->
<div class="page-wrapper" id="pos_venta">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="page-header">
                    <div class="page-title d-flex align-items-center">
                        <h3 class="d-flex align-items-center" style="font-size: 1.3rem;">
                            <img src="vistas/assets/img/icons/shopping-cart.svg" width="50" alt="" class="me-2">
                            Crear venta
                        </h3>
                    </div>
                    <?php
                    if (isset($permisos["ventas"]) && in_array("ver", $permisos["ventas"]["acciones"])) {
                    ?>
                        <div class="page-btn">
                            <a href="#" id="ver_ventas" class="btn btn-added"><i class="fas fa-eye me-2"></i>Ver ventas</a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!--======================================
                        FORMULARIO DE COMPRA DE PRODUCTO
                        ======================================-->
                        <form id="form_venta_producto">

                            <!--  ID DE LA APERTURA DE LA CAJA -->
                            <input type="hidden" id="id_movimiento_caja_venta">
                            <!-- INGRESO DE ID DEL USUARIO -->
                            <input type="hidden" id="id_usuario_venta" value="<?php echo $_SESSION["usuario"]["id_usuario"]; ?>">
                            <div class="row">
                                <!-- INGRESO DE CLIENTE -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="id_cliente" class="form-label">Seleccione el cliente(<span class="text-danger">*</span>):</label>
                                        <select name="id_cliente_venta" id="id_cliente_venta" class="js-example-basic-single select2">
                                            <option value="" selected>Seleccione un cliente</option>
                                        </select>
                                        <small id="error_cliente_venta"></small>
                                    </div>
                                </div>
                                <!-- BOTON PARA AGREGAR CLIENTE -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <a href="#" class="btn btn-sm btn-adds mt-4" id="btn_add_cliente" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente"><i class="fa fa-user-plus me-2"></i></a>
                                    </div>

                                </div>
                                <!-- INGRESO DE LA FECHA -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_egre" class="form-label">Selecione la fecha(<span class="text-danger">*</span>):</label>
                                        <input type="date" id="fecha_venta" class="form-control" name="fecha_venta" placeholder="Ingrese la fecha">
                                        <small id="error_fecha_venta"></small>
                                    </div>
                                </div>
                                <!-- INGRESO DE LA HORA -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hora_venta" class="form-label">Hora:</label>
                                        <input type="text" id="hora_venta" class="form-control" name="hora_venta" readonly>
                                        <small id="error_egreso_hora"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- INGRESO DE TIPO DE COMPROBANTE -->
                                <div class="col-md-4">
                                    <label for="comprobante_venta" class="form-label">Tipo de comprobante(<span class="text-danger">*</span>):</label>
                                    <select name="comprobante_venta" id="comprobante_venta" class="form-control">
                                        <option selected disabled>Selecione el comprobante</option>
                                        <?php
                                        foreach ($serieNumeroComprobante as $value) {
                                        ?>
                                            <option value="<?php echo $value["id_serie_num"] ?>" seriePrefijo="<?php echo $value["serie_prefijo"] ?>" folioInicial="<?php echo $value["folio_inicial"] ?>"><?php echo ucwords($value["tipo_comprobante_sn"]); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <small id="error_comprobante_venta"></small>
                                </div>
                                <!-- INGRESO DE LA SERIE -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="serie_venta" class="form-label">Serie:</label>
                                        <input type="text" id="serie_venta" name="serie_venta" placeholder="Serie">
                                        <small id="error_serie_venta" class="text-danger"></small>
                                    </div>
                                </div>
                                <!-- INGRESO DE NÚMERO -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="numero_venta" class="form-label">Número:</label>
                                        <input type="text" id="numero_venta" name="numero_venta" placeholder="Número">
                                        <small id="error_numero_venta" class="text-danger"></small>
                                    </div>
                                </div>
                                <!-- INGRESO EL INPUESTO -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="igv_venta" class="form-label me-2">Impuesto (%):</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" id="igv_venta" name="igv_venta" value="0" min="0" class="form-control me-3" style="width: 90px;">
                                            <input type="checkbox" id="igv_checkbox" name="igv_checkbox">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- BOTON DE MODAL PARA AGREGAR PRODUCTO -->
                            <div class="text-center mb-5">
                                <a href="#" class="btn btn-primary btn-add-category" data-bs-toggle="modal" data-bs-target="#modalAddProductVenta">
                                    <i class="fa fa-plus me-2"></i> Agregar producto
                                </a>
                            </div>
                            <!-- DATOS DEL DETALLE DEL PRODUCTO -->
                            <div class="row">
                                <!-- TABLA DE SELECIÓN DE PRODUCTOS -->
                                <div class="table-responsive">
                                    <table class="table" width="100%">
                                        <thead>
                                            <tr style="background: #28C76F;">
                                                <th scope="col" class="text-white">Opc</th>
                                                <th scope="col" class="text-white">Img</th>
                                                <th scope="col" class="text-white">Prod.</th>
                                                <th scope="col" class="text-white">Nª Javas</th>
                                                <th scope="col" class="text-white">Nª Unidades</th>
                                                <th scope="col" class="text-white">P. Bruto</th>
                                                <th scope="col" class="text-white">P. Tara</th>
                                                <th scope="col" class="text-white">P. Merma</th>
                                                <th scope="col" class="text-white">P. Promedio</th>
                                                <th scope="col" class="text-white">P. Neto</th>
                                                <th scope="col" class="text-white">Precio</th>
                                                <th scope="col" class="text-white">Sub Total</th>
                                            </tr>
                                        </thead>

                                        <tbody id="detalle_venta_producto">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                </div>
                                <!-- DATOS DEL SUB TOTAL Y EL TOTAL -->
                                <div class="col-md-5">
                                    <div class="pt-3 pb-2">
                                        <!-- SECCIÓN DE PRECIO DE VENTA -->
                                        <div class="flex-container">
                                            <ul>
                                                <li>
                                                    <p>Subtotal</p>
                                                    <p class="price">S/ <span id="subtotal_venta">00.00</span></p>
                                                </li>
                                                <li>
                                                    <p>IGV (%)</p>
                                                    <p class="price">S/ <span id="igv_venta_show">00.00</span></p>
                                                </li>
                                                <li class="total-value">
                                                    <p class="fw-bold">Total</p>
                                                    <p class="price">S/ <span id="total_precio_venta">00.00</span></p>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- SECCIÓN DE PRECIO DE VENTA EN VES -->
                                        <div class="flex-container mb-2">
                                            <ul>
                                                <li class="total-value">
                                                    <p class="fw-bold"></p>
                                                    <p class="price">USD <span id="total_precio_venta_ves">00.00</span></p>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- SECTION DE VENTA AL CONTADO O AL CRÉDITO -->
                                        <div class="row mb-3">
                                            <label for="" class="mb-3">Selecion el tipo de pago (<span class="text-danger">*</span>) </label>
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="forma_pago_v" value="contado">
                                                    <label class="form-check-label" for="contado">
                                                        Al contado
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="forma_pago_v" value="credito">
                                                    <label class="form-check-label" for="credito">
                                                        Al crédito
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- SECCION DE LISTA DE METODOS DE PAGO -->
                                        <div id="venta_al_contado mb-3">
                                            <select name="metodos_pago_venta" id="metodos_pago_venta" class="js-example-basic-single select">
                                                <option value="pago_efectivo">Pago Efectivo</option>
                                                <option value="yape">Yape</option>
                                                <option value="plin">Plin</option>
                                                <option value="tunki">Tunki</option>
                                                <option value="agora_pay">Agora PAY</option>
                                                <option value="bim">BIM</option>
                                                <option value="tarjeta_debito">Tarjeta de Débito</option>
                                                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                                <option value="transferencia_bancaria">Transferencia Bancaria</option>
                                            </select>
                                        </div>
                                        <!-- SECCION DE PAGO DE LA CUOTA -->
                                        <div class="mb-3 mt-3">
                                            <input type="number" name="pago_cuota_venta" id="pago_cuota_venta" class="form-control" value="0.00" placeholder="Ingrese el monto a pagar">
                                        </div>
                                        <!-- SECCION DE SUBIR RECIBO DE PAGO Y DE SERIO O NUMERO D PAGO -->
                                        <div class="mt-3">
                                            <input type="file" name="recibo_de_pago_venta" id="recibo_de_pago_venta" class="form-control mb-3">
                                            <input type="text" name="serie_de_pago_venta" id="serie_de_pago_venta" class="form-control" placeholder="Ingrese la serie o numero de pago">
                                        </div>

                                        <!-- SECCION DE CREAR VENTA -->
                                        <?php
                                        if (isset($permisos["ventas"]) && in_array("crear", $permisos["ventas"]["acciones"])) {
                                        ?>
                                            <div class="row mb-3 mt-3">
                                                <button type="button" id="btn_crear_nueva_venta" class="btn btn-block" style="background:#7367F0; color:white">
                                                    <h5><i class="fa fa-plus fa-lg text-white me-2"></i> Crear Venta</h5>
                                                </button>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MOSTRAR PRODUCTOS EN EL MODAL -->
<div class="modal fade" id="modalAddProductVenta" tabindex="-1" aria-labelledby="modalAddProductVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lista de productos</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <!-- TABLA DE LISTA DE PRODUCTOS -->
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_add_producto_venta">
                            <thead>
                                <tr>
                                    <th class="text-center">Imagen</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody id="data_productos_detalle_venta">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-end mx-4 mb-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- SECCION MOSTRAR VENTAS -->
<div class="page-wrapper" id="ventas_lista" style="display: none">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Lista de ventas</h4>
                <h6>Administrar ventas</h6>
            </div>
            <div>
                <p id="section_imprimir_mensaje_ventas"></p>
                <p id="section_imprimir_mensaje_ventas_pagado"></p>
            </div>
            <div class="page-btn">
                <a href="#" id="crear_venta" class="btn btn-added"><img src="vistas/assets/img/icons/plus.svg" alt="img" class="me-2">Crear venta</a>
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
                    <table class="table table-striped table-bordered" style="width:100%" id="tabla_lista_ventas">
                        <thead>
                            <tr>
                                <th class="text-center">N°</th>
                                <th>Cliente</th>
                                <th>Serie número</th>
                                <th>Tipo pago</th>
                                <th>Total compra</th>
                                <th>Total restante</th>
                                <th>Fecha</th>
                                <th class="text-center">Estado pago</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="data_lista_ventas">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL NUEVO CLIENTE -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear nuevo cliente</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="form_nuevo_cliente">

                <div class="modal-body">

                    <!-- INGRESO TIPO DE PERSONA -->
                    <div class="form-group">
                        <input type="hidden" id="tipo_personas_c" value="cliente">
                    </div>

                    <!-- INGRESO NOMBRE -->
                    <div class="form-group">
                        <label class="form-label">Ingrese el nombre (<span class="text-danger">*</span>)</label>
                        <input type="text" id="razon_social_c" placeholder="Ingrese el nombre completo">
                        <small id="error_razon_social_c"></small>
                    </div>


                    <div class="row">

                        <!-- INGRESO DE TIPO DE DOCUMENTOS -->
                        <div class="col-md-6">
                            <label class="form-label">Selecione el tipo de documento (<span class="text-danger">*</span>)</label>
                            <?php
                            $item = null;
                            $valor = null;
                            $tiposDocumentos = ControladorTipoDocumento::ctrMostrarTipoDocumento($item, $valor);
                            ?>
                            <select class="select" id="id_doc_c">
                                <option disabled selected>Seleccione</option>
                                <?php
                                foreach ($tiposDocumentos as $key => $value) {
                                ?>
                                    <option value="<?php echo $value["id_doc"] ?>"><?php echo $value["nombre_doc"] ?></option>
                                <?php
                                }
                                ?>
                            </select>

                            <small id="error_id_doc_c"></small>
                        </div>

                        <!-- INGRESO DE NUMERO DE DOCUMENTO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_documento" class="form-label">Ingrese el número de documento (<span class="text-danger">*</span>)</label>
                                <input type="text" id="numero_documento_c" placeholder="Ingrese el número de documento">
                                <small id="error_numero_documento_c"></small>
                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <!-- INGRESE LA DIRECCIÓN -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion" class="form-label">Ingrese la dirección </label>
                                <input type="text" id="direccion_c" placeholder="Ingrese la dirección">
                            </div>
                        </div>

                        <!-- INGRESE LA CIUDAD -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Ingrese la ciudad</label>
                                <input type="text" id="ciudad_c" placeholder="Ingrese la ciudad">
                            </div>

                        </div>
                    </div>

                    <div class="row">

                        <!-- INGRESO DE CODIGO POSTAL -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_postal" class="form-label">Ingrese el codigo postal</label>
                                <input type="text" id="codigo_postal_c" placeholder="Ingrese el código postal">
                            </div>
                        </div>

                        <!-- INGRESO DE TELEFONO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Ingrese el telefono</label>
                                <input type="text" id="telefono_c" placeholder="Ingrese el teléfono">
                            </div>

                        </div>
                    </div>

                    <!-- INGRESO DEL CORREO ELECTRÓNICO -->
                    <div class="form-group">
                        <label for="correo" class="form-label">Ingrese el correo electrónico</label>
                        <div class="pass-group">
                            <input type="text" id="correo_c" placeholder="Ingrese el correo electrónico">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sitio_web" class="form-label">Ingrese el sitio web</label>
                        <input type="text" id="sitio_web_c" placeholder="Ingrese el link del sitio web">
                    </div>

                    <div class="row">

                        <!-- INGRESO TIPO DE BANCO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_banco" class="form-label">Selecione el tipo de banco</label>
                                <select class="select" id="tipo_banco_c">
                                    <option disabled selected>Selecione</option>
                                    <option value="BCRP">Banco Central de Reserva del Perú</option>
                                    <option value="BCP">Banco de Crédito del Perú (BCP)</option>
                                    <option value="SBP">Scotiabank Perú</option>
                                    <option value="IB">Interbank</option>
                                    <option value="BBVA">BBVA Perú</option>
                                    <option value="BR">Banco Rural</option>
                                    <option value="BN">Banco de la Nación</option>
                                    <option value="BF">Banco Falabella</option>
                                </select>
                                <small id="error_tipo_banco_c"></small>
                            </div>
                        </div>

                        <!-- INGRESO DE TELEFONO -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_cuenta" class="form-label">Ingrese la cuenta bancaria</label>
                                <input type="text" id="numero_cuenta_c" placeholder="Ingrese el numero de cuenta bancaria">
                                <small id="error_numero_cuenta_c"></small>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="text-end mx-4 mb-2">
                    <button type="button" id="btn_guardar_cliente" class="btn btn-primary mx-2"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PAGAR VENTA (COMPRA) -->
<div class="modal fade" id="modalPagarVenta" tabindex="-1" aria-labelledby="modalPagarVentaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pagar deuda</h5>
                <button type="button" class="close btn_modal_ver_close_usuario" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="frm_pagar_deuda_venta">
                <div class="modal-body">
                    <!-- Campo Oculto -->
                    <input type="hidden" id="id_venta_pagar" name="id_venta_pagar">

                    <!-- Resumen de Venta -->
                    <div class="row mb-3">
                        <div class="col-md-6 text-center">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill" style="color: #28C76F"></i> Venta total:</label>
                                <h3 class="fw-bold" id="total_venta_pagar"></h3>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="form-group">
                                <label><i class="fas fa-money-bill" style="color: #FF4D4D"></i> Total restante:</label>
                                <h3 class="fw-bold text-danger" id="pago_restante_pagar"></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Métodos de Pago -->
                    <div class="row mb-3">
                        <div class="form-group">
                            <label for="metodos_pago_venta">Seleccione la forma de pago</label>
                            <select name="metodos_pago_venta" id="metodos_pago_venta_historial" class="form-select js-example-basic-single select2">
                                <option value="pago_efectivo">Pago Efectivo</option>
                                <option value="yape">Yape</option>
                                <option value="plin">Plin</option>
                                <option value="tunki">Tunki</option>
                                <option value="agora_pay">Agora PAY</option>
                                <option value="bim">BIM</option>
                                <option value="tarjeta_debito">Tarjeta de Débito</option>
                                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                <option value="transferencia_bancaria">Transferencia Bancaria</option>
                            </select>
                            <small id="error_metodos_pago_venta" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Comprobante de Pago -->
                    <div class="row mb-3">
                        <div class="form-group">
                            <label for="comprobante_pago_historial">Seleccione el comprobante</label>
                            <input type="file" name="comprobante_pago_historial" id="comprobante_pago_historial" class="form-control">
                        </div>
                        <div class="mb-2 justify-content-center text-center">
                            <img src="" alt="" class="vista_previa_comprobante_pago">
                        </div>
                    </div>

                    <!-- Serie o Número de Pago -->
                    <div class="row mb-3">
                        <div class="form-group">
                            <label for="serie_numero_pago">Ingrese la serie o número de pago</label>
                            <input type="text" name="serie_numero_pago_historial" id="serie_numero_pago_historial" class="form-control" placeholder="Ingrese número o serie de pago">
                        </div>
                    </div>

                    <!-- Monto de Pago -->
                    <div class="row mb-4">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <label for="monto_pagar_venta" class="form-label">
                                <i class="fas fa-barcode text-danger"></i> Monto a pagar:
                            </label>

                            <input type="number" id="monto_pagar_venta" name="monto_pagar_venta" class="form-control" placeholder="Ingrese el monto a pagar" min="0">
                            <small id="error_monto_pagar_venta" class="text-danger"></small>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-end mx-4 mb-2">
                    <button type="button" class="btn btn-primary" id="btn_pagar_deuda_venta">
                        <i class="fa fa-save"></i> Pagar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- MODAL EDITAR PAGO -->
<div class="modal fade" id="modal_editar_historial_pago" tabindex="-1" aria-labelledby="modal_editar_historial_pago_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar historial de pago</h5>
                <button type="button" class="close btn_modal_ver_close_usuario" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <form enctype="multipart/form-data" id="edit_frm_pagar_deuda_venta">
                <div class="modal-body">
                    <!-- Campo Oculto -->
                    <input type="hidden" id="edit_id_venta_pagar" name="edit_id_venta_pagar">
                    <input type="hidden" id="edit_edit_pago_historial" name="edit_edit_pago_historial">
                    <!-- Métodos de Pago -->
                    <div class="row mb-3">
                        <div class="form-group">
                            <label for="metodos_pago_venta">Seleccione la forma de pago</label>
                            <select name="edit_metodos_pago_venta_historial" id="edit_metodos_pago_venta_historial" class="form-select js-example-basic-single select2">
                                <option value="pago_efectivo">Pago Efectivo</option>
                                <option value="yape">Yape</option>
                                <option value="plin">Plin</option>
                                <option value="tunki">Tunki</option>
                                <option value="agora_pay">Agora PAY</option>
                                <option value="bim">BIM</option>
                                <option value="tarjeta_debito">Tarjeta de Débito</option>
                                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                <option value="transferencia_bancaria">Transferencia Bancaria</option>
                            </select>
                            <small id="edit_error_metodos_pago_venta" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Comprobante de Pago -->
                    <div class="row mb-3">
                        <div class="form-group mb-2">
                            <input type="hidden" name="actual_comprobante_pago_historial" id="actual_comprobante_pago_historial">
                            <label for="edit_comprobante_pago_historial">Seleccione el comprobante</label>
                            <input type="file" name="edit_comprobante_pago_historial" id="edit_comprobante_pago_historial" class="form-control">
                        </div>
                        <div class="mb-2 justify-content-center text-center">
                            <img src="" alt="" class="edit_vista_previa_comprobante_pago">
                        </div>
                    </div>

                    <!-- Serie o Número de Pago -->
                    <div class="row mb-3">
                        <div class="form-group">
                            <label for="serie_numero_pago">Ingrese la serie o número de pago</label>
                            <input type="text" name="edit_serie_numero_pago_historial" id="edit_serie_numero_pago_historial" class="form-control" placeholder="Ingrese número o serie de pago">
                        </div>
                    </div>

                    <!-- Monto de Pago -->
                    <div class="row mb-4">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            <input type="hidden" name="edit_monto_actual_pago" id="edit_monto_actual_pago">
                            <label for="monto_pagar_venta" class="form-label"><i class="fas fa-barcode text-danger"></i> Monto a pagar:</label>
                            <input type="number" id="edit_monto_pagar_venta" name="edit_monto_pagar_venta" class="form-control" placeholder="Ingrese el monto a pagar" min="0">
                            <small id="edit_error_monto_pagar_venta" class="text-danger"></small>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-end mx-4 mb-2">
                    <button type="button" class="btn btn-primary" id="btn_update_pagar_deuda_venta">
                        <i class="fa fa-save"></i> Actualizar pago
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- MOSTRAR HISTORIAL DE PAGOS -->
<div class="modal fade" id="modal_mostrar_historial_pago" tabindex="-1" aria-labelledby="modal_historial_pago_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de pago <span id="nombre_historial_persona_modal"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <!-- TABLA DE LISTA DE PRODUCTOS -->
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%" id="tabla_historial_pago">
                            <thead>
                                <tr>
                                    <th class="text-center">N°</th>
                                    <th>Fecha pago</th>
                                    <th>Forma</th>
                                    <th>Monto</th>
                                    <th class="text-center">Comprobante | N° Operación</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="data_historial_pago">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-end mx-4 mb-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const btnCrearVenta = document.getElementById('btn_crear_nueva_venta');

    function manejarClickBotonesVentas() {
        $("#ver_ventas").click(function() {
            $("#pos_venta").hide();
            $("#ventas_lista").show();
        });

        $("#crear_venta").click(function() {
            $("#ventas_lista").hide();
            $("#pos_venta").show();
        });
    }

    $(document).ready(manejarClickBotonesVentas);

    // Inicializar Select2 en todos los elementos
    $('.js-example-basic-single').select2({
        placeholder: "Select an option",
        allowClear: true,
    });
    // Reinicializar al abrir el modal
    $('#modalPagarVenta').on('shown.bs.modal', function() {
        $(this).find('.js-example-basic-single').select2({
            placeholder: "Select an option",
            dropdownParent: $('#modalPagarVenta')
        });
    });
    // Reinicializar al abrir el modal
    $('#modal_editar_historial_pago').on('shown.bs.modal', function() {
        $(this).find('.js-example-basic-single').select2({
            placeholder: "Select an option",
            dropdownParent: $('#modal_editar_historial_pago')
        });
    });
</script>