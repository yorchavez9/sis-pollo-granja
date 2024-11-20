<?php

class ControladorSucursal
{

    /*=============================================
	REGISTRO DE CATEGORIA
	=============================================*/
    static public function ctrCrearSucursal()
    {
        $tabla = "sucursales";
        $datos = array(
            "nombre_sucursal" => $_POST["nombre_sucursal"],
            "direccion" => $_POST["direccion"],
            "telefono" => $_POST["telefono"]
        );
        $respuesta = ModeloSucursal::mdlIngresarSucursal($tabla,    $datos);
        if ($respuesta == "ok") {
            echo json_encode("ok");
        } else {
            echo json_encode("error");
        }
    }

    /*=============================================
	MOSTRAR sucursales
	=============================================*/
    static public function ctrMostrarSucursales($item, $valor)
    {
        $tabla = "sucursales";
        $respuesta = ModeloSucursal::mdlMostrarSucursales($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
	EDITAR CATEGORIA
	=============================================*/

    static public function ctrEditarSucursal()
    {
            $tabla = "sucursales";
            $datos = array(
                "id_sucursal" => $_POST["edit_id_sucursal"],
                "nombre_sucursal" => $_POST["nombre_sucursal"],
                "direccion" => $_POST["direccion"],
                "telefono" => $_POST["telefono"]
            );
            $respuesta = ModeloSucursal::mdlEditarSucursal($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
       
    }

    /*=============================================
	BORRAR CATEGORIA
	=============================================*/
    static public function ctrBorraSucursal()
    {
        if (isset($_POST["delete_id_sucursal"])) {
            $tabla = "sucursales";
            $datos = $_POST["delete_id_sucursal"];
            $respuesta = ModeloSucursal::mdlBorrarSucursal($tabla, $datos);
            if ($respuesta == "ok") {
                echo json_encode("ok");
            }
        }
    }
}
