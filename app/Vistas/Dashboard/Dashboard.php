<div class="d-flex flex-column flex-md-row gap-2 mb-2">
    <div class="d-flex align-items-center justify-content-center">
        <span class="material-symbols-outlined text-dark h1">dashboard</span>
    </div>
    <div class="ms-2">
        <div class="h4 fw-semibold">Dashboard</div>
        <h6 class="card-subtitle mb-2 text-body-secondary">Analiza el comportamiento del inventario .</h6>
    </div>
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
    <div>
        <div class="dropdown">
            <button class="btn btn-light btn-lg d-flex align-items-center dropdown-toggle w-100 w-md-auto" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="material-symbols-outlined">inventory</span>
                <span class="ms-2" id="moduloSeleccionado">Inventario</span>
            </button>
            <div class="dropdown-menu p-2" style="min-width: 280px;">
                <a class="dropdown-item d-flex align-items-center modulo-item"
                    href="#"
                    data-value="inventory">
                    <span class="material-symbols-outlined">inventory</span>
                    <span class="ms-2">Inventario</span>
                </a>
                <a class="dropdown-item d-flex align-items-center modulo-item"
                    href="#"
                    data-value="purchase">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    <span class="ms-2">Compras</span>
                </a>
                <a class="dropdown-item d-flex align-items-center modulo-item"
                    href="#"
                    data-value="sales">
                    <span class="material-symbols-outlined">money_bag</span>
                    <span class="ms-2">Ventas</span>
                </a>
            </div>
        </div>
    </div>
</div>
<div id="stats"></div>