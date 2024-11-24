<?php

class ControladorUsuarios
{

	/*=============================================
	INGRESO DE USUARIO
	=============================================*/
	static public function ctrIngresoUsuario()
	{
		if (isset($_POST["ingUsuario"])) {
			if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"])) {
				// Encriptar la contraseña
				$encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

				// Tablas involucradas
				$tablaUsuarios = "usuarios";
				$tablaUsuarioRol = "usuario_rol";
				$tablaRol = "rol";

				// Parámetros para el modelo
				$item = "usuario";
				$valor = $_POST["ingUsuario"];

				// Llamada al modelo
				$respuesta = ModeloUsuarios::mdlMostrarUsuarioConRoles($tablaUsuarios, $tablaUsuarioRol, $tablaRol, $item, $valor);

				// Verificar credenciales
				if (!empty($respuesta) && $respuesta[0]["contrasena"] === $encriptar) {
					// Usuario encontrado y credenciales correctas
					$_SESSION["iniciarSesion"] = "ok";
					$_SESSION["id_usuario"] = $respuesta[0]["id_usuario"];
					$_SESSION["nombre_usuario"] = $respuesta[0]["nombre_usuario"];
					$_SESSION["telefono"] = $respuesta[0]["telefono"];
					$_SESSION["correo"] = $respuesta[0]["correo"];
					$_SESSION["usuario"] = $respuesta[0]["usuario"];
					$_SESSION["imagen_usuario"] = $respuesta[0]["imagen_usuario"];
					$_SESSION["roles"] = array_column($respuesta, "nombre_rol"); // Guardar roles en sesión

					echo '<script>window.location = "inicio";</script>';
				} else {
					// Credenciales incorrectas
					echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "¡Usuario o contraseña incorrectos!",
                        icon: "error",
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "ingreso";
                        }
                    });
                </script>';
				}
			}
		}
	}

	/*=============================================
	REGISTRO DE USUARIO
	=============================================*/

	static public function ctrCrearUsuario()
	{

		/* VALIDANDO IMAGEN */
		$ruta = "../vistas/img/usuarios/";
		if (isset($_FILES["imagen"]["tmp_name"])) {
			$extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
			$tipos_permitidos = array("jpg", "jpeg", "png", "gif");
			if (in_array(strtolower($extension), $tipos_permitidos)) {
				$nombre_imagen = date("YmdHis") . rand(1000, 9999);
				$ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
				if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen)) {
				} else {
				}
			} else {
			}
		}

		$tabla = "usuarios";
		$encriptar = crypt($_POST["contrasena"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
		$datos = array(
			"id_sucursal" => $_POST["id_sucursal"],
			"nombre_usuario" => $_POST["nombre_usuario"],
			"telefono" => $_POST["telefono"],
			"correo" => $_POST["correo"],
			"usuario" => $_POST["usuario"],
			"contrasena" => $encriptar,
			"imagen_usuario" => $ruta_imagen
		);
		$respuesta = ModeloUsuarios::mdlIngresarUsuario($tabla,	$datos);
		if ($respuesta == "ok") {
			echo json_encode("ok");
		} else {
			echo json_encode("error");
		}
	}

	/*=============================================
	MOSTRAR USUARIO
	=============================================*/

	static public function ctrMostrarUsuarios($item, $valor)
	{

		$tablaSucursal = "sucursales";
		$tablausuario = "usuarios";

		$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tablaSucursal, $tablausuario, $item, $valor);

		return $respuesta;
	}


	/*=============================================
	EDITAR USUARIO
	=============================================*/

	static public function ctrEditarUsuario()
	{

		/* ============================
            VALIDANDO IMAGEN
            ============================ */
		$ruta = "../vistas/img/usuarios/";
		$ruta_imagen = $_POST["edit_imagenActualUsuario"];
		if (isset($_FILES["edit_imagen"]["tmp_name"]) && !empty($_FILES["edit_imagen"]["tmp_name"])) {
			if (file_exists($ruta_imagen)) {
				unlink($ruta_imagen);
			}
			$extension = pathinfo($_FILES["edit_imagen"]["name"], PATHINFO_EXTENSION);
			$tipos_permitidos = array("jpg", "jpeg", "png", "gif");
			if (in_array(strtolower($extension), $tipos_permitidos)) {
				$nombre_imagen = date("YmdHis") . rand(1000, 9999);
				$ruta_imagen = $ruta . $nombre_imagen . "." . $extension;
				if (move_uploaded_file($_FILES["edit_imagen"]["tmp_name"], $ruta_imagen)) {

				} else {

				}
			} else {

			}
		}

		$tabla = "usuarios";
		if ($_POST["edit_contrasena"] != "") {
			$encriptar = crypt($_POST["edit_contrasena"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
		} else {
			$encriptar = $_POST["edit_actualContrasena"];
		}

		$datos = array(
			"id_usuario" => $_POST["edit_idUsuario"],
			"id_sucursal" => $_POST["edit_idSucursal"],
			"nombre_usuario" => $_POST["edit_nombre"],
			"telefono" => $_POST["edit_telefono"],
			"correo" => $_POST["edit_correo"],
			"usuario" => $_POST["edit_usuario"],
			"contrasena" => $encriptar,
			"imagen_usuario" => $ruta_imagen
		);

		$respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

		if ($respuesta == "ok") {

			echo json_encode("ok");
		}
	}

	/*=============================================
	BORRAR USUARIO
	=============================================*/

	static public function ctrBorrarUsuario()
	{

		if (isset($_POST["deleteUserId"])) {

			$tabla = "usuarios";

			$datos = $_POST["deleteUserId"];

			if ($_POST["deleteRutaUser"] != "") {
				// Verificar si el archivo existe y eliminarlo
				if (file_exists($_POST["deleteRutaUser"])) {
					unlink($_POST["deleteRutaUser"]);
				} else {
					// El archivo no existe
					echo "El archivo a eliminar no existe.";
				}
			}



			$respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $datos);

			if ($respuesta == "ok") {

				echo json_encode("ok");
			}
		}
	}
}
