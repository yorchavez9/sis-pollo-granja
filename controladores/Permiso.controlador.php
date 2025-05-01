<?php
class ControladorPermiso
{
    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    static public function ctrMostrarPermisos()
    {
        $respuesta = ModeloPermiso::mdlMostrarPermisos();
        echo $respuesta;
    }

    /*=============================================
    GUARDAR PERMISO
    =============================================*/
    static public function ctrGuardarPermiso()
    {
        if (isset($_POST['id_usuario']) && isset($_POST['id_rol']) && isset($_POST['permisos'])) {
            $idUsuario = $_POST['id_usuario'];
            $idRol = $_POST['id_rol'];
            $permisos = json_decode($_POST['permisos'], true);
            
            $respuesta = ModeloPermiso::mdlGuardarPermiso($idUsuario, $idRol, $permisos);
            echo $respuesta;
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
        }
    }

    /*=============================================
    ACTUALIZAR PERMISO
    =============================================*/
    static public function ctrActualizarPermiso()
    {
        if ( isset($_POST['id_usuario']) && isset($_POST['id_rol']) && isset($_POST['permisos'])) {
            $idUsuario = $_POST['id_usuario'];
            $idRol = $_POST['id_rol'];
            $permisos = json_decode($_POST['permisos'], true);
            
            $respuesta = ModeloPermiso::mdlActualizarPermiso($idUsuario, $idRol, $permisos);
            echo $respuesta;
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
        }
    }

    /*=============================================
    ELIMINAR PERMISO
    =============================================*/
    static public function ctrEliminarPermiso()
    {
        if (isset($_POST['id_rol'])) {
            $idRol = $_POST['id_rol'];
            
            $respuesta = ModeloPermiso::mdlEliminarPermiso($idRol);
            echo $respuesta;
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Datos incompletos"
            ]);
        }
    }
}