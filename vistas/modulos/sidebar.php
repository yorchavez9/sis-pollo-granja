<?php
// Normaliza los roles para evitar problemas de mayúsculas/minúsculas
function normalizarRoles($roles)
{
    return array_map('strtolower', $roles);
}

function tieneRol($rolesPermitidos)
{
    if (!isset($_SESSION["roles"])) {
        return false;
    }

    // Normalizamos roles
    $rolesUsuario = normalizarRoles($_SESSION["roles"]);
    $rolesPermitidos = normalizarRoles($rolesPermitidos);

    return count(array_intersect($rolesUsuario, $rolesPermitidos)) > 0;
}

// Configuración central de permisos
$permisosMenu = [
    'ceo' => ['CEO'],
    'sucursales' => ['ADMINISTRADOR', 'GERENTE', 'SUPERVISOR'],
    'personas' => ['ADMINISTRADOR', 'GERENTE', 'SUPERVISOR'],
    'inventario' => ['ADMINISTRADOR', 'GERENTE', 'SUPERVISOR'],
    'compras' => ['ADMINISTRADOR', 'GERENTE', 'SUPERVISOR', 'VENDEDOR', 'VENDEDORA', 'CAJERO', 'CAJERA'],
    'ventas' => ['ADMINISTRADOR', 'GERENTE', 'SUPERVISOR', 'VENDEDOR', 'VENDEDORA', 'CAJERO', 'CAJERA'],
    'trabajadores' => ['ADMINISTRADOR', 'GERENTE'],
    'reportes' => ['ADMINISTRADOR', 'GERENTE', 'FINANZAS', 'ANALISTA', 'CAJERA', 'CAJERO'],
    'ajustes' => ['ADMINISTRADOR', 'GERENTE'],
    'clientes' => ['ADMINISTRADOR', 'GERENTE', 'VENDEDOR', 'VENDEDORA', 'CAJERO', 'CAJERA', 'FINANZAS'],
    'facturacion' => ['ADMINISTRADOR', 'GERENTE', 'FINANZAS', 'CAJERO', 'CAJERA']
];
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="active">
                    <a href="inicio"><img src="vistas/assets/img/icons/dashboard.svg" alt="img"><span>Panel</span></a>
                </li>
                <!-- Sucursales -->
                <?php if (tieneRol($permisosMenu['sucursales'])) { ?>
                    <li>
                        <a href="sucursales">
                            <i class="fas fa-store" style="color: #808080;"></i> <!-- Color plomo -->
                            <span>Sucursales</span>
                        </a>

                    </li>
                <?php } ?>

                <!-- Personas -->
                <li class="submenu">
                    <a href="javascript:void(0);"><img src="vistas/assets/img/icons/users1.svg" alt="img"><span>Personas</span> <span class="menu-arrow"></span></a>
                    <ul>
                        <?php if (tieneRol($permisosMenu['personas'])): ?>
                            <li><a href="tipoDocumento">Tipo documento</a></li>
                            <li><a href="usuarios">Usuarios</a></li>
                            <li><a href="roles">Roles</a></li>
                            <li><a href="establecerRoles">Establecer roles</a></li>
                            <li><a href="proveedores">Proveedores</a></li>
                        <?php endif; ?>

                        <?php if (tieneRol($permisosMenu['ventas'])): ?>
                            <li><a href="clientes">Clientes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Inventario -->
                <?php if (tieneRol($permisosMenu['inventario'])): ?>
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="vistas/assets/img/icons/product.svg" alt="img"><span>Inventario</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="categorias">Categorías</a></li>
                            <li><a href="proveedores">Proveedor</a></li>
                            <li><a href="productos">Producto</a></li>
                            <li><a href="codigoBarra">Imprimir código de barras</a></li>
                        </ul>
                    </li>
                <?php endif ?>

                <!-- Compras -->
                <?php if (tieneRol($permisosMenu['compras'])): ?>
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="vistas/assets/img/icons/purchase1.svg" alt="img"><span>Compras</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="compras">Compra</a></li>
                            <li><a href="listaCompras">Lista de compras</a></li>
                        </ul>
                    </li>
                <?php endif ?>

                <!-- Ventas -->
                <?php if (tieneRol($permisosMenu['ventas'])): ?>
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="vistas/assets/img/icons/sales1.svg" alt="img"><span>Ventas</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="clientes">Clientes</a></li>
                            <li><a href="ventas">Punto de venta</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Trabajadores -->
                <?php if (tieneRol($permisosMenu['trabajadores'])): ?>
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="vistas/assets/img/icons/users1.svg" alt="img"><span>Trabajadores</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="trabajador">Trabajadores</a></li>
                            <li><a href="contratoTrabajador">Contrato trabajador</a></li>
                            <li><a href="pagoTrabajador">Pagos trabajador</a></li>
                            <li><a href="vacaciones">Vacaciones</a></li>
                            <li><a href="asistencia">Asistencia</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (tieneRol($permisosMenu['personas'])) { ?>
                    <li class="submenu">
                        <a href="javascript:void(0);"><img src="vistas/assets/img/icons/time.svg" alt="img"><span>Reportes</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="reporteSucursales">Sucursales</a></li>
                            <li><a href="reporteUsuarios">Usuarios</a></li>
                            <li><a href="reporteRoles">Roles</a></li>
                            <li><a href="reporteProveedores">Proveedores</a></li>
                            <li><a href="reporteClientes">Clientes</a></li>
                            <li><a href="reporteCategorias">Categorias</a></li>
                            <li><a href="reporteProductos">Productos</a></li>
                            <li><a href="reporteCompras">Compras</a></li>
                            <li><a href="reporteVentas">Ventas</a></li>
                            <li><a href="reporteTrabajadores">Trabajadores</a></li>
                            <li><a href="reportePagosTrabajador">Pagos trabajador</a></li>
                            <li><a href="reporteAsistencia">Asistencia</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <!-- Reportes -->

                    <?php if (tieneRol($permisosMenu['ajustes'])) { ?>
                        <li class="submenu">
                            <a href="javascript:void(0);"><img src="vistas/assets/img/icons/settings.svg" alt="img"><span>Ajustes</span> <span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="configuracionTicket">Configuración Ticket</a></li>
                                <li><a href="configuracionImpresora">Configuración Impresora</a></li>
                            </ul>
                        </li>

                        <?php } ?>
                        <!-- Ajustes -->

            </ul>
        </div>
    </div>
</div>
