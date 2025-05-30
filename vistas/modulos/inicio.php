<?php

function formatearPrecio($precio)
{
    return number_format($precio, 2, '.', ',');
}

$item = null;
$valor = null;

/* TOTAL DE VENTAS */
$sumaTotalVenta = ControladorVenta::ctrMostrarSumaTotalVenta($item, $valor);
$sumaTotalVenta = (float) $sumaTotalVenta;
$precioFormateado = formatearPrecio($sumaTotalVenta);

/* TOTAL DE COMPRAS */
$sumaTotalCompra = ControladorCompra::ctrMostrarTotalCompra($item, $valor);
$sumaTotalCompra = (float) $sumaTotalCompra;
$precioFormateadoCompra = formatearPrecio($sumaTotalCompra);

/* TOTAL DE VENTA AL CONTADO */
$sumaTotalVentaContado = ControladorVenta::ctrMostrarSumaTotalVentaContado($item, $valor);
$sumaTotalVentaContado = (float) $sumaTotalVentaContado;
$precioFormateadoContado = formatearPrecio($sumaTotalVentaContado);

/* TOTAL DE VENTA AL CREDITO */
$sumaTotalVentaCredito = ControladorVenta::ctrMostrarSumaTotalVentaCredito($item, $valor);
$sumaTotalVentaCredito = (float) $sumaTotalVentaCredito;
$precioFormateadoCredito = formatearPrecio($sumaTotalVentaCredito);

/* TOTAL DE VENTAS */
$totalVentas = ControladorVenta::ctrMostrarListaVentas($item, $valor);
$totalVentasCantidad = count($totalVentas);

/* TOTAL DE COMPRAS */
$totalCompras = ControladorCompra::ctrMostrarTotalComprasCantidad($item, $valor);
$totalComprasCantidad = count($totalCompras);

/* TOTAL DE COMPRAS */
$totalCompras = ControladorCompra::ctrMostrarTotalComprasCantidad($item, $valor);
$totalComprasCantidad = count($totalCompras);

/* MOSTRAR TOTAL DE CLIENTES */
$totalClientes = ControladorCliente::ctrMostrarTotalCliente($item, $valor);

/* MOSTRAR TOTAL DE PROVEEDORES */
$totalProveedores = ControladorProveedores::ctrMostrarTotalProveedores($item, $valor);

$rolesPermitidos = [
    "ADMINISTRADOR",
    "GERENTE",
    "SUPERVISOR",
    "VENDEDOR",
    "TRANSPORTISTA",
    "INVENTARIO"
];

?>

<div class="page-wrapper">
    <div class="content">
        <?php
        /*  echo '<pre>' . print_r($_SESSION["permisos"], true) . '</pre>'; */
        if (isset($_SESSION["roles"][0]["nombre"]) && in_array($_SESSION["roles"][0]["nombre"], $rolesPermitidos) && isset($_SESSION["permisos"]["inicio"]["acciones"]) && in_array("ver", $_SESSION["permisos"]["inicio"]["acciones"])) {
        ?>
            <div class="row" id="welcome-section">
                <div class="col-md-12 text-center mb-4">
                    <h1 class="display-4">¡Hola, <?php echo $_SESSION["usuario"]["nombre_usuario"]; ?>!</h1>
                    <p class="lead">¡Bienvenido a tu panel de control!</p>
                    <h1>😊</h1>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    var welcomeSection = document.getElementById('welcome-section');
                    if (welcomeSection) {
                        welcomeSection.style.transition = 'opacity 2s ease-out';
                        welcomeSection.style.opacity = '0';
                        setTimeout(function() {
                            welcomeSection.style.display = 'none';
                        }, 2000); // Wait for the fade-out transition to complete
                    }
                }, 5000); // 300000ms = 5 minutes
            </script>
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget dash1">
                        <div class="dash-widgetimg">
                            <span><img src="vistas/assets/img/icons/dash2.svg" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>S/ <span class="counters"><?php echo $precioFormateado ?></span></h5>
                            <h6>Total de ventas</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget">
                        <div class="dash-widgetimg">
                            <span><img src="vistas/assets/img/icons/dash1.svg" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>S/ <span class="counters"><?php echo $precioFormateadoCompra; ?></span></h5>
                            <h6>Total de compras</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget dash2">
                        <div class="dash-widgetimg">
                            <span><img src="vistas/assets/img/icons/dash3.svg" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>S/ <span class="counters"><?php echo $precioFormateadoContado ?></span></h5>
                            <h6>Ventas al contado</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="dash-widget dash3">
                        <div class="dash-widgetimg">
                            <span><img src="vistas/assets/img/icons/dash4.svg" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>S/ <span class="counters"><?php echo $precioFormateadoCredito ?></span></h5>
                            <h6>Ventas al crédito</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count">
                        <div class="dash-counts">
                            <h4><?php echo $totalClientes ?></h4>
                            <h5>Clientes</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count das1">
                        <div class="dash-counts">
                            <h4><?php echo $totalProveedores ?></h4>
                            <h5>Proveedores</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count das3">
                        <div class="dash-counts">
                            <h4><?php echo $totalVentasCantidad ?></h4>
                            <h5>Ventas</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="file"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12 d-flex">
                    <div class="dash-count das2">
                        <div class="dash-counts">
                            <h4><?php echo $totalComprasCantidad ?></h4>
                            <h5>Compras</h5>
                        </div>
                        <div class="dash-imgs">
                            <i data-feather="file-text"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Ventas y Compras</h5>
                            <div class="graph-sets">
                                <ul>
                                    <li>
                                        <span>Ventas</span>
                                    </li>
                                    <li>
                                        <span>Compras</span>
                                    </li>
                                </ul>
                                <div class="dropdown">
                                    <button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo date('Y') ?> <img src="vistas/assets/img/icons/dropdown.svg" alt="img" class="ms-2">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="sales_charts"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Nuevos productos</h4>
                            <div class="dropdown">
                                <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" class="dropset">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a href="productos" class="dropdown-item">Lista de productos</a>
                                    </li>
                                    <li>
                                        <a href="productos" class="dropdown-item">Agregar producto</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive dataview">
                                <table class="table datatable ">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $item = null;
                                        $valor = null;

                                        $productosNuevos = ControladorProducto::ctrMostrarProductosNuevos($item, $valor);

                                        $contador = 1;

                                        foreach ($productosNuevos as $producto) {
                                            // Verificar si la imagen es null o vacía, si es así asignar null
                                            $imagen = !empty($producto["imagen_producto"]) && $producto["imagen_producto"] !== 'null' ? substr($producto["imagen_producto"], 3) : null;
                                        ?>
                                            <tr>
                                                <td><?php echo $contador ?></td>
                                                <td class="productimgname">
                                                    <a href="productlist.html" class="product-img">
                                                        <?php if ($imagen): ?>
                                                            <img src="<?php echo $imagen; ?>" alt="product">
                                                        <?php else: ?>
                                                            <img src="vistas/img/productos/default.png" alt="producto sin imagen">
                                                        <?php endif; ?>
                                                    </a>
                                                    <a href="productlist.html"><?php echo $producto["nombre_producto"] ?></a>
                                                </td>
                                                <td>S/ <?php echo $producto["precio_producto"] ?></td>
                                            </tr>
                                        <?php
                                            $contador++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-0">
                <div class="card-body">
                    <h4 class="card-title">Productos por vencer</h4>
                    <div class="table-responsive dataview">
                        <table class="table datatable ">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Código producto</th>
                                    <th>Nombre producto</th>
                                    <th>Stock producto</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Fecha vencimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $item = null;
                                $valor = null;

                                $productosPorVencer = ControladorProducto::ctrMostrarProductosFechaVencimientos($item, $valor);

                                $contador = 1;

                                foreach ($productosPorVencer as $producto) {
                                ?>
                                    <tr>
                                        <td><?php echo $contador ?></td>
                                        <td><a href="javascript:void(0);"><?php echo $producto["codigo_producto"] ?></a></td>
                                        <td class="productimgname">
                                            <a class="product-img" href="productlist.html">
                                                <img src="<?php echo substr($producto["imagen_producto"], 3); ?>" alt="product">
                                            </a>
                                            <a href="productlist.html"><?php echo $producto["nombre_producto"] ?></a>
                                        </td>
                                        <td><?php echo $producto["stock_producto"] ?></td>
                                        <td><?php echo $producto["nombre_categoria"] ?></td>
                                        <td class="text-center fw-bold"><?php echo $producto["fecha_vencimiento"] ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="row justify-content-center mt-5">
                <div class="col-md-8 text-center">
                    <div class="alert" style="background: #FF9F43; color: white">
                        <h1 class="display-4">¡Hola, <?php echo $_SESSION["usuario"]["nombre_usuario"]; ?>!</h1>
                        <p class="lead">¡Bienvenido a tu panel de control!</p>
                        <h1>😊</h1>
                    </div>
                </div>
            </div>
            
        <?php
        }
        ?>

    </div>
</div>