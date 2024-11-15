<?php

require_once "Conexion.php";

class ModeloCompra{

	/*=============================================
	MOSTRAR COMPRA
	=============================================*/
	static public function mdlMostrarCompra($tablaE, $tablaDE, $tablaP, $item, $valor)
	{

		if ($item != null) {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDE as d INNER JOIN $tablaE as e on d.id_egreso = e.id_egreso INNER JOIN $tablaP as p ON p.id_persona = e.id_persona WHERE e.$item = :$item");

			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);

			$stmt->execute();

			return $stmt->fetch();
		} else {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDE as d INNER JOIN $tablaE as e on d.id_egreso = e.id_egreso INNER JOIN $tablaP as p ON p.id_persona = e.id_persona  ORDER BY e.estado_pago DESC");

			$stmt->execute();

			return $stmt->fetchAll();
		}
	}

	/*=============================================
	MOSTRAR DETALLE VENTAS
	=============================================*/

	static public function mdlMostrarListaDetalleCompra($tablaDE, $tablaP, $item, $valor)
	{

		if ($item != null) {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDE as de INNER JOIN $tablaP as p ON de.id_producto=p.id_producto WHERE $item = :$item");

			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);

			$stmt->execute();

			return $stmt->fetchAll();
		} else {

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tablaDE as de INNER JOIN $tablaP as p ON p.id_producto = de.id_producto  ORDER BY de.id_egreso DESC");

			$stmt->execute();

			return $stmt->fetchAll();
		}
	}

	/*=============================================
	MOSTRAR COMPRA
	=============================================*/

	static public function mdlMostrarCompraTotalCantidad($tablaE, $tablaDE, $item, $valor){

		$stmt = Conexion::conectar()->prepare("SELECT * from $tablaE");

		$stmt -> execute();

		return $stmt -> fetchAll();

	}

	/*=============================================
	MOSTRAR TOTAL DE COMPRAS
	=============================================*/

	static public function mdlMostrarTotalCompras($tablaE, $tablaDE, $item, $valor){

		$stmt = Conexion::conectar()->prepare("SELECT SUM(total_compra) AS total_compras FROM $tablaE");

		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result['total_compras'];

	}

	/*=============================================
	MOSTRAR SERIE Y NUMERO DE COMPRA O EGRESO
	=============================================*/

	static public function mdlMostrarSerieNumero($tabla, $item, $valor){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_egreso DESC LIMIT 1");
		
		$stmt->execute();

		return $stmt->fetchAll();

		$stmt = null;

	}

	/*=============================================
	MOSTRAR EGRESO
	=============================================*/

	static public function mdlMostrarEgreso($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item ORDER BY id_egreso DESC LIMIT 1");

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_egreso DESC LIMIT 1");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}
		

		$stmt = null;

	}


	/*=============================================
	REGISTRO DE COMPRA
	=============================================*/

	static public function mdlIngresarCompra($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                                id_persona,
                                                                id_usuario, 
                                                                fecha_egre,
																hora_egreso, 
                                                                tipo_comprobante, 
                                                                serie_comprobante, 
                                                                num_comprobante, 
                                                                impuesto,
                                                                total_compra,
                                                                total_pago,
                                                                subTotal,
                                                                igv,
                                                                tipo_pago,
                                                                estado_pago,
                                                                pago_e_y) 
                                                                VALUES (
                                                                :id_persona, 
                                                                :id_usuario, 
                                                                :fecha_egre, 
                                                                :hora_egreso, 
                                                                :tipo_comprobante, 
                                                                :serie_comprobante, 
                                                                :num_comprobante, 
                                                                :impuesto,
                                                                :total_compra,
                                                                :total_pago,
                                                                :subTotal,
                                                                :igv,
                                                                :tipo_pago,
                                                                :estado_pago,
                                                                :pago_e_y)");

		$stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
		$stmt->bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);
		$stmt->bindParam(":fecha_egre", $datos["fecha_egre"], PDO::PARAM_STR);
		$stmt->bindParam(":hora_egreso", $datos["hora_egreso"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_comprobante", $datos["tipo_comprobante"], PDO::PARAM_STR);
		$stmt->bindParam(":serie_comprobante", $datos["serie_comprobante"], PDO::PARAM_STR);
		$stmt->bindParam(":num_comprobante", $datos["num_comprobante"], PDO::PARAM_STR);
		$stmt->bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt->bindParam(":total_compra", $datos["total_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":total_pago", $datos["total_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":subTotal", $datos["subTotal"], PDO::PARAM_STR);
		$stmt->bindParam(":igv", $datos["igv"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_pago", $datos["tipo_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":estado_pago", $datos["estado_pago"], PDO::PARAM_STR);
		$stmt->bindParam(":pago_e_y", $datos["pago_e_y"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		
		$stmt = null;

	}

	/*=============================================
	REGISTRO DETALLE COMPRA
	=============================================*/

	static public function mdlIngresarDetalleCompra($tabla, $datos){

		// Consulta de inserción con el orden deseado
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                id_egreso,
                                                id_producto, 
                                                numero_javas, 
                                                numero_aves, 
                                                peso_promedio, 
                                                peso_bruto, 
                                                peso_tara, 
                                                peso_merma, 
                                                peso_neto,
                                                precio_compra, 
                                                precio_venta) 
                                          VALUES (
                                                :id_egreso, 
                                                :id_producto, 
                                                :numero_javas, 
                                                :numero_aves, 
                                                :peso_promedio, 
                                                :peso_bruto, 
                                                :peso_tara, 
                                                :peso_merma, 
                                                :peso_neto,
                                                :precio_compra, 
                                                :precio_venta)");

		// Vinculando los parámetros con los valores del array en el orden solicitado
		$stmt->bindParam(":id_egreso", $datos["id_egreso"], PDO::PARAM_INT);
		$stmt->bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);
		$stmt->bindParam(":numero_javas", $datos["numero_javas"], PDO::PARAM_INT);
		$stmt->bindParam(":numero_aves", $datos["numero_aves"], PDO::PARAM_INT);
		$stmt->bindParam(":peso_promedio", $datos["peso_promedio"], PDO::PARAM_STR);
		$stmt->bindParam(":peso_bruto", $datos["peso_bruto"], PDO::PARAM_STR);
		$stmt->bindParam(":peso_tara", $datos["peso_tara"], PDO::PARAM_STR);
		$stmt->bindParam(":peso_merma", $datos["peso_merma"], PDO::PARAM_STR);
		$stmt->bindParam(":peso_neto", $datos["peso_neto"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
		$stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);


		// Ejecutando la consulta
		$stmt->execute();


		if($stmt->execute()){

			return "ok";	

		}else{

			return "error";
		
		}

		
		$stmt = null;

	}

	/*=============================================
	EDITAR COMPRA
	=============================================*/

	static public function mdlEditarCompra($tabla, $datos){
	
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																id_persona = :id_persona, 
																id_usuario = :id_usuario, 
																fecha_egre = :fecha_egre, 
																tipo_comprobante = :tipo_comprobante, 
																serie_comprobante = :serie_comprobante, 
																num_comprobante = :num_comprobante, 
																impuesto = :impuesto
																WHERE id_producto = :id_producto");

		$stmt -> bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
		$stmt -> bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":fecha_egre", $datos["fecha_egre"], PDO::PARAM_STR);
		$stmt -> bindParam(":tipo_comprobante", $datos["tipo_comprobante"], PDO::PARAM_INT);
		$stmt -> bindParam(":serie_comprobante", $datos["serie_comprobante"], PDO::PARAM_STR);
		$stmt -> bindParam(":num_comprobante", $datos["num_comprobante"], PDO::PARAM_STR);
		$stmt -> bindParam(":impuesto", $datos["impuesto"], PDO::PARAM_STR);
		$stmt -> bindParam(":id_producto", $datos["id_producto"], PDO::PARAM_INT);

		if($stmt -> execute()){

			return "ok";
		
		}else{

			return "error";	

		}


		$stmt = null;

	}

	/*=============================================
	ACTUALIZAR COMPRA
	=============================================*/

	static public function mdlActualizarCompra($tabla, $item1, $valor1, $item2, $valor2){

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
	ACTUALIZAR STOCK PRODUCTO
	=============================================*/

	static public function mdlActualizarStockProducto($tblProducto, $idProducto, $cantidad, $precio) {
		
		$stmt = Conexion::conectar()->prepare("UPDATE $tblProducto SET precio_producto = :precio, stock_producto = stock_producto + :cantidad WHERE id_producto = :id_producto");
	
		$stmt->bindParam(":precio", $precio, PDO::PARAM_STR);
		$stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
		$stmt->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
	
		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	
		$stmt = null;
	}


	/*=============================================
	BORRAR COMPRA
	=============================================*/

	static public function mdlBorrarCompra($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_egreso = :id_egreso");
		$stmt -> bindParam(":id_egreso", $datos, PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return "error";	
		}
		$stmt = null;
	}

}
