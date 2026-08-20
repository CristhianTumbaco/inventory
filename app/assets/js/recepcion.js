$(document).ready(function () {
    //activar tabs del menu
    $('#recepcion').addClass('selected');
    $('#recepcion a').addClass('active');

    //formatear tabla
    if ($('#table').length) {
        formatDatatable('#table', 0, 'asc');
    }
    bindButtonClick('.btn-reception', createReception);
});
//guardar distribucion
function createReception() {
    const products = [];
    document.querySelectorAll('.product-item').forEach(item => {
        const product = {
            id: item.dataset.productId,
            detail_id: item.dataset.detailId,
            production_date: item.querySelector('.production_date')?.value || '',
            expiration_date: item.querySelector('.expiration_date')?.value || '',
            batch_code: item.querySelector('.batch_code')?.value || '',
            entry_temperature: item.querySelector('.entry_temperature')?.value || '',
            product_condition: item.querySelector('.product_condition')?.value || '',
            package_weight_lb: item.querySelector('.package_weight_lb')?.value || '',
            process: item.querySelector('.proceso')?.value || ''
        };
        if (product.process === '1') {
            product.location_id = item.querySelector('.location')?.value || '';
            product.package_count = item.querySelector('.package_count')?.value || '';
            product.quantity = item.querySelector('.quantity')?.value || '';
        } else if (product.process === '2') {
            product.locations = [];
            item.querySelectorAll('tbody.tabledata tr').forEach(row => {
                product.locations.push({
                    location_id: row.querySelector('.location-detail')?.value || '',
                    package_count: row.querySelector('.presentation')?.value || '',
                    quantity: row.querySelector('.units')?.value || ''
                });
            });
        }
        products.push(product);
    });
    const validation = validateReception(products);
    if (validation !== true) {
        Notify(2, validation);
        return;
    }
    sendAjax({
        endpoint: '/Recepcion/create',
        type: 'POST',
        data: products,
        beforeSend: function () {
            $('.btn-reception').prop('disabled', true).empty().append(
                $('<span>').addClass('spinner-border spinner-border-sm').attr({ role: 'status', 'aria-hidden': 'true' }).text(''),
                $('<span>').addClass('ms-2').text('Guardando...')
            );
        },
        onSuccess: (data) => {
            if (data.success) {
                Notify(1, data.message);
                setTimeout(() => {
                    window.location.href = '../Recepcion';
                }, 1500);
            } else {
                $('.btn-reception').prop('disabled', false).empty().append(
                    $('<span>').addClass('material-symbols-outlined').text('check'),
                    $('<span>').addClass('ms-2').text('Confirmar recepción')
                );
                Notify(2, data.message);
            }
        }
    });
}
function validateReception(data) {
    for (let i = 0; i < data.length; i++) {
        const product = data[i];
        // Campos base obligatorios
        const requiredFields = [
            { field: 'production_date', label: 'fecha de producción' },
            { field: 'expiration_date', label: 'fecha de vencimiento' },
            { field: 'batch_code', label: 'código de lote' },
            { field: 'entry_temperature', label: 'temperatura de llegada' },
            { field: 'product_condition', label: 'condición del producto' }
        ];
        for (let j = 0; j < requiredFields.length; j++) {
            const req = requiredFields[j];
            if (!product[req.field] || product[req.field].toString().trim() === '') {
                return `Producto ${i + 1}: la ${req.label} es obligatoria`;
            }
        }
        // Proceso 1
        if (product.process === "1") {
            if (!product.location_id || product.location_id === '') {
                return `Producto ${i + 1}: debe seleccionar una ubicación`;
            }
        }
        // Proceso 2
        if (product.process === "2") {
            if (!product.locations || product.locations.length === 0) {
                return `Producto ${i + 1}: debe agregar al menos una ubicación`;
            }
            for (let k = 0; k < product.locations.length; k++) {
                const loc = product.locations[k];
                if (!loc.location_id) {
                    return `Producto ${i + 1}, fila ${k + 1}: debe seleccionar una ubicación`;
                }
                if (!loc.package_count) {
                    return `Producto ${i + 1}, fila ${k + 1}: la cantidad es obligatoria`;
                }
                if (!loc.quantity) {
                    return `Producto ${i + 1}, fila ${k + 1}: las cantidad es obligatoria`;
                }
            }
        }
    }
    return true;
}
//proceso de calculo y distribución
$(document).on('input', '.presentation', function () {
    const table = $(this).closest('table');
    recalculateDistribution(table);
});
$(document).on('input', '.units', function () {
    const table = $(this).closest('table');
    recalculateDistribution(table);
});
function recalculateDistribution(table) {
    //cajas compradas
    const totalpackage_count = parseFloat(
        table.find('thead th[data-package_count]').data('package_count')
    ) || 0;
    //peso unitario de las cajas
    const totalpackage_weight_lb = parseFloat(
        table.find('thead th[data-package_weight_lb]').data('package_weight_lb')
    ) || 0;
    //peso total en libras
    const totalquantity = parseFloat(
        table.find('thead th[data-quantity]').data('quantity')
    ) || 0;
    let assignedQuantity = 0;
    let assignedWeight = 0;

    table.find('tbody tr').each(function () {
        const quantity = parseFloat(
            $(this).find('.presentation').val()
        ) || 0;
        assignedQuantity += quantity;
        const percentage = totalpackage_count > 0
            ? ((quantity / totalpackage_count) * 100).toFixed(2)
            : 0;
        $(this).find('.percentage').text(`${percentage}%`);
        if (totalpackage_weight_lb > 0) {
            const calUnit = (
                quantity * totalpackage_weight_lb
            ).toFixed(2);
            $(this).find('.units').val(calUnit);
            assignedWeight += parseFloat(calUnit);
            $('.units').prop('readonly', true);
        } else {
            assignedWeight += parseFloat(
                $(this).find('.units').val()
            ) || 0;
        }

    });

    const pendingPackages = totalpackage_count - assignedQuantity;
    const totalPurchasedWeight = totalquantity;

    //const pendingWeight = Math.max(0, totalPurchasedWeight - assignedWeight).toFixed(2);
    const pendingWeight = (totalPurchasedWeight - assignedWeight).toFixed(2);
    const container = table.closest('.distribution-container');
    container.find('.assigned-total').text(
        `${assignedWeight} lb (${assignedQuantity} cajas)`
    );
    container.find('.pending-total').text(
        `${pendingWeight} lb (${pendingPackages} cajas)`
    );
    const exceedPackages = assignedQuantity > totalpackage_count;
    const exceedWeight = assignedWeight > totalPurchasedWeight;
    const packagesMatch = assignedQuantity === totalpackage_count;

    const weightMatch = Math.abs(assignedWeight - totalquantity) < 0.01;
    if (exceedPackages || exceedWeight) {
        table.addClass('table-danger');
        container.find('.assigned-total').removeClass('text-warning').addClass('text-danger');
        container.find('.pending-total').removeClass('text-warning').addClass('text-danger');
        const $message = messageAssigned('error', 'text-danger', 'Cantidad excedida', `La distribución registrada supera el inventario recibido. Verifique las cantidades asignadas en las ubicaciones.`);
        container.find('.messageAssigned').empty().append($message);
        $('.btn-reception').prop('disabled', true);
    } else {
        container.find('.assigned-total').removeClass('text-danger').addClass('text-warning');
        container.find('.pending-total').removeClass('text-danger').addClass('text-warning');
        table.removeClass('table-danger');
        const $message = messageAssigned('warning', 'text-warning', 'Distribución Pendiente', 'La cantidad asignada aún no coincide con la cantidad recibida.');
        container.find('.messageAssigned').empty().append($message);
        $('.btn-reception').prop('disabled', true);
        if (packagesMatch && weightMatch) {
            container.find('.assigned-total').removeClass('text-warning').addClass('text-success');
            container.find('.pending-total').removeClass('text-warning').addClass('text-success');
            const $message = messageAssigned('check_circle_unread', 'text-success', 'Distribución Completa', 'La cantidad asignada coincide con la cantidad comprada.');
            container.find('.messageAssigned').empty().append($message);
            $('.btn-reception').prop('disabled', false);
        }
    }
}




//FUNCIONES
//mensaje de distribucion
function messageAssigned(icono, font, title, description) {

    const $div = $('<div>').addClass('d-flex align-items-center');
    $div.append(
        $('<span>')
            .addClass(`material-symbols-outlined ${font} h2`)
            .text(icono),
        $('<div>')
            .addClass('m-0 ms-3')
            .append(
                $('<b>')
                    .addClass(`h5 ${font}`)
                    .text(title),
                $('<p>')
                    .addClass(font)
                    .text(description)
            )
    );
    return $div;
}
//tipo de proceso de distribucion
$(document).on('change', '.proceso', function () {
    const proceso = $(this).val();
    const product = $(this).closest('.product-item');
    const locationContainer = product.find('.location-container');
    const distributionContainer = product.find('.distribution-container');
    if (proceso == '1') {
        $('.btn-reception').prop('disabled', false);
        locationContainer.removeClass('d-none');
        distributionContainer.addClass('d-none');
    } else if (proceso == '2') {
        $('.btn-reception').prop('disabled', true);
        locationContainer.addClass('d-none');
        distributionContainer.removeClass('d-none');
    } else {
        $('.btn-reception').prop('disabled', false);
        locationContainer.addClass('d-none');
        distributionContainer.addClass('d-none');

    }
});
//agregar template
$(document).on('click', '.add-location', function () {
    const tbody = $(this)
        .closest('table')
        .find('tbody');
    const row = $('#location-row-template').html();
    tbody.append(row);
});
//eliminar item de tabla
$(document).on('click', '.delete-row', function () {
    const table = $(this).closest('table');
    const tbody = table.find('tbody');
    if (tbody.find('tr').length > 1) {
        $(this).closest('tr').remove();
    }
    recalculateDistribution(table);
});
//validacion de locations
$(document).on('change', '.location-detail', function () {
    const currentValue = $(this).val();
    const product = $(this).closest('.product-item');
    let count = 0;
    product.find('.location-detail').each(function () {
        if ($(this).val() === currentValue) {
            count++;
        }
    });
});