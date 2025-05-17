<?php
class Conexion
{
	static public function conectar()
	{
		/* try {
			$link = new PDO(
				"mysql:host=127.0.0.1;port=3308;dbname=base_datos_pollo",
				"root",
				""
			);
			$link->exec("set names utf8");
			return $link;
		} catch (PDOException $e) {
			die("Error de conexión: " . $e->getMessage());
		} */
		try {
			$link = new PDO(
				"mysql:host=localhost;dbname=bd_sis_pollo",
				"root",
				""
			);
			$link->exec("set names utf8");
			return $link;
		} catch (PDOException $e) {
			die("Error de conexión: " . $e->getMessage());
		}
	}
}
