<?php

/* CONTROLADORES */

require_once "controladores/Plantilla.controlador.php";

require_once "controladores/Sucursal.controlador.php";
require_once "controladores/Usuario.controlador.php";
require_once "controladores/Rol.controlador.php";
require_once "controladores/UsuarioRoles.controlador.php";
require_once "controladores/Tipo.documento.controlador.php";
require_once "controladores/Proveedor.controlador.php";
require_once "controladores/Cliente.controlador.php";
require_once "controladores/Categoria.controlador.php";
require_once "controladores/Producto.controlador.php";
require_once "controladores/Compra.controlador.php";
require_once "controladores/Lista.compra.controlador.php";
require_once "controladores/Ventas.controlador.php";
require_once "controladores/Cotizacion.controllador.php";
require_once "controladores/Trabajador.controlador.php";
require_once "controladores/Contrato.trabajador.controlador.php";
require_once "controladores/Pago.trabajador.controlador.php";
require_once "controladores/Vacaciones.controlador.php";
require_once "controladores/Configuracion.ticket.controlador.php";
require_once "controladores/Impresora.controlador.php";
require_once "controladores/Serie.num.controlador.php";
require_once "controladores/Historial.pago.controlador.php";
require_once "controladores/Configuraracion.sistema.controlador.php";
require_once "controladores/Correo.config.controlador.php";
require_once "controladores/Caja.general.controlador.php";
require_once "controladores/Gastos.ingreso.controlador.php";

/* MODELOS */

require_once "modelos/Sucursal.modelo.php";
require_once "modelos/Usuario.modelo.php";
require_once "modelos/Rol.modelo.php";
require_once "modelos/UsuarioRoles.modelo.php";
require_once "modelos/Tipo.documento.modelo.php";
require_once "modelos/Proveedor.modelo.php";
require_once "modelos/Cliente.modelo.php";
require_once "modelos/Categoria.modelo.php";
require_once "modelos/Producto.modelo.php";
require_once "modelos/Compra.modelo.php";
require_once "modelos/Ventas.modelo.php";
require_once "modelos/Cotizacion.modelo.php";
require_once "modelos/Categoria.modelo.php";
require_once "modelos/Trabajador.modelo.php";
require_once "modelos/Contrato.trabajador.modelo.php";
require_once "modelos/Pago.trabajador.modelo.php";
require_once "modelos/Vacaciones.modelo.php";
require_once "modelos/Configuracion.ticket.modelo.php";
require_once "modelos/Impresora.modelo.php";
require_once "modelos/Serie.num.modelo.php";
require_once "modelos/Historial.pago.modelo.php";
require_once "modelos/Configuracion.sistema.modelo.php";
require_once "modelos/Correo.config.modelo.php";
require_once "modelos/Caja.general.modelo.php";
require_once "modelos/Gastos.ingreso.modelo.php";


$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();
