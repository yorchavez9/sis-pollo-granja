<?php
$permisos = $_SESSION["permisos"] ?? [];
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Dashboard -->
                <?php if (isset($permisos["inicio"])) : ?>
                <li class="active">
                    <a href="inicio"><i class="fas fa-home"></i><span>Panel Principal</span></a>
                </li>
                <?php endif; ?>
                
                <!-- Sucursales -->
                <?php if (isset($permisos["sucursales"])) : ?>
                <li>
                    <a href="sucursales"><i class="fas fa-store"></i><span>Sucursales</span></a>
                </li>
                <?php endif; ?>
                
                <!-- Personas - Solo mostrar si tiene al menos un permiso -->
                <?php if (isset($permisos["tipo_documento"]) || isset($permisos["proveedores"]) || isset($permisos["clientes"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-users"></i><span>Personas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["tipo_documento"])) : ?>
                        <li><a href="tipoDocumento"><i class="fas fa-id-card"></i> Tipo de Documento</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["proveedores"])) : ?>
                        <li><a href="proveedores"><i class="fas fa-truck"></i> Proveedores</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["clientes"])) : ?>
                        <li><a href="clientes"><i class="fas fa-user-friends"></i> Clientes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Usuarios y Seguridad -->
                <?php if (isset($permisos["roles"]) || isset($permisos["usuarios"]) || isset($permisos["permisos"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-user-shield"></i><span>Seguridad</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["roles"])) : ?>
                        <li><a href="roles"><i class="fas fa-user-tag"></i> Roles</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["usuarios"])) : ?>
                        <li><a href="usuarios"><i class="fas fa-users-cog"></i> Usuarios</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["permisos"])) : ?>
                        <li><a href="permisos"><i class="fas fa-key"></i> Permisos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Inventario y Productos -->
                <?php if (isset($permisos["categorias"]) || isset($permisos["productos"]) || isset($permisos["codigo_barra"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-boxes"></i><span>Inventario</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["categorias"])) : ?>
                        <li><a href="categorias"><i class="fas fa-tags"></i> Categorías</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["productos"])) : ?>
                        <li><a href="productos"><i class="fas fa-box-open"></i> Productos</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["codigo_barra"])) : ?>
                        <li><a href="codigoBarra"><i class="fas fa-barcode"></i> Códigos de Barras</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Compras -->
                <?php if (isset($permisos["compras"]) || isset($permisos["lista_compras"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-shopping-basket"></i><span>Compras</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["compras"])) : ?>
                        <li><a href="compras"><i class="fas fa-cart-plus"></i> Registrar Compra</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["lista_compras"])) : ?>
                        <li><a href="listaCompras"><i class="fas fa-list"></i> Historial Compras</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Ventas -->
                <?php if (isset($permisos["cotizacion"]) || isset($permisos["ventas"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-cash-register"></i><span>Ventas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["cotizacion"])) : ?>
                        <li><a href="cotizacion"><i class="fas fa-file-invoice-dollar"></i> Cotizaciones</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["ventas"])) : ?>
                        <li><a href="ventas"><i class="fas fa-shopping-cart"></i> Punto de Venta</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Gestión de Caja -->
                <?php if (isset($permisos["caja_general"]) || isset($permisos["arqueos_caja"]) || 
                      isset($permisos["gastos_ingresos"]) || isset($permisos["reportes_caja"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-cash-register"></i><span>Caja</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["arqueos_caja"])) : ?>
                        <li><a href="arqueosCaja"><i class="fas fa-calculator"></i> Arqueos</a></li>
                        <?php endif; ?>

                        <?php if (isset($permisos["caja_general"])) : ?>
                        <li><a href="cajaGeneral"><i class="fas fa-door-open"></i> Apertura/Cierre</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["gastos_ingresos"])) : ?>
                        <li><a href="gastosIngresos"><i class="fas fa-exchange-alt"></i> Gastos/Ingresos</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reportes_caja"])) : ?>
                        <li><a href="reportesCaja"><i class="fas fa-chart-bar"></i> Reportes Caja</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Trabajadores -->
                <?php if (isset($permisos["trabajador"]) || isset($permisos["contrato_trabajador"]) || 
                      isset($permisos["pago_trabajador"]) || isset($permisos["vacaciones"]) || 
                      isset($permisos["asistencia"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-user-tie"></i><span>Trabajadores</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["trabajador"])) : ?>
                        <li><a href="trabajador"><i class="fas fa-users"></i> Trabajadores</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["contrato_trabajador"])) : ?>
                        <li><a href="contratoTrabajador"><i class="fas fa-file-signature"></i> Contratos</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["pago_trabajador"])) : ?>
                        <li><a href="pagoTrabajador"><i class="fas fa-money-bill-wave"></i> Pagos</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["vacaciones"])) : ?>
                        <li><a href="vacaciones"><i class="fas fa-umbrella-beach"></i> Vacaciones</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["asistencia"])) : ?>
                        <li><a href="asistencia"><i class="fas fa-calendar-check"></i> Asistencias</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Reportes -->
                <?php if (isset($permisos["reporte_sucursales"]) || isset($permisos["reporte_usuarios"]) || 
                      isset($permisos["reporte_roles"]) || isset($permisos["reporte_proveedores"]) || 
                      isset($permisos["reporte_clientes"]) || isset($permisos["reporte_productos"]) || 
                      isset($permisos["reporte_compras"]) || isset($permisos["reporte_ventas"]) || 
                      isset($permisos["reporte_trabajadores"]) || isset($permisos["reporte_pagos_trabajador"]) || 
                      isset($permisos["reporte_asistencia"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-chart-bar"></i><span>Reportes</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (isset($permisos["reporte_sucursales"])) : ?>
                        <li><a href="reporteSucursales"><i class="fas fa-store"></i> Sucursales</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_usuarios"])) : ?>
                        <li><a href="reporteUsuarios"><i class="fas fa-users-cog"></i> Usuarios</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_roles"])) : ?>
                        <li><a href="reporteRoles"><i class="fas fa-user-tag"></i> Roles</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_proveedores"])) : ?>
                        <li><a href="reporteProveedores"><i class="fas fa-truck"></i> Proveedores</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_clientes"])) : ?>
                        <li><a href="reporteClientes"><i class="fas fa-user-friends"></i> Clientes</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_productos"])) : ?>
                        <li><a href="reporteProductos"><i class="fas fa-box-open"></i> Productos</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_compras"])) : ?>
                        <li><a href="reporteCompras"><i class="fas fa-shopping-basket"></i> Compras</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_ventas"])) : ?>
                        <li><a href="reporteVentas"><i class="fas fa-cash-register"></i> Ventas</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_trabajadores"])) : ?>
                        <li><a href="reporteTrabajadores"><i class="fas fa-user-tie"></i> Trabajadores</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_pagos_trabajador"])) : ?>
                        <li><a href="reportePagosTrabajador"><i class="fas fa-money-bill-wave"></i> Pagos Trabajadores</a></li>
                        <?php endif; ?>
                        
                        <?php if (isset($permisos["reporte_asistencia"])) : ?>
                        <li><a href="reporteAsistencia"><i class="fas fa-calendar-check"></i> Asistencias</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Configuración -->
                <?php if (isset($permisos["configuracion"])) : ?>
                <li class="submenu">
                    <a href="javascript:void(0);"><i class="fas fa-cog"></i><span>Configuración</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="configuracionSistema"><i class="fas fa-sliders-h"></i> Config. Sistema</a></li>
                        <li><a href="configuracionTicket"><i class="fas fa-ticket-alt"></i> Config. comprobante</a></li>
                        <li><a href="configuracionCorreo"><i class="fas fa-envelope"></i> Correo</a></li>
                        <li><a href="numFolio"><i class="fas fa-list-ol"></i> Folios</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>