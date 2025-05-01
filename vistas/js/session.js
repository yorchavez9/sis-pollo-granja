$(document).ready(function(){
    async function obtenerSesion() {
        try {
            const response = await fetch('ajax/Sesion.usuario.ajax.php', {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            const data = await response.json();
            return data.status === false ? null : data;
        } catch (error) {
            console.error('Error al obtener sesión:', error);
            return null;
        }
    }
    
    obtenerSesion().then(datos => {
        if (datos) {
            console.log('Sesión obtenida:', datos);
        }
    });
});