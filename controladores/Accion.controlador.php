<?php
class ControladorAccion
{

    /*=============================================
    MOSTRAR ACCIONES
    =============================================*/
    static public function ctrMostrarAcciones()
    {
        $respuesta = ModeloAccion::mdlMostrarAcciones();
        echo $respuesta;
    }
}