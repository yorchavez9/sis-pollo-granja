<?php
$rol = $_SESSION["nombre_rol"];
$modulos = $_SESSION["modulos"];
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
  
                <!-- Panel principal -->
                <?php if (in_array('inicio', explode(',', $modulos))) : 
                    ?>
                    <li class="active">
                        <a href="inicio"><img src="vistas/assets/img/icons/dashboard.svg" alt="img"><span>Panel</span></a>
                    </li>
                <?php endif; ?>

                <!-- Sucursales -->
                <?php if (in_array('sucursales', explode(',', $modulos))) : ?>
                    <li>
                        <a href="sucursales">
                            <i class="fas fa-store" style="color: #808080;"></i> <!-- Color plomo -->
                            <span>Sucursales</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Personas -->
                 <?php 
                 $section_personas = false;
                 ?>
                <li class="submenu">
                    <?php ob_start() ?>
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/users1.svg" alt="img"><span>Personas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (in_array('tipo_documento', explode(',', $modulos))) : $section_personas = true; ?>
                            <li><a href="tipoDocumento">Tipo documento</a></li>
                        <?php endif; ?>
                        <?php if (in_array('roles', explode(',', $modulos))) : $section_personas = true; ?>
                            <li><a href="roles">Roles</a></li>
                        <?php endif; ?>
                        <?php if (in_array('usuarios', explode(',', $modulos))) : $section_personas = true; ?>
                            <li><a href="usuarios">Usuarios</a></li>
                        <?php endif; ?>
                        <?php if (in_array('permisos', explode(',', $modulos))) : $section_personas = true; ?>
                            <li><a href="permisos">Permisos</a></li>
                        <?php endif; ?>
                        <?php if (in_array('proveedores', explode(',', $modulos))) : $section_personas = true; ?>
                            <li><a href="proveedores">Proveedores</a></li>
                        <?php endif; ?>
                        <?php if (in_array('clientes', explode(',', $modulos))) : $section_personas = true; ?>
                            <li><a href="clientes">Clientes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_personas = ob_get_clean();
                if($section_personas){
                    echo $show_section_personas;
                }
                ?>

                <!-- Inventario -->
                <?php
                $section_inventario = false;
                ?>
                <li class="submenu">
                    <?php ob_start() ?>
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/product.svg" alt="img"><span>Inventario</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (in_array('categorias', explode(',', $modulos))) :  $section_inventario = true; ?>
                            <li><a href="categorias">Categorías</a></li>
                        <?php endif; ?>
                        <?php if (in_array('proveedores', explode(',', $modulos))) : $section_inventario = true; ?>
                            <li><a href="proveedores">Proveedor</a></li>
                        <?php endif; ?>
                        <?php if (in_array('productos', explode(',', $modulos))) : $section_inventario = true; ?>
                            <li><a href="productos">Producto</a></li>
                        <?php endif; ?>
                        <?php if (in_array('codigo_barra', explode(',', $modulos))) : $section_inventario = true; ?>
                            <li><a href="codigoBarra">Imprimir código de barras</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_inventario = ob_get_clean();
                if($section_inventario){
                    echo $show_section_inventario;
                }
                ?>

                <!-- Compras -->
                <?php
                $section_compras = false;
                ?>
                <li class="submenu">
                    <?php ob_start(); ?>
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/purchase1.svg" alt="img"><span>Compras</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (in_array('compras', explode(',', $modulos))) : $section_compras = true; ?>
                            <li><a href="compras">Compra</a></li>
                        <?php endif; ?>
                        <?php if (in_array('lista_compras', explode(',', $modulos))) : $section_compras = true ?>
                            <li><a href="listaCompras">Lista de compras</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_compras = ob_get_clean();
                if($section_compras){
                    echo $show_section_compras;
                }
                ?>

                <!-- Ventas -->
                <?php
                $section_ventas = false;
                ?>
                <li class="submenu">
                    <?php ob_start(); ?>
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/sales1.svg" alt="img"><span>Ventas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (in_array('cotizacion', explode(',', $modulos))) : $section_ventas = true; ?>
                            <li><a href="cotizacion">Cotizaciones</a></li>
                        <?php endif; ?>
                        <?php if (in_array('ventas', explode(',', $modulos))) : $section_ventas = true; ?>
                            <li><a href="ventas">Punto de venta</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_ventas = ob_get_clean();
                if($section_ventas){
                    echo $show_section_ventas;
                }
                ?>

                <!-- CAJA -->
                <?php
                $section_caja = false;
                ?>
                <li class="submenu">
                    <?php ob_start(); ?>
                    <a href="javascript:void(0);">
                        <i class="fas fa-cash-register" style="color: #808080;"></i><span>Caja</span> <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <?php if (in_array('caja_general', explode(',', $modulos))) : $section_caja = true; ?>
                            <li><a href="cajaGeneral">Apertura & Cierre</a></li>
                        <?php endif; ?>
                        <?php if (in_array('arqueos_caja', explode(',', $modulos))) : $section_caja = true; ?>
                            <li><a href="arqueosCaja">Arqueo de caja</a></li>
                        <?php endif; ?>
                        <?php if (in_array('gastos_ingresos', explode(',', $modulos))) : $section_caja = true; ?>
                            <li><a href="gastosIngresos">Gastos/Ingresos extras</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reportes_caja', explode(',', $modulos))) : $section_caja = true; ?>
                            <li><a href="reportesCaja">Reportes Gastos/Ingresos</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_ventas', explode(',', $modulos))) : $section_caja = true; ?>
                            <li><a href="reporteVentas">Reportes de ventas</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_compras', explode(',', $modulos))) : $section_caja = true; ?>
                            <li><a href="reporteCompras">Reportes de compras</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_caja = ob_get_clean();
                if($section_caja){
                    echo $show_section_caja;
                }
                ?>
                
                <!-- Trabajadores -->
                <?php
                $section_trabajadores = false;
                ?>
                <li class="submenu">
                    <?php ob_start(); ?>
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/users1.svg" alt="img"><span>Trabajadores</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (in_array('trabajador', explode(',', $modulos))) : $section_trabajadores = true; ?>
                            <li><a href="trabajador">Trabajadores</a></li>
                        <?php endif; ?>
                        <?php if (in_array('contrato_trabajador', explode(',', $modulos))) : $section_trabajadores = true; ?>
                            <li><a href="contratoTrabajador">Contrato trabajador</a></li>
                        <?php endif; ?>
                        <?php if (in_array('pago_trabajador', explode(',', $modulos))) : $section_trabajadores = true; ?>
                            <li><a href="pagoTrabajador">Pagos trabajador</a></li>
                        <?php endif; ?>
                        <?php if (in_array('vacaciones', explode(',', $modulos))) : $section_trabajadores = true; ?>
                            <li><a href="vacaciones">Vacaciones</a></li>
                        <?php endif; ?>
                        <?php if (in_array('asistencia', explode(',', $modulos))) : $section_trabajadores = true; ?>
                            <li><a href="asistencia">Asistencia</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_trabajadores = ob_get_clean();
                if($section_trabajadores){
                    echo $show_section_trabajadores;
                }
                ?>

                <!-- REPORTES -->
                <?php
                $section_reportes = false;
                ?>
                <li class="submenu">
                    <?php ob_start(); ?>
                    <a href="javascript:void(0);">
                        <img src="vistas/assets/img/icons/time.svg" alt="img">
                        <span>Reportes</span> <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <?php if (in_array('reporte_sucursales', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteSucursales">Sucursales</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_usuarios', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteUsuarios">Usuarios</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_roles', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteRoles">Roles</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_proveedores', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteProveedores">Proveedores</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_clientes', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteClientes">Clientes</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_productos', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteProductos">Productos</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_compras', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteCompras">Compras</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_ventas', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteVentas">Ventas</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_trabajadores', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteTrabajadores">Trabajadores</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_pagos_trabajador', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reportePagosTrabajador">Pagos trabajador</a></li>
                        <?php endif; ?>
                        <?php if (in_array('reporte_asistencia', explode(',', $modulos))) : $section_reportes = true; ?>
                            <li><a href="reporteAsistencia">Asistencia</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php
                $show_section_reportes = ob_get_clean();
                if($section_reportes){
                    echo $show_section_reportes;
                }
                ?>

                <!-- Reportes -->

                <?php if (in_array('configuracion', explode(',', $modulos))) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/settings.svg" alt="img"><span>Ajustes</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="configuracionSistema">Config sistema</a></li>
                        <li><a href="configuracionTicket">Config. comprobante</a></li>
                        <li><a href="configuracionCorreo">Config. correo</a></li>
                        <li><a href="numFolio">Config Número y Folios</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Ajustes -->

            </ul>
        </div>
    </div>
</div>