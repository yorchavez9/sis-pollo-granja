<?php

class ControladorConfiguracionSistema
{


    /*=============================================
	REGISTRO DE CONFIGURACION TICKET
	=============================================*/

    static public function ctrCrearConfiguracionSistema()
    {
        $rutaBase = "../vistas/img/sistema/";
        $rutasImagenes = [];
        $camposArchivos = [
            "icon_pestana_sis" => "icon_pestana",
            "img_sidebar_sis" => "img_sidebar",
            "img_sidebar_min" => "img_sidebar_min",
            "img_login" => "img_login",
            "icon_login" => "icon_login"
        ];

        // Procesar las imágenes
        foreach ($camposArchivos as $campoFormulario => $campoBD) {
            if (isset($_FILES[$campoFormulario]["tmp_name"]) && !empty($_FILES[$campoFormulario]["tmp_name"])) {
                $archivo = $_FILES[$campoFormulario];
                $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
                $tiposPermitidos = ["jpg", "jpeg", "png", "gif"];
                if (in_array(strtolower($extension), $tiposPermitidos)) {
                    $nombreImagen = date("YmdHis") . rand(1000, 9999) . "." . $extension;
                    $rutaImagen = $rutaBase . $nombreImagen;
                    if (move_uploaded_file($archivo["tmp_name"], $rutaImagen)) {
                        $rutasImagenes[$campoBD] = $rutaImagen;
                    } else {
                        echo json_encode([
                            "estado" => "error",
                            "mensaje" => "Error al guardar el archivo: $campoFormulario."
                        ]);
                        exit;
                    }
                } else {
                    echo json_encode([
                        "estado" => "error",
                        "mensaje" => "Formato de archivo no permitido para $campoFormulario. Solo se permiten JPG, JPEG, PNG, GIF."
                    ]);
                    exit;
                }
            } else {
                $rutasImagenes[$campoBD] = null;
            }
        }

        // Agregar el nombre al arreglo
        $nombre_sis = isset($_POST['nombre_sis']) ? $_POST['nombre_sis'] : null;

        $tabla = "config_sistema";
        $datos = array_merge(["nombre" => $nombre_sis], $rutasImagenes);

        $respuesta = ModeloConfiguracionSistema::mdlIngresarConfiguracionSistema($tabla, $datos);

        if ($respuesta == "ok") {
            $response = array(
                    "mensaje" => "Configuración guardada correctamente",
                    "estado" => "ok"
                );
            echo json_encode($response);
        } else {
            $response = array(
                "mensaje" => "Error al guardar la configuración",
                "estado" => "error"
            );
            echo json_encode($response);
        }
    }


    /*=============================================
	MOSTRAR PRODUCTO
	=============================================*/
    static public function ctrMostrarConfiguracionSistema($item, $valor)
    {
        $tabla = "config_sistema";
        $respuesta = ModeloConfiguracionSistema::mdlMostrarConfiguracionSistema($tabla, $item, $valor);
        return $respuesta;
    }


    /*=============================================
	EDITAR CONFIGURACION TICKET
	=============================================*/
    static public function ctrEditarConfiguracionSistema()
    {
        // Rutas base para las imágenes
        $ruta_base = "../vistas/img/sistema/";

        // Array para almacenar las rutas de las imágenes actualizadas
        $rutas_imagenes = array(
                "icon_pestana" => $_POST["actual_icon_pestana_sis"],
                "img_sidebar" => $_POST["actual_img_sidebar_sis"],
                "img_sidebar_min" => $_POST["actual_img_sidebar_min"],
                "img_login" => $_POST["actual_img_login"],
                "icon_login" => $_POST["actual_icon_login"]
            );

        // Archivos enviados
        $archivos = array(
            "icon_pestana" => "edit_icon_pestana_sis",
            "img_sidebar" => "edit_img_sidebar_sis",
            "img_sidebar_min" => "edit_img_sidebar_min",
            "img_login" => "edit_img_login",
            "icon_login" => "edit_icon_login"
        );

        // Procesar cada archivo
        foreach ($archivos as $key => $input_name) {
            if (isset($_FILES[$input_name]["tmp_name"]) && !empty($_FILES[$input_name]["tmp_name"])) {
                // Eliminar la imagen actual si existe
                if (file_exists($rutas_imagenes[$key])) {
                    unlink($rutas_imagenes[$key]);
                }

                $extension = pathinfo($_FILES[$input_name]["name"], PATHINFO_EXTENSION);
                $tipos_permitidos = array("jpg", "jpeg", "png", "gif");

                if (in_array(strtolower($extension), $tipos_permitidos)) {
                    $nombre_imagen = date("YmdHis") . rand(1000, 9999);
                    $ruta_imagen = $ruta_base . $nombre_imagen . "." . $extension;

                    if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $ruta_imagen)) {
                        $rutas_imagenes[$key] = $ruta_imagen;
                    }
                }
            }
        }

        // Preparar datos para el modelo
        $tabla = "config_sistema";

        $datos = array(
            "id_img" => $_POST["edit_id_configuracion_sistema"],
            "nombre" => $_POST["edit_nombre_sis"],
            "icon_pestana" => $rutas_imagenes["icon_pestana"],
            "img_sidebar" => $rutas_imagenes["img_sidebar"],
            "img_sidebar_min" => $rutas_imagenes["img_sidebar_min"],
            "img_login" => $rutas_imagenes["img_login"],
            "icon_login" => $rutas_imagenes["icon_login"]
        );

        $respuesta = ModeloConfiguracionSistema::mdlEditarConfiguracionSistema($tabla, $datos);

        if ($respuesta == "ok") {
            echo json_encode("ok");
        } else {
            echo json_encode("error");
        }
    }


    /*=============================================
	BORRAR CONFIGURACION TICKET
	=============================================*/
    static public function ctrBorrarConfiguracionSistema()
    {
        if (isset($_POST["idSistemaDelete"])) {
            $tabla = "config_sistema";
            $datos = $_POST["idSistemaDelete"];
            $rutas_a_eliminar = array(
                $_POST["delete_url_icon_pestana"],
                $_POST["delete_url_img_sidebar"],
                $_POST["delete_url_img_sidebar_min"],
                $_POST["delete_url_img_login"],
                $_POST["delete_url_icon_login"]
            );

            foreach ($rutas_a_eliminar as $ruta) {
                if ($ruta != "" && file_exists($ruta)) {
                    if (unlink($ruta)) {
                      
                    } else {
                        
                    }
                } else {
                    echo json_encode("error");
                }
            }

            $respuesta = ModeloConfiguracionSistema::mdlBorrarConfiguracionSistema($tabla, $datos);


            if ($respuesta == "ok") {
                echo json_encode("ok");
            } else {
                echo json_encode("error");
            }
        }
    }
}
