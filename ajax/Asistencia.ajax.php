<?php
require_once "../controladores/Asistencia.controlador.php";
require_once "../modelos/Asistencia.modelo.php";

class AjaxAsistencia {

    /*=============================================
    EDITAR ASISTENCIA
    =============================================*/
    public $fechaAsistencia;

    public function ajaxEditarAsistencia() {
        $item = "fecha_asistencia";
        $valor = $this->fechaAsistencia;
        $respuesta = ControladorAsistencia::ctrMostrarAsistencia($item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    VER LISTA ASISTENCIA
    =============================================*/
    public $fechaAsistenciaVer;

    public function ajaxVerListaAsistencia() {
        $item = "fecha_asistencia";
        $valor = $this->fechaAsistenciaVer;
        $respuesta = ControladorAsistencia::ctrMostrarListaAsistencia($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR ASISTENCIA
=============================================*/
if(isset($_POST["fechaAsistencia"])) {
    $editar = new AjaxAsistencia();
    $editar->fechaAsistencia = $_POST["fechaAsistencia"];
    $editar->ajaxEditarAsistencia();
}

/*=============================================
VER LISTA ASISTENCIA
=============================================*/
if(isset($_POST["fechaAsistenciaVer"])) {
    $ver = new AjaxAsistencia();
    $ver->fechaAsistenciaVer = $_POST["fechaAsistenciaVer"];
    $ver->ajaxVerListaAsistencia();
}

/*=============================================
GUARDAR ASISTENCIA
=============================================*/
if(isset($_POST["fecha_asistencia_a"])) {
    $crearAsistencia = new ControladorAsistencia();
    $crearAsistencia->ctrCrearAsistencia();
}

/*=============================================
ACTUALIZAR ASISTENCIA
=============================================*/
if(isset($_POST["fecha_asistencia"])) {
    $actualizarAsistencia = new ControladorAsistencia();
    $actualizarAsistencia->ctrActualizarAsistencia();
}

/*=============================================
BORRAR ASISTENCIA
=============================================*/
if(isset($_POST["fechaAsistenciaDelete"])) {
    $borrarAsistencia = new ControladorAsistencia();
    $borrarAsistencia->ctrBorrarAsistencia();
}

/*=============================================
MOSTRAR ASISTENCIAS
=============================================*/
if(!isset($_POST) || empty($_POST)) {
    $item = null;
    $valor = null;
    $asistencias = ControladorAsistencia::ctrMostrarAsistencia($item, $valor);
    echo json_encode($asistencias);
}