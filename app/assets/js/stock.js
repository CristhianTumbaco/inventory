$(document).ready(function () {
    $('#stock').addClass('selected');
    $('#stock a').addClass('active');
    initView();
});
const componentFilters = {
    locations: '',
    products: ''
};
function initView() {
    fetchStats();
    fetchStatus();
    fetchList();
}
function fetchStats() {
    sendAjax({
        endpoint: '/Stock/stats',
        type: 'POST',
        data: componentFilters,
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
function fetchStatus() {
    sendAjax({
        endpoint: '/Stock/status',
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
function fetchList() {
    sendAjax({
        endpoint: '/Stock/list',
        type: 'POST',
        data: componentFilters,
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
    data.forEach(product => {
        rows += `<tr class="fw-semibold bg-info-subtle">
            <td>${product.producto}</td>
            <td></td>
            <td class="text-nowrap">
                <div class="d-flex align-items-center justify-content-end">
                    <span class="material-symbols-outlined icon-fill text-primary">weight</span>
                    <span class="ms-3">${product.totalCantidad} Lbs</span>
                </div>
            </td>
            <td class="text-end">${product.totalToneladas} T</td>
            <td class="text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <span class="material-symbols-outlined icon-fill text-primary">package_2</span>
                    <span class="ms-3">${product.totalPaquetes}</span>
                </div>
            </td>
            <td>-</td>
            <td></td>
        </tr>`;
        product.details.forEach(subproduct => {
            const productoId = product.productoId;
            const subproductoId = subproduct.subproductoId;
            rows += `<tr class="bg-light">
                <td class="ps-5 fw-bold">
                    <div class="d-flex align-items-center">
                        <span class="material-symbols-outlined icon-fill fs-2 text-gray-100">circle</span>
                        <span class="ms-3">${subproduct.subproducto}</span>
                    </div>
                </td>
                <td></td>
                <td class="text-end">${subproduct.totalCantidad} Lbs</td>
                <td class="text-end">${subproduct.totalToneladas} T</td>
                <td class="text-end">${subproduct.totalPaquetes}</td>
                <td>-</td>
                <td>
                    <div class="d-flex align-items-center justify-content-center user-select-none">
                        <div class="d-flex align-items-center">
                            <a href="../Stock/${subproductoId}" class="material-symbols-outlined edit h3 text-gray-100" title="Administrar">settings</a>
                        </div>
                    </div>
                </td>
            </tr>`;
            subproduct.details.forEach(location => {
                const fechaStr = location.ultima_actualizacion;
                const fecha = new Date(fechaStr.replace(" ", "T"));
                rows += `<tr>
                    <td class="ps-5">
                        <div class="ps-5">
                            <span class="material-symbols-outlined icon-fill fs-2 text-gray">circle</span>
                            <span class="ms-3"> ${location.ubicacion}</span>
                        </div>
                    </td>
                    <td>${location.presentacion}</td>
                    <td class="text-end">${location.cantidad} Lbs</td>
                    <td class="text-end">${location.toneladas} T</td>
                    <td class="text-end">${location.paquetes}</td>
                    <td>
                        <div class="fw-bold fs-3">${formatoRelativo(fecha)}</div>
                        <div class="text-muted">${formatoFechaCorta(fecha)}</div>
                    </td>
                    <td></td>
                </tr>`;
            });
        });
    });
    let html = `<div class="table-responsive border rounded-3 shadow mt-3">
            <table class="table align-middle mb-0 text-dark fs-4">
                <thead>
                    <tr>
                        <th>Producto / Subproducto / Ubicación</th>
                        <th>Presentación</th>
                        <th class="text-end">Cantidad (Libras)</th>
                        <th class="text-end">Cantidad (Toneladas)</th>
                        <th class="text-end">Paquetes</th>
                        <th>Última Actualización</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                ${rows}
                </tbody>
            </table>
        </div>`;
    $('#lists').empty().html(html);
}
function buildStatus(data) {
    const locations = data.locations;
    const products = data.products;
    let optionLocations = '';
    let optionProducts = '';
    locations.forEach(item => {
        optionLocations += `<option value="${item.id}">${item.name}</option>`;
    });
    products.forEach(item => {
        optionProducts += `<option value="${item.id}">${item.name}</option>`;
    });
    let html = `
    <div class="d-flex flex-column flex-md-row gap-2">
            <select class="form-select form-select-lg rounded-3 mb-md-0" id="products">
                <option value="">Todos los Productos</option>
                ${optionProducts}
            </select>
            <select class="form-select form-select-lg rounded-3 mb-md-0" id="locations">
                <option value="">Todas las ubicaciones</option>
                ${optionLocations}
            </select>
        </div>`;
    $('#status').empty().html(html);

}
function buildStats(data) {
    const fechaStr = data.last_update || '';
    let fecha = '';
    if (fechaStr) {
        fecha = new Date(fechaStr.replace(" ", "T"));
    }
    let html = `
    <div class="row g-3 mb-3 user-select-none">
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-primary-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">package_2</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Total Almacenado</div>
                            <div class="fw-semibold h4">${Number((data.total_quantity / 2200).toFixed(3))} T</div>
                            <div class="text-muted h6">${Number(data.total_quantity) || 0} Lbs</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-warning-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-warning" style="font-size: 2rem;">package</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Total Paquetes</div>
                            <div class="fw-semibold h4">${Number(data.total_packages) || 0}</div>
                            <div class="text-muted h6">Paquetes disponibles</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-success-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-success" style="font-size: 2rem;">content_paste</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Productos Activos</div>
                            <div class="fw-semibold h4">${data.total_items || 0}</div>
                            <div class="text-muted h6">Productos registrados</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow border mb-1 card-hover h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-2">
                        <div class="bg-info-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                            <span class="material-symbols-outlined text-info" style="font-size: 2rem;">calendar_today</span>
                        </div>
                        <div>
                            <div class="text-muted fw-bold h5">Última Actualización</div>
                            <div class="fw-semibold h4">${formatoRelativo(fecha)}</div>
                            <div class="text-muted h6">${formatoFechaCorta(fecha)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#stats').empty().html(html);
}


function formatoFechaCorta(fecha) {
    if (fecha === '') {
        return '';
    }
    var dia = String(fecha.getDate()).padStart(2, "0");
    var mes = String(fecha.getMonth() + 1).padStart(2, "0");
    var anio = fecha.getFullYear();
    return `${dia}/${mes}/${anio}`;
}

function formatoRelativo(fecha) {
    if (fecha === '') {
        return '';
    }
    const ahora = new Date();
    const hoy = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate());
    const f = new Date(fecha.getFullYear(), fecha.getMonth(), fecha.getDate());

    const diffMs = hoy - f;
    const diffDias = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const horaTexto = fecha.toLocaleTimeString("es-ES", {
        hour: "2-digit",
        minute: "2-digit"
    });
    if (diffDias === 0) {
        return `Hoy, ${horaTexto}`;
    }
    if (diffDias === 1) {
        return `Ayer, ${horaTexto}`;
    }
    return `Hace ${diffDias} días, ${horaTexto}`;
}
$(document).on('change', '#products', function () {
    const value = $(this).val();
    componentFilters.products = value;
    fetchStats();
    fetchList();
});
$(document).on('change', '#locations', function () {
    const value = $(this).val();
    componentFilters.locations = value;
    fetchStats();
    fetchList();
});


