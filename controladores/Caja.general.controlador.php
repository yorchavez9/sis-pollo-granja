<?php

class ControladorCajaGeneral
{

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
        $tabla = "categorias";
        $datos = array(
            "id_categoria" => $_POST["edit_id_categoria"],
            "nombre_categoria" => $_POST["edit_nombre_categoria"],
            "descripcion" => $_POST["edit_descripcion_categoria"]
        );
        $respuesta = ModeloCajaGeneral::mdlEditarCajaGeneral($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
	}

	/*=============================================
	BORRAR CAJA GENERAL
	=============================================*/
	static public function ctrBorraCajaGeneral()
	{
        $tabla = "categorias";
        $datos = $_POST["deleteIdCategoria"];
        $respuesta = ModeloCajaGeneral::mdlBorrarCajaGeneral($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
	}
}
