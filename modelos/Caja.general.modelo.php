<?php

require_once "Conexion.php";

class ModeloCajaGeneral
{

	/*=============================================
	MOSTRAR CAJA
	=============================================*/
	static public function mdlMostrarCajaGeneral($tabla, $item, $valor){
		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id_movimiento DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
	}

	/*=============================================
	REGISTRAR CAJA
	=============================================*/
	static public function mdlIngresarCajaGeneral($tabla, $datos) {
        $conexion = Conexion::conectar();
        $stmtVerificar = $conexion->prepare("SELECT COUNT(*) AS conteo FROM $tabla WHERE fecha_apertura = :fecha_apertura");
        $stmtVerificar->bindParam(":fecha_apertura", $datos["fecha_apertura"], PDO::PARAM_STR);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        if ($resultado['conteo'] > 0) {
            return [
                "status" => false,
                "message" => "Ya existe un registro de caja para esta fecha."
            ];
        }

        $stmt = $conexion->prepare("INSERT INTO $tabla(
                                        id_usuario, 
                                        monto_inicial,
                                        fecha_apertura,
                                        fecha_cierre
                                    ) VALUES (
                                        :id_usuario, 
                                        :monto_inicial,
                                        :fecha_apertura,
                                        :fecha_cierre
                                    )");
    
        $stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":monto_inicial", $datos["monto_inicial"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_apertura", $datos["fecha_apertura"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_cierre", $datos["fecha_cierre"], PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            return [
                "status" => true,
                "message" => "El saldo inicial se guardó con éxito"
            ];
        } else {
            return [
                "status" => false,
                "message" => "Error al crear el saldo inicial"
            ];
        }
    }
    

	/*=============================================
	EDITAR CAJA
	=============================================*/
	static public function mdlEditarCajaGeneral($tabla, $datos){
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
	ACTUALIZAR CAJA
	=============================================*/
	static public function mdlActualizarCajaGeneral($tabla, $item1, $valor1, $item2, $valor2){
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
	BORRAR CAJA
	=============================================*/
	static public function mdlBorrarCajaGeneral($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_categoria = :id_categoria");
		$stmt -> bindParam(":id_categoria", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
	}
}