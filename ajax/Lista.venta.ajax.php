<?php

require_once "../controladores/Ventas.controlador.php";
require_once "../controladores/Producto.controlador.php";
require_once "../modelos/Ventas.modelo.php";
require_once "../modelos/Producto.modelo.php";

class AjaxListaVentas
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
        $respuesta = ControladorVenta::ctrMostrarListaVentas($item, $valor);
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
    ACTIVAR PRODUCTOS
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
        ModeloProducto::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);
    }
}

/*=============================================
GESTIÓN DE ACCIONES MEDIANTE POST
=============================================*/

// Agregar producto a la lista de ventas
if (isset($_POST["id_producto_edit"])) {
    $editar = new AjaxListaVentas();
    $editar->id_producto_edit = $_POST["id_producto_edit"];
    $editar->ajaxAddProducto();
}
// Editar venta
elseif (isset($_POST["idVenta"])) {
    $editar = new AjaxListaVentas();
    $editar->idVenta = $_POST["idVenta"];
    $editar->ajaxEditarVenta();
}
// Actualizar venta
elseif (isset($_POST["edit_id_venta"])) {
    ControladorVenta::ctrEditarVenta();
}
// Ver detalle de producto
elseif (isset($_POST["idProductoVer"])) {
    $verDetalle = new AjaxListaVentas();
    $verDetalle->idProductoVer = $_POST["idProductoVer"];
    $verDetalle->ajaxVerProducto();
}
// Guardar producto
elseif (isset($_POST["id_categoria_P"])) {
    ControladorProducto::ctrCrearProducto();
}
// Actualizar pago de deuda
elseif (isset($_POST["id_venta_pagar"])) {
    ControladorVenta::ctrActualizarDeudaVenta();
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
elseif (isset($_POST["ventaIdDelete"])) {
    ControladorVenta::ctrBorrarVenta();
}
// Mostrar todas las ventas en la tabla
else {
    $item = null;
    $valor = null;
    $mostrarVentas = ControladorVenta::ctrMostrarListaVentas($item, $valor);

    $tblVenta = array_map(function ($ventas) {
        return [
            'id_venta' => $ventas['id_venta'],
            'id_persona' => $ventas['id_persona'],
            'razon_social' => $ventas['razon_social'],
            'tipo_comprobante_sn' => $ventas['tipo_comprobante_sn'],
            'serie_prefijo' => $ventas['serie_prefijo'],
            'num_comprobante' => $ventas['num_comprobante'],
            'tipo_pago' => $ventas['tipo_pago'],
            'total_venta' => $ventas['total_venta'],
            'total_pago' => $ventas['total_pago'],
            'fecha_venta' => $ventas['fecha_venta'],
            'estado_pago' => $ventas['estado_pago']
        ];
    }, $mostrarVentas);
    echo json_encode($tblVenta);
}
