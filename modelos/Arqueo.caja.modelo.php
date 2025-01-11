<?php

require_once "Conexion.php";

class ModeloArqueoCaja
{

	/*=============================================
	MOSTRAR ARQUEO CAJA
	=============================================*/
	static public function mdlMostrarArqueoCaja($tabla, $item, $valor){
		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id_arqueo DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
	}

	/*=============================================
	REGISTRAR ARQUEO CAJA
	=============================================*/
	static public function mdlIngresarArqueoCaja($tabla, $datos) {
        // Verificar si ya existe un arqueo para la fecha actual
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM $tabla WHERE fecha_arqueo = :fecha_arqueo");
        $stmt->bindParam(":fecha_arqueo", $datos["fecha_arqueo"], PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetchColumn();
    
        // Si ya existe un arqueo para esa fecha, no insertar
        if ($resultado > 0) {
            return [
                "status" => false,
                "message" => "Ya existe un arqueo registrado para esta fecha."
            ];
        }
    
        // Si no existe, insertar el nuevo arqueo
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                    id_movimiento_caja, 
                                                                    id_usuario,
                                                                    fecha_arqueo,
                                                                    monto_sistema,
                                                                    monto_fisico,
                                                                    diferencia,
                                                                    observaciones
                                                                )
                                                                VALUES (
                                                                    :id_movimiento_caja, 
                                                                    :id_usuario,
                                                                    :fecha_arqueo,
                                                                    :monto_sistema,
                                                                    :monto_fisico,
                                                                    :diferencia,
                                                                    :observaciones
                                                                )");
    
        $stmt->bindParam(":id_movimiento_caja", $datos["id_movimiento_caja"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_arqueo", $datos["fecha_arqueo"], PDO::PARAM_STR);
        $stmt->bindParam(":monto_sistema", $datos["monto_sistema"], PDO::PARAM_STR);
        $stmt->bindParam(":monto_fisico", $datos["monto_fisico"], PDO::PARAM_STR);
        $stmt->bindParam(":diferencia", $datos["diferencia"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "El arqueo se ha registrado con éxito"
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al registrar el arqueo"
            ];
        }
    }
    
	/*=============================================
	EDITAR ARQUEO CAJA
	=============================================*/
	static public function mdlEditarArqueoCaja($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_movimiento_caja = :id_movimiento_caja, 
																id_usuario = :id_usuario,
																fecha_arqueo = :fecha_arqueo,
																monto_sistema = :monto_sistema,
																monto_fisico = :monto_fisico,
																diferencia = :diferencia,
																observaciones = :observaciones
																WHERE id_arqueo = :id_arqueo");

		$stmt -> bindParam(":id_movimiento_caja", $datos["id_movimiento_caja"], PDO::PARAM_INT);
        $stmt -> bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt -> bindParam(":fecha_arqueo", $datos["fecha_arqueo"], PDO::PARAM_STR);
        $stmt -> bindParam(":monto_sistema", $datos["monto_sistema"], PDO::PARAM_STR);
        $stmt -> bindParam(":monto_fisico", $datos["monto_fisico"], PDO::PARAM_STR);
        $stmt -> bindParam(":diferencia", $datos["diferencia"], PDO::PARAM_STR);
        $stmt -> bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt -> bindParam(":id_arqueo", $datos["id_arqueo"], PDO::PARAM_INT);

		if($stmt -> execute()){
			return [
                "status" => true,
                "message" => "El arqueo se ha editado con éxito"
            ];
		}else{
			return [
                "status" => false,
                "message" => "Error al editar el arqueo"
            ];	
		}
	}

	/*=============================================
	ACTUALIZAR ARQUEO CAJA
	=============================================*/
	static public function mdlActualizarArqueoCaja($tabla, $item1, $valor1, $item2, $valor2){
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
	BORRAR ARQUEO CAJA
	=============================================*/
	static public function mdlBorrarArqueoCaja($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_arqueo = :id_arqueo");
		$stmt -> bindParam(":id_arqueo", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return [
                "status" => true,
                "message" => "El arqueo se ha eliminado con éxito"
            ];
		}else{
			return [
                "status" => false,
                "message" => "Error al eliminar el arqueo"
            ];	
		}
	}
}