<?php

require_once "Conexion.php";

class ModeloModulo
{
    /*=============================================
    MOSTRAR MODULOS
    =============================================*/
    static public function mdlMostrarModulos()
    {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM modulos");
            $stmt->execute();
            return json_encode([
                "status" => true,
                "data" => $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
