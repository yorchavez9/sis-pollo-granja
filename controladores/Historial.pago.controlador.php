<?php

class ControladorHistorialPago
{

    /*=============================================
	MOSTRAR HISTORIAL PAGO PDF O TICKET
	=============================================*/
    static public function ctrMostrarHistorialPagoPdf($item, $valor)
    {
        $tabla_ventas = "ventas";
        $tabla_historial_pago = "historial_pagos";
        $tabla_personas = "personas";
        $respuesta = ModeloHistorialPago::mdlMostrarHistorialPagoPDF($tabla_ventas, $tabla_historial_pago, $tabla_personas, $item, $valor);
        return $respuesta;
    }


    /*=============================================
	MOSTRAR HISTORIAL PAGO
	=============================================*/
    static public function ctrMostrarHistorialPago($item, $valor)
    {
        $tabla_ventas = "ventas";
        $tabla_historial_pago = "historial_pagos";
        $tabla_personas = "personas";
        $respuesta = ModeloHistorialPago::mdlMostrarHistorialPago($tabla_ventas, $tabla_historial_pago, $tabla_personas, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	ACTUALIZAR EL PAGO DE DEUDA EGRESO
	=============================================*/
    static public function ctrActualizarDeudaVenta()
    {
        /* =========================================
        ACTUALIZANDO EL MONTO DE VENTA DEL PAGO AL CREDITO
        ========================================= */
        $tabla = "ventas";
        $totalPago = number_format($_POST["monto_pagar_venta"], 2, '.', '');
        $datos = array(
            "id_venta" => $_POST["id_venta_pagar"],
            "total_pago" => $totalPago
        );

        ModeloHistorialPago::mdlActualizarPagoPendiente($tabla, $datos);

        /* =========================================
        INGRESANDO DATOS AL HISTORIAL DE PAGO
        ========================================= */

        $ruta = "../vistas/img/comprobantes/";
        if (isset($_FILES["comprobante_pago_historial"]["tmp_name"])) {
            $extension = pathinfo($_FILES["comprobante_pago_historial"]["name"], PATHINFO_EXTENSION);
            $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
            if (in_array(strtolower($extension), $tipos_permitidos)) {
                $nombre_imagen = date("YmdHis") . rand(1000, 9999);
                $ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
                if (move_uploaded_file($_FILES["comprobante_pago_historial"]["tmp_name"], $ruta_imagen)) {
                } else {
                }
            } else {
            }
        }

        /* ==========================================
		HISTORIAL DE PAGO
		========================================== */
        $tablaHistorialPago = "historial_pagos";
        $datosHistorialPago = array(
            "id_venta" => $_POST["id_venta_pagar"],
            "monto_pago" => $_POST["monto_pagar_venta"],
            "forma_pago" => $_POST["metodos_pago_venta_historial"],
            "numero_serie_pago" => !empty($_POST["serie_numero_pago_historial"]) ? $_POST["serie_numero_pago_historial"] : null,
            "comprobante_imagen" => isset($_FILES["comprobante_pago_historial"]["tmp_name"]) ? $ruta_imagen : null
        );

        $responseHistoial = ModeloHistorialPago::mdlIngresoHistorialPago($tablaHistorialPago, $datosHistorialPago);
        echo json_encode($responseHistoial);
    }


    /*=============================================
	EDITAR HISTORIAL PAGO
	=============================================*/
    static public function ctrEditarHistorialPago()
    {
        if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["edit_nombre_producto"])) {
            /* ============================
            VALIDANDO IMAGEN
            ============================ */
            $ruta = "../vistas/img/productos/";
            $ruta_imagen = $_POST["edit_imagen_actual_p"];
            if (isset($_FILES["edit_imagen_producto"]["tmp_name"]) && !empty($_FILES["edit_imagen_producto"]["tmp_name"])) {
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen);
                }
                $extension = pathinfo($_FILES["edit_imagen_producto"]["name"], PATHINFO_EXTENSION);
                $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
                if (in_array(strtolower($extension), $tipos_permitidos)) {
                    $nombre_imagen = date("YmdHis") . rand(1000, 9999);
                    $ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
                    if (move_uploaded_file($_FILES["edit_imagen_producto"]["tmp_name"], $ruta_imagen)) {
                    } else {
                    }
                } else {
                }
            }

            $tabla = "productos";
            $datos = array(
                "id_producto" => $_POST["edit_id_producto"],
                "id_categoria" => $_POST["edit_id_categoria_p"],
                "codigo_producto" => $_POST["edit_codigo_producto"],
                "nombre_producto" => $_POST["edit_nombre_producto"],
                "precio_producto" => $_POST["edit_precio_producto"],
                "stock_producto" => $_POST["edit_stock_producto"],
                "fecha_vencimiento" => $_POST["edit_fecha_vencimiento"],
                "descripcion_producto" => $_POST["edit_descripcion_producto"],
                "imagen_producto" => $ruta_imagen
            );
            $respuesta = ModeloHistorialPago::mdlEditarHistorialPago($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
        } else {
            echo json_encode("error");
        }
    }

    /*=============================================
	BORRAR PRODUCTO
	=============================================*/

    static public function ctrBorrarHistorialPago()
    {
        $tabla = "historial_pagos";
        $datos = $_POST["id_delete_pago_historial"];
        if ($_POST["url_imagen_historial_pago"] != "") {
            // Verificar si el archivo existe y eliminarlo
            if (file_exists($_POST["url_imagen_historial_pago"])) {
                unlink($_POST["url_imagen_historial_pago"]);
            } else {
                // El archivo no existe
                echo "El archivo a eliminar no existe.";
            }
        }
        $respuesta = ModeloHistorialPago::mdlBorrarHistorialPago($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }
}
