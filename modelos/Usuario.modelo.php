<?php

require_once "Conexion.php";

class ModeloUsuarios{

	/*=============================================
	MOSTRAR USUARIOS
	=============================================*/

	static public function mdlMostrarUsuarioConRoles($tablaUsuario, $tablaUsuarioRol, $tablaRol, $item, $valor)
	{
		$stmt = Conexion::conectar()->prepare("
												SELECT 
													u.id_usuario,
													u.nombre_usuario,
													u.telefono,
													u.contrasena,
													u.correo,
													u.usuario,
													u.imagen_usuario,
													r.id_rol,
													r.nombre_rol
												FROM 
													$tablaUsuario AS u
												INNER JOIN 
													$tablaUsuarioRol AS ur ON u.id_usuario = ur.id_usuario
												INNER JOIN 
													$tablaRol AS r ON ur.id_rol = r.id_rol
												WHERE 
													u.$item = :$item AND u.estado_usuario = 1
											");
		$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver múltiples roles si existen
	}

	/*=============================================
	MOSTRAR USUARIOS
	=============================================*/

	static public function mdlMostrarUsuarios($tablaSucursal, $tablausuario, $item, $valor){

		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaSucursal as s inner join $tablausuario as u on s.id_sucursal = u.id_sucursal WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaSucursal as s inner join $tablausuario as u on s.id_sucursal = u.id_sucursal ORDER BY u.id_usuario DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
		$stmt = null;
	}

	/*=============================================
	REGISTRO DE USUARIO
	=============================================*/

	static public function mdlIngresarUsuario($tabla, $datos)
	{
		// Conexión a la base de datos
		$conexion = Conexion::conectar();

		// Verificar si el usuario ya existe (por usuario o correo)
		$stmt = $conexion->prepare("SELECT COUNT(*) FROM $tabla WHERE usuario = :usuario OR correo = :correo");
		$stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);

		$stmt->execute();
		$usuarioExistente = $stmt->fetchColumn();

		if ($usuarioExistente > 0) {
			return [
				"status" => "warning",
				"message" => "El usuario o correo ya están registrados."
			];
		}

		// Si el usuario no existe, proceder con la inserción
		$stmt = $conexion->prepare("INSERT INTO $tabla(
            id_sucursal, 
            nombre_usuario, 
            telefono, 
            correo, 
            usuario, 
            contrasena, 
            imagen_usuario) 
        VALUES (
            :id_sucursal, 
            :nombre_usuario, 
            :telefono, 
            :correo, 
            :usuario, 
            :contrasena, 
            :imagen_usuario)");

		$stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre_usuario", $datos["nombre_usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
		$stmt->bindParam(":contrasena", $datos["contrasena"], PDO::PARAM_STR);
		$stmt->bindParam(":imagen_usuario", $datos["imagen_usuario"], PDO::PARAM_STR);

		if ($stmt->execute()) {
			return [
				"status" => "ok",
				"message" => "Usuario registrado exitosamente."
			];
		} else {
			return [
				"status" => "error",
				"message" => "Ocurrió un error al registrar el usuario."
			];
		}

		$stmt = null; // Liberar la conexión
	}


	/*=============================================
	EDITAR USUARIO
	=============================================*/

	static public function mdlEditarUsuario($tabla, $datos){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_sucursal = :id_sucursal, 
																nombre_usuario = :nombre_usuario, 
																telefono = :telefono, 
																correo = :correo, 
																usuario = :usuario, 
																contrasena = :contrasena, 
																imagen_usuario = :imagen_usuario
																WHERE id_usuario = :id_usuario");

		$stmt -> bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);
		$stmt -> bindParam(":nombre_usuario", $datos["nombre_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt -> bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
		$stmt -> bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":contrasena", $datos["contrasena"], PDO::PARAM_STR);
		$stmt -> bindParam(":imagen_usuario", $datos["imagen_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}


		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR USUARIO
	=============================================*/
	static public function mdlActualizarUsuario($tabla, $item1, $valor1, $item2, $valor2)
	{

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
		$stmt->bindParam(":" . $item1, $valor1, PDO::PARAM_STR);
		$stmt->bindParam(":" . $item2, $valor2, PDO::PARAM_STR);

		if ($stmt->execute()) {
			return "ok";
		} else {
			print_r($stmt->errorInfo()); // Muestra los errores de SQL
			return "error";
		}

	
	}


	/*=============================================
	BORRAR USUARIO
	=============================================*/

	static public function mdlBorrarUsuario($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_usuario = :id_usuario");
		$stmt -> bindParam(":id_usuario", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
		$stmt = null;
	}
}
