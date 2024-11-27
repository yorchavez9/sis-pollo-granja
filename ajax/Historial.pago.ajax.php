<?php

require_once "../controladores/Historial.pago.controlador.php";
require_once "../modelos/Historial.pago.modelo.php";

class AjaxHistorialPago
{

    /*=============================================
	EDITAR PRODUCTO
	=============================================*/
    public $idProducto;
    public function ajaxEditarProducto()
    {
        $item = "id_producto";
        $valor = $this->idProducto;
        $respuesta = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
	MOSTRAR DETALLE PRODUCTO
	=============================================*/
    public $idProductoVer;
    public function ajaxVerProducto()
    {
        $item = "id_producto";
        $valor = $this->idProductoVer;
        $respuesta = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
	ACTIVAR PRODUCTO
	=============================================*/
    public $activarProducto;
    public $activarId;
    public function ajaxActivarProducto()
    {
        $tabla = "productos";
        $item1 = "estado_producto";
        $valor1 = $this->activarProducto;
        $item2 = "id_producto";
        $valor2 = $this->activarId;
        $respuesta = ModeloProducto::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
    }

    /*=============================================
	VALIDAR NO REPETIR PRODUCTO
	=============================================*/
    public $validarUsuario;
    public function ajaxValidarUsuario()
    {
        $item = "usuario";
        $valor = $this->validarUsuario;
        $respuesta = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR PRODUCTO
=============================================*/
if (isset($_POST["idProducto"])) {
    $editar = new AjaxHistorialPago();
    $editar->idProducto = $_POST["idProducto"];
    $editar->ajaxEditarProducto();
}

/* VER DETALLE PRODUCTO */ 
elseif (isset($_POST["idProductoVer"])) {
    $verDetalle = new AjaxHistorialPago();
    $verDetalle->idProductoVer = $_POST["idProductoVer"];
    $verDetalle->ajaxVerProducto();
}

/* MOSTRAR PRODUCTO POR STOCK */ 
elseif (isset($_POST["cantStock"])) {
    $item = null;
    $valor = $_POST["cantStock"];
    $productos = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);

    echo json_encode($productos);
}
/* ACTIVAR PRODUCTO */ 
elseif (isset($_POST["activarProducto"])) {

    $activarProducto = new AjaxHistorialPago();
    $activarProducto->activarProducto = $_POST["activarProducto"];
    $activarProducto->activarId = $_POST["activarId"];
    $activarProducto->ajaxActivarProducto();
}
/* VALIDAR PRODUCTO */ 
elseif (isset($_POST["validarUsuario"])) {
    $valUsuario = new AjaxHistorialPago();
    $valUsuario->validarUsuario = $_POST["validarUsuario"];
    $valUsuario->ajaxValidarUsuario();
}
/* GUARDAR PRODUCTO */ 
elseif (isset($_POST["id_categoria_P"])) {
    $crearProducto = new ControladorHistorialPago();
    $crearProducto->ctrCrearHistorialPago();
}
/* ACTUALIZAR PRODUCTO */ 
elseif (isset($_POST["edit_id_producto"])) {
    $editProducto = new ControladorHistorialPago();
    $editProducto->ctrEditarHistorialPago();
}
/* BORRAR PRODUCTO */ 
elseif (isset($_POST["idProductoDelete"])) {
    $borrarProducto = new ControladorHistorialPago();
    $borrarProducto->ctrBorrarHistorialPago();
}
/* MOSTRAR PRODUCTOS EN LA TABLA */ 
else {
    $item = "id_venta";
    $valor = $_POST["id_venta_historial"];
    $mostrar_historial_pagos = ControladorHistorialPago::ctrMostrarHistorialPago($item, $valor);
    $tabla_historial_pagos = array();
    foreach ($mostrar_historial_pagos as $key => $historial) {
        $fila = array(
            'id_pago' => $historial['id_pago'],
            'razon_social' => $historial['razon_social'],
            'id_venta' => $historial['id_venta'],
            'fecha_pago' => $historial['fecha_pago'],
            'tipo_pago' => $historial['tipo_pago'],
            'forma_pago' => $historial['forma_pago'],
            'monto_pago' => $historial['monto_pago'],
            'estado_pago' => $historial['estado_pago'],
            'numero_serie_pago' => $historial['numero_serie_pago'],
            'comprobante_imagen' => $historial['comprobante_imagen']
        );
        $tabla_historial_pagos[] = $fila;
    }
    echo json_encode($tabla_historial_pagos);
}
