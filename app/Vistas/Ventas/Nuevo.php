<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Ventas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Registrar venta</li>
    </ol>
</nav>
<div class="row">
    <div class="d-flex user-select-none mb-3 col-12">
        <div class="d-flex align-items-center justify-content-center">
            <span class="material-symbols-outlined text-dark h1">save</span>
        </div>
        <div class="ms-2">
            <div class="h4 fw-semibold">Registrar Venta</div>
            <h6 class="card-subtitle mb-2 text-body-secondary">Complete la siguiente información para registrar una nueva venta.</h6>
        </div>
    </div>
    <div class="col-12 col-lg-4">

        <div class="card shadow w-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <h5 class="card-title fw-semibold mb-0">
                    <span class="material-symbols-outlined align-middle">remove_shopping_cart</span>
                    Datos de la Venta
                </h5>
            </div>
            <div class="card-body p-3">
                <div>
                    <div class="fw-semibold text-dark h5">Información General</div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Proveedor <b class="text-danger">*</b></label>
                        <div class="input-group input-group-sm">
                            <select class="form-select rounded-0 select2" id="supplier">
                                <option value="" disabled selected>Seleccione...</option>
                                <?php
                                foreach ($suppliers as $supplier) {
                                    echo '<option value="' . $supplier->getId() . '">' . $supplier->getName() . '</option>';
                                }
                                ?>
                            </select>
                            <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center btn-supplier">
                                <span class="material-symbols-outlined">add_circle</span>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">N° de Factura</label>
                        <input type="text" class="form-control rounded-0" id="invoice_number" placeholder="Ingrese el número de factura" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Fecha de Venta <b class="text-danger">*</b></label>
                        <input type="date" class="form-control rounded-0" value="<?php echo date('Y-m-d'); ?>" id="sale_date" />
                    </div>
                    <hr>
                    <div class="fw-semibold text-dark h5">Pago</div>
                    <div class="row">
                        <div class="mb-2 col-lg-6 col-12">
                            <label class="form-label fw-bold">Forma de Pago <b class="text-danger">*</b></label>
                            <select class="form-select rounded-0" id="payment_method">
                                <?php
                                foreach ($payments as $payment) {
                                    echo '<option value="' . $payment->getId() . '">' . $payment->getName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-2 col-lg-6 col-12">
                            <label class="form-label fw-bold">Estado <b class="text-danger">*</b></label>
                            <select class="form-select rounded-0" id="status">
                                <?php
                                foreach ($status as $statu) {
                                    echo '<option value="' . $statu->getId() . '">' . $statu->getName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Notas</label>
                        <textarea class="form-control rounded-0" id="notes" placeholder="Ingrese notas adicionales"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card shadow w-100">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center user-select-none">
                    <h5 class="card-title fw-semibold mb-2 mb-md-0">Productos</h5>
                    <button type="button" class="btn btn-primary d-flex align-items-center btn-createProduct ms-md-auto w-md-auto">
                        <span class="material-symbols-outlined">add</span>
                        <span class="ms-2">Agregar Producto</span>
                    </button>
                </div>
                <div>
                    <div class="row g-3 user-select-none d-flex justify-content-end mt-1">
                        <div class="col-md-4">
                            <div class="card shadow border mb-1 card-hover h-100">
                                <div class="card-body d-flex align-items-center gap-3 p-2">
                                    <div class="bg-primary-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                                        <span class="material-symbols-outlined text-primary" style="font-size: 2rem;">package_2</span>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold h6">Productos</div>
                                        <div class="fw-semibold h5" id="tabproduct">0</div>
                                        <div class="text-muted h6">Item</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow border mb-1 card-hover h-100">
                                <div class="card-body d-flex align-items-center gap-3 p-2">
                                    <div class="bg-success-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                                        <span class="material-symbols-outlined text-success" style="font-size: 2rem;">shopping_cart</span>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold h6">Cantidad Total</div>
                                        <div class="fw-semibold h5" id="tabquantity">0</div>
                                        <div class="text-muted h6">Libras</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow border mb-1 card-hover h-100">
                                <div class="card-body d-flex align-items-center gap-3 p-2">
                                    <div class="bg-warning-subtle rounded-4 p-3 d-flex justify-content-center align-items-center">
                                        <span class="material-symbols-outlined text-warning" style="font-size: 2rem;">shoppingmode</span>
                                    </div>
                                    <div>
                                        <div class="text-muted fw-bold h6">Subtotal</div>
                                        <div class="fw-semibold h5" id="tabsubtotal">$0</div>
                                        <div class="text-muted h6">Sin impuestos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive shadow border rounded-3 mt-3">
                        <table id="productsTable" class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Subproducto</th>
                                    <th>Presentación</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="productsContent"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow w-100">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center user-select-none">
                    <div class="fw-semibold h4 mb-md-0">Subtotal</div>
                    <div class="ms-auto fw-semibold">
                        <div class="money-wrapper w-md-auto">
                            <span class="money-symbol">$</span>
                            <input type="text" class="form-control form-control-lg fw-semibold money-input decimal-only" id="subtotal" placeholder="Subtotal de la venta" readonly disabled />
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-md-row align-items-md-center user-select-none mt-2">
                    <div class="fw-semibold h4">Impuestos</div>
                    <div class="ms-auto fw-semibold">
                        <div class="money-wrapper w-md-auto">
                            <span class="money-symbol">$</span>
                            <input type="text" class="form-control form-control-lg fw-semibold money-input decimal-only" id="taxes" placeholder="Ingrese los impuestos" />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-column flex-md-row align-items-md-center user-select-none mt-2">
                    <div class="fw-semibold h1">Total</div>
                    <div class="ms-auto fw-semibold">
                        <div class="money-wrapper w-md-auto">
                            <span class="money-symbol">$</span>
                            <input type="text" class="form-control form-control-lg fw-semibold money-input decimal-only" value="" id="total" placeholder="Total de la venta" readonly disabled />
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" id="btncreateSale" class="btn btn-primary btn-lg mt-3 d-flex align-items-center btn-createSale">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="ms-2">Registrar Venta</span>
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal"></div>