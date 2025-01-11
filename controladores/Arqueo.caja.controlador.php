<?php

class ControladorArqueoCaja
{

	/*=============================================
	REGISTRO DE ARQUEO CAJA
	=============================================*/
	static public function ctrCrearArqueoCaja()
	{
		$tabla = "arqueos_caja";
		$datos = array(
			"id_movimiento_caja" => $_POST["id_movimiento_caja"],
			"id_usuario" => $_POST["id_usuario"],
			"fecha_arqueo" => $_POST["fecha_arqueo"],
			"monto_sistema" => $_POST["monto_sistema"],
			"monto_fisico" => $_POST["monto_fisico"],
			"diferencia" => $_POST["diferencia"],
			"observaciones" => $_POST["observaciones"]
		);
		$respuesta = ModeloArqueoCaja::mdlIngresarArqueoCaja($tabla, $datos);
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
	MOSTRAR ARQUEO CAJA
	=============================================*/
	static public function ctrMostrarArqueoCaja($item, $valor)
	{
		$tabla = "arqueos_caja";
		$respuesta = ModeloArqueoCaja::mdlMostrarArqueoCaja($tabla, $item, $valor);
		return $respuesta;
	}

    /*=============================================
	EDITAR ARQUEO CAJA
	=============================================*/
    static public function ctrEditarArqueoCaja()
    {

        $tabla = "arqueos_caja";
        $datos = array(
            "id_arqueo" => $_POST["id_arqueo_edit"],
            "id_movimiento_caja" => $_POST["id_movimiento_edit"],
			"id_usuario" => $_POST["id_usuario_edit"],
			"fecha_arqueo" => $_POST["fecha_arqueo_edit"],
			"monto_sistema" => $_POST["monto_sistema_edit"],
			"monto_fisico" => $_POST["monto_fisico_edit"],
			"diferencia" => $_POST["diferencia_edit"],
			"observaciones" => $_POST["observaciones_edit"]
        );
        $respuesta = ModeloArqueoCaja::mdlEditarArqueoCaja($tabla,$datos);
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
	BORRAR ARQUEO CAJA
	=============================================*/
	static public function ctrBorraArqueoCaja()
	{
        $tabla = "arqueos_caja";
        $datos = $_POST["idArqueoCajaDelete"];
        $respuesta = ModeloArqueoCaja::mdlBorrarArqueoCaja($tabla, $datos);
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
}
