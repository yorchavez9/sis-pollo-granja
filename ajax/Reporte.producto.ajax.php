<?php

require_once "../controladores/Producto.controlador.php";
require_once "../modelos/Producto.modelo.php";

if(
    (isset($_POST["filtro_categoria"]) && !empty($_POST["filtro_categoria"])) ||
    (isset($_POST["filtro_estado"]) && ($_POST["filtro_estado"] !== '')) ||
    (isset($_POST["filtro_precio_min"]) && !empty($_POST["filtro_precio_min"])) ||
    (isset($_POST["filtro_precio_max"]) && !empty($_POST["filtro_precio_max"])) ||
    (isset($_POST["filtro_fecha_desde"]) && !empty($_POST["filtro_fecha_desde"])) ||
    (isset($_POST["filtro_fecha_hasta"]) && !empty($_POST["filtro_fecha_hasta"]))
    ){

        $respuesta = ControladorProducto::ctrReporteProductos();
        echo json_encode($respuesta);

}else{
    $item = null;
    $valor = null;
    $mostrarProductos = ControladorProducto::ctrMostrarProductos($item, $valor);

    $tablaProductos = array();

    foreach ($mostrarProductos as $key => $usuario) {

        $fila = array(
            'id_producto' => $usuario['id_producto'],
            'id_categoria' => $usuario['id_categoria'],
            'nombre_categoria' => $usuario['nombre_categoria'],
            'codigo_producto' => $usuario['codigo_producto'],
            'nombre_producto' => $usuario['nombre_producto'],
            'precio_producto' => $usuario['precio_producto'],
            'stock_producto' => $usuario['stock_producto'],
            'fecha_vencimiento' => $usuario['fecha_vencimiento'],
            'descripcion_producto' => $usuario['descripcion_producto'],
            'imagen_producto' => $usuario['imagen_producto'],
            'estado_producto' => $usuario['estado_producto'],
            'fecha_producto' => $usuario['fecha_producto']
        );


        $tablaProductos[] = $fila;
    }


    echo json_encode($tablaProductos);

}
