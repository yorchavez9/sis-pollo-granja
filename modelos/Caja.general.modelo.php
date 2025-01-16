<?php

require_once "Conexion.php";

class ModeloCajaGeneral
{

	/*=============================================
    MOSTRAR REPORTE EN GRAFICOS Y TABLAS DE VENTAS
    =============================================*/
	static public function mdlMostrarResumenVentas(){
		$stmt = Conexion::conectar()->prepare("SELECT 
													dv.id_producto,
													p.nombre_producto,
													SUM(dv.numero_aves) AS total_vendido,
													(p.precio_producto - p.precio_compra) AS ganancia_por_unidad,
													SUM(dv.numero_aves) * (p.precio_producto - p.precio_compra) AS ganancia_total
												FROM 
													detalle_venta dv
												JOIN 
													productos p ON dv.id_producto = p.id_producto
												WHERE DATE(dv.fecha_detalle) = CURDATE() -- Cambia CURDATE() por una fecha específica si lo deseas
												GROUP BY dv.id_producto, p.nombre_producto, p.precio_producto, p.precio_compra
												ORDER BY ganancia_total DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
	}


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
																id_usuario  = :id_usuario, 
																tipo_movimiento  = :tipo_movimiento, 
																egresos = :egresos,
																ingresos = :ingresos,
																monto_inicial = :monto_inicial,
																monto_final = :monto_final,
																fecha_cierre = :fecha_cierre,
																estado = :estado
																WHERE id_movimiento = :id_movimiento");

		$stmt -> bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
		$stmt -> bindParam(":tipo_movimiento", $datos["tipo_movimiento"], PDO::PARAM_STR);
		$stmt -> bindParam(":egresos", $datos["egresos"], PDO::PARAM_STR);
		$stmt -> bindParam(":ingresos", $datos["ingresos"], PDO::PARAM_STR);
		$stmt -> bindParam(":monto_inicial", $datos["monto_inicial"], PDO::PARAM_STR);
		$stmt -> bindParam(":monto_final", $datos["monto_final"], PDO::PARAM_STR);
		$stmt -> bindParam(":fecha_cierre", $datos["fecha_cierre"], PDO::PARAM_STR);
		$stmt -> bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_movimiento", $datos["id_movimiento"], PDO::PARAM_INT);
		if($stmt -> execute()){
			return [
                "status" => true,
                "message" => "La caja se cerró exitosamente"
            ];
		}else{
			return [
                "status" => false,
                "message" => "Error al cerrar la caja"
            ];
		}
	}

	/*=============================================
	EDITAR CAJA
	=============================================*/
	static public function mdlActualizarMontos($tabla, $datos){
		/* Sumar a los campos egresos e ingresos */
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
													egresos = egresos + :egresos, 
													ingresos = ingresos + :ingresos 
												WHERE id_movimiento = :id_movimiento");
	
		$stmt->bindParam(":egresos", $datos["egresos"], PDO::PARAM_STR);
		$stmt->bindParam(":ingresos", $datos["ingresos"], PDO::PARAM_STR);
		$stmt->bindParam(":id_movimiento", $datos["id_movimiento"], PDO::PARAM_INT);
	
		if ($stmt->execute()) {
			return [
				"status" => true,
				"message" => "Los montos se actualizaron exitosamente"
			];
		} else {
			return [
				"status" => false,
				"message" => "Error al actualizar los montos"
			];
		}
	}
	
	/*=============================================
	EDITAR CAJA ACTUALIZANDO
	=============================================*/
	static public function mdlActualizarMontosEdit($tabla, $datos) {
		// Dependiendo de la acción, realiza suma o resta en los campos de ingresos o egresos
		$operacionEgresos = ($datos["accion"] == "suma") ? "+" : "-";
		$operacionIngresos = ($datos["accion"] == "suma") ? "+" : "-";
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
												egresos = egresos $operacionEgresos :egresos, 
												ingresos = ingresos $operacionIngresos :ingresos 
											  WHERE id_movimiento = :id_movimiento");
	
		$stmt->bindParam(":egresos", $datos["egresos"], PDO::PARAM_STR);
		$stmt->bindParam(":ingresos", $datos["ingresos"], PDO::PARAM_STR);
		$stmt->bindParam(":id_movimiento", $datos["id_movimiento"], PDO::PARAM_INT);
	
		if ($stmt->execute()) {
			return [
				"status" => true,
				"message" => "Los montos se actualizaron exitosamente"
			];
		} else {
			return [
				"status" => false,
				"message" => "Error al actualizar los montos"
			];
		}
	}
	
	
	/*=============================================
	ELIMINANDO EL EGRESO O INGRESO (ACTUALIZACION)
	=============================================*/
	static public function mdlActualizarMontosDelete($tabla, $datos) {
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
													egresos = egresos - :egresos, 
													ingresos = ingresos - :ingresos 
												WHERE id_movimiento = :id_movimiento");
	
		$stmt->bindParam(":egresos", $datos["egresos"], PDO::PARAM_STR);
		$stmt->bindParam(":ingresos", $datos["ingresos"], PDO::PARAM_STR);
		$stmt->bindParam(":id_movimiento", $datos["id_movimiento"], PDO::PARAM_INT);
	
		if ($stmt->execute()) {
			return [
				"status" => true
			];
		} else {
			return [
				"status" => false
			];
		}
	}
	
}