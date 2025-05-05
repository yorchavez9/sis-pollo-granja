<?php

class ControladorAsistencia {

    /*=============================================
    REGISTRO DE ASISTENCIA
    =============================================*/
    static public function ctrCrearAsistencia() {
        if(isset($_POST["fecha_asistencia_a"])) {
            $tabla = "asistencia_trabajadores";
            $fecha_asistencia = $_POST["fecha_asistencia_a"];
            $hora_entrada = $_POST["hora_entrada_a"];
            $hora_salida = $_POST["hora_salida_a"];
            $asistencias = json_decode($_POST["datosAsistenciaJSON"], true);

            $respuesta = "";
            $errores = 0;

            foreach ($asistencias as $dato) {
                $datos = array(
                    'id_trabajador' => $dato['id_trabajador'],
                    'fecha_asistencia' => $fecha_asistencia,
                    'hora_entrada' => $hora_entrada,
                    'hora_salida' => $hora_salida,
                    'estado' => $dato['estado'],
                    'observaciones' => $dato['observacion']
                );

                $resultado = ModeloAsistencia::mdlIngresarAsistencia($tabla, $datos);
                if($resultado != "ok") {
                    $errores++;
                }
            }

            if($errores == 0) {
                echo json_encode("ok");
            } else {
                echo json_encode("error");
            }
        }
    }

    /*=============================================
    MOSTRAR ASISTENCIA
    =============================================*/
    static public function ctrMostrarAsistencia($item, $valor) {
        $tablaT = "trabajadores";
        $tablaA = "asistencia_trabajadores";
        $respuesta = ModeloAsistencia::mdlMostrarAsistencia($tablaT, $tablaA, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR LISTA ASISTENCIA
    =============================================*/
    static public function ctrMostrarListaAsistencia($item, $valor) {
        $tablaT = "trabajadores";
        $tablaA = "asistencia_trabajadores";
        $respuesta = ModeloAsistencia::mdlMostrarListaAsistencia($tablaT, $tablaA, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    ACTUALIZAR ASISTENCIA
    =============================================*/
    static public function ctrActualizarAsistencia() {
        if(isset($_POST["fecha_asistencia"])) {
            $tabla = "asistencia_trabajadores";
            $fecha_asistencia = $_POST["fecha_asistencia"];
            $hora_entrada = $_POST["hora_entrada"];
            $hora_salida = $_POST["hora_salida"];
            $datosAsistencia = json_decode($_POST["datosAsistencia"], true);

            // Primero eliminamos los registros existentes para esa fecha
            $borrar = ModeloAsistencia::mdlBorrarAsistenciaPorFecha($tabla, $fecha_asistencia);
            
            if($borrar === "ok") {
                $errores = 0;
                
                // Insertamos los nuevos registros
                foreach($datosAsistencia as $dato) {
                    $nuevo_dato = array(
                        'id_trabajador' => $dato['id_trabajador'],
                        'fecha_asistencia' => $fecha_asistencia,
                        'hora_entrada' => $hora_entrada,
                        'hora_salida' => $hora_salida,
                        'estado' => $dato['estado'],
                        'observaciones' => $dato['observacion']
                    );
                    
                    $resultado = ModeloAsistencia::mdlIngresarAsistencia($tabla, $nuevo_dato);
                    if($resultado != "ok") {
                        $errores++;
                    }
                }
                
                if($errores == 0) {
                    echo json_encode("ok");
                } else {
                    echo json_encode("error");
                }
            } else {
                echo json_encode("error");
            }
        }
    }

    /*=============================================
    BORRAR ASISTENCIA
    =============================================*/
    static public function ctrBorrarAsistencia() {
        if(isset($_POST["fechaAsistenciaDelete"])) {
            $tabla = "asistencia_trabajadores";
            $fecha = $_POST["fechaAsistenciaDelete"];
            
            $respuesta = ModeloAsistencia::mdlBorrarAsistenciaPorFecha($tabla, $fecha);
            
            if($respuesta == "ok") {
                echo json_encode("ok");
            } else {
                echo json_encode("error");
            }
        }
    }

    
}