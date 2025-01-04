<?php


class ControladorCotizacion
{


    /*=============================================
	MOSTRAR LISTA COTIZACIONES
	=============================================*/

    static public function ctrMostrarListaCotizaciones($item, $valor)
    {
        $tabla_personas = "personas";
        $tabla_cotizacion = "cotizaciones";
        $tabla_usuarios = "usuarios";
        $tabla_s_n = "serie_num_comprobante";
        $respuesta = ModeloCotizacion::mdlMostrarListaCotizacion($tabla_personas, $tabla_cotizacion, $tabla_usuarios, $tabla_s_n, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR REPORTE DE COTIZACIONES
    =============================================*/
    public static function ctrReporteCotizaciones()
    {
        $tabla_personas = "personas";
        $tabla_cotizacion = "ventas";
        $tabla_usuarios = "usuarios";
        $tabla_s_n = "serie_num_comprobante";

        // Capturamos los filtros
        $filtros = [
            "filtro_usuario_venta" => isset($_POST['filtro_usuario_venta']) ? $_POST['filtro_usuario_venta'] : null,
            "filtro_fecha_desde_venta" => isset($_POST['filtro_fecha_desde_venta']) ? $_POST['filtro_fecha_desde_venta'] : null,
            "filtro_fecha_hasta_venta" => isset($_POST['filtro_fecha_hasta_venta']) ? $_POST['filtro_fecha_hasta_venta'] : null,
            "filtro_tipo_comprobante_venta" => isset($_POST['filtro_tipo_comprobante_venta']) ? $_POST['filtro_tipo_comprobante_venta'] : null,
            "filtro_estado_pago_venta" => isset($_POST['filtro_estado_pago_venta']) ? $_POST['filtro_estado_pago_venta'] : null,
            "filtro_total_venta_min" => isset($_POST['filtro_total_venta_min']) ? $_POST['filtro_total_venta_min'] : null,
            "filtro_total_venta_max" => isset($_POST['filtro_total_venta_max']) ? $_POST['filtro_total_venta_max'] : null
        ];

        // Pasamos los filtros al modelo
        $respuesta = ModeloCotizacion::mdlReporteVentas($tabla_personas, $tabla_cotizacion, $tabla_usuarios, $tabla_s_n, $filtros);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR REPORTE DE COTIZACIONES PDF
    =============================================*/
    public static function ctrReporteCotizacionesPDF()
    {
        $tabla_personas = "personas";
        $tabla_cotizacion = "ventas";
        $tabla_usuarios = "usuarios";
        $tabla_s_n = "serie_num_comprobante";

        // Capturamos los filtros
        $filtros = [
            "filtro_usuario_venta" => isset($_GET['filtro_usuario_venta']) ? $_GET['filtro_usuario_venta'] : null,
            "filtro_fecha_desde_venta" => isset($_GET['filtro_fecha_desde_venta']) ? $_GET['filtro_fecha_desde_venta'] : null,
            "filtro_fecha_hasta_venta" => isset($_GET['filtro_fecha_hasta_venta']) ? $_GET['filtro_fecha_hasta_venta'] : null,
            "filtro_tipo_comprobante_venta" => isset($_GET['filtro_tipo_comprobante_venta']) ? $_GET['filtro_tipo_comprobante_venta'] : null,
            "filtro_estado_pago_venta" => isset($_GET['filtro_estado_pago_venta']) ? $_GET['filtro_estado_pago_venta'] : null,
            "filtro_total_venta_min" => isset($_GET['filtro_total_venta_min']) ? $_GET['filtro_total_venta_min'] : null,
            "filtro_total_venta_max" => isset($_GET['filtro_total_venta_max']) ? $_GET['filtro_total_venta_max'] : null
        ];

        // Pasamos los filtros al modelo
        $respuesta = ModeloCotizacion::mdlReporteVentas($tabla_personas, $tabla_cotizacion, $tabla_usuarios, $tabla_s_n, $filtros);

        return $respuesta;
    }

    /*=============================================
	MOSTRAR SUMA TOTAL DE COTIZACION
	=============================================*/
    static public function ctrMostrarSumaTotalCotizacion($item, $valor)
    {
        $tablaD = "detalle_venta";
        $tablaV = "ventas";
        $tablaP = "personas";
        $respuesta = ModeloCotizacion::mdlMostrarSumaTotalVenta($tablaD, $tablaV, $tablaP, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	MOSTRAR SUMA TOTAL DE COTIZACION
	=============================================*/
    static public function ctrMostrarSumaTotalVentaContado($item, $valor)
    {
        $tablaD = "detalle_venta";
        $tablaV = "ventas";
        $tablaP = "personas";
        $respuesta = ModeloCotizacion::mdlMostrarSumaTotalVentaContado($tablaD, $tablaV, $tablaP, $item, $valor);
        return $respuesta;
    }


    /*=============================================
	MOSTRAR SUMA TOTAL DE VENTA
	=============================================*/

    static public function ctrMostrarSumaTotalVentaCredito($item, $valor)
    {
        $tablaD = "detalle_venta";
        $tablaV = "ventas";
        $tablaP = "personas";
        $respuesta = ModeloCotizacion::mdlMostrarSumaTotalCredito($tablaD, $tablaV, $tablaP, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	MOSTRAR REPORTE VENTAS
	=============================================*/

    static public function ctrMostrarReporteVentas($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
    {
        $tablaVentas = "ventas";
        $tablaDetalleV = "detalle_venta";
        $tablaProducto = "productos";
        $tablaUsuario = "usuarios";
        $tablaPersona = "personas";
        $respuesta = ModeloCotizacion::mdlMostrarReporteVenta($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto);
        return $respuesta;
    }

    /*=============================================
	MOSTRAR REPORTE VENTAS RANGO DE FECHAS
	=============================================*/

    static public function ctrMostrarReporteVentasRangoFechas($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
    {
        $tablaVentas = "ventas";
        $tablaDetalleV = "detalle_venta";
        $tablaProducto = "productos";
        $tablaUsuario = "usuarios";
        $tablaPersona = "personas";
        $respuesta = ModeloCotizacion::mdlMostrarReporteVentaRangoFechas($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto);
        return $respuesta;
    }

    /*=============================================
	MOSTRAR REPORTE CREDITOS POR CLIENTE
	=============================================*/

    static public function ctrMostrarReporteVentasCreditoCliente($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto, $id_cliente_reporte)
    {
        $tablaVentas = "ventas";
        $tablaDetalleV = "detalle_venta";
        $tablaProducto = "productos";
        $tablaUsuario = "usuarios";
        $tablaPersona = "personas";
        $respuesta = ModeloCotizacion::mdlMostrarReporteVentaCreditosCliente($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto, $id_cliente_reporte);
        return $respuesta;
    }

    /*=============================================
	MOSTRAR REPORTE PRECIOS MODIFICADO EN LA VENTA
	=============================================*/

    static public function ctrMostrarReporteVentasPrecioProducto($fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto)
    {

        $tablaVentas = "ventas";
        $tablaDetalleV = "detalle_venta";
        $tablaProducto = "productos";
        $tablaUsuario = "usuarios";
        $tablaPersona = "personas";

        $respuesta = ModeloCotizacion::mdlMostrarReporteVentaPrecioProducto($tablaVentas, $tablaDetalleV, $tablaProducto, $tablaUsuario, $tablaPersona, $fecha_desde, $fecha_hasta, $id_usuario, $tipo_pago, $descuento_producto);

        return $respuesta;
    }


    /*=============================================
	MOSTRAR DETALLE VENTA
	=============================================*/

    static public function ctrMostrarDetalleVenta($item, $valor)
    {

        $tablaDV = "detalle_venta";
        $tablaP = "productos";

        $respuesta = ModeloCotizacion::mdlMostrarListaDetalleVenta($tablaDV, $tablaP, $item, $valor);

        return $respuesta;
    }

    /*=============================================
	REGISTRO DE COTIZACION
	=============================================*/
    static public function ctrCrearCotizacion()
    {
        $tabla = "cotizaciones";
        $pago_total = 0;
        if ($_POST["tipo_pago"] == "contado") {
            $pago_total = $_POST["total"];
        } else {
            $pago_total = 0;
        }
        $datos = array(
            "id_persona" => $_POST["id_cliente_venta"],
            "id_usuario" => $_POST["id_usuario_cotizacion"],
            "fecha_cotizacion" => $_POST["fecha_venta"],
            "hora_cotizacion" => $_POST["hora_venta"],
            "id_serie_num" => $_POST["comprobante_venta"],
            "validez" => $_POST["validez_contizacion"],
            "impuesto" => $_POST["igv_venta"],
            "total_cotizacion" => $_POST["total"],
            "total_pago" => $pago_total,
            "sub_total" => $_POST["subtotal"],
            "igv_total" => $_POST["igv"],
            "tipo_pago" => $_POST["tipo_pago"],
            "estado_pago" => $_POST["estado_pago"],
            "forma_pago" => $_POST["metodos_pago_venta"]
        );

        ModeloCotizacion::mdlIngresarCotizacion($tabla, $datos);
    
        /* ==========================================
		MOSTRANDO EL ULTIMO ID REGISTRADO
		========================================== */
        $tablac = "cotizaciones";
        $tablasnc = "serie_num_comprobante";
        $item = null;
        $valor = null;
        $respuestaDetalleCotizacion = ModeloCotizacion::mdlMostrarIdCotizacion($tablac, $tablasnc, $item, $valor);
        $id_cotizacion_ultimo = null;
        $tipo_comprobante_sn = null;
        foreach ($respuestaDetalleCotizacion as $value) {
            $id_cotizacion_ultimo = $value["id_cotizacion"];
            $tipo_comprobante_sn = $value["tipo_comprobante_sn"];
        }

        /* ==========================================
		INGRESO DE DATOS AL DETALLE COTIZACION
		========================================== */
        $tblDetalleVenta = "detalle_cotizacion";
        $productos = json_decode($_POST["productoAddVenta"], true);
        $datos = array();
        foreach ($productos as $dato) {
            $nuevo_dato = array(
                'id_cotizacion' => $id_cotizacion_ultimo,
                'id_producto' => $dato['id_producto_venta'],
                'numero_javas' => $dato['numero_javas'],
                'numero_aves' => $dato['numero_aves'],
                'peso_promedio' => $dato['peso_promedio'],
                'peso_bruto' => $dato['peso_bruto'],
                'peso_tara' => $dato['peso_tara'],
                'peso_merma' => $dato['peso_merma'],
                'peso_neto' => $dato['peso_neto'],
                'precio_venta' => $dato['precio_venta']
            );
            $datos[] = $nuevo_dato;
            $respuestaDatos = ModeloCotizacion::mdlIngresarDetalleCotizacion($tblDetalleVenta, $nuevo_dato);
        }

        if($respuestaDatos["status"] == true){
            echo json_encode([
                "status" => $respuestaDatos["status"],
                "message" => $respuestaDatos["message"],
                "id_cotizacion" => $id_cotizacion_ultimo,
                "tipo_comprobante" => $tipo_comprobante_sn
            ]);
        }else{
            echo json_encode([
                "status" => $respuestaDatos["status"],
                "message" => $respuestaDatos["message"]
            ]);
        }
    }

    /*=============================================
	EDITAR VENTA
	=============================================*/

    static public function ctrEditarVenta()
    {



        $tabla = "ventas";

        $pago_total = 0;

        if ($_POST["tipo_pago"] == "contado") {

            $pago_total = $_POST["total"];
        } else {
            $pago_total = 0;
        }




        $datos = array(
            "id_venta" => $_POST["edit_id_venta"],
            "id_persona" => $_POST["id_cliente_venta"],
            "id_usuario" => $_POST["id_usuario_venta"],
            "fecha_venta" => $_POST["fecha_venta"],
            "tipo_comprobante" => $_POST["comprobante_venta"],
            "serie_comprobante" => $_POST["serie_venta"],
            "num_comprobante" => $_POST["numero_venta"],
            "impuesto" => $_POST["igv_venta"],
            "total_venta" => $_POST["total"],
            "total_pago" => $pago_total,
            "sub_total" => $_POST["subtotal"],
            "igv" => $_POST["igv"],
            "tipo_pago" => $_POST["tipo_pago"],
            "estado_pago" => $_POST["estado_pago"],
            "pago_e_y" => $_POST["pago_e_y"]
        );



        $respuesta = ModeloCotizacion::mdlEditarVenta($tabla, $datos);


        /* ==========================================
		ACTUALIZANDO LOS DATOS DEL DETALLE PRODUCTO
		========================================== */

        $tblDetalleVenta = "detalle_venta";

        $productos = json_decode($_POST["productoAddVenta"], true);



        $datos = array();

        foreach ($productos as $dato) {
            $nuevo_dato = array(
                "id_venta" => $_POST["edit_id_venta"],
                'id_producto' => $dato['id_producto'],
                'precio_venta' => $dato['precio_venta'],
                'cantidad_u' => $dato['cantidad_u'],
                'cantidad_kg' => $dato['cantidad_kg']
            );

            $datos[] = $nuevo_dato;

            $respuestaDatos = ModeloCotizacion::mdlEditarDetalleVenta($tblDetalleVenta, $nuevo_dato);
        }

        /* ==========================================
		ACTUALIZANDO EL STOCK DEL PRODUCTO
		========================================== */

        $tblProducto = "productos";

        $stocks = json_decode($_POST["productoAddVenta"], true);

        foreach ($stocks as $value) {

            $idProducto = $value['id_producto'];
            $cantidad = $value['cantidad_u'];

            // Actualizar el stock del producto
            $respStock = ModeloCotizacion::mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad);
        }





        if ($respuestaDatos == "ok") {

            $response = array(
                "mensaje" => "La venta se actualizó con éxito",
                "estado" => "ok"
            );

            echo json_encode($response);
        } else {

            $response = array(
                "mensaje" => "Error al actualizar la venta",
                "estado" => "error"
            );

            echo json_encode($response);
        }
    }

    /*=============================================
	BORRAR VENTA
	=============================================*/

    static public function ctrBorrarVenta()
    {

        if (isset($_POST["ventaIdDelete"])) {

            $tablaV = "ventas";

            $datos = $_POST["ventaIdDelete"];

            $respuesta = ModeloCotizacion::mdlBorrarVenta($tablaV, $datos);


            $tablaD = "detalle_venta";

            $respuestaDetalle = ModeloCotizacion::mdlBorrarDetalleVenta($tablaD, $datos);

            echo json_encode($respuestaDetalle);
        }
    }
}
