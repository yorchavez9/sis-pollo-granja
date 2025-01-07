<?php

require_once "../controladores/Correo.config.controlador.php";
require_once "../modelos/Correo.config.modelo.php";

class AjaxCorreoConfig
{

    /*=============================================
	EDITAR CONFIGURACION CORREO
	=============================================*/
    public $idCategoria;
    public function ajaxEditarCategoria()
    {
        $item = "id_categoria";
        $valor = $this->idCategoria;
        $respuesta = ControladorCorreoConfig::ctrMostrarConfigCorreo($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR CONFIGURACION CORREO
=============================================*/
if (isset($_POST["idCategoria"])) {
    $editar = new AjaxCorreoConfig();
    $editar->idCategoria = $_POST["idCategoria"];
    $editar->ajaxEditarCategoria();
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
elseif (isset($_POST["edit_id_categoria"])) {
    $editCategoria = new ControladorCorreoConfig();
    $editCategoria->ctrEditarConfigCorreo();
}

/*=============================================
ELIMINAR CONFIGURACION CORREO
=============================================*/
elseif (isset($_POST["deleteIdCategoria"])) {
    $borrarCategoria = new ControladorCorreoConfig();
    $borrarCategoria->ctrBorraConfigCorreo();
}

/*=============================================
MOSTRAR CONFIGURACION CORREO
=============================================*/
else {
    $item = null;
    $valor = null;
    $mostrarCategorias = ControladorCorreoConfig::ctrMostrarConfigCorreo($item, $valor);
    $tablaUsuarios = array();
    foreach ($mostrarCategorias as $key => $usuario) {
        $fila = array(
            'id_categoria' => $usuario['id_categoria'],
            'nombre_categoria' => $usuario['nombre_categoria'],
            'descripcion' => $usuario['descripcion'],
            'fecha' => $usuario['fecha']
        );

        $tablaUsuarios[] = $fila;
    }
    echo json_encode($tablaUsuarios);
}
