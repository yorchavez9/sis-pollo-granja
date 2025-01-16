<?php

class ControladorCajaGeneral
{

	/* ==============================================
	MOSTRAR RESULTADOS Y CALCULOS DE LAS VENTAS
	============================================== */
	static public function ctrMostrarCalcularVentas(){
        $respuesta = ModeloCajaGeneral::mdlMostrarResumenVentas();
        return $respuesta;
	}

	/*=============================================
	REGISTRO DE CAJA GENERAL
	=============================================*/
	static public function ctrCrearCajaGeneral()
	{
		$tabla = "movimientos_caja";
		$datos = array(
			"id_usuario" => $_POST["id_usuario_caja"],
			"monto_inicial" => $_POST["monto_inicial_caja"],
			"fecha_apertura" => $_POST["fecha_apertura_caja"],
			"fecha_cierre" => $_POST["fecha_cierre_caja"]
		);
		$respuesta = ModeloCajaGeneral::mdlIngresarCajaGeneral($tabla,	$datos);
		if ($respuesta["status"] == true) {
			echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
		} else {
			echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
		}
	}

	/*=============================================
	MOSTRAR CAJA GENERAL
	=============================================*/
	static public function ctrMostrarCajaGeneal($item, $valor)
	{
		$tabla = "movimientos_caja";
		$respuesta = ModeloCajaGeneral::mdlMostrarCajaGeneral($tabla, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	EDITAR CAJA GENERAL
	=============================================*/
	static public function ctrEditarCajaGeneral()
	{
        $tabla = "movimientos_caja";
        $datos = array(
            "id_movimiento" => $_POST["id_movimiento_update"],
            "id_usuario" => $_POST["id_usuario_update"],
            "tipo_movimiento" => $_POST["tipo_movimiento_update"],
            "egresos" => $_POST["egresos_update"],
            "ingresos" => $_POST["ingresos_update"],
            "monto_inicial" => $_POST["monto_inicial_update"],
            "monto_final" => $_POST["monto_final_update"],
            "fecha_cierre" => $_POST["fecha_cierre_update"],
            "estado" => $_POST["estado_update"]
        );
        $respuesta = ModeloCajaGeneral::mdlEditarCajaGeneral($tabla, $datos);
        if ($respuesta["status"] == true) {
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        }else{
            echo json_encode([
                "status" => $respuesta["status"],
                "message" => $respuesta["message"]
            ]);
        }
	}


	/*=============================================
	BORRAR CAJA GENERAL
	=============================================*/
	
}
