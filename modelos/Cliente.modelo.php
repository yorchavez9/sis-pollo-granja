<?php

require_once "Conexion.php";
class ModeloCliente
{
	/*=============================================
	MOSTRAR CLIENTE
	=============================================*/
	static public function mdlMostrarCliente($tablaDoc, $tablaPer, $item, $valor)
	{
		if ($item != null) {
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaDoc as doc inner join $tablaPer as p on doc.id_doc = p.id_doc WHERE $item = :$item");
			$stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetch();
		} else {
			$stmt = Conexion::conectar()->prepare("SELECT * from $tablaDoc as doc inner join $tablaPer as p on doc.id_doc = p.id_doc WHERE p.tipo_persona = 'cliente' ORDER BY p.id_persona DESC");
			$stmt->execute();
			return $stmt->fetchAll();
		}
		$stmt = null;
	}

	/* ===============================================
	MOSTRAR REPORTE CLIENTE PARA LA TABLA DEL MODULO
	=============================================== */
	public static function mdlReporteClientesVenta($filtros)
	{
		// Crear la consulta base
		$sql = "SELECT * FROM
			serie_num_comprobante AS sn 
		INNER JOIN 
			ventas AS v ON sn.id_serie_num = v.id_serie_num 
		INNER JOIN 
			personas AS p ON v.id_persona = p.id_persona
		INNER JOIN usuarios AS u ON u.id_usuario = v.id_usuario WHERE 1 = 1";

		// Arreglo de parámetros para la consulta
		$params = [];

		// Filtro por id_persona (cliente)
		if (!empty($filtros['id_cliente'])) {
			$sql .= " AND p.id_persona = :id_persona";
			$params[':id_persona'] = $filtros['id_cliente'];
		}

		// Filtro por rango de fecha de venta (opcional)
		if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
			$sql .= " AND v.fecha_venta BETWEEN :fecha_desde AND :fecha_hasta";
			$params[':fecha_desde'] = $filtros['fecha_desde'];
			$params[':fecha_hasta'] = $filtros['fecha_hasta'];
		}

		// Filtro por tipo de venta (opcional)
		if (!empty($filtros['tipo_venta'])) {
			$sql .= " AND v.tipo_pago = :tipo_pago";
			$params[':tipo_pago'] = $filtros['tipo_venta'];
		}

		// Ordenar por fecha de venta (DESC)
		$sql .= " ORDER BY v.fecha_venta DESC";

		// Preparar y ejecutar la consulta
		$stmt = Conexion::conectar()->prepare($sql);
		$stmt->execute($params);

		// Retornar los resultados
		return $stmt->fetchAll();
	}

	/* ====================================================
	MOSTRAR REPORTE DE CLIENTE PARA EL PDF
	==================================================== */
	public static function mdlReporteClienteVentaPDF($filtros){
		$sql = "SELECT 
					v.id_venta,
					dv.id_venta,
					v.fecha_venta,
					p.razon_social,
					pd.nombre_producto,
					dv.numero_javas,
					dv.numero_aves,
					dv.peso_promedio,
					dv.peso_bruto,
					dv.peso_tara,
					dv.peso_neto,
					dv.precio_venta,
					v.total_venta,
					v.total_pago,
					(v.total_venta - v.total_pago) AS saldo
				FROM 
					personas AS p
				INNER JOIN 
					ventas AS v ON p.id_persona = v.id_persona
				INNER JOIN 
					detalle_venta AS dv ON v.id_venta = dv.id_venta
				INNER JOIN 
					productos AS pd ON pd.id_producto = dv.id_producto
				WHERE 1 = 1";

		$params = [];

		if (!empty($filtros['id_cliente'])) {
			$sql .= " AND p.id_persona = :id_persona";
			$params[':id_persona'] = $filtros['id_cliente'];
		}

		if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
			$sql .= " AND v.fecha_venta BETWEEN :fecha_desde AND :fecha_hasta";
			$params[':fecha_desde'] = $filtros['fecha_desde'];
			$params[':fecha_hasta'] = $filtros['fecha_hasta'];
		}

		if (!empty($filtros['tipo_venta'])) {
			$sql .= " AND v.tipo_pago = :tipo_pago";
			$params[':tipo_pago'] = $filtros['tipo_venta'];
		}

		$sql .= " ORDER BY v.fecha_venta DESC";

		$stmt = Conexion::conectar()->prepare($sql);
		$stmt->execute($params);

		return $stmt->fetchAll();
	}

	/* ===============================================
	MOSTRAR REPORTE CLIENTE VENTAS LISTA
	=============================================== */
	static public function mdlMostrarClienteVentas()
	{
		// Sin filtros
		$sql = "SELECT 
		v.id_venta,
		u.nombre_usuario,
		u.id_usuario,
		p.id_persona,
		p.razon_social,
		p.numero_documento,
		p.direccion,
		p.telefono,
		p.email,
		sn.tipo_comprobante_sn, 
		sn.serie_prefijo,
		v.num_comprobante,
		v.impuesto, 
		v.tipo_pago, 
		v.total_venta,
		v.sub_total,
		v.igv, 
		v.total_pago, 
		v.fecha_venta, 
		v.hora_venta, 
		v.estado_pago
		FROM 
			serie_num_comprobante AS sn 
		INNER JOIN 
			ventas AS v ON sn.id_serie_num = v.id_serie_num 
		INNER JOIN 
			personas AS p ON v.id_persona = p.id_persona
		INNER JOIN usuarios AS u ON u.id_usuario = v.id_usuario ORDER BY  v.id_venta DESC";

			$stmt = Conexion::conectar()->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll(); // Retorna todos los resultados
	}

	/*=============================================
	MOSTRAR TOTAL DE CLIENTES
	=============================================*/

	static public function mdlMostrarTotalClientes($tablaDoc, $tablaPer, $item, $valor)
	{

		$stmt = Conexion::conectar()->prepare("SELECT tipo_persona, COUNT(*) as total_cliente FROM $tablaPer WHERE tipo_persona = 'cliente'");

		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result['total_cliente'];
	}

	/*=============================================
	REGISTRAR CLIENTE
	=============================================*/

	static public function mdlIngresarCliente($tabla, $datos)
	{

		// Verificar si el email está vacío. Si es vacío, lo dejamos como NULL.
		$email = !empty($datos["email"]) ? $datos["email"] : NULL;
		// Continuar con la inserción
		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                                                tipo_persona, 
                                                razon_social, 
                                                id_doc, 
                                                numero_documento, 
                                                direccion, 
                                                ciudad, 
                                                codigo_postal, 
                                                telefono, 
                                                email,
                                                sitio_web,
                                                tipo_banco,
                                                numero_cuenta
                                                )
                                                VALUES (
                                                :tipo_persona, 
                                                :razon_social, 
                                                :id_doc, 
                                                :numero_documento, 
                                                :direccion, 
                                                :ciudad, 
                                                :codigo_postal, 
                                                :telefono, 
                                                :email,
                                                :sitio_web,
                                                :tipo_banco,
                                                :numero_cuenta
                                                )");

		$stmt->bindParam(":tipo_persona", $datos["tipo_persona"], PDO::PARAM_STR);
		$stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
		$stmt->bindParam(":id_doc", $datos["id_doc"], PDO::PARAM_INT);
		$stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo_postal", $datos["codigo_postal"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $email, PDO::PARAM_STR);  // Aquí se pasa el valor de email, que puede ser NULL
		$stmt->bindParam(":sitio_web", $datos["sitio_web"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_banco", $datos["tipo_banco"], PDO::PARAM_STR);
		$stmt->bindParam(":numero_cuenta", $datos["numero_cuenta"], PDO::PARAM_STR);
		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	EDITAR CLIENTE
	=============================================*/

	static public function mdlEditarCliente($tabla, $datos)
	{
		$email = !empty($datos["email"]) ? $datos["email"] : NULL;
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																razon_social = :razon_social, 
																id_doc = :id_doc, 
																numero_documento = :numero_documento, 
																direccion = :direccion, 
																ciudad = :ciudad, 
																codigo_postal = :codigo_postal, 
																telefono = :telefono, 
																email = :email, 
																sitio_web = :sitio_web, 
																tipo_banco = :tipo_banco,
																numero_cuenta = :numero_cuenta
																WHERE id_persona = :id_persona");

		$stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
		$stmt->bindParam(":id_doc", $datos["id_doc"], PDO::PARAM_INT);
		$stmt->bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR);
		$stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
		$stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
		$stmt->bindParam(":codigo_postal", $datos["codigo_postal"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt->bindParam(":sitio_web", $datos["sitio_web"], PDO::PARAM_STR);
		$stmt->bindParam(":tipo_banco", $datos["tipo_banco"], PDO::PARAM_STR);
		$stmt->bindParam(":numero_cuenta", $datos["numero_cuenta"], PDO::PARAM_STR);
		$stmt->bindParam(":id_persona", $datos["id_persona"], PDO::PARAM_INT);
		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	ACTUALIZAR CLIENTE
	=============================================*/

	static public function mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2)
	{
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
		$stmt->bindParam(":" . $item1, $valor1, PDO::PARAM_STR);
		$stmt->bindParam(":" . $item2, $valor2, PDO::PARAM_STR);
		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}

	/*=============================================
	BORRAR CLIENTE
	=============================================*/

	static public function mdlBorrarCliente($tabla, $datos)
	{
		$stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_persona = :id_persona");
		$stmt->bindParam(":id_persona", $datos, PDO::PARAM_INT);
		if ($stmt->execute()) {
			return "ok";
		} else {
			return "error";
		}
	}
}
