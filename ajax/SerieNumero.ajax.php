<?php
require_once "../modelos/Compra.modelo.php";
require_once "../controladores/Compra.controlador.php";

if (isset($_POST["tipoComprobante"])) {
    $item = null;
    $valor = $_POST["tipoComprobante"];

    $SerieNumero = ControladorCompra::ctrMostrarSerieNumero($item, $valor);
    return $SerieNumero;
}
