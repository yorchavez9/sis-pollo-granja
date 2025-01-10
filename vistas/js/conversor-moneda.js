$(document).ready(function () {
    let currentRate = 0;

    async function getExchangeRate() {
        try {
            const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
            const data = await response.json();
            return data.rates.VES;
        } catch (error) {
            console.error('Error obteniendo tasa:', error);
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
        const usdAmount = parseFloat($('#precio_producto').val());
        const usdAmountEdit = parseFloat($('#edit_precio_producto').val());
        if (!isNaN(usdAmount) && currentRate > 0) {
            const vesAmount = usdAmount * currentRate;
            const vesAmountEdit = usdAmountEdit * currentRate;
            $('#value_precio_producto').text(`${vesAmount.toLocaleString('es-VE', { minimumFractionDigits: 2 })} VES`);
            $('#value_precio_producto_edit').text(`${vesAmountEdit.toLocaleString('es-VE', { minimumFractionDigits: 2 })} VES`);
        }
    }

    function convertVesToUsd() {
        const vesAmount = parseFloat($('#vesAmount').val());
        if (!isNaN(vesAmount) && currentRate > 0) {
            const usdAmount = vesAmount / currentRate;
            $('#usdResult').text(`${usdAmount.toLocaleString('en-US', { minimumFractionDigits: 2 })} USD`);
        }
    }

    updateRate();
    setInterval(updateRate, 60 * 60 * 1000);

    $('#precio_producto').on('input', convertUsdToVes);
    $('#edit_precio_producto').on('input', convertUsdToVes);
    $('#vesAmount').on('input', convertVesToUsd);
});
