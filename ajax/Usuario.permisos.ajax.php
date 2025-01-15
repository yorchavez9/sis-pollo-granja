<?php

require_once "../controladores/UsuarioRoles.controlador.php";
require_once "../modelos/UsuarioRoles.modelo.php";

class AjaxPermisos
{

    /*=============================================
	EDITAR PERMISOS
	=============================================*/
    public $idCategoria;
    public function ajaxEditarCategoria()
    {
        $item = "id_categoria";
        $valor = $this->idCategoria;
        $respuesta = ControladorUsuarioRoles::ctrMostrarUsuarioRoles($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR PERMISO
=============================================*/
if (isset($_POST["idCategoria"])) {
    $editar = new AjaxPermisos();
    $editar->idCategoria = $_POST["idCategoria"];
    $editar->ajaxEditarCategoria();
}

//GUARDAR PERSMISO
elseif (isset($_POST["id_usuario_permiso"])) {
    $crear = new ControladorUsuarioRoles();
    $crear->ctrCrearUsuarioRoles();
}

//ACTUALIZAR PERMISO
elseif(isset($_POST["edit_id_categoria"])){
    $actualizar = new ControladorUsuarioRoles();
    $actualizar->ctrEditarUsuarioRoles();
}

//ELIMINAR PERMISO
elseif(isset($_POST["idUsuarioPermisoDelete"])){
    $borrar = new ControladorUsuarioRoles();
    $borrar->ctrBorrarUsuarioRoles();
}

//MOSTRAR PERMISO
else{
    $item = null;
    $valor = null;
    $reponse = ControladorUsuarioRoles::ctrMostrarUsuarioRoles($item, $valor);
  
    $tabla = array();
    
    foreach ($reponse as $key => $data) {
        $fila = array(
            'id_usuario' => $data['id_usuario'],
            'nombre_usuario' => $data['nombre_usuario'],
            'usuario' => $data['usuario'],
            'correo' => $data['correo'],
            'imagen_usuario' => $data['imagen_usuario'],
            'estado_usuario' => $data['estado_usuario'],
            'nombre_rol' => $data['nombre_rol'],
            'modulos' => $data['modulos'],
            'id_rol' => $data['id_rol']
        );
        $tabla[] = $fila;
    }
    echo json_encode($tabla);
}
?>

