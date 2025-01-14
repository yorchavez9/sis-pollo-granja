<?php

class ControladorAccion
{

	/*=============================================
	REGISTRO DE ACCION
	=============================================*/
	static public function ctrCrearAccion()
	{
		$tabla = "acciones";
		$datos = array(
			"nombre_categoria" => $_POST["nombre_categoria"],
			"descripcion" => $_POST["descripcion_categoria"]
		);
		$respuesta = ModeloAccion::mdlIngresarAccion($tabla,	$datos);
		if ($respuesta == "ok") {
			echo json_encode("ok");
		} else {
			echo json_encode("error");
		}
	}

	/*=============================================
	MOSTRAR ACCIONES
	=============================================*/
	static public function ctrMostrarAcciones($item, $valor)
	{
		$tabla = "acciones";
		$respuesta = ModeloAccion::mdlMostrarAcciones($tabla, $item, $valor);
		return $respuesta;
	}

    /*=============================================
	EDITAR ACCION
	=============================================*/
    static public function ctrEditarAccion()
    {
        $tabla = "acciones";
        $datos = array(
            "id_categoria" => $_POST["edit_id_categoria"],
            "nombre_categoria" => $_POST["edit_nombre_categoria"],
            "descripcion" => $_POST["edit_descripcion_categoria"]
        );
        $respuesta = ModeloAccion::mdlEditarAccion($tabla, $datos);

        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }

	/*=============================================
	BORRAR ACCION
	=============================================*/
	static public function ctrBorraAccion()
    {

        $tabla = "acciones";
        $datos = $_POST["deleteIdCategoria"];
        $respuesta = ModeloAccion::mdlBorrarAccion($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }
}
