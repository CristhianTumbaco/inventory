$(document).ready(function () {
    //activar tabs del menu
    $('#trazabilidad').addClass('selected');
    $('#trazabilidad a').addClass('active');
    initView();
});
const commonsFilters = {
    status: '',
    period: 'month',
    dateFrom: null,
    dateTo: null,
    search: ''
};
function initView() {
    buildStatus();
    fetchList();
}
function fetchList() {
    sendAjax({
        endpoint: '/Trazabilidad/list',
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
                <td>
                    <div class="fw-bold">${(item.batch_code)}</div>
                    <div class="text-muted fs-2">${(item.notes || '')}</div>
                </td>
                <td>${(formatModule(item.batch_type))}</td>
                <td>${(item.product)}</td>
                <td>${(item.subproduct)}</td>
                <td>${(item.production_date)}</td>
                <td>${(item.expiration_date)}</td>
                <td class="text-center user-select-none">
                    <a href="${ruta_web}/Trazabilidad/${item.id}" class="btn-admin">
                      <span class="material-symbols-outlined edit text-empresa btn-admin h1" title="Historial">history</span>
                    </a>
                </td>
            </tr>
        `;
    });

    const html = `
        <div class="table-responsive mt-3">
            <table class="table table-hover border text-nowrap" id="table">
                <thead class="bg-light user-select-none">
                    <tr>
                        <th>Código Lote</th>
                        <th>Módulo Origen</th>
                        <th>Producto</th>
                        <th>Subproducto</th>
                        <th>Fecha de Producción</th>
                        <th>Fecha de Expiración</th>
                        <th class="text-center" data-orderable="false">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
    `;

    $('#lists').html(html);
    formatDatatable('#table', 2, 'asc');
}
function formatModule(value){
    switch (value) {
        case 'production':
            return '<span class="badge bg-warning">Producción</span>';
        case 'purchase':
            return '<span class="badge bg-primary">Compra</span>';
        default:
            return value;
    }
}

function buildStatus() {
    let html = `
    <div class="d-flex flex-column flex-md-row gap-2">
        <div class="w-100 w-md-auto"></div>
        <div class="ms-md-auto">
            <div class="dropdown">
                <button class="btn btn-light btn-lg d-flex align-items-center dropdown-toggle w-100 w-md-auto" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <span class="ms-2" id="periodoSeleccionado">Este mes</span>
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