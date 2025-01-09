
let currentRate = 0;

// Función para obtener la tasa de cambio
async function getExchangeRate() {
    try {
        // Usando exchangerate-api.com (reemplaza YOUR_API_KEY con una clave real)
        const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
        const data = await response.json();
        return data.rates.VES;
    } catch (error) {
        console.error('Error obteniendo tasa:', error);
        // Plan B: Usar otra API si la primera falla
        try {
            const response = await fetch('https://open.er-api.com/v6/latest/USD');
            const data = await response.json();
            return data.rates.VES;
        } catch (error2) {
            console.error('Error en API de respaldo:', error2);
            return null;
        }
    }
}

// Función para actualizar la tasa
async function updateRate() {
    try {
        const rate = await getExchangeRate();
        if (rate) {
            currentRate = rate;
            document.getElementById('currentRate').textContent =
                `${rate.toLocaleString('es-VE', { minimumFractionDigits: 2 })} VES/USD`;
            document.getElementById('lastUpdate').textContent =
                new Date().toLocaleString('es-VE');
            document.getElementById('error').textContent = '';
        }
    } catch (error) {
        document.getElementById('error').textContent =
            'Error al obtener la tasa de cambio. Intentando nuevamente...';
        console.error('Error:', error);
    }
}

// Función para convertir USD a VES
function convertUsdToVes() {
    const usdAmount = parseFloat(document.getElementById('precio_producto').value);
    if (!isNaN(usdAmount) && currentRate > 0) {
        const vesAmount = usdAmount * currentRate;
        document.getElementById('value_precio_producto').textContent =
            `${vesAmount.toLocaleString('es-VE', { minimumFractionDigits: 2 })} VES`;
    }
}

// Función para convertir VES a USD
function convertVesToUsd() {
    const vesAmount = parseFloat(document.getElementById('vesAmount').value);
    if (!isNaN(vesAmount) && currentRate > 0) {
        const usdAmount = vesAmount / currentRate;
        document.getElementById('usdResult').textContent =
            `${usdAmount.toLocaleString('en-US', { minimumFractionDigits: 2 })} USD`;
    }
}

// Actualizar cada hora
updateRate();
setInterval(updateRate, 60 * 60 * 1000);

// Eventos de entrada para conversión en tiempo real
document.getElementById('precio_producto').addEventListener('input', convertUsdToVes);
document.getElementById('vesAmount').addEventListener('input', convertVesToUsd);
