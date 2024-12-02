<?php

require_once "../controladores/Configuraracion.sistema.controlador.php";
require_once "../modelos/Configuracion.sistema.modelo.php";

class AjaxConfiguracionSistema
{
    /*=============================================
	EDITAR CONFIGURACION
	=============================================*/
    public $idSistema;
    public function ajaxEditarrespuesta()
    {
        $item = "id_img";
        $valor = $this->idSistema;
        $respuesta = ControladorConfiguracionSistema::ctrMostrarConfiguracionSistema($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR CONSFIGURACION
=============================================*/
if (isset($_POST["idSistema"])) {

    $editar = new AjaxConfiguracionSistema();
    $editar->idSistema = $_POST["idSistema"];
    $editar->ajaxEditarrespuesta();
}

/*=============================================
GUARDAR CONFIGURACION
=============================================*/ 
elseif (isset($_POST["nombre_sis"])) {
    $crear = new ControladorConfiguracionSistema();
    $crear->ctrCrearConfiguracionSistema();
}

/*=============================================
ACTUALIZAR CONFIGURACION 
=============================================*/ 
elseif (isset($_POST["edit_id_configuracion_sistema"])) {

    $edit = new ControladorConfiguracionSistema();
    $edit->ctrEditarConfiguracionSistema();
}

/*=============================================
ELIMINAR CONFIGURACION 
=============================================*/ 
elseif (isset($_POST["idSistemaDelete"])) {

    $borrar = new ControladorConfiguracionSistema();
    $borrar->ctrBorrarConfiguracionSistema();
}

/*=============================================
MOSTRAR CONFIGURACION 
=============================================*/ 
else {

    $item = null;
    $valor = null;
    $respuesta = ControladorConfiguracionSistema::ctrMostrarConfiguracionSistema($item, $valor);
    $tabla = array();
    foreach ($respuesta as $key => $value) {
        $fila = array(
            'id_img' => $value['id_img'],
            'nombre' => $value['nombre'],
            'icon_pestana' => $value['icon_pestana'],
            'img_sidebar' => $value['img_sidebar'],
            'img_sidebar_min' => $value['img_sidebar_min'],
            'img_login' => $value['img_login'],
            'icon_login' => $value['icon_login'],
            'fecha' => $value['fecha']
        );
        $tabla[] = $fila;
    }
    echo json_encode($tabla);
}
