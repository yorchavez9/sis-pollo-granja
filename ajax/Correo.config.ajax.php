<?php

require_once "../controladores/Correo.config.controlador.php";
require_once "../modelos/Correo.config.modelo.php";

class AjaxCorreoConfig
{

    /*=============================================
	EDITAR CONFIGURACION CORREO
	=============================================*/
    public $idCorreoConfig;
    public function ajaxEditarConfigCorreo()
    {
        $item = "id";
        $valor = $this->idCorreoConfig;
        $respuesta = ControladorCorreoConfig::ctrMostrarConfigCorreo($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR CONFIGURACION CORREO
=============================================*/
if (isset($_POST["idCorreoConfig"])) {
    $editar = new AjaxCorreoConfig();
    $editar->idCorreoConfig = $_POST["idCorreoConfig"];
    $editar->ajaxEditarConfigCorreo();
}

/*=============================================
GUARDAR CONFIGURACION CORREO
=============================================*/
elseif (isset($_POST["id_usuario"])) {
    $crearConfigCorreo = new ControladorCorreoConfig();
    $crearConfigCorreo->ctrCrearConfigCorreo();
}

/*=============================================
ACTUALIAZAR CONFIGURACION CORREO
=============================================*/
elseif (isset($_POST["edit_id_correo_config"])) {
    $editConfigCorreo = new ControladorCorreoConfig();
    $editConfigCorreo->ctrEditarConfigCorreo();
}

/*=============================================
ELIMINAR CONFIGURACION CORREO
=============================================*/
elseif (isset($_POST["idCorreoConfigDelete"])) {
    $borrarCorreoConfig = new ControladorCorreoConfig();
    $borrarCorreoConfig->ctrBorraConfigCorreo();
}

/*=============================================
MOSTRAR CONFIGURACION CORREO
=============================================*/
else {
    $item = null;
    $valor = null;
    $mostrarConfiguraciones = ControladorCorreoConfig::ctrMostrarConfigCorreo($item, $valor);
    $tablaConfiguracion = array();
    foreach ($mostrarConfiguraciones as $key => $usuario) {
        $fila = array(
            'id' => $usuario['id'],
            'id_usuario' => $usuario['id_usuario'],
            'smtp' => $usuario['smtp'],
            'usuario' => $usuario['usuario'],
            'password' => $usuario['password'],
            'puerto' => $usuario['puerto'],
            'correo_remitente' => $usuario['correo_remitente'],
            'nombre_remitente' => $usuario['nombre_remitente'],
            'fecha' => $usuario['fecha']
        );

        $tablaConfiguracion[] = $fila;
    }
    echo json_encode($tablaConfiguracion);
}
