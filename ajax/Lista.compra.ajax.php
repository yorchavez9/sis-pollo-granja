<?php

require_once "../controladores/Lista.compra.controlador.php";
require_once "../modelos/Lista.compra.modelo.php";
require_once "../controladores/Compra.controlador.php";
require_once "../modelos/Compra.modelo.php";


//BORRAR COMPRA 
if(isset($_POST["idEgresoDelete"])){

    $borrarCompra = new ControladorCompra();
    $borrarCompra->ctrBorrarCompra();

}else if(isset($_POST["id_egreso_pagar"])){
    $pagar = new ControladorListaCompra();
    $pagar->ctrActualizarDeudaEgreso();
}
//MOSTRAR COMPRA EN LA TABLA
else{

    $item = null;
    $valor = null;
    $mostrarEgresos = ControladorListaCompra::ctrMostrarListaEgreso($item, $valor);
    $tblEgreso = array();
    foreach ($mostrarEgresos as $key => $egreso) {
        
        $fila = array(
            'id_detalle_egreso' => $egreso['id_detalle_egreso'],
            'id_egreso' => $egreso['id_egreso'],
            'id_producto' => $egreso['id_producto'],
            'id_persona' => $egreso['id_persona'],
            'precio_compra' => $egreso['precio_compra'],
            'precio_venta' => $egreso['precio_venta'],
            'numero_javas' => $egreso['numero_javas'],
            'numero_aves' => $egreso['numero_aves'],
            'id_egreso' => $egreso['id_egreso'],
            'id_usuario' => $egreso['id_usuario'],
            'fecha_egre' => $egreso['fecha_egre'],
            'hora_egreso' => $egreso['hora_egreso'],
            'tipo_comprobante' => $egreso['tipo_comprobante'],
            'serie_comprobante' => $egreso['serie_comprobante'],
            'num_comprobante' => $egreso['num_comprobante'],
            'impuesto' => $egreso['impuesto'],
            'total_compra' => $egreso['total_compra'],
            'total_pago' => $egreso['total_pago'],
            'subTotal' => $egreso['subTotal'],
            'igv' => $egreso['igv'],
            'tipo_pago' => $egreso['tipo_pago'],
            'estado_pago' => $egreso['estado_pago'],
            'pago_e_y' => $egreso['pago_e_y'],
            'fecha_egreso' => $egreso['fecha_egreso'],
            'razon_social' => $egreso['razon_social']
        );
        
        
        $tblEgreso[] = $fila;
    }
    
    
    echo json_encode($tblEgreso);
}
