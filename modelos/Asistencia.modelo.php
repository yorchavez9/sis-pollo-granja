<?php
require_once "Conexion.php";

class ModeloAsistencia
{

    /*=============================================
    REGISTRAR ASISTENCIA
    =============================================*/
    static public function mdlIngresarAsistencia($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
            id_trabajador, fecha_asistencia, hora_entrada, hora_salida, estado, observaciones
        ) VALUES (
            :id_trabajador, :fecha_asistencia, :hora_entrada, :hora_salida, :estado, :observaciones
        )");

        $stmt->bindParam(":id_trabajador", $datos["id_trabajador"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_asistencia", $datos["fecha_asistencia"], PDO::PARAM_STR);
        $stmt->bindParam(":hora_entrada", $datos["hora_entrada"], PDO::PARAM_STR);
        $stmt->bindParam(":hora_salida", $datos["hora_salida"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    MOSTRAR ASISTENCIA
    =============================================*/
    static public function mdlMostrarAsistencia($tablaT, $tablaA, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("
                SELECT DISTINCT a.fecha_asistencia, a.hora_entrada, a.hora_salida 
                FROM $tablaA AS a 
                WHERE a.$item = :$item 
                LIMIT 1
            ");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("
                SELECT DISTINCT fecha_asistencia 
                FROM $tablaA 
                ORDER BY fecha_asistencia DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        }
        $stmt = null;
    }

    /*=============================================
    MOSTRAR LISTA ASISTENCIA
    =============================================*/
    static public function mdlMostrarListaAsistencia($tablaT, $tablaA, $item, $valor)
    {
        $stmt = Conexion::conectar()->prepare("
            SELECT t.id_trabajador, t.nombre, a.estado, a.observaciones 
            FROM $tablaT AS t 
            LEFT JOIN $tablaA AS a ON t.id_trabajador = a.id_trabajador 
            AND a.fecha_asistencia = :fecha_asistencia 
            ORDER BY t.nombre
        ");
        $stmt->bindParam(":fecha_asistencia", $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    BORRAR ASISTENCIA POR FECHA
    =============================================*/
    static public function mdlBorrarAsistenciaPorFecha($tabla, $fecha_asistencia)
    {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE fecha_asistencia = :fecha_asistencia");
        $stmt->bindParam(":fecha_asistencia", $fecha_asistencia, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
MOSTRAR REPORTE DE ASISTENCIA (SIN FILTROS)
=============================================*/
static public function mdlMostrarReporteAsistencia($tablaT, $tablaA, $item, $valor) {
    $stmt = Conexion::conectar()->prepare("
        SELECT 
            a.id_asistencia,
            t.nombre,
            a.fecha_asistencia,
            a.hora_entrada,
            a.hora_salida,
            a.estado,
            a.observaciones
        FROM 
            $tablaA AS a
        INNER JOIN 
            $tablaT AS t ON a.id_trabajador = t.id_trabajador
        ORDER BY a.fecha_asistencia DESC, t.nombre ASC
    ");
    
    $stmt->execute();
    return $stmt->fetchAll();
    
    $stmt = null;
}

    /*=============================================
REPORTE DE ASISTENCIA CON FILTROS
=============================================*/
    static public function mdlReporteAsistencia($tablaT, $tablaA, $filtros)
    {
        // Crear la consulta base
        $sql = "
        SELECT 
            a.id_asistencia,
            t.nombre,
            a.fecha_asistencia,
            a.hora_entrada,
            a.hora_salida,
            a.estado,
            a.observaciones
        FROM 
            $tablaA AS a
        INNER JOIN 
            $tablaT AS t ON a.id_trabajador = t.id_trabajador
        WHERE 1 = 1
    ";

        // Aplicar filtros
        if (!empty($filtros['filtro_trabajador_asistencia'])) {
            $sql .= " AND a.id_trabajador = :id_trabajador";
        }

        if (!empty($filtros['filtro_estado_asistencia'])) {
            $sql .= " AND a.estado = :estado";
        }

        if (!empty($filtros['filtro_fecha_desde_asistencia']) && !empty($filtros['filtro_fecha_hasta_asistencia'])) {
            $sql .= " AND a.fecha_asistencia BETWEEN :fecha_desde AND :fecha_hasta";
        } elseif (!empty($filtros['filtro_fecha_desde_asistencia'])) {
            $sql .= " AND a.fecha_asistencia >= :fecha_desde";
        } elseif (!empty($filtros['filtro_fecha_hasta_asistencia'])) {
            $sql .= " AND a.fecha_asistencia <= :fecha_hasta";
        }

        $sql .= " ORDER BY a.fecha_asistencia DESC, t.nombre ASC";

        $stmt = Conexion::conectar()->prepare($sql);

        // Bind parameters
        if (!empty($filtros['filtro_trabajador_asistencia'])) {
            $stmt->bindParam(":id_trabajador", $filtros['filtro_trabajador_asistencia'], PDO::PARAM_INT);
        }

        if (!empty($filtros['filtro_estado_asistencia'])) {
            $stmt->bindParam(":estado", $filtros['filtro_estado_asistencia'], PDO::PARAM_STR);
        }

        if (!empty($filtros['filtro_fecha_desde_asistencia'])) {
            $stmt->bindParam(":fecha_desde", $filtros['filtro_fecha_desde_asistencia'], PDO::PARAM_STR);
        }

        if (!empty($filtros['filtro_fecha_hasta_asistencia'])) {
            $stmt->bindParam(":fecha_hasta", $filtros['filtro_fecha_hasta_asistencia'], PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll();

        $stmt = null;
    }
}
