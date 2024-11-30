<?php

class ControladorPagos
{

	/*=============================================
	REGISTRO DE PAGOS
	=============================================*/
	static public function ctrCrearPagos()
	{

		$tabla = "pagos_trabajadores";
		$datos = array(
			"id_contrato" => $_POST["id_contrato_pago"],
			"monto_pago" => $_POST["monto_pago_t"],
			"fecha_pago" => $_POST["fecha_pago_t"]
		);
		$respuesta = ModeloPago::mdlIngresarPagos($tabla, $datos);
		if ($respuesta == "ok") {
			echo json_encode("ok");
		} else {
			echo json_encode("error");
		}
	}

	/*=============================================
	MOSTRAR PAGOS
	=============================================*/

	static public function ctrMostrarPagos($item, $valor)
	{
		$tabla_t = "trabajadores";
		$tabla_contrato_t = "contratos_trabajadores";
		$tabla_pago_t = "pagos_trabajadores";
		$respuesta = ModeloPago::mdlMostrarPagos($tabla_t, $tabla_contrato_t, $tabla_pago_t, $item, $valor);
		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE PAGOS DE TRABAJADORES
    =============================================*/

	public static function ctrReportePagosTrabajador()
	{
		$tabla_t = "trabajadores";
		$tabla_contrato_t = "contratos_trabajadores";
		$tabla_pago_t = "pagos_trabajadores";
		// Capturamos los filtros
		$filtros = [
			"filtro_estado_pago_t" => isset($_POST['filtro_estado_pago_t']) ? $_POST['filtro_estado_pago_t'] : null,
			"filtro_fecha_desde_pago_t" => isset($_POST['filtro_fecha_desde_pago_t']) ? $_POST['filtro_fecha_desde_pago_t'] : null,
			"filtro_fecha_hasta_pago_t" => isset($_POST['filtro_fecha_hasta_pago_t']) ? $_POST['filtro_fecha_hasta_pago_t'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloPago::mdlReportePagoTrabajador($tabla_t, $tabla_contrato_t, $tabla_pago_t, $filtros);

		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE PAGOS DE TRABAJADORES PDF
    =============================================*/

	public static function ctrReportePagosTrabajadorPDF()
	{
		$tabla_t = "trabajadores";
		$tabla_contrato_t = "contratos_trabajadores";
		$tabla_pago_t = "pagos_trabajadores";
		// Capturamos los filtros
		$filtros = [
			"filtro_estado_pago_t" => isset($_GET['filtro_estado_pago_t']) ? $_GET['filtro_estado_pago_t'] : null,
			"filtro_fecha_desde_pago_t" => isset($_GET['filtro_fecha_desde_pago_t']) ? $_GET['filtro_fecha_desde_pago_t'] : null,
			"filtro_fecha_hasta_pago_t" => isset($_GET['filtro_fecha_hasta_pago_t']) ? $_GET['filtro_fecha_hasta_pago_t'] : null
		];

		// Pasamos los filtros al modelo
		$respuesta = ModeloPago::mdlReportePagoTrabajador($tabla_t, $tabla_contrato_t, $tabla_pago_t, $filtros);

		return $respuesta;
	}

    /*=============================================
	EDITAR PAGO
	=============================================*/

    static public function ctrEditarPago()
    {



        $tabla = "contratos_trabajadores";

        $datos = array(
            "id_contrato" => $_POST["edit_id_contrato"],
            "id_trabajador" => $_POST["edit_id_trabajador_contrato"],
            "tiempo_contrato" => $_POST["edit_tiempo_contrato_t"],
            "tipo_sueldo" => $_POST["edit_tipo_sueldo_c"],
            "sueldo" => $_POST["edit_sueldo_trabajador"]
        );


        $respuesta = ModeloContrato::mdlEditarContrato($tabla, $datos);

        if ($respuesta == "ok") {

            echo json_encode("ok");
        }
    }

	/*=============================================
	BORRAR PAGO
	=============================================*/

	static public function ctrBorrarPago()
	{

		if (isset($_POST["idPagoDelete"])) {

			$tabla = "pagos_trabajadores";

			$datos = $_POST["idPagoDelete"];

			$respuesta = ModeloPago::mdlBorrarPagos($tabla, $datos);

			if ($respuesta == "ok") {

				echo json_encode("ok");
			}
		}
	}
}
