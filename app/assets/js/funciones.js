const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
let currentRequest = null;
$(document).on('input', 'input.decimal-only', function () {
    let val = $(this).val();

    // Valida el formato decimal
    if (!/^\d*\.?\d{0,2}$/.test(val)) {
        val = val.replace(/[^0-9.]/g, '');

        const parts = val.split('.');
        if (parts.length > 2) {
            val = parts[0] + '.' + parts[1];
        }

        if (parts[1]) {
            val = parts[0] + '.' + parts[1].substring(0, 2);
        }

        $(this).val(val);
    }
});
$('.select2').select2({
    theme: 'bootstrap4'
});
$(document).on('blur', 'input.decimal-only', function () {
    let val = $(this).val();
    const tr = $(this).closest('tr');
    if (val) {
        let num = parseFloat(val);
        if (!isNaN(num)) {
            $(this).val(num.toFixed(2));
        } else {
            $(this).val('');
        }
    }
    //table.row(tr).invalidate().draw(false);
});
$(document).on('input', '.numer-only', function () {
    let valor = $(this).val();
    if (!/^\d*$/.test(valor)) {
        $(this).val(valor.replace(/\D/g, ''));
    }
});
$(document).on('input', '.porcentaje-only', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 3) {
        this.value = this.value.slice(0, 3);
    }
    let num = parseInt(this.value || '0', 10);
    if (num > 100) {
        this.value = '100';
    }
});
$(document).on('input', '.temperature', function () {
    let value = this.value;
    value = value.replace(/[^0-9.-]/g, '');
    value = value.replace(/(?!^)-/g, '');
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    const match = value.match(/^(-?\d+)(\.\d{0,2})?/);
    if (match) {
        value = match[1] + (match[2] || '');
    }
    let num = parseFloat(value);
    if (!isNaN(num)) {
        if (num > 100) {
            value = '100';
        } else if (num < -100) {
            value = '-100';
        }
    }
    this.value = value;
});

function sendAjax({
    endpoint,
    type = 'GET',
    data = {},
    beforeSend = () => { },
    onSuccess,
    onComplete = () => { }
}) {

    const ajaxConfig = {
        url: ruta_web + endpoint,
        type,
        beforeSend,
        success: function (response) {
            try {
                onSuccess(response);
            } catch (e) {
                console.log(e);
                Notify(2, 'Ocurrio un error inesperado');
            }
        },
        error: function () {
            Notify(2, 'Ocurrio un error inesperado');
        },
        complete: function () {
            onComplete();
        }
    };
    if (type.toUpperCase() !== 'GET') {
        ajaxConfig.contentType = 'application/json';
        ajaxConfig.data = JSON.stringify(data);
    } else if (Object.keys(data).length > 0) {
        ajaxConfig.data = data;
    }
    $.ajax(ajaxConfig);
}

function bindButtonClick(buttonClass, callback, dataAttribute = null) {
    $(document).on('click', buttonClass, function () {
        const value = dataAttribute ? $(this).data(dataAttribute) : null;
        if (callback) {
            callback(value);
        }
    });
}

function restartTooplip() {
    $('[data-bs-toggle="tooltip"]').tooltip('dispose');
    $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
}
function buildModalGeneric(idModal, accion, titulo, subtitulo, icon, campos = []) {
    const $modal = $('<div>').addClass('modal fade').attr({
        id: idModal,
        tabindex: '-1',
        'aria-labelledby': idModal + 'Label',
        'aria-hidden': 'true',
        'data-bs-backdrop': 'static',
        'data-bs-keyboard': 'false'
    });
    const $dialog = $('<div>').addClass('modal-dialog');
    const $content = $('<div>').addClass('modal-content');
    const $header = $('<div>').addClass('modal-header')
        .append(
            $('<div>').addClass('d-flex user-select-none').append(
                $('<div>').addClass('d-flex align-items-center justify-content-center').append(
                    $('<span>').addClass('material-symbols-outlined h1 text-primary').text(icon)
                ),
                $('<div>').addClass('ms-2').append(
                    $('<div>').addClass('h4 fw-semibold').text(titulo),
                    $('<h6>').addClass('card-subtitle mb-2 text-body-secondary').text(subtitulo)
                ),
            )
        )
        .append(
            $('<button>')
                .addClass('btn-close')
                .attr({
                    type: 'button',
                    'data-bs-dismiss': 'modal',
                    'aria-label': 'Close'
                })
        );
    const $body = $('<div>').addClass('modal-body');
    campos.forEach(campo => {
        const $group = $('<div>').addClass('mb-2');
        const $label = $('<label>')
            .addClass('form-label')
            .attr('for', campo.id)
            .text(campo.label);
        if (campo.required) {
            $label.append(
                $('<span>')
                    .addClass('text-danger ms-1')
                    .text('*')
            );
        }
        $group.append($label);
        $group.append(
            $('<input>')
                .attr({
                    type: campo.type || 'text',
                    id: campo.id,
                    placeholder: campo.placeholder || '',
                    required: campo.required || false
                })
                .addClass(campo.class || 'form-control')
        );

        $body.append($group);
    });
    const $footer = $('<div>').addClass('modal-footer')
        .append(
            $('<button>')
                .addClass('btn btn-primary' + (accion ? ' ' + accion : ''))
                .attr({
                    type: 'button'
                })
                .text('Guardar')
        );
    $content.append($header, $body, $footer);
    $dialog.append($content);
    $modal.append($dialog);

    //$('body').append($modal);

    return $modal;
}

function validateRequiredFields(fields) {
    for (const field of fields) {
        const value = $(field).val();

        if (value === null || value === undefined || value.toString().trim() === '') {
            $(field).focus();
            return false;
        }
    }

    return true;
}

function loadingTable(containerId, rows = 5) {
    let html = `<div class="table-skeleton">`;
    for (let i = 0; i < rows; i++) {
        html += `
                <div class="skeleton-row">
                    <div class="skeleton skeleton-checkbox"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-date"></div>
                    <div class="skeleton skeleton-total"></div>
                    <div class="skeleton skeleton-badge"></div>
                </div>
            `;
    }
    html += '</div>';
    $('#' + containerId).empty().html(html);
}
function loadingStats(containerId, cards = 4) {
    let html = '<div class="row g-3 mb-3 user-select-none">';
    for (let i = 0; i < cards; i++) {
        html += `
            <div class="col-md-3">
                <div class="card shadow border mb-1">
                    <div class="card-body d-flex align-items-center gap-3 p-2">

                        <div class="skeleton skeleton-icon"></div>

                        <div class="flex-grow-1">
                            <div class="skeleton skeleton-title mb-2"></div>
                            <div class="skeleton skeleton-number mb-2"></div>
                            <div class="skeleton skeleton-subtitle"></div>
                        </div>

                    </div>
                </div>
            </div>
        `;
    }
    html += '</div>';
    $('#' + containerId).empty().html(html);
}

function loadingChart(containerId) {
    let html = `<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="skeleton skeleton-title mb-2"></div>
                <div class="skeleton w-100" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-2">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="skeleton skeleton-icon"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton skeleton-title mb-2"></div>
                            <div class="skeleton skeleton-number mb-2"></div>
                            <div class="skeleton skeleton-subtitle"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="skeleton skeleton-icon"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton skeleton-title mb-2"></div>
                            <div class="skeleton skeleton-number mb-2"></div>
                            <div class="skeleton skeleton-subtitle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>`;
    $('#' + containerId).empty().html(html);
}
