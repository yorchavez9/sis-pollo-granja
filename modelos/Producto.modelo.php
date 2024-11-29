<?php

require_once "Conexion.php";

class ModeloProducto{

	/*=============================================
	MOSTRAR PRODUCTOS
	=============================================*/

	static public function mdlMostrarProducto($tablaC, $tablaP, $item, $valor){
		if($item != null){
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaC as c inner join $tablaP as p on c.id_categoria = p.id_categoria WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaC as c inner join $tablaP as p on c.id_categoria = p.id_categoria ORDER BY p.id_producto DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
		
		$stmt = null;
	}

	/*=============================================
	MOSTRAR REPORTE DE PRODUCTOS
	=============================================*/
	public static function mdlReporteProductos($tabla_categoria, $tabla_producto, $filtro_categoria, $filtro_estado, $filtro_precio_min, $filtro_precio_max, $filtro_fecha_desde, $filtro_fecha_hasta)
	{
		// Crear la consulta base
		$query = "SELECT 
                p.id_producto, 
                p.codigo_producto, 
                p.nombre_producto, 
                p.precio_producto, 
                p.stock_producto, 
                p.fecha_vencimiento, 
                p.descripcion_producto, 
                p.imagen_producto, 
                p.estado_producto, 
                p.fecha_producto, 
                c.nombre_categoria
              FROM $tabla_producto p
              JOIN $tabla_categoria c ON p.id_categoria = c.id_categoria
              WHERE 1";  // Usamos WHERE 1 para facilitar la adición dinámica de condiciones

		// Arreglo de parámetros para la consulta
		$params = [];

		// Filtro por categoría
		if ($filtro_categoria !== null && $filtro_categoria !== '') {
			$query .= " AND p.id_categoria = ?";
			$params[] = $filtro_categoria;
		}

		// Filtro por estado
		if ($filtro_estado !== null && $filtro_estado !== ''
		) {
			$query .= " AND p.estado_producto = ?";
			$params[] = $filtro_estado;
		}

		// Filtro por rango de precio
		if ($filtro_precio_min !== null && $filtro_precio_max !== null && $filtro_precio_min !== '' && $filtro_precio_max !== '') {
			$query .= " AND p.precio_producto BETWEEN ? AND ?";
			$params[] = $filtro_precio_min;
			$params[] = $filtro_precio_max;
		}

		// Filtro por rango de fecha de vencimiento
		if ($filtro_fecha_desde !== null && $filtro_fecha_desde !== '' && $filtro_fecha_hasta !== null && $filtro_fecha_hasta !== '') {
			$query .= " AND p.fecha_vencimiento BETWEEN ? AND ?";
			$params[] = $filtro_fecha_desde;
			$params[] = $filtro_fecha_hasta;
		}

		// Ordenar por fecha de producto (DESC)
		$query .= " ORDER BY p.fecha_producto DESC";

		// Preparar y ejecutar la consulta
		$stmt = Conexion::conectar()->prepare($query);
		$stmt->execute($params);

		// Retornar los resultados
		return $stmt->fetchAll();
	}


	/*=============================================
	MOSTRAR PRODUCTOS NUEVOS
	=============================================*/
	static public function mdlMostrarProductoNuevos($tablaC, $tablaP, $item, $valor) {
		if ($item != null) {
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaC as c inner join $tablaP as p on c.id_categoria = p.id_categoria WHERE $item = :$item");
			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetch();
		} else {
			// Ajuste de la consulta para obtener los 5 productos más recientes
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaC as c inner join $tablaP as p on c.id_categoria = p.id_categoria ORDER BY p.fecha_producto DESC LIMIT 5");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
		$stmt = null;
	}
	

	/*=============================================
	REGISTRO DE PRODUCTOS
	=============================================*/

	static public function mdlIngresarProducto($tabla, $datos){
		if (empty($datos["fecha_vencimiento"])) {
			$datos["fecha_vencimiento"] = NULL; // Asignar NULL si no se proporciona fecha
		}
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                id_categoria,
                                                                codigo_producto, 
                                                                nombre_producto, 
                                                                precio_producto, 
                                                                stock_producto, 
                                                                fecha_vencimiento, 
                                                                descripcion_producto, 
                                                                imagen_producto) 
                                                                VALUES (
                                                                :id_categoria, 
                                                                :codigo_producto, 
                                                                :nombre_producto, 
                                                                :precio_producto, 
                                                                :stock_producto, 
                                                                :fecha_vencimiento, 
                                                                :descripcion_producto, 
                                                                :imagen_producto)");

		$stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		$stmt->bindParam(":codigo_producto", $datos["codigo_producto"], PDO::PARAM_STR);
		$stmt->bindParam(":nombre_producto", $datos["nombre_producto"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_producto", $datos["precio_producto"], PDO::PARAM_STR);
		$stmt->bindParam(":stock_producto", $datos["stock_producto"], PDO::PARAM_INT);
		$stmt->bindParam(":fecha_vencimiento", $datos["fecha_vencimiento"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion_producto", $datos["descripcion_producto"], PDO::PARAM_STR);
		$stmt->bindParam(":imagen_producto", $datos["imagen_producto"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		
		$stmt = null;

	}

	/*=============================================
	EDITAR PRODUCTOS
	=============================================*/

	static public function mdlEditarProducto($tabla, $datos){
		if (empty($datos["fecha_vencimiento"])) {
			$datos["fecha_vencimiento"] = NULL; // Asignar NULL si no se proporciona fecha
		}
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_categoria = :id_categoria, 
																codigo_producto = :codigo_producto, 
																nombre_producto = :nombre_producto, 
																precio_producto = :precio_producto, 
																stock_producto = :stock_producto, 
																fecha_vencimiento = :fecha_vencimiento, 
																descripcion_producto = :descripcion_producto, 
																imagen_producto = :imagen_producto
																WHERE id_producto = :id_producto");

		$stmt -> bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);
		$stmt -> bindParam(":codigo_producto", $datos["codigo_producto"], PDO::PARAM_STR);
		$stmt -> bindParam(":nombre_producto", $datos["nombre_producto"], PDO::PARAM_STR);
		$stmt -> bindParam(":precio_producto", $datos["precio_producto"], PDO::PARAM_STR);
		$stmt -> bindParam(":stock_producto", $datos["stock_producto"], PDO::PARAM_INT);
		$stmt -> bindParam(":fecha_vencimiento", $datos["fecha_vencimiento"], PDO::PARAM_STR);
		$stmt -> bindParam(":descripcion_producto", $datos["descripcion_producto"], PDO::PARAM_STR);
		$stmt -> bindParam(":imagen_producto", $datos["imagen_producto"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}


		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR PRODUCTOS
	=============================================*/

	static public function mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2){
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
	BORRAR PRODUCTOS
	=============================================*/

	static public function mdlBorrarProducto($tabla, $datos){
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_producto = :id_producto");
		$stmt -> bindParam(":id_producto", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
		$stmt = null;
	}
}
