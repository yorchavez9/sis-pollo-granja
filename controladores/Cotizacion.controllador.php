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
	MOSTRAR DETALLE VENTA
	=============================================*/

    static public function ctrMostrarDetalleCotizacionVenta($item, $valor)
    {
        $tablaDC = "detalle_cotizacion";
        $respuesta = ModeloCotizacion::mdlMostrarListaDetalleCotizacionVenta($tablaDC, $item, $valor);
        return $respuesta;
    }
  
    /*=============================================
	MOSTRAR DETALLE VENTA
	=============================================*/

    static public function ctrMostrarDetalleCotizacion($item, $valor)
    {
        $tablaDC = "detalle_cotizacion";
        $tablaP = "productos";
        $respuesta = ModeloCotizacion::mdlMostrarListaDetalleCotizacion($tablaDC, $tablaP, $item, $valor);
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
	BORRAR VENTA
	=============================================*/
    static public function ctrBorrarCotizacion()
    {
        $tablaV = "cotizaciones";
        $datos = $_POST["idCotizacionDelete"];
        $respuesta = ModeloCotizacion::mdlBorrarCotizacion($tablaV, $datos);
        $tablaD = "detalle_cotizacion";
        $respuestaDetalle = ModeloCotizacion::mdlBorrarDetalleCotizacion($tablaD, $datos);
        echo json_encode($respuestaDetalle);
    
    }
}
