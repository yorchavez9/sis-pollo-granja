<?php

require_once "Conexion.php";

class ModeloAccion
{

	/*=============================================
	MOSTRAR ACCIONES
	=============================================*/

	static public function mdlMostrarAcciones($tabla, $item, $valor){
		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id_accion ASC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
	}

	/*=============================================
	REGISTRAR ACCIONES
	=============================================*/

	static public function mdlIngresarAccion($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                    nombre_categoria, 
                                                                    descripcion
                                                                    )
                                                                    VALUES (
                                                                    :nombre_categoria, 
                                                                    :descripcion
                                                                    )");

		$stmt->bindParam(":nombre_categoria", $datos["nombre_categoria"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);

		if($stmt->execute()){
			return "ok";	
		}else{
			return "error";
		}
	}

	/*=============================================
	EDITAR ACCIONES
	=============================================*/

	static public function mdlEditarAccion($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																nombre_categoria = :nombre_categoria, 
																descripcion = :descripcion
																WHERE id_categoria = :id_categoria");

		$stmt -> bindParam(":nombre_categoria", $datos["nombre_categoria"], PDO::PARAM_STR);
		$stmt -> bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
	}

	/*=============================================
	ACTUALIZAR ACCIONES
	=============================================*/

	static public function mdlActualizarAccion($tabla, $item1, $valor1, $item2, $valor2){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":".$item2, $valor2, PDO::PARAM_STR);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
	}

	/*=============================================
	BORRAR ACCIONES
	=============================================*/

	static public function mdlBorrarAccion($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_categoria = :id_categoria");
		$stmt -> bindParam(":id_categoria", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
	}
}