<?php
class ControladorModulo
{

    /*=============================================
    MOSTRAR MODULOS
    =============================================*/
    static public function ctrMostrarModulos()
    {
        $respuesta = ModeloModulo::mdlMostrarModulos();
        echo $respuesta;
    }
}