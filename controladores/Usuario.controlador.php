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

        // Validar entrada
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

        // Verificar contraseña
        if (!password_verify($password, $respuesta["contrasena"])) {
            return self::jsonResponse(false, "Error al ingresar, vuelve a intentarlo");
        }

        // Verificar estado
        if ($respuesta["estado"] != 1) {
            return self::jsonResponse(false, "El usuario está desactivado");
        }

        // Crear sesión
        $_SESSION["iniciarSesion"] = "ok";
        $datosSesion = ModeloUsuarios::mdlObtenerDatosSesion($respuesta["id_usuario"]);
        
        // Obtener roles y permisos del usuario
        $roles = ModeloUsuarios::mdlObtenerRolesUsuario($respuesta["id_usuario"]);
        $permisos = ModeloUsuarios::mdlObtenerPermisosUsuario($respuesta["id_usuario"]);

        $_SESSION["usuario"] = $datosSesion;
        $_SESSION["roles"] = $roles;
        $_SESSION["permisos"] = $permisos;
        

        // Actualizar último login
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
        // Validar campos obligatorios
        $camposRequeridos = ["usuario", "telefono", "correo", "contrasena", "id_sucursal", "nombre_usuario"];
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo])) {
                return self::jsonResponse(false, "El campo $campo es requerido");
            }
        }

        $tabla = "usuarios";
        $usuario = trim($_POST["usuario"]);

        // Verificar usuario existente
        $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, "usuario", $usuario);
        
        if ($respuesta["status"] === false) {
            return self::jsonResponse(false, $respuesta["message"]);
        }
        
        if ($respuesta["exists"]) {
            return self::jsonResponse(false, "El nombre de usuario ya está en uso");
        }

        // Procesar imagen
        $imagen = self::procesarImagen("imagen", "../vistas/img/usuarios/");

        // Crear usuario
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
    ACTUALIZAR USUARIO
    =============================================*/
    static public function ctrActualizarUsuario()
    {
        if (!isset($_POST["id_usuario"])) {
            return self::jsonResponse(false, "ID de usuario requerido");
        }

        $tabla = "usuarios";
        $idUsuario = (int)$_POST["id_usuario"];

        // Verificar usuario existente si se cambia el nombre
        if (isset($_POST["usuario"])) {
            $usuario = trim($_POST["usuario"]);
            $existe = ModeloUsuarios::mdlVerificarUsuarioExistente($tabla, "usuario", $usuario, $idUsuario);
            
            if ($existe) {
                return self::jsonResponse(false, "El nombre de usuario ya está en uso");
            }
        }

        // Obtener usuario actual para manejo de imagen
        $usuarioActual = ModeloUsuarios::mdlMostrarUsuarios($tabla, "id_usuario", $idUsuario);
        if ($usuarioActual["status"] === false) {
            return self::jsonResponse(false, $usuarioActual["message"]);
        }

        // Procesar imagen
        $imagen = $usuarioActual["data"]["imagen"]; // Mantener la actual por defecto
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            // Eliminar imagen anterior si existe
            if ($imagen && file_exists("../vistas/img/usuarios/$imagen")) {
                unlink("../vistas/img/usuarios/$imagen");
            }
            $imagen = self::procesarImagen("imagen", "../vistas/img/usuarios/");
        }

        // Preparar datos
        $datos = [
            "id_usuario" => $idUsuario,
            "id_sucursal" => (int)$_POST["id_sucursal"],
            "id_persona" => (int)$_POST["id_persona"],
            "nombre_usuario" => htmlspecialchars(trim($_POST["nombre_usuario"])),
            "usuario" => $usuario ?? $usuarioActual["data"]["usuario"],
            "contrasena" => !empty($_POST["contrasena"]) ? password_hash($_POST["contrasena"], PASSWORD_DEFAULT) : null,
            "imagen" => $imagen,
            "estado" => (int)$_POST["estado"]
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

        // Obtener usuario para eliminar imagen
        $usuario = ModeloUsuarios::mdlMostrarUsuarios($tabla, "id_usuario", $idUsuario);
        if ($usuario["status"] === false) {
            return self::jsonResponse(false, $usuario["message"]);
        }

        $respuesta = ModeloUsuarios::mdlBorrarUsuario($tabla, $idUsuario);
        echo $respuesta;
    }

    /*=============================================
    MÉTODOS PRIVADOS AUXILIARES
    =============================================*/
    
    /**
     * Procesa la imagen subida
     */
    private static function procesarImagen($campo, $directorio)
    {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] != 0) {
            return null;
        }

        // Validar tipo de archivo
        $permitidos = ["image/jpeg", "image/png", "image/gif"];
        if (!in_array($_FILES[$campo]["type"], $permitidos)) {
            return null;
        }

        // Crear directorio si no existe
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

    /**
     * Devuelve una respuesta JSON estandarizada
     */
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