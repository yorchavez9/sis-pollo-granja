<?php

require_once "../controladores/Cotizacion.controllador.php";
require_once "../controladores/Producto.controlador.php";
require_once "../modelos/Cotizacion.modelo.php";
require_once "../modelos/Producto.modelo.php";

class AjaxListaCotizacion
{
    /*=============================================
    AGREGAR PRODUCTO A LA LISTA DE VENTAS
    =============================================*/
    public $id_producto_edit;

    public function ajaxAddProducto()
    {
        $item = "id_producto";
        $valor = $this->id_producto_edit;
        $respuesta = ControladorProducto::ctrMostrarProductos($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    EDITAR VENTA
    =============================================*/
    public $idVenta;

    public function ajaxEditarVenta()
    {
        $item = "id_venta";
        $valor = $this->idVenta;
        $respuesta = ControladorCotizacion::ctrMostrarListaCotizaciones($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    MOSTRAR DETALLE VENTAS
    =============================================*/
    public $idProductoVer;

    public function ajaxVerProducto()
    {
        $item = "id_producto";
        $valor = $this->idProductoVer;
        $respuesta = ControladorProducto::ctrMostrarProductos($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    ACTIVAR COTIZACIONES
    =============================================*/
    public $activarCotizacion;
    public $activarId;

    public function ajaxActivarCotizacion()
    {
        $tabla = "cotizaciones";
        $item1 = "estado";
        $valor1 = $this->activarCotizacion;
        $item2 = "id_cotizacion";
        $valor2 = $this->activarId;
        ModeloCotizacion::mdlActualizarCotizacion($tabla, $item1, $valor1, $item2, $valor2);
    }
}

/*=============================================
GESTIÓN DE ACCIONES MEDIANTE POST
=============================================*/

// Agregar producto a la lista de ventas
if (isset($_POST["id_producto_edit"])) {
    $editar = new AjaxListaCotizacion();
    $editar->id_producto_edit = $_POST["id_producto_edit"];
    $editar->ajaxAddProducto();
}

/*=============================================
ESTADOS DE ACTIVACION DE LA COTIZACION
=============================================*/
 elseif (isset($_POST["activarCotizacion"])) {
    $activarCotizacion = new AjaxListaCotizacion();
    $activarCotizacion->activarCotizacion = $_POST["activarCotizacion"];
    $activarCotizacion->activarId = $_POST["activarId"];
    $activarCotizacion->ajaxActivarCotizacion();
}
// Editar venta
elseif (isset($_POST["idVenta"])) {
    $editar = new AjaxListaCotizacion();
    $editar->idVenta = $_POST["idVenta"];
    $editar->ajaxEditarVenta();
}
// Actualizar venta
elseif (isset($_POST["edit_id_venta"])) {
    ControladorCotizacion::ctrEditarVenta();
}
// Ver detalle de producto
elseif (isset($_POST["idProductoVer"])) {
    $verDetalle = new AjaxListaCotizacion();
    $verDetalle->idProductoVer = $_POST["idProductoVer"];
    $verDetalle->ajaxVerProducto();
}
// Guardar producto
elseif (isset($_POST["id_categoria_P"])) {
    ControladorProducto::ctrCrearProducto();
}

// Actualizar producto
elseif (isset($_POST["edit_id_producto"])) {
    ControladorProducto::ctrEditarProducto();
}
// Borrar producto
elseif (isset($_POST["idProductoDelete"])) {
    ControladorProducto::ctrBorrarProducto();
}
// Borrar venta
elseif (isset($_POST["idCotizacionDelete"])) {
    ControladorCotizacion::ctrBorrarCotizacion();
}
// Mostrar todas las ventas en la tabla
else {
    $item = null;
    $valor = null;
    $mostrarVentas = ControladorCotizacion::ctrMostrarListaCotizaciones($item, $valor);
    $tblVenta = array_map(function ($ventas) {
        return [
            'id_cotizacion' => $ventas['id_cotizacion'],
            'id_persona' => $ventas['id_persona'],
            'razon_social' => $ventas['razon_social'],
            'tipo_comprobante_sn' => $ventas['tipo_comprobante_sn'],
            'validez' => $ventas['validez'],
            'total_cotizacion' => $ventas['total_cotizacion'],
            'fecha_cotizacion' => $ventas['fecha_cotizacion'],
            'hora_cotizacion' => $ventas['hora_cotizacion'],
            'estado' => $ventas['estado'],
            
        ];
    }, $mostrarVentas);
    echo json_encode($tblVenta);
}
