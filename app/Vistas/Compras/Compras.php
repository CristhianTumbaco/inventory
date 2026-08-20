<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Compras</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reporte</li>
    </ol>
</nav>
<div class="card shadow-lg w-100">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row user-select-none mb-3">
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined text-dark h1">shopping_bag</span>
                <div class="ms-2">
                    <div class="h4 fw-semibold">Compras</div>
                    <h6 class="card-subtitle mb-2 text-body-secondary">
                        Consulta y gestión de todas las compras realizadas.
                    </h6>
                </div>
            </div>
            <div class="ms-md-auto d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                <button type="button" class="btn btn-light border btn-reporte d-flex align-items-center justify-content-center">
                    <span class="material-symbols-outlined">download</span>
                    <span class="ms-2">Exportar</span>
                </button>
                <a href="<?php echo RUTA_WEB; ?>/Compras/Registrar"
                    class="btn btn-primary d-flex align-items-center justify-content-center">
                    <span class="material-icons-outlined">add</span>
                    <span class="ms-2">Registrar Compra</span>
                </a>
            </div>
        </div>
        <div id="stats"></div>
        <div id="status"></div>
        <div id="lists"></div>
    </div>
</div>