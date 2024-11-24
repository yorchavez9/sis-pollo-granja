<?php
require_once "Conexion.php";

class ModeloSerieNumero
{
    /*=============================================
	MOSTRAR SERIE NUMERO
	=============================================*/
    static public function mdlMostrarSerieNumero($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * from $tabla ORDER BY id_serie_num DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt = null;
    }

    /*=============================================
	REGISTRAR SERIE NUMERO
	=============================================*/

    static public function mdlIngresarSerieNumero($tabla, $datos) {
        // Verificar si el tipo de comprobante ya existe
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE tipo_comprobante_sn = :tipo_comprobante_sn");
        $stmt->bindParam(":tipo_comprobante_sn", $datos["tipo_comprobante_sn"], PDO::PARAM_STR);
        $stmt->execute();

        // Si ya existe, retornar un error
        if ($stmt->rowCount() > 0) {
            return "error_tipo_comprobante_existente";  // Mensaje para indicar que ya existe
        }

        // Si no existe, proceder con la inserción
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(tipo_comprobante_sn, 
                                                                serie_prefijo,
                                                                folio_inicial,
                                                                folio_final)
                                                            VALUES (:tipo_comprobante_sn, 
                                                                :serie_prefijo, 
                                                                :folio_inicial,
                                                                :folio_final)");

        // Vincular los parámetros correctamente
        $stmt->bindParam(":tipo_comprobante_sn", $datos["tipo_comprobante_sn"], PDO::PARAM_STR);
        $stmt->bindParam(":serie_prefijo", $datos["serie_prefijo"], PDO::PARAM_STR);
        $stmt->bindParam(":folio_inicial", $datos["folio_inicial"], PDO::PARAM_INT);
        $stmt->bindParam(":folio_final", $datos["folio_final"], PDO::PARAM_INT);

        // Ejecutar la consulta y devolver el resultado
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }



    /*=============================================
	EDITAR SERIE NUMERO
	=============================================*/

    static public function mdlEditarSerieNumero($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
																tipo_comprobante_sn = :tipo_comprobante_sn, 
																serie_prefijo = :serie_prefijo,
																folio_inicial = :folio_inicial,
																folio_final = :folio_final
																WHERE id_serie_num = :id_serie_num");

        $stmt->bindParam(":tipo_comprobante_sn", $datos["tipo_comprobante_sn"], PDO::PARAM_STR);
        $stmt->bindParam(":serie_prefijo", $datos["serie_prefijo"], PDO::PARAM_STR);
        $stmt->bindParam(":folio_inicial", $datos["folio_inicial"], PDO::PARAM_INT);
        $stmt->bindParam(":folio_final", $datos["folio_final"], PDO::PARAM_INT);
        $stmt->bindParam(":id_serie_num", $datos["id_serie_num"], PDO::PARAM_INT);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }


        $stmt = null;
    }

    /*=============================================
	ACTUALIZAR SERIE NUMERO
	=============================================*/

    static public function mdlActualizarSerieNumero($tabla, $item1, $valor1, $item2, $valor2)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");

        $stmt->bindParam(":" . $item1, $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":" . $item2, $valor2, PDO::PARAM_STR);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }


        $stmt = null;
    }

    /*=============================================
	BORRAR SERIE NUMERO
	=============================================*/

    static public function mdlBorrarSerieNumero($tabla, $datos)
    {

        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_serie_num = :id_serie_num");

        $stmt->bindParam(":id_serie_num", $datos, PDO::PARAM_INT);

        if ($stmt->execute()) {

            return "ok";
        } else {

            return "error";
        }


        $stmt = null;
    }
}
