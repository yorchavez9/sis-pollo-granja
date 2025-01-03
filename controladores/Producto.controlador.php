<?php

class ControladorProducto
{

	/*=============================================
	REGISTRO DE PRODUCTO
	=============================================*/

	static public function ctrCrearProducto()
	{

		/* VALIDANDO IMAGEN */
		$ruta = "../vistas/img/productos/";
		$ruta_imagen = "";
		if (isset($_FILES["imagen_producto"]["tmp_name"])) {
			$extension = pathinfo($_FILES["imagen_producto"]["name"], PATHINFO_EXTENSION);
			$tipos_permitidos = array("jpg", "jpeg", "png", "gif");
			if (in_array(strtolower($extension), $tipos_permitidos)) {
				$nombre_imagen = date("YmdHis") . rand(1000, 9999);
				$ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
				if (move_uploaded_file($_FILES["imagen_producto"]["tmp_name"], $ruta_imagen)) {
				} else {
				}
			} else {
			}
		}


		$tabla = "productos";


		$datos = array(
			"id_categoria" => $_POST["id_categoria_P"],
			"codigo_producto" => $_POST["codigo_producto"],
			"nombre_producto" => $_POST["nombre_producto"],
			"precio_producto" => $_POST["precio_producto"],
			"stock_producto" => $_POST["stock_producto"],
			"fecha_vencimiento" => $_POST["fecha_vencimiento"],
			"descripcion_producto" => $_POST["descripcion_producto"],
			"imagen_producto" => $ruta_imagen
		);

        $respuesta = ModeloProducto::mdlIngresarProducto($tabla, $datos);

        if ($respuesta == "ok") {

            $response = array(
                "mensaje" => "Producto guardado correctamente",
                "estado" => "ok"
            );

            echo json_encode($response);

        } else {

            $response = array(
                "mensaje" => "Error al guardar el producto",
                "estado" => "error"
            );

            echo json_encode($response);

        }
    }

	/*=============================================
	MOSTRAR PRODUCTO
	=============================================*/

	static public function ctrMostrarProductos($item, $valor)
	{
		$tablaC = "categorias";
		$tablaP = "productos";
		$respuesta = ModeloProducto::mdlMostrarProducto($tablaC, $tablaP, $item, $valor);
		return $respuesta;
	}

	/*=============================================
    MOSTRAR REPORTE DE PRODUCTOS
    =============================================*/

	public static function ctrReporteProductos()
	{
		$tabla_categoria = "categorias";
		$tabla_producto = "productos";

		// Capturamos los filtros
		$filtro_categoria = isset($_POST['filtro_categoria']) ? $_POST['filtro_categoria'] : null;
		$filtro_estado = isset($_POST['filtro_estado']) ? $_POST['filtro_estado'] : null;
		$filtro_precio_min = isset($_POST['filtro_precio_min']) ? $_POST['filtro_precio_min'] : null;
		$filtro_precio_max = isset($_POST['filtro_precio_max']) ? $_POST['filtro_precio_max'] : null;
		$filtro_fecha_desde = isset($_POST['filtro_fecha_desde']) ? $_POST['filtro_fecha_desde'] : null;
		$filtro_fecha_hasta = isset($_POST['filtro_fecha_hasta']) ? $_POST['filtro_fecha_hasta'] : null;

		// Pasamos los filtros al modelo
		$respuesta = ModeloProducto::mdlReporteProductos($tabla_categoria, $tabla_producto, $filtro_categoria, $filtro_estado, $filtro_precio_min, $filtro_precio_max, $filtro_fecha_desde, $filtro_fecha_hasta);

		return $respuesta;
	}

	public static function ctrReporteProductosPDF()
	{
		$tabla_categoria = "categorias";
		$tabla_producto = "productos";

		// Capturamos los filtros
		$filtro_categoria = isset($_GET['filtro_categoria']) ? $_GET['filtro_categoria'] : null;
		$filtro_estado = isset($_GET['filtro_estado']) ? $_GET['filtro_estado'] : null;
		$filtro_precio_min = isset($_GET['filtro_precio_min']) ? $_GET['filtro_precio_min'] : null;
		$filtro_precio_max = isset($_GET['filtro_precio_max']) ? $_GET['filtro_precio_max'] : null;
		$filtro_fecha_desde = isset($_GET['filtro_fecha_desde']) ? $_GET['filtro_fecha_desde'] : null;
		$filtro_fecha_hasta = isset($_GET['filtro_fecha_hasta']) ? $_GET['filtro_fecha_hasta'] : null;

		// Pasamos los filtros al modelo
		$respuesta = ModeloProducto::mdlReporteProductos($tabla_categoria, $tabla_producto, $filtro_categoria, $filtro_estado, $filtro_precio_min, $filtro_precio_max, $filtro_fecha_desde, $filtro_fecha_hasta);

		return $respuesta;
	}
	/*=============================================
	MOSTRAR PRODUCTO POR FECHA DE VENCIMIENTO
	=============================================*/

	static public function ctrMostrarProductosFechaVencimientos($item, $valor)
	{

		$tablaC = "categorias";
		$tablaP = "productos";

		$respuesta = ModeloProducto::mdlMostrarProductoFechaVencimiento($tablaC,
			$tablaP,
			$item,
			$valor
		);

		return $respuesta;
	}

	/*=============================================
	MOSTRAR PRODUCTOS NUEVOS
	=============================================*/

	static public function ctrMostrarProductosNuevos($item, $valor)
	{
		$tablaC = "categorias";
		$tablaP = "productos";
		$respuesta = ModeloProducto::mdlMostrarProductoNuevos($tablaC, $tablaP, $item, $valor);
		return $respuesta;
	}

	/*=============================================
	EDITAR PRODUCTO
	=============================================*/

	static public function ctrEditarProducto()
	{
		if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["edit_nombre_producto"])) {
			/* ============================
            VALIDANDO IMAGEN
            ============================ */
            $ruta = "../vistas/img/productos/";
            $ruta_imagen = $_POST["edit_imagen_actual_p"];
            if (isset($_FILES["edit_imagen_producto"]["tmp_name"]) && !empty($_FILES["edit_imagen_producto"]["tmp_name"])) {
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen);
                }
                $extension = pathinfo($_FILES["edit_imagen_producto"]["name"], PATHINFO_EXTENSION);
                $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
                if (in_array(strtolower($extension), $tipos_permitidos)) {
                    $nombre_imagen = date("YmdHis") . rand(1000, 9999);
                    $ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
                    if (move_uploaded_file($_FILES["edit_imagen_producto"]["tmp_name"], $ruta_imagen)) {
                        /* echo "Imagen subida correctamente."; */
                    } else {
                        /* echo "Error al subir la imagen."; */
                    }
                } else {

                    /* echo "Solo se permiten archivos de imagen JPG, JPEG, PNG o GIF."; */
                }
            }

			$tabla = "productos";
			$datos = array(
				"id_producto" => $_POST["edit_id_producto"],
				"id_categoria" => $_POST["edit_id_categoria_p"],
				"codigo_producto" => $_POST["edit_codigo_producto"],
				"nombre_producto" => $_POST["edit_nombre_producto"],
				"precio_producto" => $_POST["edit_precio_producto"],
				"stock_producto" => $_POST["edit_stock_producto"],
				"fecha_vencimiento" => $_POST["edit_fecha_vencimiento"],
				"descripcion_producto" => $_POST["edit_descripcion_producto"],
				"imagen_producto" => $ruta_imagen
			);

			$respuesta = ModeloProducto::mdlEditarProducto($tabla, $datos);

			if ($respuesta == "ok") {
				echo json_encode("ok");
			}

		} else {

			echo json_encode("error");
		}
	}

	/*=============================================
	BORRAR PRODUCTO
	=============================================*/

	static public function ctrBorrarProducto()
	{

		if (isset($_POST["idProductoDelete"])) {

			$tabla = "productos";

			$datos = $_POST["idProductoDelete"];

			if ($_POST["deleteRutaImagenProducto"] != "") {
				// Verificar si el archivo existe y eliminarlo
				if (file_exists($_POST["deleteRutaImagenProducto"])) {
					unlink($_POST["deleteRutaImagenProducto"]);
				} else {
					// El archivo no existe
					echo "El archivo a eliminar no existe.";
				}
			}
			
			

			$respuesta = ModeloProducto::mdlBorrarProducto($tabla, $datos);

			if ($respuesta == "ok") {

				echo json_encode("ok");
			}
		}
	}
}
