<?php
class Conexion
{
	static public function conectar()
	{

		try {
			$link = new PDO(
				"mysql:host=localhost;dbname=", // Espeficar el nombre de la base de datos
				"root", // Usuario de la base de datos
				"" // Contraseña del usuario de la base de datos
			);
			$link->exec("set names utf8");
			return $link;
		} catch (PDOException $e) {
			die("Error de conexión: " . $e->getMessage());
		}
	}
}
