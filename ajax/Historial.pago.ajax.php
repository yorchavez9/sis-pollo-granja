<?php

require_once "../controladores/Historial.pago.controlador.php";
require_once "../modelos/Historial.pago.modelo.php";

class AjaxHistorialPago
{

    /*=============================================
	EDITAR PRODUCTO
	=============================================*/
    public $idPago;
    public function ajaxEditarProducto()
    {
        $item = "id_pago";
        $valor = $this->idPago;
        $respuesta = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR PRODUCTO
=============================================*/
if (isset($_POST["idPago"])) {
    $editar = new AjaxHistorialPago();
    $editar->idPago = $_POST["idPago"];
    $editar->ajaxEditarProducto();
}

// GUARDAR EL HISTORIAL DE PAGO O ACTUALIZAR
elseif (isset($_POST["id_venta_pagar"])) {
    ControladorHistorialPago::ctrActualizarDeudaVenta();
}
/* ACTUALIZAR PRODUCTO */ 
elseif (isset($_POST["edit_edit_pago_historial"])) {
    $editPago = new ControladorHistorialPago();
    $editPago->ctrEditarHistorialPago();
}
/* BORRAR PRODUCTO */ 
elseif (isset($_POST["id_delete_pago_historial"])) {
    $borrarPago = new ControladorHistorialPago();
    $borrarPago->ctrBorrarHistorialPago();
}
/* MOSTRAR PRODUCTOS EN LA TABLA */ 
elseif(isset($_POST["id_venta_historial"])) {
    $item = "id_venta";
    $valor = $_POST["id_venta_historial"];
    $mostrar_historial_pagos = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);
    $tabla_historial_pagos = array();
    foreach ($mostrar_historial_pagos as $key => $historial) {
        $fila = array(
            'id_pago' => $historial['id_pago'],
            'razon_social' => $historial['razon_social'],
            'id_venta' => $historial['id_venta'],
            'forma_pago' => $historial['forma_pago'],
            'monto_pago' => $historial['monto_pago'],
            'numero_serie_pago' => $historial['numero_serie_pago'],
            'comprobante_imagen' => $historial['comprobante_imagen'],
            'fecha_registro' => $historial['fecha_registro']
        );
        $tabla_historial_pagos[] = $fila;
    }
    echo json_encode($tabla_historial_pagos);
}
