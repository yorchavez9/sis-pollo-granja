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
        $totalPago = number_format($_POST["monto_pagar_venta"], 2,'.','');
        $datos = array(
            "id_venta" => $_POST["id_venta_pagar"],
            "total_pago" => $totalPago
        );

        $response_save_update = ModeloHistorialPago::mdlActualizarPagoPendiente($tabla, $datos);
        if ($response_save_update["estado"] == "ok") {

            /* ==========================================
            HISTORIAL DE PAGO
            ========================================== */

            $ruta = "../vistas/img/comprobantes/";
            if (isset($_FILES["comprobante_pago_historial"]["tmp_name"])) {
                $extension = pathinfo($_FILES["comprobante_pago_historial"]["name"], PATHINFO_EXTENSION);
                $tipos_permitidos = array("jpg",
                    "jpeg",
                    "png",
                    "gif"
                );
                if (in_array(strtolower($extension), $tipos_permitidos)) {
                    $nombre_imagen = date("YmdHis") . rand(1000, 9999);
                    $ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
                    if (move_uploaded_file($_FILES["comprobante_pago_historial"]["tmp_name"], $ruta_imagen)) {
                    } else {
                    }
                } else {
                }
            }

            $tablaHistorialPago = "historial_pagos";
            $datosHistorialPago = array(
                    "id_venta" => $_POST["id_venta_pagar"],
                    "monto_pago" => $_POST["monto_pagar_venta"],
                    "forma_pago" => $_POST["metodos_pago_venta_historial"],
                    "numero_serie_pago" => !empty($_POST["serie_numero_pago_historial"]) ? $_POST["serie_numero_pago_historial"] : null,
                    "comprobante_imagen" => isset($_FILES["comprobante_pago_historial"]["tmp_name"]) ? $ruta_imagen : null
                );

            $response_save_historial = ModeloHistorialPago::mdlIngresoHistorialPago($tablaHistorialPago, $datosHistorialPago);

            if($response_save_historial['message'] == "ok"){
                echo json_encode([
                    'estado' => $response_save_update["estado"],
                    'message' => $response_save_update["mensaje"],
                    'data' => $response_save_historial
                ]);
            }
        }else{
            echo json_encode([
                'estado' => $response_save_update["estado"],
                'message' => $response_save_update["mensaje"]
            ]);
        }
    }


    /*=============================================
	EDITAR HISTORIAL PAGO
	=============================================*/
    static public function ctrEditarHistorialPago()
    {

        /* =========================================
        EDITANDO EL PAGO
        ========================================= */
        $tabla = "ventas";
        $pagoTotal = number_format($_POST["edit_monto_actual_pago"], 2, '.', ''); // Monto por defecto
        $pagoEditado = number_format($_POST["edit_monto_pagar_venta"], 2, '.', ''); // Monto editado
        $datos = array(
            "id_venta" => $_POST["edit_id_venta_pagar"],
            "total_pago" => $pagoTotal,
            "total_actual" => $pagoEditado
        );

        $response_pago_edit = ModeloHistorialPago::mdlActualizarPagoPendienteEdit($tabla, $datos);

        if ($response_pago_edit["estado"] == "ok") {

            /* ============================
            VALIDANDO IMAGEN
            ============================ */
            $ruta = "../vistas/img/comprobantes/";
            $ruta_imagen = $_POST["actual_comprobante_pago_historial"];
            if (isset($_FILES["edit_comprobante_pago_historial"]["tmp_name"]) && !empty($_FILES["edit_comprobante_pago_historial"]["tmp_name"])) {
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen);
                }
                $extension = pathinfo($_FILES["edit_comprobante_pago_historial"]["name"], PATHINFO_EXTENSION);
                $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
                if (in_array(strtolower($extension), $tipos_permitidos)) {
                    $nombre_imagen = date("YmdHis") . rand(1000, 9999);
                    $ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
                    if (move_uploaded_file($_FILES["edit_comprobante_pago_historial"]["tmp_name"], $ruta_imagen)) {
                    } else {
                    }
                } else {
                }
            }

            /* ==========================================
            EDITANDO HISTORIAL DE PAGO
            ========================================== */
            $monto_pago = '';
            if ($pagoEditado != 0.00 || $pagoEditado !== '' || $pagoEditado != null) {
                $monto_pago = $pagoEditado;
            } else {
                $monto_pago = $pagoTotal;
            }
            $tablaHistorialPago = "historial_pagos";
            $datos = array(
                "id_venta" => $_POST["edit_id_venta_pagar"],
                "id_pago" => $_POST["edit_edit_pago_historial"],
                "monto_pago" => $monto_pago,
                "forma_pago" => $_POST["edit_metodos_pago_venta_historial"],
                "numero_serie_pago" => !empty($_POST["edit_serie_numero_pago_historial"]) ? $_POST["edit_serie_numero_pago_historial"] : null,
                "comprobante_imagen" => (isset($_FILES["edit_comprobante_pago_historial"]["tmp_name"])) || isset($_POST["actual_comprobante_pago_historial"]) ? $ruta_imagen : null
            );

            $response_historial = ModeloHistorialPago::mdlEditarHistorialPago($tablaHistorialPago, $datos);
            if($response_historial == "ok"){
                echo json_encode([
                    'estado' => $response_pago_edit["estado"],
                    'message' => $response_pago_edit["mensaje"]
                ]);
            }

        }else{
            echo json_encode([
                'estado' => $response_pago_edit["estado"],
                'message' => $response_pago_edit["mensaje"]
            ]);
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
                /* echo "El archivo a eliminar no existe."; */
            }
        }
        $respuesta = ModeloHistorialPago::mdlBorrarHistorialPago($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }
}
