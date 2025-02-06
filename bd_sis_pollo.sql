-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-02-2025 a las 19:54:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_sis_pollo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones`
--

INSERT INTO `acciones` (`id_accion`, `accion`) VALUES
(1, 'agregar'),
(2, 'editar'),
(3, 'ver'),
(4, 'eliminar'),
(5, 'imprimir'),
(6, 'descargar'),
(7, 'activar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueos_caja`
--

CREATE TABLE `arqueos_caja` (
  `id_arqueo` int(11) NOT NULL,
  `id_movimiento_caja` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_arqueo` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto_sistema` decimal(10,2) NOT NULL,
  `monto_fisico` decimal(10,2) NOT NULL,
  `diferencia` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arqueos_caja`
--

INSERT INTO `arqueos_caja` (`id_arqueo`, `id_movimiento_caja`, `id_usuario`, `fecha_arqueo`, `monto_sistema`, `monto_fisico`, `diferencia`, `observaciones`) VALUES
(9, 43, 21, '2025-02-03 05:00:00', 3572.00, 3572.00, 0.00, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_trabajadores`
--

CREATE TABLE `asistencia_trabajadores` (
  `id_asistencia` int(11) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `fecha_asistencia` date NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  `estado` enum('Presente','Tarde','Falta') NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `descripcion`, `fecha`) VALUES
(11, 'Pollos', '', '2025-02-03 09:03:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_correo`
--

CREATE TABLE `config_correo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `smtp` varchar(150) NOT NULL,
  `usuario` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `puerto` int(11) NOT NULL,
  `correo_remitente` varchar(150) NOT NULL,
  `nombre_remitente` varchar(150) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_sistema`
--

CREATE TABLE `config_sistema` (
  `id_img` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `icon_pestana` varchar(255) NOT NULL,
  `img_sidebar` varchar(255) NOT NULL,
  `img_sidebar_min` varchar(255) NOT NULL,
  `img_login` varchar(255) NOT NULL,
  `icon_login` varchar(255) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_ticket`
--

CREATE TABLE `config_ticket` (
  `id_config_ticket` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha_config_ticket` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `config_ticket`
--

INSERT INTO `config_ticket` (`id_config_ticket`, `nombre_empresa`, `ruc`, `telefono`, `correo`, `direccion`, `logo`, `mensaje`, `fecha_config_ticket`) VALUES
(3, 'Apuuray', '2034455678', '920468502', 'apuuray@gmail.com', 'Av. Los perdidos', '../vistas/img/ticket/202501132109044565.png', 'Gracias por la compra', '2025-01-13 15:09:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratos_trabajadores`
--

CREATE TABLE `contratos_trabajadores` (
  `id_contrato` int(11) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `tiempo_contrato` int(11) NOT NULL,
  `tipo_sueldo` varchar(100) NOT NULL,
  `sueldo` decimal(10,2) NOT NULL,
  `fecha_contrato` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizaciones`
--

CREATE TABLE `cotizaciones` (
  `id_cotizacion` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_cotizacion` date NOT NULL,
  `hora_cotizacion` text NOT NULL,
  `id_serie_num` int(11) NOT NULL,
  `serie_cotizacion` varchar(20) NOT NULL,
  `validez` int(11) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT NULL,
  `total_cotizacion` decimal(10,2) NOT NULL,
  `total_pago` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `igv_total` decimal(10,2) DEFAULT NULL,
  `tipo_pago` varchar(100) DEFAULT NULL,
  `estado_pago` varchar(100) DEFAULT NULL,
  `forma_pago` varchar(100) DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id_cotizacion`, `id_persona`, `id_usuario`, `fecha_cotizacion`, `hora_cotizacion`, `id_serie_num`, `serie_cotizacion`, `validez`, `impuesto`, `total_cotizacion`, `total_pago`, `sub_total`, `igv_total`, `tipo_pago`, `estado_pago`, `forma_pago`, `estado`) VALUES
(184, 52, 21, '2025-02-03', '09:44:02 AM', 17, 'B001', 1, 0.00, 288.00, 288.00, 288.00, 0.00, 'contado', 'completado', 'pago_efectivo', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cotizacion`
--

CREATE TABLE `detalle_cotizacion` (
  `id_detalle_contizacion` int(11) NOT NULL,
  `id_cotizacion` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `numero_javas` int(11) DEFAULT NULL,
  `numero_aves` int(11) DEFAULT NULL,
  `peso_promedio` decimal(10,2) DEFAULT NULL,
  `peso_bruto` decimal(10,2) DEFAULT NULL,
  `peso_tara` decimal(10,2) DEFAULT NULL,
  `peso_merma` decimal(10,2) DEFAULT NULL,
  `peso_neto` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_cotizacion`
--

INSERT INTO `detalle_cotizacion` (`id_detalle_contizacion`, `id_cotizacion`, `id_producto`, `numero_javas`, `numero_aves`, `peso_promedio`, `peso_bruto`, `peso_tara`, `peso_merma`, `peso_neto`, `precio_venta`) VALUES
(193, 184, 34, 0, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 24.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_egreso`
--

CREATE TABLE `detalle_egreso` (
  `id_detalle_egreso` int(11) NOT NULL,
  `id_egreso` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `numero_javas` int(11) NOT NULL,
  `numero_aves` int(11) NOT NULL,
  `peso_promedio` decimal(10,2) NOT NULL,
  `peso_bruto` decimal(10,2) NOT NULL,
  `peso_tara` decimal(10,2) DEFAULT NULL,
  `peso_merma` decimal(10,2) DEFAULT NULL,
  `peso_neto` decimal(10,2) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_egreso`
--

INSERT INTO `detalle_egreso` (`id_detalle_egreso`, `id_egreso`, `id_producto`, `numero_javas`, `numero_aves`, `peso_promedio`, `peso_bruto`, `peso_tara`, `peso_merma`, `peso_neto`, `precio_compra`, `precio_venta`) VALUES
(99, 99, 34, 0, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 12.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id_detalle_venta` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `numero_javas` int(11) NOT NULL,
  `numero_aves` int(11) NOT NULL,
  `peso_promedio` decimal(10,2) NOT NULL,
  `peso_bruto` decimal(10,2) NOT NULL,
  `peso_tara` decimal(10,2) DEFAULT NULL,
  `peso_merma` decimal(10,2) DEFAULT NULL,
  `peso_neto` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `fecha_detalle` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id_detalle_venta`, `id_venta`, `id_producto`, `numero_javas`, `numero_aves`, `peso_promedio`, `peso_bruto`, `peso_tara`, `peso_merma`, `peso_neto`, `precio_venta`, `fecha_detalle`) VALUES
(265, 274, 34, 0, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 24.00, '2025-02-06'),
(266, 275, 35, 0, 15, 0.00, 0.00, 0.00, 0.00, 0.00, 12.00, '2025-02-06'),
(267, 275, 34, 0, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 24.00, '2025-02-06'),
(268, 276, 35, 0, 15, 0.00, 0.00, 0.00, 0.00, 0.00, 12.00, '2025-02-06'),
(269, 277, 35, 0, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 12.00, '2025-02-06'),
(270, 277, 34, 0, 17, 0.00, 0.00, 0.00, 0.00, 0.00, 24.00, '2025-02-06'),
(271, 278, 35, 0, 122, 0.00, 0.00, 0.00, 0.00, 0.00, 12.00, '2025-02-06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresos`
--

CREATE TABLE `egresos` (
  `id_egreso` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_movimiento_caja` int(11) NOT NULL,
  `fecha_egre` date NOT NULL,
  `hora_egreso` text NOT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie_comprobante` varchar(10) NOT NULL,
  `num_comprobante` varchar(10) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT NULL,
  `total_compra` decimal(10,2) NOT NULL,
  `total_pago` decimal(10,2) NOT NULL,
  `subTotal` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) DEFAULT NULL,
  `tipo_pago` varchar(30) NOT NULL,
  `estado_pago` varchar(50) NOT NULL,
  `pago_e_y` varchar(50) NOT NULL,
  `fecha_egreso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `egresos`
--

INSERT INTO `egresos` (`id_egreso`, `id_persona`, `id_usuario`, `id_movimiento_caja`, `fecha_egre`, `hora_egreso`, `tipo_comprobante`, `serie_comprobante`, `num_comprobante`, `impuesto`, `total_compra`, `total_pago`, `subTotal`, `igv`, `tipo_pago`, `estado_pago`, `pago_e_y`, `fecha_egreso`) VALUES
(99, 46, 21, 43, '2025-02-03', '09:36:52 AM', 'factura', '12', '', 0.00, 144.00, 144.00, 144.00, 0.00, 'contado', 'completado', '', '2025-02-03 09:36:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gasto_ingreso`
--

CREATE TABLE `gasto_ingreso` (
  `id_gasto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_movimiento_caja` int(11) NOT NULL,
  `tipo` enum('egreso','ingreso') NOT NULL,
  `concepto` varchar(200) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `detalles` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gasto_ingreso`
--

INSERT INTO `gasto_ingreso` (`id_gasto`, `id_usuario`, `id_movimiento_caja`, `tipo`, `concepto`, `monto`, `detalles`, `fecha`) VALUES
(28, 21, 43, 'egreso', 'compra de olbsas', 45.00, '', '2025-02-03 15:28:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id_pago` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `monto_pago` decimal(10,2) NOT NULL,
  `forma_pago` varchar(20) NOT NULL,
  `numero_serie_pago` varchar(50) DEFAULT NULL,
  `comprobante_imagen` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `historial_pagos`
--

INSERT INTO `historial_pagos` (`id_pago`, `id_venta`, `monto_pago`, `forma_pago`, `numero_serie_pago`, `comprobante_imagen`, `fecha_registro`) VALUES
(276, 274, 288.00, 'pago_efectivo', NULL, NULL, '2025-02-06 12:51:58'),
(277, 275, 468.00, 'pago_efectivo', NULL, NULL, '2025-02-06 12:53:33'),
(278, 276, 180.00, 'pago_efectivo', NULL, NULL, '2025-02-06 12:54:22'),
(279, 277, 552.00, 'pago_efectivo', NULL, NULL, '2025-02-06 12:55:00'),
(280, 278, 1468.00, 'pago_efectivo', NULL, NULL, '2025-02-06 12:56:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impresora`
--

CREATE TABLE `impresora` (
  `id_impresora` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ip_impresora` varchar(50) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `modulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `modulo`) VALUES
(39, 'inicio'),
(40, 'sucursales'),
(41, 'tipo_documento'),
(42, 'roles'),
(43, 'usuarios'),
(44, 'establecer_roles'),
(45, 'proveedores'),
(46, 'clientes'),
(47, 'categorias'),
(48, 'productos'),
(49, 'codigo_barra'),
(50, 'compras'),
(51, 'lista_compras'),
(52, 'cotizacion'),
(53, 'ventas'),
(54, 'caja_general'),
(55, 'arqueos_caja'),
(56, 'gastos_ingresos'),
(57, 'reportes_caja'),
(60, 'trabajador'),
(61, 'contrato_trabajador'),
(62, 'pago_trabajador'),
(63, 'vacaciones'),
(64, 'asistencia'),
(65, 'reporte_sucursales'),
(66, 'reporte_usuarios'),
(67, 'reporte_roles'),
(68, 'reporte_proveedores'),
(69, 'reporte_clientes'),
(70, 'reporte_productos'),
(71, 'reporte_compras'),
(72, 'reporte_ventas'),
(73, 'reporte_trabajadores'),
(74, 'reporte_pagos_trabajador'),
(75, 'reporte_asistencia'),
(76, 'configuracion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_caja`
--

CREATE TABLE `movimientos_caja` (
  `id_movimiento` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_movimiento` enum('apertura','cierre') NOT NULL DEFAULT 'apertura',
  `egresos` decimal(10,2) DEFAULT 0.00,
  `ingresos` decimal(10,2) DEFAULT 0.00,
  `monto_inicial` decimal(10,2) DEFAULT 0.00,
  `monto_final` decimal(10,2) DEFAULT 0.00,
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierto','cerrado') DEFAULT 'abierto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos_caja`
--

INSERT INTO `movimientos_caja` (`id_movimiento`, `id_usuario`, `tipo_movimiento`, `egresos`, `ingresos`, `monto_inicial`, `monto_final`, `fecha_apertura`, `fecha_cierre`, `estado`) VALUES
(43, 21, 'apertura', 189.00, 7200.00, 20.00, 0.00, '2025-01-17 00:00:00', '2025-01-17 00:00:00', 'abierto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_trabajadores`
--

CREATE TABLE `pagos_trabajadores` (
  `id_pagos` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `monto_pago` decimal(10,2) NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado_pago` int(11) DEFAULT 1,
  `fecha_pago_default` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
  `tipo_persona` varchar(50) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `id_doc` int(11) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sitio_web` varchar(100) DEFAULT NULL,
  `estado_persona` int(11) DEFAULT 1,
  `tipo_banco` varchar(50) NOT NULL,
  `numero_cuenta` varchar(50) DEFAULT NULL,
  `fecha_persona` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id_persona`, `tipo_persona`, `razon_social`, `id_doc`, `numero_documento`, `direccion`, `ciudad`, `codigo_postal`, `telefono`, `email`, `sitio_web`, `estado_persona`, `tipo_banco`, `numero_cuenta`, `fecha_persona`) VALUES
(46, 'proveedor', 'Avicola', 2, '203445566', '', 'Lima', '', '925602416', '', '', 1, 'null', '', '2025-01-14 06:47:39'),
(51, 'cliente', 'Pedro', 4, '32143432', '', '', '', '', NULL, '', 1, 'null', '', '2025-01-14 07:02:11'),
(52, 'cliente', 'Jorge', 4, '72232334', '', '', '', '51925602416', 'djjmygm160399@gmail.com', '', 1, 'null', '', '2025-01-14 07:07:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `codigo_producto` varchar(20) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_producto` decimal(10,2) NOT NULL,
  `stock_producto` int(11) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `descripcion_producto` text DEFAULT NULL,
  `imagen_producto` varchar(100) DEFAULT NULL,
  `estado_producto` int(11) DEFAULT 1,
  `fecha_producto` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_categoria`, `codigo_producto`, `nombre_producto`, `precio_compra`, `precio_producto`, `stock_producto`, `fecha_vencimiento`, `descripcion_producto`, `imagen_producto`, `estado_producto`, `fecha_producto`) VALUES
(34, 11, 'P0012', 'Polllo B2', 12.00, 24.00, 282, NULL, '', '', 1, '2025-02-03 09:16:53'),
(35, 11, 'Asado', 'Pollo para asado', 0.00, 12.00, 81, NULL, '', '', 1, '2025-02-06 12:52:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'ADMINISTRADOR', 'Encargado de todo el sistema '),
(2, 'GERENTE', 'Encargado de administrar las tareas del dueño se les asigna '),
(3, 'TRABAJADOR', 'Trabajadores fijos o estables'),
(4, 'AYUDANTE', 'Ayudante temporal de los trabajadores'),
(5, 'FINANZAS', 'Encargado de la contabilidad de los puntos de ventas que se les asigna'),
(7, 'VENDEDOR', 'Registro de caja de las ventas del día'),
(9, 'CEO', 'Updated rol ceo'),
(11, 'PELADOR', 'Pela los pollos'),
(12, 'COMEDOR', ''),
(13, 'CAJERA', ''),
(14, 'CAJERO', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_acciones`
--

CREATE TABLE `role_acciones` (
  `id_usuario` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `id_accion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `role_acciones`
--

INSERT INTO `role_acciones` (`id_usuario`, `id_rol`, `id_modulo`, `id_accion`) VALUES
(21, 1, 39, 1),
(21, 1, 39, 2),
(21, 1, 39, 3),
(21, 1, 39, 4),
(21, 1, 39, 5),
(21, 1, 39, 6),
(21, 1, 39, 7),
(21, 1, 40, 1),
(21, 1, 40, 2),
(21, 1, 40, 3),
(21, 1, 40, 4),
(21, 1, 40, 5),
(21, 1, 40, 6),
(21, 1, 40, 7),
(21, 1, 41, 1),
(21, 1, 41, 2),
(21, 1, 41, 3),
(21, 1, 41, 4),
(21, 1, 41, 5),
(21, 1, 41, 6),
(21, 1, 41, 7),
(21, 1, 42, 1),
(21, 1, 42, 2),
(21, 1, 42, 3),
(21, 1, 42, 4),
(21, 1, 42, 5),
(21, 1, 42, 6),
(21, 1, 42, 7),
(21, 1, 43, 1),
(21, 1, 43, 2),
(21, 1, 43, 3),
(21, 1, 43, 4),
(21, 1, 43, 5),
(21, 1, 43, 6),
(21, 1, 43, 7),
(21, 1, 44, 1),
(21, 1, 44, 2),
(21, 1, 44, 3),
(21, 1, 44, 4),
(21, 1, 44, 5),
(21, 1, 44, 6),
(21, 1, 44, 7),
(21, 1, 45, 1),
(21, 1, 45, 2),
(21, 1, 45, 3),
(21, 1, 45, 4),
(21, 1, 45, 5),
(21, 1, 45, 6),
(21, 1, 45, 7),
(21, 1, 46, 1),
(21, 1, 46, 2),
(21, 1, 46, 3),
(21, 1, 46, 4),
(21, 1, 46, 5),
(21, 1, 46, 6),
(21, 1, 46, 7),
(21, 1, 47, 1),
(21, 1, 47, 2),
(21, 1, 47, 3),
(21, 1, 47, 4),
(21, 1, 47, 5),
(21, 1, 47, 6),
(21, 1, 47, 7),
(21, 1, 48, 1),
(21, 1, 48, 2),
(21, 1, 48, 3),
(21, 1, 48, 4),
(21, 1, 48, 5),
(21, 1, 48, 6),
(21, 1, 48, 7),
(21, 1, 49, 1),
(21, 1, 49, 2),
(21, 1, 49, 3),
(21, 1, 49, 4),
(21, 1, 49, 5),
(21, 1, 49, 6),
(21, 1, 49, 7),
(21, 1, 50, 1),
(21, 1, 50, 2),
(21, 1, 50, 3),
(21, 1, 50, 4),
(21, 1, 50, 5),
(21, 1, 50, 6),
(21, 1, 50, 7),
(21, 1, 51, 1),
(21, 1, 51, 2),
(21, 1, 51, 3),
(21, 1, 51, 4),
(21, 1, 51, 5),
(21, 1, 51, 6),
(21, 1, 51, 7),
(21, 1, 52, 1),
(21, 1, 52, 2),
(21, 1, 52, 3),
(21, 1, 52, 4),
(21, 1, 52, 5),
(21, 1, 52, 6),
(21, 1, 52, 7),
(21, 1, 53, 1),
(21, 1, 53, 2),
(21, 1, 53, 3),
(21, 1, 53, 4),
(21, 1, 53, 5),
(21, 1, 53, 6),
(21, 1, 53, 7),
(21, 1, 54, 1),
(21, 1, 54, 2),
(21, 1, 54, 3),
(21, 1, 54, 4),
(21, 1, 54, 5),
(21, 1, 54, 6),
(21, 1, 54, 7),
(21, 1, 55, 1),
(21, 1, 55, 2),
(21, 1, 55, 3),
(21, 1, 55, 4),
(21, 1, 55, 5),
(21, 1, 55, 6),
(21, 1, 55, 7),
(21, 1, 56, 1),
(21, 1, 56, 2),
(21, 1, 56, 3),
(21, 1, 56, 4),
(21, 1, 56, 5),
(21, 1, 56, 6),
(21, 1, 56, 7),
(21, 1, 57, 1),
(21, 1, 57, 2),
(21, 1, 57, 3),
(21, 1, 57, 4),
(21, 1, 57, 5),
(21, 1, 57, 6),
(21, 1, 57, 7),
(21, 1, 60, 1),
(21, 1, 60, 2),
(21, 1, 60, 3),
(21, 1, 60, 4),
(21, 1, 60, 5),
(21, 1, 60, 6),
(21, 1, 60, 7),
(21, 1, 61, 1),
(21, 1, 61, 2),
(21, 1, 61, 3),
(21, 1, 61, 4),
(21, 1, 61, 5),
(21, 1, 61, 6),
(21, 1, 61, 7),
(21, 1, 62, 1),
(21, 1, 62, 2),
(21, 1, 62, 3),
(21, 1, 62, 4),
(21, 1, 62, 5),
(21, 1, 62, 6),
(21, 1, 62, 7),
(21, 1, 63, 1),
(21, 1, 63, 2),
(21, 1, 63, 3),
(21, 1, 63, 4),
(21, 1, 63, 5),
(21, 1, 63, 6),
(21, 1, 63, 7),
(21, 1, 64, 1),
(21, 1, 64, 2),
(21, 1, 64, 3),
(21, 1, 64, 4),
(21, 1, 64, 5),
(21, 1, 64, 6),
(21, 1, 64, 7),
(21, 1, 65, 1),
(21, 1, 65, 2),
(21, 1, 65, 3),
(21, 1, 65, 4),
(21, 1, 65, 5),
(21, 1, 65, 6),
(21, 1, 65, 7),
(21, 1, 66, 1),
(21, 1, 66, 2),
(21, 1, 66, 3),
(21, 1, 66, 4),
(21, 1, 66, 5),
(21, 1, 66, 6),
(21, 1, 66, 7),
(21, 1, 67, 1),
(21, 1, 67, 2),
(21, 1, 67, 3),
(21, 1, 67, 4),
(21, 1, 67, 5),
(21, 1, 67, 6),
(21, 1, 67, 7),
(21, 1, 68, 1),
(21, 1, 68, 2),
(21, 1, 68, 3),
(21, 1, 68, 4),
(21, 1, 68, 5),
(21, 1, 68, 6),
(21, 1, 68, 7),
(21, 1, 69, 1),
(21, 1, 69, 2),
(21, 1, 69, 3),
(21, 1, 69, 4),
(21, 1, 69, 5),
(21, 1, 69, 6),
(21, 1, 69, 7),
(21, 1, 70, 1),
(21, 1, 70, 2),
(21, 1, 70, 3),
(21, 1, 70, 4),
(21, 1, 70, 5),
(21, 1, 70, 6),
(21, 1, 70, 7),
(21, 1, 71, 1),
(21, 1, 71, 2),
(21, 1, 71, 3),
(21, 1, 71, 4),
(21, 1, 71, 5),
(21, 1, 71, 6),
(21, 1, 71, 7),
(21, 1, 72, 1),
(21, 1, 72, 2),
(21, 1, 72, 3),
(21, 1, 72, 4),
(21, 1, 72, 5),
(21, 1, 72, 6),
(21, 1, 72, 7),
(21, 1, 73, 1),
(21, 1, 73, 2),
(21, 1, 73, 3),
(21, 1, 73, 4),
(21, 1, 73, 5),
(21, 1, 73, 6),
(21, 1, 73, 7),
(21, 1, 74, 1),
(21, 1, 74, 2),
(21, 1, 74, 3),
(21, 1, 74, 4),
(21, 1, 74, 5),
(21, 1, 74, 6),
(21, 1, 74, 7),
(21, 1, 75, 1),
(21, 1, 75, 2),
(21, 1, 75, 3),
(21, 1, 75, 4),
(21, 1, 75, 5),
(21, 1, 75, 6),
(21, 1, 75, 7),
(21, 1, 76, 1),
(21, 1, 76, 2),
(21, 1, 76, 3),
(21, 1, 76, 4),
(21, 1, 76, 5),
(21, 1, 76, 6),
(21, 1, 76, 7),
(22, 14, 45, 3),
(22, 14, 46, 1),
(22, 14, 46, 3),
(22, 14, 47, 3),
(22, 14, 48, 3),
(22, 14, 49, 1),
(22, 14, 49, 2),
(22, 14, 49, 3),
(22, 14, 49, 4),
(22, 14, 49, 5),
(22, 14, 49, 6),
(22, 14, 50, 1),
(22, 14, 50, 2),
(22, 14, 50, 3),
(22, 14, 50, 7),
(22, 14, 51, 1),
(22, 14, 51, 2),
(22, 14, 51, 3),
(22, 14, 51, 4),
(22, 14, 51, 5),
(22, 14, 51, 6),
(22, 14, 51, 7),
(22, 14, 52, 3),
(22, 14, 52, 5),
(22, 14, 52, 6),
(22, 14, 53, 1),
(22, 14, 53, 2),
(22, 14, 53, 3),
(22, 14, 53, 5),
(22, 14, 53, 6),
(22, 14, 54, 1),
(22, 14, 54, 2),
(22, 14, 54, 3),
(22, 14, 54, 4),
(22, 14, 54, 5),
(22, 14, 54, 6),
(22, 14, 54, 7),
(22, 14, 55, 1),
(22, 14, 55, 2),
(22, 14, 55, 3),
(22, 14, 55, 4),
(22, 14, 55, 5),
(22, 14, 55, 6),
(22, 14, 55, 7),
(22, 14, 56, 1),
(22, 14, 56, 2),
(22, 14, 56, 3),
(22, 14, 56, 4),
(22, 14, 56, 5),
(22, 14, 56, 6),
(22, 14, 56, 7),
(22, 14, 57, 1),
(22, 14, 57, 2),
(22, 14, 57, 3),
(22, 14, 57, 4),
(22, 14, 57, 5),
(22, 14, 57, 6),
(22, 14, 57, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_modulos`
--

CREATE TABLE `role_modulos` (
  `id_usuario` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_modulo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `role_modulos`
--

INSERT INTO `role_modulos` (`id_usuario`, `id_rol`, `id_modulo`) VALUES
(21, 1, 39),
(21, 1, 40),
(21, 1, 41),
(21, 1, 42),
(21, 1, 43),
(21, 1, 44),
(21, 1, 45),
(21, 1, 46),
(21, 1, 47),
(21, 1, 48),
(21, 1, 49),
(21, 1, 50),
(21, 1, 51),
(21, 1, 52),
(21, 1, 53),
(21, 1, 54),
(21, 1, 55),
(21, 1, 56),
(21, 1, 57),
(21, 1, 60),
(21, 1, 61),
(21, 1, 62),
(21, 1, 63),
(21, 1, 64),
(21, 1, 65),
(21, 1, 66),
(21, 1, 67),
(21, 1, 68),
(21, 1, 69),
(21, 1, 70),
(21, 1, 71),
(21, 1, 72),
(21, 1, 73),
(21, 1, 74),
(21, 1, 75),
(21, 1, 76),
(22, 14, 45),
(22, 14, 46),
(22, 14, 47),
(22, 14, 48),
(22, 14, 49),
(22, 14, 50),
(22, 14, 51),
(22, 14, 52),
(22, 14, 53),
(22, 14, 54),
(22, 14, 55),
(22, 14, 56),
(22, 14, 57);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serie_num_comprobante`
--

CREATE TABLE `serie_num_comprobante` (
  `id_serie_num` int(11) NOT NULL,
  `tipo_comprobante_sn` varchar(50) NOT NULL,
  `serie_prefijo` varchar(20) NOT NULL,
  `folio_inicial` int(11) NOT NULL,
  `folio_final` int(11) NOT NULL,
  `fecha_sn` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `serie_num_comprobante`
--

INSERT INTO `serie_num_comprobante` (`id_serie_num`, `tipo_comprobante_sn`, `serie_prefijo`, `folio_inicial`, `folio_final`, `fecha_sn`) VALUES
(16, 'ticket', 'T001', 1, 999, '2025-01-13 15:10:06'),
(17, 'boleta', 'B001', 1, 999, '2025-01-13 15:10:46'),
(18, 'factura', 'F001', 1, 999, '2025-01-13 15:11:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id_sucursal` int(11) NOT NULL,
  `nombre_sucursal` varchar(100) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id_sucursal`, `nombre_sucursal`, `direccion`, `telefono`, `estado`) VALUES
(4, 'Sucursal Centro', 'Av. Principal 123, Ciudad Central', '123456789', 1),
(5, 'Sucursal Norte', 'Calle Norte 45, Distrito Norte', '987654321', 1),
(6, 'Sucursal Sur', 'Av. Sur 987, Ciudad Sur', '456789123', 1),
(7, 'Sucursal Este', 'Av. Este 567, Barrio Este', '321654987', 1),
(8, 'Sucursal Oeste', 'Calle Oeste 234, Zona Oeste', '789123456', 0),
(9, 'Lim123', 'Lima 001', '978990345', 1),
(10, 'Lim123', 'Lima 001', '978990345', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documentos`
--

CREATE TABLE `tipo_documentos` (
  `id_doc` int(11) NOT NULL,
  `nombre_doc` varchar(50) NOT NULL,
  `fecha_doc` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tipo_documentos`
--

INSERT INTO `tipo_documentos` (`id_doc`, `nombre_doc`, `fecha_doc`) VALUES
(2, 'RUC', '2024-11-22 11:30:13'),
(3, 'CEDULA', '2024-11-22 11:30:19'),
(4, 'DNI', '2025-01-13 15:15:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id_trabajador` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `num_documento` varchar(20) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `cv` varchar(100) DEFAULT NULL,
  `tipo_pago` varchar(50) DEFAULT NULL,
  `num_cuenta` varchar(30) DEFAULT NULL,
  `estado_trabajador` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(100) NOT NULL,
  `imagen_usuario` varchar(50) DEFAULT NULL,
  `estado_usuario` int(11) DEFAULT 1,
  `fecha_usuario` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_sucursal`, `nombre_usuario`, `telefono`, `correo`, `usuario`, `contrasena`, `imagen_usuario`, `estado_usuario`, `fecha_usuario`) VALUES
(21, 9, 'Jorge Chavez Huincho', '920468502', 'djjmygm160399@gmail.com', 'Apuuray12345', '$2a$07$asxx54ahjppf45sd87a5au.KWXKi/QEnipU29qpDnSWgzsF5pKqrK', '../vistas/img/usuarios/202501171902238319.jpg', 1, '2025-01-17 12:52:34'),
(22, 7, 'Lucas Sarmiento', '6767564523', 'lucas@gmail.com', 'Lucas12345', '$2a$07$asxx54ahjppf45sd87a5auB2geDJi5/CFHtmw8SGgR.6WhypJYVAu', NULL, 1, '2025-01-17 13:05:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_modulos_acciones`
--

CREATE TABLE `usuario_modulos_acciones` (
  `id_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_accion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

CREATE TABLE `usuario_roles` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_roles`
--

INSERT INTO `usuario_roles` (`id_usuario`, `id_rol`) VALUES
(21, 1),
(22, 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones`
--

CREATE TABLE `vacaciones` (
  `id_vacacion` int(11) NOT NULL,
  `id_trabajador` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado_vacion` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_movimiento_caja` int(11) NOT NULL,
  `fecha_venta` date NOT NULL,
  `hora_venta` text NOT NULL,
  `id_serie_num` int(11) NOT NULL,
  `serie_comprobante` varchar(10) NOT NULL,
  `num_comprobante` varchar(10) NOT NULL,
  `impuesto` decimal(10,2) DEFAULT NULL,
  `total_venta` decimal(10,2) NOT NULL,
  `total_pago` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) DEFAULT NULL,
  `tipo_pago` varchar(20) DEFAULT NULL,
  `forma_pago` varchar(20) DEFAULT NULL,
  `numero_serie_pago` varchar(50) DEFAULT NULL,
  `pago_delante` decimal(10,2) DEFAULT NULL,
  `estado_pago` varchar(20) DEFAULT NULL,
  `fecha_venta_a` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_persona`, `id_usuario`, `id_movimiento_caja`, `fecha_venta`, `hora_venta`, `id_serie_num`, `serie_comprobante`, `num_comprobante`, `impuesto`, `total_venta`, `total_pago`, `sub_total`, `igv`, `tipo_pago`, `forma_pago`, `numero_serie_pago`, `pago_delante`, `estado_pago`, `fecha_venta_a`) VALUES
(274, 52, 21, 43, '2025-02-06', '12:51:57 PM', 17, 'B001', '1', 0.00, 288.00, 288.00, 288.00, 0.00, 'contado', 'pago_efectivo', '', 288.00, 'completado', '2025-02-06 12:51:58'),
(275, 52, 21, 43, '2025-02-06', '12:53:33 PM', 18, 'F001', '1', 0.00, 468.00, 468.00, 468.00, 0.00, 'contado', 'pago_efectivo', '', 468.00, 'completado', '2025-02-06 12:53:33'),
(276, 51, 21, 43, '2025-02-06', '12:54:22 PM', 17, 'B001', '2', 0.00, 180.00, 180.00, 180.00, 0.00, 'contado', 'pago_efectivo', '', 180.00, 'completado', '2025-02-06 12:54:22'),
(277, 51, 21, 43, '2025-02-06', '12:55:00 PM', 16, 'T001', '1', 0.00, 552.00, 552.00, 552.00, 0.00, 'contado', 'pago_efectivo', '', 552.00, 'completado', '2025-02-06 12:55:00'),
(278, 51, 21, 43, '2025-02-06', '12:56:00 PM', 18, 'F001', '2', 0.00, 1464.00, 1464.00, 1464.00, 0.00, 'contado', 'pago_efectivo', '', 1468.00, 'completado', '2025-02-06 12:56:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
  ADD PRIMARY KEY (`id_accion`);

--
-- Indices de la tabla `arqueos_caja`
--
ALTER TABLE `arqueos_caja`
  ADD PRIMARY KEY (`id_arqueo`),
  ADD KEY `id_movimiento_caja` (`id_movimiento_caja`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `asistencia_trabajadores`
--
ALTER TABLE `asistencia_trabajadores`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_trabajador` (`id_trabajador`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `config_correo`
--
ALTER TABLE `config_correo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `config_sistema`
--
ALTER TABLE `config_sistema`
  ADD PRIMARY KEY (`id_img`);

--
-- Indices de la tabla `config_ticket`
--
ALTER TABLE `config_ticket`
  ADD PRIMARY KEY (`id_config_ticket`);

--
-- Indices de la tabla `contratos_trabajadores`
--
ALTER TABLE `contratos_trabajadores`
  ADD PRIMARY KEY (`id_contrato`),
  ADD KEY `id_trabajador` (`id_trabajador`);

--
-- Indices de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD PRIMARY KEY (`id_cotizacion`),
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_serie_num` (`id_serie_num`);

--
-- Indices de la tabla `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  ADD PRIMARY KEY (`id_detalle_contizacion`),
  ADD KEY `id_cotizacion` (`id_cotizacion`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_egreso`
--
ALTER TABLE `detalle_egreso`
  ADD PRIMARY KEY (`id_detalle_egreso`),
  ADD KEY `id_egreso` (`id_egreso`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id_detalle_venta`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `egresos`
--
ALTER TABLE `egresos`
  ADD PRIMARY KEY (`id_egreso`),
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `fk_movimiento` (`id_movimiento_caja`);

--
-- Indices de la tabla `gasto_ingreso`
--
ALTER TABLE `gasto_ingreso`
  ADD PRIMARY KEY (`id_gasto`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_movimiento_caja` (`id_movimiento_caja`);

--
-- Indices de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `impresora`
--
ALTER TABLE `impresora`
  ADD PRIMARY KEY (`id_impresora`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pagos_trabajadores`
--
ALTER TABLE `pagos_trabajadores`
  ADD PRIMARY KEY (`id_pagos`),
  ADD KEY `id_contrato` (`id_contrato`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_doc` (`id_doc`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `role_acciones`
--
ALTER TABLE `role_acciones`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_accion` (`id_accion`);

--
-- Indices de la tabla `role_modulos`
--
ALTER TABLE `role_modulos`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `serie_num_comprobante`
--
ALTER TABLE `serie_num_comprobante`
  ADD PRIMARY KEY (`id_serie_num`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id_sucursal`);

--
-- Indices de la tabla `tipo_documentos`
--
ALTER TABLE `tipo_documentos`
  ADD PRIMARY KEY (`id_doc`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id_trabajador`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `usuario_modulos_acciones`
--
ALTER TABLE `usuario_modulos_acciones`
  ADD PRIMARY KEY (`id_usuario`,`id_modulo`,`id_accion`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_accion` (`id_accion`);

--
-- Indices de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `vacaciones`
--
ALTER TABLE `vacaciones`
  ADD PRIMARY KEY (`id_vacacion`),
  ADD KEY `id_trabajador` (`id_trabajador`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_persona` (`id_persona`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_serie_num` (`id_serie_num`),
  ADD KEY `fk_movimiento_caja` (`id_movimiento_caja`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `arqueos_caja`
--
ALTER TABLE `arqueos_caja`
  MODIFY `id_arqueo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `asistencia_trabajadores`
--
ALTER TABLE `asistencia_trabajadores`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `config_correo`
--
ALTER TABLE `config_correo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `config_sistema`
--
ALTER TABLE `config_sistema`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `config_ticket`
--
ALTER TABLE `config_ticket`
  MODIFY `id_config_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `contratos_trabajadores`
--
ALTER TABLE `contratos_trabajadores`
  MODIFY `id_contrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  MODIFY `id_cotizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT de la tabla `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  MODIFY `id_detalle_contizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT de la tabla `detalle_egreso`
--
ALTER TABLE `detalle_egreso`
  MODIFY `id_detalle_egreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id_detalle_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=272;

--
-- AUTO_INCREMENT de la tabla `egresos`
--
ALTER TABLE `egresos`
  MODIFY `id_egreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `gasto_ingreso`
--
ALTER TABLE `gasto_ingreso`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT de la tabla `impresora`
--
ALTER TABLE `impresora`
  MODIFY `id_impresora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT de la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `pagos_trabajadores`
--
ALTER TABLE `pagos_trabajadores`
  MODIFY `id_pagos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `serie_num_comprobante`
--
ALTER TABLE `serie_num_comprobante`
  MODIFY `id_serie_num` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id_sucursal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_documentos`
--
ALTER TABLE `tipo_documentos`
  MODIFY `id_doc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id_trabajador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `vacaciones`
--
ALTER TABLE `vacaciones`
  MODIFY `id_vacacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arqueos_caja`
--
ALTER TABLE `arqueos_caja`
  ADD CONSTRAINT `arqueos_caja_ibfk_1` FOREIGN KEY (`id_movimiento_caja`) REFERENCES `movimientos_caja` (`id_movimiento`),
  ADD CONSTRAINT `arqueos_caja_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `asistencia_trabajadores`
--
ALTER TABLE `asistencia_trabajadores`
  ADD CONSTRAINT `asistencia_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `config_correo`
--
ALTER TABLE `config_correo`
  ADD CONSTRAINT `config_correo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `contratos_trabajadores`
--
ALTER TABLE `contratos_trabajadores`
  ADD CONSTRAINT `contratos_trabajadores_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotizaciones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotizaciones_ibfk_3` FOREIGN KEY (`id_serie_num`) REFERENCES `serie_num_comprobante` (`id_serie_num`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_cotizacion`
--
ALTER TABLE `detalle_cotizacion`
  ADD CONSTRAINT `detalle_cotizacion_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_cotizacion_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_egreso`
--
ALTER TABLE `detalle_egreso`
  ADD CONSTRAINT `detalle_egreso_ibfk_1` FOREIGN KEY (`id_egreso`) REFERENCES `egresos` (`id_egreso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_egreso_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `egresos`
--
ALTER TABLE `egresos`
  ADD CONSTRAINT `egresos_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `egresos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento` FOREIGN KEY (`id_movimiento_caja`) REFERENCES `movimientos_caja` (`id_movimiento`);

--
-- Filtros para la tabla `gasto_ingreso`
--
ALTER TABLE `gasto_ingreso`
  ADD CONSTRAINT `gasto_ingreso_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gasto_ingreso_ibfk_2` FOREIGN KEY (`id_movimiento_caja`) REFERENCES `movimientos_caja` (`id_movimiento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD CONSTRAINT `historial_pagos_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_caja`
--
ALTER TABLE `movimientos_caja`
  ADD CONSTRAINT `movimientos_caja_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos_trabajadores`
--
ALTER TABLE `pagos_trabajadores`
  ADD CONSTRAINT `pagos_trabajadores_ibfk_1` FOREIGN KEY (`id_contrato`) REFERENCES `contratos_trabajadores` (`id_contrato`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `personas`
--
ALTER TABLE `personas`
  ADD CONSTRAINT `personas_ibfk_1` FOREIGN KEY (`id_doc`) REFERENCES `tipo_documentos` (`id_doc`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `role_acciones`
--
ALTER TABLE `role_acciones`
  ADD CONSTRAINT `role_acciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_acciones_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_acciones_ibfk_3` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_acciones_ibfk_4` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `role_modulos`
--
ALTER TABLE `role_modulos`
  ADD CONSTRAINT `role_modulos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_modulos_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_modulos_ibfk_3` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_modulos_acciones`
--
ALTER TABLE `usuario_modulos_acciones`
  ADD CONSTRAINT `usuario_modulos_acciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `usuario_modulos_acciones_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`),
  ADD CONSTRAINT `usuario_modulos_acciones_ibfk_3` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`);

--
-- Filtros para la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD CONSTRAINT `usuario_roles_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `vacaciones`
--
ALTER TABLE `vacaciones`
  ADD CONSTRAINT `vacaciones_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id_trabajador`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_movimiento_caja` FOREIGN KEY (`id_movimiento_caja`) REFERENCES `movimientos_caja` (`id_movimiento`),
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `personas` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_serie_num`) REFERENCES `serie_num_comprobante` (`id_serie_num`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
