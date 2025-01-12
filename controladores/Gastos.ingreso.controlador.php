<?php

class ControladorGastoIngreso
{

    /*=============================================
	REGISTRO GASTO INGRESO
	=============================================*/
    static public function ctrCrearGastoIngreso()
    {
        $tabla = "gasto_ingreso";
        $tabla_caja = "movimientos_caja";

        // Datos para la tabla `gasto_ingreso`
        $datos = array(
            "id_usuario" => $_POST["id_usuario"],
            "id_movimiento_caja" => $_POST["id_movimiento_caja"],
            "tipo" => $_POST["tipo"],
            "concepto" => $_POST["concepto"],
            "monto" => $_POST["monto"],
            "detalles" => $_POST["detalles"]
        );

        $respuesta = ModeloGastoIngreso::mdlIngresarGastoIngreso($tabla, $datos);

        if ($respuesta["status"] == true) {
            // Actualizar los montos en la tabla `movimientos_caja`
            $datosActualizar = array(
                "id_movimiento" => $_POST["id_movimiento_caja"],
                "egresos" => ($_POST["tipo"] == "egreso") ? $_POST["monto"] : 0,
                "ingresos" => ($_POST["tipo"] == "ingreso") ? $_POST["monto"] : 0
            );

            $actualizar = ModeloCajaGeneral::mdlActualizarMontos($tabla_caja, $datosActualizar);
            
            if ($actualizar["status"] == true) {
                echo json_encode([
                    "status" => true,
                    "message" => "Los datos se guardaron con éxito"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Error al guardar los datos"
                ]);
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Error al registrar el gasto/ingreso."
            ]);
        }
    }


    /*=============================================
	MOSTRAR GASTO INGRESO
	=============================================*/
    static public function ctrMostrarGastoIngreso($item, $valor)
    {
        $tabla = "gasto_ingreso";
        $respuesta = ModeloGastoIngreso::mdlMostrarGastosIngresos($tabla, $item, $valor);
        return $respuesta;
    }

    	/*=============================================
    MOSTRAR REPORTE DE PRODUCTOS
    =============================================*/

	public static function ctrReporteGastosIngresos()
	{
		$tabla = "gasto_ingreso";
		$id_usuario = isset($_POST['id_usuario_reporte']) ? $_POST['id_usuario_reporte'] : null;
		$tipo = isset($_POST['tipo_reporte']) ? $_POST['tipo_reporte'] : null;
		$fecha_desde = isset($_POST['fecha_desde_reporte']) ? $_POST['fecha_desde_reporte'] : null;
		$fecha_hasta = isset($_POST['fecha_hasta_reporte']) ? $_POST['fecha_hasta_reporte'] : null;

		$respuesta = ModeloGastoIngreso::mdlReporteGastosIngresos($tabla, $id_usuario, $tipo, $fecha_desde, $fecha_hasta);
		return $respuesta;
	}

    	/*=============================================
    MOSTRAR REPORTE DE PRODUCTOS
    =============================================*/

	public static function ctrReporteGastosIngresosPDF()
	{
		$tabla = "gasto_ingreso";
		$id_usuario = isset($_GET['id_usuario_reporte']) ? $_GET['id_usuario_reporte'] : null;
		$tipo = isset($_GET['tipo_reporte']) ? $_GET['tipo_reporte'] : null;
		$fecha_desde = isset($_GET['fecha_desde_reporte']) ? $_GET['fecha_desde_reporte'] : null;
		$fecha_hasta = isset($_GET['fecha_hasta_reporte']) ? $_GET['fecha_hasta_reporte'] : null;

		$respuesta = ModeloGastoIngreso::mdlReporteGastosIngresos($tabla, $id_usuario, $tipo, $fecha_desde, $fecha_hasta);
		return $respuesta;
	}

    /*=============================================
	EDITAR GASTO INGRESO
	=============================================*/
    static public function ctrEditarGastoIngreso()
    {
        $tabla = "gasto_ingreso";
        $tabla_caja = "movimientos_caja";

        $datos = array(
            "id_gasto" => $_POST["id_gasto_edit"],
            "id_movimiento_caja" => $_POST["id_movimiento_caja_ingreso_egreso"],
            "tipo" => $_POST["tipo_edit"],
            "concepto" => $_POST["concepto_edit"],
            "monto" => $_POST["monto_edit"],
            "detalles" => $_POST["detalles_edit"]
        );
        $respuesta = ModeloGastoIngreso::mdlEditarGastoIngreso($tabla, $datos);
        if ($respuesta["status"] == true) {
            // Actualizar los montos en la tabla `movimientos_caja`
            $datosActualizar = array(
                "id_movimiento" => $_POST["id_movimiento_caja_ingreso_egreso"],
                "egresos" => ($_POST["tipo_edit"] == "egreso") ? $_POST["monto_edit_final"] : 0,
                "ingresos" => ($_POST["tipo_edit"] == "ingreso") ? $_POST["monto_edit_final"] : 0,
                "accion" => $_POST["accion_monto"]
            );

            $actualizar = ModeloCajaGeneral::mdlActualizarMontosEdit($tabla_caja, $datosActualizar);
            
            if ($actualizar["status"] == true) {
                echo json_encode([
                    "status" => true,
                    "message" => "Los datos se actualizaron con éxito"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Error al actualizar los datos"
                ]);
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Error al actualizar el gasto/ingreso."
            ]);
        }
    }

    /*=============================================
	BORRAR GASTO INGRESO
	=============================================*/
    static public function ctrBorraGastoIngreso()
    {
        $tabla = "gasto_ingreso";
        $tabla_caja = "movimientos_caja";
        $datos = $_POST["idGatosIngresoDelete"];

        $respuesta = ModeloGastoIngreso::mdlBorrarGastoIngreso($tabla, $datos);

        if ($respuesta["status"] == true) {
            // Actualizar los montos en la tabla `movimientos_caja`
            $datosActualizar = array(
                "id_movimiento" => $_POST["IdmovimientoCaja"],
                "egresos" => ($_POST["tipoMovimiento"] == "egreso") ? $_POST["montoIngresoGastoDelete"] : 0,
                "ingresos" => ($_POST["tipoMovimiento"] == "ingreso") ? $_POST["montoIngresoGastoDelete"] : 0
            );

            $actualizar = ModeloCajaGeneral::mdlActualizarMontosDelete($tabla_caja, $datosActualizar);
            
            if ($actualizar["status"] == true) {
                echo json_encode([
                    "status" => true,
                    "message" => "Eliminado con éxito"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Error al eliminar"
                ]);
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Error al actualizar el gasto/ingreso."
            ]);
        }
    }
}
