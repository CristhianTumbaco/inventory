<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Venta</a></li>
        <li class="breadcrumb-item active" aria-current="page">Resumen de la venta</li>
        <li class="breadcrumb-item active" aria-current="page"><?= $venta['factura'] ?></li>
    </ol>
</nav>
<div class="card mb-2">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <a href="<?php echo RUTA_WEB; ?>/Ventas/" class="material-symbols-outlined edit">arrow_back</a>
                <h5 class="mb-0 fw-semibold">Resumen de Venta</h5>
            </div>
        </div>
        <div class="row align-items-center h6">
            <div class="col-md-6 col-12">
                <div class="d-flex gap-3 border border-0 border-end">
                    <div class="icon-box">
                        <span class="material-symbols-outlined icon">description</span>
                    </div>
                    <div>
                        <div class="text-muted h4">Factura</div>
                        <div class="fw-semibold mb-2 h3"><?= $venta['factura'] ?></div>
                        <div class="text-muted">
                            <span class="material-symbols-outlined icon me-1">calendar_month</span>
                            <?= $venta['fecha_venta'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12">
                <div class="mt-2">
                    <small class="text-muted fw-bold">Proveedor</small>
                    <div class="fw-semibold d-flex aligns-items-center mt-2">
                        <span class="material-symbols-outlined icon">store</span>
                        <span class="ms-2"><?= $venta['proveedor'] ?></span>
                    </div>
                </div>
                <small class="text-muted fw-bold">Estado</small>
                <div class="mt-2">
                    <div class="dropdown">
                        <button data-value="<?= $venta['estadoId'] ?>" data-id="<?= $venta['id'] ?>" class="btn btn-light btn-lg d-flex align-items-center dropdown-toggle leftdrop w-100 w-md-auto" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-symbols-outlined icon-fill <?= $claseEstado ?>">fiber_manual_record</span>
                            <span class="ms-2" id="statusSel"><?= $venta['estado'] ?></span>
                        </button>
                        <div class="dropdown-menu w-100">
                            <?php
                            foreach ($status as $item) {
                                $claseEstado = $colores[$item->getName()] ?? 'text-secondary';
                            ?>
                                <a class="dropdown-item d-flex align-items-center status-item"
                                    href="#"
                                    data-value="<?= $item->getId() ?>">
                                    <span class="material-symbols-outlined icon-fill <?= $claseEstado ?>">fiber_manual_record</span>
                                    <span class="ms-2"><?= $item->getName() ?></span>
                                </a>
                            <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12">
                <small class="text-muted fw-bold">Usuario</small>
                <div class="fw-semibold d-flex aligns-items-center mt-2 mb-2">
                    <span class="material-symbols-outlined icon">person</span>
                    <span class="ms-2"><?= $venta['usuario'] ?></span>
                </div>
                <small class="text-muted fw-bold">Método Pago</small>
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">account_balance</span>
                    <span class="ms-2"><?= $venta['metodo_pago'] ?></span>
                </div>
            </div>
            <div class="col-md-12 col-12">
                <small class=" fw-bold">Notas</small>
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">chat</span>
                    <span class="ms-2"><?= $venta['notas'] ?></span>
                </div>
            </div>
        </div>
        <hr>
        <div class="mt-3">
            <div class="d-flex h5">
                <span class="material-symbols-outlined">package_2</span>
                <span class="ms-2">Detalle de Productos</span>
            </div>
            <div class="table-responsive border mt-3 text-dark">
                <table class="table align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Presentación</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Costo Unit.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalCategoria = 0;
                        foreach ($venta['productos'] as $item):
                            $totalCategoria += $item['costo_total'];
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= $item['producto'] ?></div>
                                    <div><?= $item['subproducto'] ?></div>
                                    <div class="text-muted fs-1"><?= $item['descripcion'] ?></div>
                                </td>
                                <td>
                                    <span class="badge text-bg-light"><?= $item['categoria'] ?></span>
                                </td>
                                <td><?= ($item['cantidad_paquete'] * 1) . " " . $item['presentacion'] ?></td>
                                <td class="text-end"><?= ($item['cantidad'] * 1) ?> Libras</td>
                                <td class="text-end">$<?= number_format($item['costo_unitario'], 2) ?></td>
                                <td class="text-end fw-semibold text-primary">$<?= number_format($item['costo_total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="row justify-content-end">
                <div class="col-lg-4">
                    <table class="table table-borderless border border-top-0">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end fw-semibold">$<?= number_format($venta['subtotal'], 2) ?></td>
                        </tr>
                        <tr>
                            <td>Impuestos</td>
                            <td class="text-end fw-semibold text-danger">-$<?= number_format($venta['impuestos'], 2) ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="pt-3">
                                <strong>Total</strong>
                            </td>
                            <td class="text-end pt-3">
                                <span class="h5 fw-semibold text-primary">$<?= number_format($venta['total_venta'], 2) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>