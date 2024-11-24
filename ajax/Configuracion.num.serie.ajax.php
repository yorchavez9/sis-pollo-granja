<?php

require_once "../controladores/Serie.num.controlador.php";
require_once "../modelos/Serie.num.modelo.php";

class AjaxSerieNumero
{
    /*=============================================
	EDITAR SERIE NUMERO
	============================================*/
    public $idSerieNumero;
    public function ajaxEditarSerieNumero()
    {
        $item = "id_serie_num";
        $valor = $this->idSerieNumero;
        $respuesta = ControladorSerieNumero::ctrMostrarSerieNumero($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR CATEGORIA
=============================================*/
if (isset($_POST["idSerieNumero"])) {
    $editar = new AjaxSerieNumero();
    $editar->idSerieNumero = $_POST["idSerieNumero"];
    $editar->ajaxEditarSerieNumero();
}

//GUARDAR SERIE NUMERO
elseif (isset($_POST["tipo_comprobante"])) {
    $crearSerieNumero = new ControladorSerieNumero();
    $crearSerieNumero->ctrCrearSerieNumero();
}

//ACTUALIZAR SERIE NUMERO
elseif (isset($_POST["edit_id_serie_num"])) {
    $editSerieNumero = new ControladorSerieNumero();
    $editSerieNumero->ctrEditarSerieNumero();
}

//ELIMINAR SERIE NUMERO
elseif (isset($_POST["DeleteidSerieNumero"])) {
    $borrarSerieNumero = new ControladorSerieNumero();
    $borrarSerieNumero->ctrBorraSerieNumero();
}

//MOSTRAR SERIES Y NUMEROS
else {
    $item = null;
    $valor = null;
    $mostrarSerieNumero = ControladorSerieNumero::ctrMostrarSerieNumero($item, $valor);
    $tablaNumeroSerie = array();
    foreach ($mostrarSerieNumero as $key => $usuario) {
        $fila = array(
            'id_serie_num' => $usuario['id_serie_num'],
            'tipo_comprobante_sn' => $usuario['tipo_comprobante_sn'],
            'serie_prefijo' => $usuario['serie_prefijo'],
            'folio_inicial' => $usuario['folio_inicial'],
            'folio_final' => $usuario['folio_final'],
            'fecha_sn' => $usuario['fecha_sn']
        );
        $tablaNumeroSerie[] = $fila;
    }
    echo json_encode($tablaNumeroSerie);
}
