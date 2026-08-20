$(document).ready(function () {
    //activar tabs del menu
    $('#recepcion').addClass('selected');
    $('#recepcion a').addClass('active');
    initView();
});
const receptionFilters = {
    status: '',
    period: '',
    dateFrom: null,
    dateTo: null,
    search: ''
};
function initView() {
    fetchStats();
    buildStatus();
    fetchList();
}
function fetchStats() {
    sendAjax({
        endpoint: '/Recepcion/stats',
        type: 'POST',
        data: receptionFilters,
        beforeSend: function () {
            loadingStats('stats');
        },
        onSuccess: (data) => {
            setTimeout(() => {
                if (data.success) {
                    buildStats(data.data);
                } else {
                    Notify(2, data.message);
                }
            }, 1200);
        }
    });
}
function fetchList() {
    sendAjax({
        endpoint: '/Recepcion/list',
        type: 'POST',
        data: receptionFilters,
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
        const dias_atraso = parseFloat(item.dias_atraso);
        let advertencia = '';
        if (dias_atraso > 5) {
            advertencia = '<span class="material-symbols-outlined text-danger" title="Atrasada">warning</span>';
        }
        rows += `
            <tr>
                <td>
                    <h6 class="fw-semibold mb-1">${item.factura}</h6>
                    <span class="fw-normal">Código: ${String(item.codigo).padStart(5, '0') || ''}</span>
                </td>
                <td>${(item.proveedor)}</td>
                <td>${(item.fecha_compra)}</td>
                <td>
                <div class="d-flex align-items-center">
                        <span class="material-symbols-outlined text-primary">deployed_code_alert</span>
                        <span class="ms-2 text-dark">${(item.cantidad_items)} items</span>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-outlined text-primary">weight</span>
                        <span class="ms-2 text-dark">${(item.peso_total)}</span>
                    </div>
                </td>
                <td>
                    <button type="button"
                        class="btn bg-warning-subtle text-warning d-flex align-items-center">
                        <span class="material-symbols-outlined">schedule</span>
                        <span class="ms-2">Pendiente</span>
                    </button>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        ${advertencia}
                        <span class="ms-2">${(item.dias_atraso)} días</span>
                    </div>    
                </td>
                <td class="text-center user-select-none">
                    <a href="${ruta_web}/Recepcion/${item.codigo}" class="btn-admin">
                      <span class="material-symbols-outlined edit text-empresa btn-admin h1" title="Administrar">inventory</span>
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
                        <th>Factura</th>
                        <th>Proveedor</th>
                        <th>Fecha de Compra</th>
                        <th>Productos</th>
                        <th>Peso Total</th>
                        <th>Estado</th>
                        <th>Días Pendientes</th>
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
function buildStats(data) {
    let html = `
    <div class="row g-3 mb-3 user-select-none">
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-primary-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">list_alt_check</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Pendientes</div>
                            <div class="fw-semibold h4">${data.compras_pendientes || 0}</div>
                            <div class="text-muted h6">Ordenes por recibir</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-success-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-success" style="font-size: 2rem;">weight</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Peso Pendiente</div>
                            <div class="fw-semibold h4">${data.cantidad_pendiente || 0} Lbs</div>
                            <div class="text-muted h6">Total por recepcionar</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-warning-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-warning" style="font-size: 2rem;">calendar_today</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Días Vencido</div>
                            <div class="fw-semibold h4">${data.max_dias_atraso || 0}</div>
                            <div class="text-muted h6">Máximo día sin recepcionar</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-info-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-info" style="font-size: 2rem;">group</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Proveedores</div>
                            <div class="fw-semibold h4">${data.proveedores || 0}</div>
                            <div class="text-muted h6">con pendientes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#stats').empty().html(html);
}
function buildStatus() {
    let html = `
    <div class="d-flex flex-column flex-md-row gap-2">
        <div class="w-100 w-md-auto"></div>
        <div class="ms-md-auto">
            <div class="dropdown">
                <button class="btn btn-light btn-lg d-flex align-items-center dropdown-toggle w-100 w-md-auto" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="ms-2" id="periodoSeleccionado">Todos</span>
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
            receptionFilters.period = value;
            receptionFilters.dateFrom = null;
            receptionFilters.dateTo = null;
            periodoSeleccionado.textContent = text;
            rangoPersonalizado.classList.add('d-none');
            fetchStats();
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
        receptionFilters.period = 'custom';
        receptionFilters.dateFrom = desde;
        receptionFilters.dateTo = hasta;

        const desdeFormateado = formatearFecha(desde);
        const hastaFormateado = formatearFecha(hasta);
        periodoSeleccionado.textContent =
            `${desdeFormateado} - ${hastaFormateado}`;

        rangoPersonalizado.classList.add('d-none');
        fetchStats();
        fetchList();
    });
}
function formatearFecha(fecha) {

    const [year, month, day] = fecha.split('-');

    return `${day}/${month}/${year}`;
}