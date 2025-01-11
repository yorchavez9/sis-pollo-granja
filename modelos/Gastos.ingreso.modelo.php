<?php

require_once "Conexion.php";

class ModeloGastoIngreso
{

	/*=============================================
	MOSTRAR GASTOS INGRESOS
	=============================================*/
	static public function mdlMostrarGastosIngresos($tabla, $item, $valor){
		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id_gasto DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
	}

	/*=============================================
	REGISTRAR GASTOS INGRESOS
	=============================================*/
	static public function mdlIngresarGastoIngreso($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                    id_usuario, 
                                                                    id_movimiento_caja,
                                                                    tipo,
                                                                    concepto,
                                                                    monto,
                                                                    detalles
                                                                    )
                                                                    VALUES (
                                                                    :id_usuario, 
                                                                    :id_movimiento_caja,
                                                                    :tipo,
                                                                    :concepto,
                                                                    :monto,
                                                                    :detalles)");

		$stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
        $stmt->bindParam(":id_movimiento_caja", $datos["id_movimiento_caja"], PDO::PARAM_INT);
        $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
        $stmt->bindParam(":concepto", $datos["concepto"], PDO::PARAM_STR);
        $stmt->bindParam(":monto", $datos["monto"], PDO::PARAM_STR);
        $stmt->bindParam(":detalles", $datos["detalles"], PDO::PARAM_STR);


		if($stmt->execute()){
			return [
                "status" => true,
                "message" => "Resgitrado con éxito"
            ];	
		}else{
			return [
                "status" => false,
                "message" => "Error al registrar el gasto o ingreso"
            ];
		}
	}

	/*=============================================
	EDITAR GASTOS INGRESOS
	=============================================*/
	static public function mdlEditarGastoIngreso($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																tipo = :tipo, 
																concepto = :concepto,
																monto = :monto,
																detalles = :detalles
																WHERE id_gasto = :id_gasto");

		$stmt -> bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
		$stmt -> bindParam(":concepto", $datos["concepto"], PDO::PARAM_STR);
		$stmt -> bindParam(":monto", $datos["monto"], PDO::PARAM_STR);
		$stmt -> bindParam(":detalles", $datos["detalles"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_gasto", $datos["id_gasto"], PDO::PARAM_INT);
		if($stmt -> execute()){
			return [
                "status" => true,
                "message" => "Actualizado con éxito"
            ];
		}else{
			return [
                "status" => false,
                "message" => "Error al actualizar el gasto o ingreso"
            ];	
		}
	}

	/*=============================================
	BORRAR GASTOS INGRESOS
	=============================================*/
	static public function mdlBorrarGastoIngreso($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_gasto = :id_gasto");
		$stmt -> bindParam(":id_gasto", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return [
                "status" => true
            ];
		}else{
			return [
                "status" => false
            ];	
		}
	}
}