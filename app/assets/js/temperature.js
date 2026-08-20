$(document).ready(function () {
    //activar tabs del menu
    $('#temperatura').addClass('selected');
    $('#temperatura a').addClass('active');
    bindButtonClick('.btn-register', fetchRegister);
    bindButtonClick('.btn-temperature', createTemperature);
    initView();
});
const commonsFilters = {
    period: 'week',
    dateFrom: null,
    dateTo: null
};
function initView() {
    buildStatus();
    fetchList();
}
function fetchList() {
    sendAjax({
        endpoint: '/Temperatura/list',
        type: 'POST',
        data: commonsFilters,
        beforeSend: function () {
            loadingTable('lists');
        },
        onSuccess: (data) => {
            setTimeout(() => {
                if (data.success) {
                    buildList(data.data);
                } else {
                    Notify(2, data.message);
                }
            }, 1200);
        }
    });
}
function buildList(data) {
    let rows = '';
    data.forEach(item => {
        rows += `
            <tr>
                <td>${(item.name)}</td>
                <td>${(Number(item.temperature))}</td>
                <td>${(item.scale)}</td>
                <td>${(item.humidity || '')}</td>
                <td>${(item.recorded_at)}</td>
            </tr>
        `;
    });

    const html = `
        <div class="table-responsive mt-3">
            <table class="table table-hover border text-nowrap" id="table">
                <thead class="bg-light user-select-none">
                    <tr>
                        <th>Ubicación</th>
                        <th>Temperatura</th>
                        <th>Escala</th>
                        <th>Humedad</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
    `;

    $('#lists').html(html);
    formatDatatable('#table', 4, 'desc');
}

function buildStatus() {
    let html = `
    <div class="d-flex flex-column flex-md-row gap-2">
        <div class="w-100 w-md-auto"></div>
        <div class="ms-md-auto">
            <div class="dropdown">
                <button class="btn btn-light btn-lg d-flex align-items-center dropdown-toggle w-100 w-md-auto" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="material-symbols-outlined">view_week</span>
                    <span class="ms-2" id="periodoSeleccionado">Esta semana</span>
                </button>
                <div class="dropdown-menu p-2" style="min-width: 280px;">
                    <a class="dropdown-item d-flex align-items-center periodo-item"
                        href="#"
                        data-value="all">
                        <span class="material-symbols-outlined">date_range</span>
                        <span class="ms-2">Todos</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center periodo-item"
                        href="#"
                        data-value="today">
                        <span class="material-symbols-outlined">today</span>
                        <span class="ms-2">Hoy</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center periodo-item"
                        href="#"
                        data-value="week">
                        <span class="material-symbols-outlined">view_week</span>
                        <span class="ms-2">Esta semana</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center periodo-item"
                        href="#"
                        data-value="month">
                        <span class="material-symbols-outlined">calendar_month</span>
                        <span class="ms-2">Este mes</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center periodo-item"
                        href="#"
                        data-value="last_month">
                        <span class="material-symbols-outlined">history</span>
                        <span class="ms-2">Mes pasado</span>
                    </a>
                    <hr class="dropdown-divider">
                    <a class="dropdown-item d-flex align-items-center periodo-item"
                        href="#"
                        data-value="custom"
                        id="btnPersonalizado">
                        <span class="material-symbols-outlined">edit_calendar</span>
                        <span class="ms-2">Personalizado</span>
                    </a>
                    <div id="rangoPersonalizado" class="px-2 pt-2 d-none">
                        <label class="form-label small mb-1">Desde</label>
                        <input
                            type="date"
                            id="fechaDesde"
                            class="form-control form-control-sm mb-2">
                        <label class="form-label small mb-1">Hasta</label>
                        <input
                            type="date"
                            id="fechaHasta"
                            class="form-control form-control-sm mb-2">
                        <button
                            id="btnAplicarRango"
                            class="btn btn-primary btn-sm w-100">
                            Aplicar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="ms-2">
            <div class="position-relative">
                <span class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y ms-3">search</span>
                <input type="text" class="form-control form-control-lg ps-5" id="tablebuscador" placeholder="¿Buscas algo?">
            </div>
        </div>
    </div>`;
    $('#status').empty().html(html);
    dropdownFormat();
}

function dropdownFormat() {
    const periodoSeleccionado = document.getElementById('periodoSeleccionado');
    const rangoPersonalizado = document.getElementById('rangoPersonalizado');
    if (!periodoSeleccionado) return;
    document.querySelectorAll('.periodo-item').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const value = this.dataset.value;
            const text = this.querySelector('.ms-2').textContent;
            if (value === 'custom') {
                rangoPersonalizado.classList.remove('d-none');
                return;
            }
            commonsFilters.period = value;
            commonsFilters.dateFrom = null;
            commonsFilters.dateTo = null;
            periodoSeleccionado.textContent = text;
            rangoPersonalizado.classList.add('d-none');
            fetchList();
        });

    });
    document.getElementById('btnAplicarRango')?.addEventListener('click', function () {
        const desde = document.getElementById('fechaDesde').value;
        const hasta = document.getElementById('fechaHasta').value;
        if (!desde || !hasta) {
            Notify(2, 'Debe seleccionar ambas fechas');
            return;
        }
        commonsFilters.period = 'custom';
        commonsFilters.dateFrom = desde;
        commonsFilters.dateTo = hasta;

        const desdeFormateado = formatearFecha(desde);
        const hastaFormateado = formatearFecha(hasta);
        periodoSeleccionado.textContent =
            `${desdeFormateado} - ${hastaFormateado}`;

        rangoPersonalizado.classList.add('d-none');
        fetchList();
    });
}
function formatearFecha(fecha) {

    const [year, month, day] = fecha.split('-');

    return `${day}/${month}/${year}`;
}
function fetchRegister() {
    sendAjax({
        endpoint: '/Temperatura/catalogs',
        type: 'GET',
        beforeSend: function () {
            modalGeneric('modalRegister', 'btn-temperature', 'Registrar Temperatura', 'Registre los cambios de temperatura de los cuartos frios.', 'snowflake', '');
        },
        onSuccess: (data) => {
            if (data.success) {
                buildModal(data.data);
            } else {
                Notify(2, data.message);
            }
        }
    });
}
function buildModal(data) {
    const locations = data.locations;
    let options = '<option value="">Seleccione...</option>';
    locations.forEach(item => {
        options += `<option value="${item.id}">${item.name}</option>`;

    });
    let html = `
    <div class="mb-2">
        <label class="fw-bold">Ubicación</label>
        <select class="form-select" id="locations">
        ${options}
        </select>
    </div>
    <div class="mb-2">
        <label class="fw-bold">Temperatura</label>
        <input type="text" id="temperature" class="form-control temperature" placeholder="0 °C">
    </div>
    <div class="mb-2">
        <label class="fw-bold">Humedad</label>
        <input type="text" id="humidity" class="form-control temperature" placeholder="0.00">
    </div>
    <div class="mb-2">
        <label class="fw-bold">Escala</label>
        <select class="form-select" id="scale">
            <option value="C">C</option>
            <option value="F">F</option>
        </select>
    </div>
    `;
    $('#bodygroup').empty().html(html);
}
function modalGeneric(id, action, title, subtitle, icon, size) {
    const $modal = buildModalGeneric(id, action, title, subtitle, icon);
    $('#modal').empty().append($modal);
    $('#' + id + ' .modal-dialog').addClass(size);
    $('#' + id + ' .modal-body').attr('id', 'bodygroup');
    $('#' + id).modal('show');
    $('#bodygroup').html('<div class="text-center mt-3">Cargando...</div>');
}
function createTemperature() {
    const requiredFields = [
        '#locations',
        '#temperature'
    ];

    if (!validateRequiredFields(requiredFields)) {
        Notify(2, 'Rellene el formulario.');
        return;
    }
    const data = {
        "location": $('#locations').val(),
        "temperature": $('#temperature').val(),
        "humidity": $('#humidity').val(),
        "scale": $('#scale').val(),
    }
    sendAjax({
        endpoint: '/Temperatura/create',
        type: 'POST',
        data,
        beforeSend: function () {
        },
        onSuccess: (data) => {
            if (data.success) {
                Notify(1, data.message);
                $('#modalRegister').modal('hide');
                fetchList();
            } else {
                Notify(2, data.message);
            }

        }
    });
}