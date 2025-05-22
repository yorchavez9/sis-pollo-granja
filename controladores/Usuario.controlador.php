<?php
class ControladorUsuarios
{
    /* ============================================
    LOGIN USUARIO
    ============================================ */
    static public function ctrLoginUsuario()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_POST["ingUsuario"], $_POST["ingPassword"])) {
            return self::jsonResponse(false, "Datos incompletos");
        }

        $usuario = trim($_POST["ingUsuario"]);
        $password = $_POST["ingPassword"];

        if (!preg_match('/^[a-zA-Z0-9]+$/', $usuario) || 
            !preg_match('/^[a-zA-Z0-9]+$/', $password)) {
            return self::jsonResponse(false, "Caracteres no válidos");
        }

        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarLoginUsuario($tabla, "usuario", $usuario);

        if (!$respuesta || $respuesta["usuario"] !== $usuario) {
            return self::jsonResponse(false, "Error al ingresar, vuelve a intentarlo");
        }

        if (!password_verify($password, $respuesta["contrasena"])) {
            return self::jsonResponse(false, "Error al ingresar, vuelve a intentarlo");
        }

        if ($respuesta["estado"] != 1) {
            return self::jsonResponse(false, "El usuario está desactivado");
        }

        $_SESSION["iniciarSesion"] = "ok";
        $datosSesion = ModeloUsuarios::mdlObtenerDatosSesion($respuesta["id_usuario"]);
        $roles = ModeloUsuarios::mdlObtenerRolesUsuario($respuesta["id_usuario"]);
        $permisos = ModeloUsuarios::mdlObtenerPermisosUsuario($respuesta["id_usuario"]);

        $_SESSION["usuario"] = $datosSesion;
        $_SESSION["roles"] = $roles;
        $_SESSION["permisos"] = $permisos;
        
        $fecha = date('Y-m-d H:i:s');
        ModeloUsuarios::mdlActualizarUsuario($tabla, "ultimo_login", $fecha, "id_usuario", $respuesta["id_usuario"]);

        return self::jsonResponse(true, "Inicio de sesión exitoso", [
            "redirect" => "inicio",
            "datosUsuario" => $datosSesion,
            "roles" => $roles,
            "permisos" => $permisos,
            "id_usuario" => $respuesta["id_usuario"],
        ]);
    }

    /*=============================================
    REGISTRO DE USUARIO
    =============================================*/
    static public function ctrCrearUsuario()
    {
        $camposRequeridos = ["usuario", "telefono", "correo", "contrasena", "id_sucursal", "nombre_usuario"];
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo])) {
                return self::jsonResponse(false, "El campo $campo es requerido");
            }
        }

        $tabla = "usuarios";
        $usuario = trim($_POST["usuario"]);

        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, "usuario", $usuario);
        
        if ($respuesta["status"] === false) {
            return self::jsonResponse(false, $respuesta["message"]);
        }
        
        if ($respuesta["exists"]) {
            return self::jsonResponse(false, "El nombre de usuario ya está en uso");
        }

        $imagen = self::procesarImagen("imagen", "../vistas/img/usuarios/");

        $datos = [
            "id_sucursal" => (int)$_POST["id_sucursal"],
            "nombre_usuario" => htmlspecialchars(trim($_POST["nombre_usuario"])),
            "telefono" => $_POST["telefono"],
            "correo" => $_POST["correo"],
            "usuario" => $usuario,
            "contrasena" => password_hash($_POST["contrasena"], PASSWORD_DEFAULT),
            "imagen_usuario" => $imagen,
            "estado" => 1
        ];

        $respuesta = ModeloUsuarios::mdlIngresarUsuario($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    MOSTRAR USUARIOS
    =============================================*/
    static public function ctrMostrarUsuarios($item, $valor)
    {
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    MOSTRAR USUARIOS
    =============================================*/
    static public function ctrMostrarUsuariosReporte($item, $valor)
    {
        $tabla = "usuarios";
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    EDITAR USUARIO (Obtener datos)
    =============================================*/
    static public function ctrEditarUsuario()
    {
        $tabla = "usuarios";
        $item = "id_usuario";
        $valor = $_POST["id_usuario"];
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    EDITAR USUARIO (Obtener datos)
    =============================================*/
    static public function ctrVerUsuario()
    {
        $tabla = "usuarios";
        $item = "id_usuario";
        $valor = $_POST["id_usuario"];
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
        echo json_encode($respuesta);
    }

    /*=============================================
    ACTUALIZAR USUARIO
    =============================================*/
    static public function ctrActualizarUsuario()
    {
        if (!isset($_POST["id_usuario"])) {
            return self::jsonResponse(false, "ID de usuario requerido");
        }

        $tabla = "usuarios";
        $idUsuario = (int)$_POST["id_usuario"];

        $usuarioActual = ModeloUsuarios::mdlMostrarUsuarios($tabla, "id_usuario", $idUsuario);
        if ($usuarioActual["status"] === false) {
            return self::jsonResponse(false, $usuarioActual["message"]);
        }

        if (isset($_POST["usuario"])) {
            $usuario = trim($_POST["usuario"]);
            $existe = ModeloUsuarios::mdlVerificarUsuarioExistente($tabla, "usuario", $usuario, $idUsuario);
            if ($existe) {
                return self::jsonResponse(false, "El nombre de usuario ya está en uso");
            }
        }

        $imagen = $usuarioActual["data"]["imagen_usuario"];
        if (isset($_FILES["imagen_usuario"]) && $_FILES["imagen_usuario"]["error"] == 0) {
            if ($imagen && file_exists("../vistas/img/usuarios/$imagen")) {
                unlink("../vistas/img/usuarios/$imagen");
            }
            $imagen = self::procesarImagen("imagen_usuario", "../vistas/img/usuarios/");
        }

        $datos = [
            "id_usuario" => $idUsuario,
            "id_sucursal" => (int)$_POST["id_sucursal"],
            "nombre_usuario" => htmlspecialchars(trim($_POST["nombre_usuario"])),
            "telefono" => $_POST["telefono"],
            "correo" => $_POST["correo"],
            "usuario" => $usuario ?? $usuarioActual["data"]["usuario"],
            "contrasena" => !empty($_POST["contrasena"]) ? password_hash($_POST["contrasena"], PASSWORD_DEFAULT) : null,
            "imagen_usuario" => $imagen
        ];

        $respuesta = ModeloUsuarios::mdlActualizarUsuarioU($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    CAMBIAR ESTADO DE USUARIO
    =============================================*/
    static public function ctrCambiarEstadoUsuario()
    {
        if (!isset($_POST["id_usuario"], $_POST["estado"])) {
            return self::jsonResponse(false, "Datos incompletos");
        }

        $tabla = "usuarios";
        $datos = [
            "id_usuario" => (int)$_POST["id_usuario"],
            "estado" => (int)$_POST["estado"]
        ];

        $respuesta = ModeloUsuarios::mdlCambiarEstadoUsuario($tabla, $datos);
        echo $respuesta;
    }

    /*=============================================
    ELIMINAR USUARIO
    =============================================*/
    static public function ctrBorrarUsuario()
    {
        if (!isset($_POST["id_usuario"])) {
            return self::jsonResponse(false, "ID de usuario requerido");
        }

        $tabla = "usuarios";
        $idUsuario = (int)$_POST["id_usuario"];

        $usuario = ModeloUsuarios::mdlMostrarUsuarios($tabla, "id_usuario", $idUsuario);
        if ($usuario["status"] === false) {
            return self::jsonResponse(false, $usuario["message"]);
        }

        if (!empty($_POST["ruta_imagen"])) {
            $rutaImagen = "../vistas/img/usuarios/" . $_POST["ruta_imagen"];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $idUsuario);
        echo $respuesta;
    }

    /*=============================================
    MÉTODOS PRIVADOS AUXILIARES
    =============================================*/
    
    private static function procesarImagen($campo, $directorio)
    {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] != 0) {
            return null;
        }

        $permitidos = ["image/jpeg", "image/png", "image/gif"];
        if (!in_array($_FILES[$campo]["type"], $permitidos)) {
            return null;
        }

        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreArchivo = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES[$campo]["name"]);
        $rutaTemp = $_FILES[$campo]["tmp_name"];
        $rutaDestino = $directorio . $nombreArchivo;

        if (move_uploaded_file($rutaTemp, $rutaDestino)) {
            return $nombreArchivo;
        }

        return null;
    }

    private static function jsonResponse($status, $message, $data = [])
    {
        $response = [
            "status" => $status,
            "message" => $message
        ];
        
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        
        echo json_encode($response);
        return;
    }
}