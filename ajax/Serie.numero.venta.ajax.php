<?php
require_once "../modelos/Ventas.modelo.php";
require_once "../controladores/Ventas.controlador.php";

if (isset($_POST["tipoComprobante"])) {
    $item = null;
    $valor = $_POST["tipoComprobante"];

    $SerieNumero = ControladorVenta::ctrMostrarSerieNumero($item, $valor);
    return $SerieNumero;
}
