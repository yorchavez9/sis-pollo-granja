<?php

class ControladorModulos
{


	/*=============================================
	REGISTRO DE MODULOS
	=============================================*/
	static public function ctrCrearModulo()
	{

		$tabla = "categorias";
		$datos = array(
			"nombre_categoria" => $_POST["nombre_categoria"],
			"descripcion" => $_POST["descripcion_categoria"]
		);
		$respuesta = ModeloModulos::mdlIngresarModulo($tabla,	$datos);
		if ($respuesta == "ok") {
			echo json_encode("ok");
		} else {
			echo json_encode("error");
		}
	}

	/*=============================================
	MOSTRAR MODULOS
	=============================================*/
	static public function ctrMostrarModulos($item, $valor)
	{
		$tabla = "modulos";
		$respuesta = ModeloModulos::mdlMostrarModulos($tabla, $item, $valor);
		return $respuesta;
	}

    /*=============================================
	EDITAR MODULOS
	=============================================*/
    static public function ctrEditarModulo()
    {
        $tabla = "categorias";
        $datos = array(
            "id_categoria" => $_POST["edit_id_categoria"],
            "nombre_categoria" => $_POST["edit_nombre_categoria"],
            "descripcion" => $_POST["edit_descripcion_categoria"]
        );
        $respuesta = ModeloModulos::mdlEditarModulo($tabla, $datos);

        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }

	/*=============================================
	BORRAR MODULOS
	=============================================*/
	static public function ctrBorraModulo()
    {

        $tabla = "categorias";
        $datos = $_POST["deleteIdCategoria"];
        $respuesta = ModeloModulos::mdlBorrarModulo($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }
}
