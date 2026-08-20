$(document).ready(function () {
    //activar tabs del menu
    $('#compras').addClass('selected');
    $('#compras a').addClass('active');
    initView();
    bindButtonClick('.btn-reporte', purchaseReport);
});
const purchaseFilters = {
    status: '',
    period: 'month',
    dateFrom: null,
    dateTo: null,
    search: ''
};
function initView() {
    fetchStats();
    fetchStatus();
    fetchList();
}
function fetchStatus() {
    sendAjax({
        endpoint: '/Compras/status',
        onSuccess: (data) => {
            setTimeout(() => {
                if (data.success) {
                    buildStatus(data.data);
                } else {
                    Notify(2, data.message);
                }
            }, 1200);
        }
    });
}
function fetchStats() {
    sendAjax({
        endpoint: '/Compras/stats',
        type: 'POST',
        data: purchaseFilters,
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
        endpoint: '/Compras/list',
        type: 'POST',
        data: purchaseFilters,
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
        const badgeClass =
            item.estado === 'Pagada' ? 'success' : 'warning';
        rows += `
            <tr>
                <td>
                    <h6 class="fw-semibold mb-1">${item.proveedor}</h6>
                    <span>
                        <div class="d-flex align_item_center fw-normal">
                            <span class="material-symbols-outlined">id_card</span>
                            <span class="ms-2">${item.identificacion || ''}</span>
                        </div>
                    </span>
                </td>
                <td>
                    <h6 class="fw-semibold mb-1">${item.factura}</h6>
                    <span class="fw-normal">${item.notas || ''}</span>
                </td>
                <td>
                    <h6 class="fw-semibold mb-1">${item.cantidad_items}</h6>
                    <span class="fw-normal">productos</span>
                </td>
                <td>${(item.fecha_compra)}</td>
                <td class="fw-bold text-primary">$${(item.total_compra)}</td>
                 <td>${getStatusBadge(item.estado)}</td>
                <td class="text-center user-select-none">
                    <a href="${ruta_web}/Compras/${item.codigo}" class="btn-admin">
                      <span class="material-symbols-outlined edit text-empresa btn-admin h3" title="Resumen">arrow_forward_ios</span>
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
                        <th>Proveedor</th>
                        <th>Factura</th>
                        <th>Items</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
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
    formatDatatable('#table', 3, 'desc');
}
function getStatusBadge(status) {

    const config = {
        'Pagada': {
            class: 'success',
            icon: 'check_circle'
        },
        'Pendiente': {
            class: 'warning',
            icon: 'schedule'
        },
        'Anulada': {
            class: 'danger',
            icon: 'cancel'
        }
    };

    const badge = config[status] || {
        class: 'secondary',
        icon: 'help'
    };

    return `
        <button type="button"
            class="btn bg-${badge.class}-subtle btn-sm text-${badge.class} d-flex align-items-center">
            <span class="material-symbols-outlined">${badge.icon}</span>
            <span class="ms-2">${status}</span>
        </button>
    `;
}
function buildStatus(data) {
    let html = `
    <div class="d-flex flex-column flex-md-row gap-2">
        <div class="w-100 w-md-auto">
            <div class="d-flex flex-column flex-sm-row gap-2" role="group">
            ${buildStatusFilters(data)}
            </div>
        </div>
        <div class="ms-md-auto">
            <div class="dropdown">
                <button class="btn btn-light btn-lg d-flex align-items-center dropdown-toggle w-100 w-md-auto" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="ms-2" id="periodoSeleccionado">Este Mes</span>
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
            <div class="w-md-auto">
            <div class="position-relative">
                <span class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y ms-3">search</span>
                <input type="text" class="form-control form-control-lg ps-5" id="tablebuscador" placeholder="¿Buscas algo?">
            </div>
        </div>
    </div> `;
    $('#status').empty().html(html);
    dropdownFormat();
    bindStatusFilters();

}
function buildStatusFilters(statuses) {
    let html = `
        <input type="radio"
               class="btn-check"
               name="estado"
               id="all"
               value=""
               checked>

        <label class="btn btn-lg btn-light d-flex align-items-center rounded-2"
               for="all">
            <span class="material-symbols-outlined icon-fill text-primary fs-5">
                fiber_manual_record
            </span>
            <span class="ms-2">Todas</span>
        </label>
    `;
    const colors = {
        'Pagada': 'success',
        'Pendiente': 'warning',
        'Anulada': 'danger'
    };
    statuses.forEach(status => {
        const color = colors[status.name] || 'secondary';
        html += `
            <input type="radio"
                   class="btn-check"
                   name="estado"
                   id="status_${status.id}"
                   value="${status.id}">
            <label class="btn btn-lg btn-light d-flex align-items-center rounded-2"
                   for="status_${status.id}">
                <span class="material-symbols-outlined icon-fill text-${color} fs-5">
                    fiber_manual_record
                </span>
                <span class="ms-2">${status.name}</span>
            </label>
        `;
    });
    return html;
}
function buildStats(data) {
    const statusMap = Object.fromEntries(
        data.statusPurchases.map(item => [item.estado, item.cantidad])
    );

    const dataformat = {
        totalPurchases: data.totalPurchases.cantidad,
        pendingPurchases: statusMap.Pendiente || 0,
        paidPurchases: statusMap.Pagada || 0,
        totalMonth: data.totalPurchases.total || 0
    };
    renderStats(dataformat);
}
function renderStats(data) {
    let html = `
    <div class="row g-3 mb-3 user-select-none">
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-primary-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">shopping_bag</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Total de Compras</div>
                            <div class="fw-semibold h4">${data.totalPurchases}</div>
                            <div class="text-muted h6">Todas las compras</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-warning-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-warning" style="font-size: 2rem;">schedule</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Pendientes</div>
                            <div class="fw-semibold h4">${data.pendingPurchases}</div>
                            <div class="text-muted h6">Por Pagar</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-success-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-success" style="font-size: 2rem;">check_circle</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Pagadas</div>
                            <div class="fw-semibold h4">${data.paidPurchases}</div>
                            <div class="text-muted h6">Compras Completadas</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-info-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-info" style="font-size: 2rem;">paid</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Total Comprado</div>
                            <div class="fw-semibold h4">$${data.totalMonth}</div>
                            <div class="text-muted h6">Totalizado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#stats').empty().html(html);
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
            purchaseFilters.period = value;
            purchaseFilters.dateFrom = null;
            purchaseFilters.dateTo = null;
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
        purchaseFilters.period = 'custom';
        purchaseFilters.dateFrom = desde;
        purchaseFilters.dateTo = hasta;

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
function bindStatusFilters() {
    $('input[name="estado"]').on('change', function () {
        purchaseFilters.status = $(this).val();
        fetchStats();
        fetchList();
    });

}
//reportes
function purchaseReport() {
    sendAjax({
        endpoint: '/Compras/report',
        type: 'POST',
        data: purchaseFilters,
        beforeSend: function () {
        },
        onSuccess: (data) => {
            if (data.success) {
                const datos = data.data;
                exportToExcel(datos, "ReporteCompras");
            } else {
                Notify(2, data.message);
            }
        }
    });
}