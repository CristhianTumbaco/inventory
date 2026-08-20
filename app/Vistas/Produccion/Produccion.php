<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Producción</a></li>
        <li class="breadcrumb-item active" aria-current="page">Proceso</li>
    </ol>
</nav>
<div class="d-flex user-select-none mb-3">
    <div class="d-flex align-items-center justify-content-center">
        <span class="material-symbols-outlined text-dark h1">factory</span>
    </div>
    <div class="ms-2">
        <div class="h4 fw-semibold">Nueva Producción</div>
        <h6 class="card-subtitle mb-2 text-body-secondary">Transforma materias primas en nuevos productos.</h6>
    </div>
</div>
<div class="card mb-2">
    <div class="card-body p-3">
        <div class="d-flex mb-3">
            <div>
                <div class="fw-semibold text-empresa fs-4">1. Materias primas utilizadas</div>
                <div class="text-muted fs-2">Agrega los productos que se utilizaran en esta producción.</div>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center btn-prima">
                    <span class="material-symbols-outlined">add</span>
                    <span class="ms-2">Agregar materia prima</span>
                </button>
            </div>
        </div>
        <div class="table-responsive border rounded-3">
            <table class="table m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Producto</th>
                        <th>Subproducto</th>
                        <th>Ubicación</th>
                        <th>Lote</th>
                        <th>Paquetes Seleccionados</th>
                        <th>Peso Total</th>
                    </tr>
                </thead>
                <tbody id="tbodyMaterialPrima"></tbody>
                <tfoot class="table-light text-primary">
                    <tr>
                        <td colspan="5" class="fw-bold">Total materia prima a utilizar:</td>
                        <td class="fw-bold" id="resumenItemsMaterial">0 Items</td>
                        <td class="fw-bold" id="resumenTotalMaterial">0 Libras</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="card mb-2">
    <div class="card-body p-3">
        <div class="d-flex mb-3">
            <div>
                <div class="fw-semibold text-empresa fs-4">2. Productos generados</div>
                <div class="text-muted fs-2">Ingresa los productos obtenidos como resultado de esta producción.</div>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center btn-generated">
                    <span class="material-symbols-outlined">add</span>
                    <span class="ms-2">Agregar producto</span>
                </button>
            </div>
        </div>
        <div class="table-responsive border rounded-3">
            <table class="table m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Producto</th>
                        <th>Subproducto</th>
                        <th>Ubicación</th>
                        <th>Paquetes</th>
                        <th>Peso por Paquetes</th>
                        <th>Total</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyProductGenerated">
                </tbody>
                <tfoot class="table-light text-primary">
                    <tr>
                        <td colspan="5" class="fw-bold">Total productos generados:</td>
                        <td class="fw-bold" id="resumenItemsGenerated">0 Items</td>
                        <td class="fw-bold" id="resumenTotalGenerated">0 Libras</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body p-3">
        <div class="fw-semibold text-empresa fs-4">3. Resumen del Proceso</div>
        <div class="row">
            <div class="col-8">
                <div class="row g-3 user-select-none d-flex justify-content-end mt-1">
                    <div class="col-md-4">
                        <div class="card shadow border mb-1 card-hover h-100">
                            <div class="card-body d-flex align-items-center gap-3 p-2">
                                <div class="bg-primary-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                                    <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">package_2</span>
                                </div>
                                <div>
                                    <div class="text-muted fw-bold h6">Materia Prima Total</div>
                                    <div class="fw-semibold h5 indicador1">0 Libras</div>
                                    <div class="text-muted h6 indicador5">0 Items</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow border mb-1 card-hover h-100">
                            <div class="card-body d-flex align-items-center gap-3 p-2">
                                <div class="bg-success-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                                    <span class="material-symbols-outlined text-success" style="font-size: 2rem;">check_circle</span>
                                </div>
                                <div>
                                    <div class="text-muted fw-bold h6">Productos Obtenidos</div>
                                    <div class="fw-semibold h5 indicador2">0 Libras</div>
                                    <div class="text-muted h6 indicador6">0 Items</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow border mb-1 card-hover h-100">
                            <div class="card-body d-flex align-items-center gap-3 p-2">
                                <div class="bg-danger-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                                    <span class="material-symbols-outlined text-danger" style="font-size: 2rem;">delete</span>
                                </div>
                                <div>
                                    <div class="text-muted fw-bold h6">Merma Estimada</div>
                                    <div class="fw-semibold h5 indicador3">0 Libras</div>
                                    <div class="text-muted h6 indicador4">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <div class="alert alert-success  fs-2" role="alert">La merma se calcula automáticamente como la diferencia entre la materia prima utilizada y los productos obtenidos</div>
                </div>
            </div>
            <div class="col-4">
                <div class="table-responsive border rounded-3">
                    <table class="table table-sm table-borderless">
                        <tr class="border-bottom bg-light">
                            <th colspan="2" class="fw-semibold p-2">Detalle del Cálculo</th>
                        </tr>
                        <tr>
                            <th>Materia Prima Total:</th>
                            <td class="text-end text-primary fw-semibold indicador1">0 Libras</td>
                        </tr>
                        <tr>
                            <th>Productos Obtenidos:</th>
                            <td class="text-end text-success fw-semibold indicador2">0 Libras</td>
                        </tr>
                        <tr class="border-bottom">
                            <th>Merma Estimada:</th>
                            <td class="text-end text-danger fw-semibold indicador3">0 Libras</td>
                        </tr>
                        <tr>
                            <th>Pocentaje de Merma:</th>
                            <td class="text-end text-danger fw-semibold indicador4">0%</td>
                        </tr>
                    </table>
                </div>

                <div class="mt-2">
                    <button class="btn btn-dark d-flex align-items-center justify-content-center btn-create w-100">
                        <span class="material-symbols-outlined">check</span>
                        <span class="ms-2">Confirmar Producción</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>

<div id="modal"></div>