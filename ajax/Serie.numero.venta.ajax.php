<?php
require_once "../modelos/Ventas.modelo.php";
require_once "../controladores/Ventas.controlador.php";

if($_POST["tipo_comprobante"] == '' || $_POST["tipo_comprobante"] == null){
    echo json_encode("No existe comprobante");
}
else if (isset($_POST["tipo_comprobante"]) && isset($_POST["folio_inicial"])) {

    $item = null;
    $valor = $_POST["tipo_comprobante"];
    $folioInicial = $_POST["folio_inicial"];
    $SerieNumero = ControladorVenta::ctrMostrarSerieNumero($item, $valor, $folioInicial);
    echo json_encode($SerieNumero);
}
