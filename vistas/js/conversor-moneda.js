$(document).ready(function () {
    let currentRate = 0;

    async function getExchangeRate() {
        try {
            const response = await fetch('https://api.exchangerate-api.com/v4/latest/PEN');
            const data = await response.json();
            return data.rates.USD;
        } catch (error) {
            console.error('Error obteniendo tasa:', error);
            try {
                const response = await fetch('https://open.er-api.com/v6/latest/PEN');
                const data = await response.json();
                return data.rates.USD;
            } catch (error2) {
                console.error('Error en API de respaldo:', error2);
                return null;
            }
        }
    }

    async function updateRate() {
        try {
            const rate = await getExchangeRate();
            if (rate) {
                currentRate = rate;
                $('#currentRate').text(`${rate.toLocaleString('es-VE', { minimumFractionDigits: 2 })} VES/USD`);
                $('#lastUpdate').text(new Date().toLocaleString('es-VE'));
                $('#error').text('');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function convertUsdToVes() {
        const usdAmount = $('#precio_producto').val().trim();
        const usdAmountEdit = $('#edit_precio_producto').val().trim();
    
        if (usdAmount === '' || isNaN(parseFloat(usdAmount)) || parseFloat(usdAmount) === 0) {
            $('#value_precio_producto').text('0,00 USD');
        } else if (currentRate > 0) {
            const vesAmount = parseFloat(usdAmount) * currentRate;
            $('#value_precio_producto').text(`${vesAmount.toLocaleString('es-VE', { minimumFractionDigits: 2 })} USD`);
        }
    
        if (usdAmountEdit === '' || isNaN(parseFloat(usdAmountEdit)) || parseFloat(usdAmountEdit) === 0) {
            $('#value_precio_producto_edit').text('0,00 USD');
        } else if (currentRate > 0) {
            const vesAmountEdit = parseFloat(usdAmountEdit) * currentRate;
            $('#value_precio_producto_edit').text(`${vesAmountEdit.toLocaleString('es-VE', { minimumFractionDigits: 2 })} USD`);
        }
    }
    
    function convertVesToUsd() {
        const vesAmount = $('#vesAmount').val().trim();
    
        if (vesAmount === '' || isNaN(parseFloat(vesAmount)) || parseFloat(vesAmount) === 0) {
            $('#usdResult').text('0,00 USD');
        } else if (currentRate > 0) {
            const usdAmount = parseFloat(vesAmount) / currentRate;
            $('#usdResult').text(`${usdAmount.toLocaleString('en-US', { minimumFractionDigits: 2 })} USD`);
        }
    }
    
    

    updateRate();
    setInterval(updateRate, 60 * 60 * 1000);

    $('#precio_producto').on('input', convertUsdToVes);
    $('#edit_precio_producto').on('input', convertUsdToVes);
    $('#vesAmount').on('input', convertVesToUsd);
});
