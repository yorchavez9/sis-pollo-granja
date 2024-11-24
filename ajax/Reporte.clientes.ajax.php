<?php

require_once "../controladores/Cliente.controlador.php";
require_once "../modelos/Cliente.modelo.php";

//ELIMINAR CLIENTE
if(isset($_POST["deleteIdCliente"])){

    $borrarCliente = new ControladorCliente();
    $borrarCliente->ctrBorraCliente();

}
//MOSTRAR CLIENTE
else{

    $item = null;
    $valor = null;
    $mostrarClientes = ControladorCliente::ctrMostrarCliente($item, $valor);
    
    $tablaCliente = array();
    
    foreach ($mostrarClientes as $key => $usuario) {
        
        $fila = array(
            'id_persona' => $usuario['id_persona'],
            'tipo_persona' => $usuario['tipo_persona'],
            'razon_social' => $usuario['razon_social'],
            'id_doc' => $usuario['id_doc'],
            'nombre_doc' => $usuario['nombre_doc'],
            'numero_documento' => $usuario['numero_documento'],
            'direccion' => $usuario['direccion'],
            'ciudad' => $usuario['ciudad'],
            'codigo_postal' => $usuario['codigo_postal'],
            'telefono' => $usuario['telefono'],
            'email' => $usuario['email'],
            'sitio_web' => $usuario['sitio_web'],
            'estado_persona' => $usuario['estado_persona'],
            'tipo_banco' => $usuario['tipo_banco'],
            'numero_cuenta' => $usuario['numero_cuenta'],
            'fecha_persona' => $usuario['fecha_persona']
        );
    
        
        $tablaCliente[] = $fila;
    }
    
    
    echo json_encode($tablaCliente);
}


?>
