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
	MOSTRAR REPORTE DE GASTOS INGRESOS EXTRAS
	=============================================*/
	public static function mdlReporteGastosIngresos($tabla, $id_usuario, $tipo, $fecha_desde, $fecha_hasta)
	{
		// Crear la consulta base
		$query = "SELECT *  FROM $tabla WHERE 1";  

		// Arreglo de parámetros para la consulta
		$params = [];

		// Filtro por categoría
		if ($id_usuario !== null && $id_usuario !== '') {
			$query .= " AND id_usuario = ?";
			$params[] = $id_usuario;
		}

		// Filtro por estado
		if ($tipo !== null && $tipo !== ''
		) {
			$query .= " AND tipo = ?";
			$params[] = $tipo;
		}

		// Filtro por rango de fecha de vencimiento
		if ($fecha_desde !== null && $fecha_desde !== '' && $fecha_hasta !== null && $fecha_hasta !== '') {
			$query .= " AND fecha BETWEEN ? AND ?";
			$params[] = $fecha_desde;
			$params[] = $fecha_hasta;
		}

		// Ordenar por fecha de producto (DESC)
		$query .= " ORDER BY fecha DESC";

		// Preparar y ejecutar la consulta
		$stmt = Conexion::conectar()->prepare($query);
		$stmt->execute($params);

		// Retornar los resultados
		return $stmt->fetchAll();
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