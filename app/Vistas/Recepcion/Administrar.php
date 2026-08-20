<?php
$condicion = [
    'congelado' => 'Congelado',
    'fresco' => 'Fresco',
    'descongelado' => 'Descongelado',
    'dañado' => 'Dañado'
];
?>
<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb user-select-none">
        <li class="breadcrumb-item"><a href="#">Recepción</a></li>
        <li class="breadcrumb-item active" aria-current="page">Recepciones Pendientes</li>
        <li class="breadcrumb-item active" aria-current="page"><?= $data['invoice_number'] ?></li>
    </ol>
</nav>
<div class="card shadow-lg w-100">
    <div class="card-body p-4">
        <div class="d-flex user-select-none d-flex align-items-center">
            <a href="<?php echo RUTA_WEB; ?>/Recepcion/" class="material-symbols-outlined text-primary edit">arrow_back</a>
            <h5 class="m-3 card-title fw-semibold">Recepción de Compra</h5>
        </div>
        <div class="row text-dark p-3 h5">
            <div class="col-lg-4 col-12 mb-2">
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">store</span>
                    <span class="ms-2">Proveedor</span>
                </div>
                <div class="mt-2"><?= $data['supplier'] ?></div>
            </div>
            <div class="col-lg-4 col-12 mb-2">
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">calendar_month</span>
                    <span class="ms-2">Fecha de la Compra</span>
                </div>
                <div class="mt-2"><?= $data['purchase_date'] ?></div>
            </div>
            <div class="col-lg-4 col-12 mb-2">
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">description</span>
                    <span class="ms-2">Factura</span>
                </div>
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span><?= $data['invoice_number'] ?></span>
                    <a href="<?= RUTA_WEB ?>/Compras/<?= $id ?>" title="Ir a la Compra" class="material-symbols-outlined icon ms-2 edit text-primary">link_2</a>
                </div>
            </div>
            <div class="col-lg-4 col-12 mb-2">
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">person</span>
                    <span class="ms-2">Responsable</span>
                </div>
                <div class="mt-2"><?= $data['user'] ?></div>
            </div>
            <div class="col-lg-4 col-12 mb-2">
                <div class="fw-semibold d-flex aligns-items-center mt-2">
                    <span class="material-symbols-outlined icon">chat</span>
                    <span class="ms-2">Notas</span>
                </div>
                <div class="mt-2"><?= $data['notes'] ?? '-' ?></div>
            </div>
        </div>
        <div class="fw-semibold text-dark h5">Productos de la Compra</div>
        <?php
        $i = 1;
        foreach ($data['products'] as $product) {
            $weight = "";
            if ($product['package_weight_lb'] != null && $product['package_weight_lb'] != 0) {
                $weight = "(" . ($product['package_weight_lb'] * 1) . " Lb)";
            }
        ?>
            <div class="card shadow mb-3 border border-1 product-item text-dark" data-detail-id="<?= $product['detail_id'] ?>" data-product-id="<?= $product['idbatch'] ?>">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row gap-2 user-select-none">
                        <div class="text-dark fw-semibold mb-md-0"><?= $i . ". " . $product['product'] . " " . $product['subproduct'] ?></div>
                        <div class="ms-md-auto">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <div class="text-muted ms-2 mb-md-0">Cantidad comprada: <b class="text-primary"><?= ($product['quantity'] * 1) ?> Libras</b></div>
                                <div class="text-muted ms-2 mb-md-0">Presentación: <b class="text-primary"><?= ($product['package_count'] * 1) . " " . $product['presentation'] ?> <?= $weight ?></b></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-12 mb-2">
                            <label class="fw-semibold">Fecha de Producción <b class="text-danger">*</b></label>
                            <input type="date" class="form-control production_date" value="<?= $product['production_date'] ?>">
                        </div>
                        <div class="col-lg-4 col-12 mb-2">
                            <label class="fw-semibold">Fecha de Vencimiento <b class="text-danger">*</b></label>
                            <input type="date" class="form-control expiration_date" value="<?= $product['expiration_date'] ?>">
                        </div>
                        <div class="col-lg-4 col-12 mb-2">
                            <label class="fw-semibold">Lote / Código <b class="text-danger">*</b></label>
                            <input type="text" class="form-control batch_code" value="<?= $product['batch_code'] ?>" readonly>
                        </div>
                        <div class="col-lg-4 col-12 mb-2">
                            <label class="fw-semibold">Temperatura de Llegada (°C) <b class="text-danger">*</b></label>
                            <input type="text" class="form-control temperature entry_temperature" placeholder="Ingrese la temperatura">
                        </div>
                        <div class="col-lg-4 col-12 mb-2">
                            <label class="fw-semibold">Condición del Producto <b class="text-danger">*</b></label>
                            <select class="form-select product_condition">
                                <?php foreach ($condicion as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-12 mb-2">
                            <label class="fw-semibold">Proceso <b class="text-danger">*</b></label>
                            <select class="form-select proceso">
                                <option value="1">Todo en una ubicación</option>
                                <option value="2">Dividir entre ubicaciones</option>
                            </select>
                        </div>
                        <input type="hidden" class="package_weight_lb" value="<?= $product['package_weight_lb'] ?>">
                        <div class="location-container">
                            <div class="col-lg-4 col-12 mb-2">
                                <label class="fw-semibold">Ubicación <b class="text-danger">*</b></label>
                                <select class="form-select location">
                                    <option value="" disabled selected>Seleccione...</option>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?= $location['id'] ?>"><?= $location['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="hidden" class="package_count" value="<?= $product['package_count'] ?>">
                            <input type="hidden" class="quantity" value="<?= $product['quantity'] ?>">
                        </div>
                        <div class="distribution-container d-none px-0">
                            <div class="col-12 row mt-3 w-auto">
                                <div class="col-lg-7 col-12 table-responsive">
                                    <table class="table text-dark shadow border border-1">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="p-2">Ubicación<b class="text-danger">*</b></th>
                                                <th class="w-15 p-2" data-package_count="<?= $product['package_count'] ?>" data-quantity="<?= $product['quantity'] ?>"><?= $product['presentation'] ?><b class="text-danger">*</b></th>
                                                <th class="w-15 p-2" data-package_weight_lb="<?= $product['package_weight_lb'] ?>">Libras</th>
                                                <th class="p-2">% del total</th>
                                                <th class="text-center p-2">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tabledata">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center add-location">
                                                        <span class="material-symbols-outlined">add</span>
                                                        <span class="ms-2">Agregar ubicación</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-lg-5 col-12">
                                    <div class="card bg-light border shadow">
                                        <div class="card-body p-2">
                                            <div class="fw-semibold text-dark">Resumen de distribución</div>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="text-dark">Total Comprado:</td>
                                                    <td class="fw-semibold text-dark"><?= $product['quantity'] ?> Libras (<?= $product['package_count'] . " " . $product['presentation'] ?>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dark">Total Asignado:</td>
                                                    <td class="fw-semibold assigned-total text-warning">
                                                        0 Libras (0 <?= $product['presentation'] ?>)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-dark">Pendiente por asignar:</td>
                                                    <td class="fw-semibold pending-total text-warning">
                                                        <?= $product['quantity'] ?> Libras
                                                        (<?= $product['package_count'] ?> <?= $product['presentation'] ?>)
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="card-footer p-2 messageAssigned">
                                            <div class="d-flex align-items-center">
                                                <span class="material-symbols-outlined text-warning h2">warning</span>
                                                <div class="m-0 ms-3">
                                                    <b class="h5 text-warning">Distribución Pendiente</b>
                                                    <p class="text-warning">La cantidad asignada aún no coincide con la cantidad recibida.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if ($product['package_weight_lb'] == null) {
                                    ?>
                                        <div class="alert alert-warning" role="alert">
                                            <div class="d-flex align-items-center">
                                                <span class="material-symbols-outlined text-warning h2">warning</span>
                                                <div class="ms-3">El peso por paquete no esta definido, se calculara automáticamente.</div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            $i++;
        }
        ?>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary d-flex align-items-center ms-auto btn-reception">
            <span class="material-symbols-outlined">check</span>
            <span class="ms-2">Confirmar recepción</span>
        </button>
    </div>
</div>
<template id="location-row-template">
    <tr>
        <td>
            <select class="form-select form-select-sm location-detail">
                <option value="" disabled selected>Seleccione...</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?= $location['id'] ?>">
                        <?= $location['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm decimal-only presentation">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm decimal-only units">
        </td>
        <td class="percentage text-center">0%</td>
        <td class="text-center user-select-none">
            <span class="material-symbols-outlined delete-row text-danger" style="cursor:pointer">
                delete
            </span>
        </td>
    </tr>
</template>