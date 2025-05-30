<?php

require_once "Conexion.php";

class ModeloPago{

	/*=============================================
	MOSTRAR PAGOS
	=============================================*/

	static public function mdlMostrarPagos($tablaT, $tablaC, $tablaP, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaT INNER JOIN $tablaC ON $tablaT.id_trabajador = $tablaC.id_trabajador INNER JOIN $tablaP ON $tablaC.id_contrato = $tablaP.id_contrato WHERE $item = :$item");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaT INNER JOIN $tablaC ON $tablaT.id_trabajador = $tablaC.id_trabajador INNER JOIN $tablaP ON $tablaC.id_contrato = $tablaP.id_contrato");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}
		


		$stmt = null;

	}

	/*=============================================
	MOSTRAR REPORTE DE PAGOS DE TRABAJADOR
	=============================================*/
	public static function mdlReportePagoTrabajador($tabla_t, $tabla_contrato_t, $tabla_pago_t, $filtros)
	{
		// Crear la consulta base
		$sql = "SELECT 
						t.nombre,
						pt.monto_pago,
						pt.fecha_pago,
						pt.estado_pago
					FROM 
						$tabla_t AS t 
					INNER JOIN 	$tabla_contrato_t AS ct ON t.id_trabajador = ct.id_trabajador 
					INNER JOIN  $tabla_pago_t AS pt ON ct.id_contrato = pt.id_contrato
					WHERE 1 = 1";

		// Arreglo de parámetros para la consulta
		$params = [];

		if (!empty($filtros['filtro_estado_pago_t'])) {
			$sql .= " AND pt.estado_pago = :estado";
			$params[':estado'] = $filtros['filtro_estado_pago_t'];
		}

		// Filtro por rango de fecha de vencimiento
		if (!empty($filtros['filtro_fecha_desde_pago_t']) && !empty($filtros['filtro_fecha_hasta_pago_t'])) {
			$sql .= " AND pt.fecha_pago BETWEEN :fecha_desde AND :fecha_hasta";
			$params[':fecha_desde'] = $filtros['filtro_fecha_desde_pago_t'];
			$params[':fecha_hasta'] = $filtros['filtro_fecha_hasta_pago_t'];
		}

		// Ordenar por fecha de producto (DESC)
		$sql .= " ORDER BY pt.fecha_pago DESC";

		// Preparar y ejecutar la consulta
		$stmt = Conexion::conectar()->prepare($sql);
		$stmt->execute($params);

		// Retornar los resultados
		return $stmt->fetchAll();
	}


	/*=============================================
	REGISTRO DE PAGO
	=============================================*/

	static public function mdlIngresarPagos($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                id_contrato, 
                                                                monto_pago, 
                                                                fecha_pago) 
		                                                  VALUES (
                                                                :id_contrato, 
                                                                :monto_pago, 
                                                                :fecha_pago)");

		$stmt->bindParam(":id_contrato", $datos["id_contrato"], PDO::PARAM_INT);
		$stmt->bindParam(":monto_pago", $datos["monto_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":fecha_pago", $datos["fecha_pago"], PDO::PARAM_STR);


		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		
		$stmt = null;

	}

	/*=============================================
	EDITAR PAGO
	=============================================*/

	static public function mdlEditarPagos($tabla, $datos){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_trabajador = :id_trabajador, 
																tiempo_contrato = :tiempo_contrato, 
																tipo_sueldo = :tipo_sueldo, 
																sueldo = :sueldo
																WHERE id_contrato = :id_contrato");

		$stmt -> bindParam(":id_trabajador", $datos["id_trabajador"], PDO::PARAM_INT);
		$stmt -> bindParam(":tiempo_contrato", $datos["tiempo_contrato"], PDO::PARAM_INT);
		$stmt -> bindParam(":tipo_sueldo", $datos["tipo_sueldo"], PDO::PARAM_STR);
		$stmt -> bindParam(":sueldo", $datos["sueldo"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_contrato", $datos["id_contrato"], PDO::PARAM_INT);


		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}


		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR PAGO
	=============================================*/

	static public function mdlActualizarPagos($tabla, $item1, $valor1, $item2, $valor2){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");

		$stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
		$stmt -> bindParam(":".$item2, $valor2, PDO::PARAM_STR);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}


		$stmt = null;

	}

	/*=============================================
	BORRAR PAGO
	=============================================*/

	static public function mdlBorrarPagos($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_pagos = :id_pagos");

		$stmt -> bindParam(":id_pagos", $datos, PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}


		$stmt = null;


	}

}
