<?php

class ControladorSerieNumero
{

    /*=============================================
	REGISTRO DE SERIE NUMERO
	=============================================*/

    static public function ctrCrearSerieNumero()
    {
        $tabla = "serie_num_comprobante";
        $datos = array(
            "tipo_comprobante_sn" => $_POST["tipo_comprobante"],
            "serie_prefijo" => $_POST["serie_prefijo"],
            "folio_inicial" => $_POST["folio_inicial"],
            "folio_final" => $_POST["folio_final"]
        );
        $respuesta = ModeloSerieNumero::mdlIngresarSerieNumero($tabla, $datos);
        if ($respuesta) {
            echo json_encode($respuesta);
        }

    }

    /*=============================================
	MOSTRAR SERIE NUMERO
	=============================================*/

    static public function ctrMostrarSerieNumero($item, $valor)
    {
        $tabla = "serie_num_comprobante";
        $respuesta = ModeloSerieNumero::mdlMostrarSerieNumero($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	EDITAR SERIE NUMERO
	=============================================*/

    static public function ctrEditarSerieNumero()
    {
        $tabla = "serie_num_comprobante";
        $datos = array(
            "id_serie_num" => $_POST["edit_id_serie_num"],
            "tipo_comprobante_sn" => $_POST["edit_tipo_comprobante"],
            "serie_prefijo" => $_POST["edit_serie_prefijo"],
            "folio_inicial" => $_POST["edit_folio_inicial"],
            "folio_final" => $_POST["edit_folio_final"]
        );
        $respuesta = ModeloSerieNumero::mdlEditarSerieNumero($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
 
    }

    /*=============================================
	BORRAR SERIE NUMERO
	=============================================*/

    static public function ctrBorraSerieNumero()
    {
        $tabla = "serie_num_comprobante";
        $datos = $_POST["DeleteidSerieNumero"];
        $respuesta = ModeloSerieNumero::mdlBorrarSerieNumero($tabla, $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        }
    }
}
