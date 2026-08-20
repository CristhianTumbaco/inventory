<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Stock</a></li>
        <li class="breadcrumb-item active" aria-current="page">Administrar</li>
    </ol>
</nav>

<div class="card shadow-lg w-100">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row user-select-none mb-3">
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined text-dark h1">inventory</span>
                <div class="ms-2">
                    <div class="h4 fw-semibold"><?= $data['producto'] . " " . $data['subproducto'] ?></div>
                    <h6 class="card-subtitle mb-2 text-body-secondary">Administración del inventario seleccionado</h6>
                </div>
            </div>
            <div class="ms-md-auto d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                <?php if ($_SESSION['ctrol'] == 0) { ?>
                    <button class="btn btn-danger d-flex align-items-center justify-content-center btn-merma">
                        <span class="material-symbols-outlined">deployed_code_alert</span>
                        <span class="ms-2">Registrar Merma</span>
                    </button>
                <?php } ?>
            </div>
        </div>
        <div class="table-responsive border rounded-3 shadow mt-3">
            <table class="table table-hover table-borderless">
                <thead class="user-select-none">
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Paquetes</th>
                        <th class="text-end">Peso por Paquete</th>
                        <th class="text-end">Peso Total</th>
                        <th>Lote</th>
                        <th>Fecha de Producción</th>
                        <th>Fecha de Expiración</th>
                        <th></th>
                    </tr>
                </thead>
                <?php
                foreach ($data['locations'] as $location) {
                ?>
                    <tbody>
                        <tr class="bg-light user-select-none">
                            <td class="p-3 fw-semibold" colspan="10">
                                <div class="d-flex align-items-center">
                                    <span class="material-symbols-outlined text-info">snowflake</span>
                                    <span class="ms-3"><?= $location['ubicacion'] ?></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="location-group" data-location-id="<?= $location['ubicacionId'] ?>">
                        <?php
                        foreach ($location['packages'] as $package) {
                            if (!isset($batchColors[$package['batch_code']])) {
                                $batchColors[$package['batch_code']] =
                                    $bootstrapColors[$colorIndex % count($bootstrapColors)];
                                $colorIndex++;
                            }
                            $badgeColor = $batchColors[$package['batch_code']];
                            $weight = ($package['package_weight_lb'] * 1) . " Libras";
                            if ($package['units_per_package'] != null) {
                                $weight = ($package['package_weight_lb'] * 1) .
                                    " Libras (" . $package['units_per_package'] . " U)";
                            }
                        ?>
                            <tr data-id="<?= $package['id'] ?>" data-location="<?= $location['ubicacion'] ?>" data-expiration-date="<?= $package['expiration_date'] ?>" data-package-count="<?= $package['package_count'] ?>">
                                <td class="ps-5 text-nowrap">
                                    <input class="form-check-input edit package-checkbox" type="checkbox" value="<?= $package['id'] ?>" name="packages[]">
                                    <span class="ms-2"><?= $data['subproducto'] ?></span>
                                </td>
                                <td class="text-end"><?= ($package['package_count'] * 1) . " " . $package['presentacion'] ?></td>
                                <td class="text-end"><?= $weight ?></td>

                                <td class="text-end"><?= ($package['quantity_lb'] * 1) ?> Libras</td>
                                <td>
                                    <span class="badge text-bg-<?= $badgeColor ?>"><?= $package['batch_code'] ?></span>
                                </td>
                                <td><?= $package['production_date'] ?></td>
                                <td><?= $package['expiration_date'] ?></td>
                                <td class="text-center user-select-none">
                                    <?php if ($_SESSION['ctrol'] == 0) { ?>
                                        <span class="material-symbols-outlined h3 edit text-gray-100 drag-handle">drag_indicator</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
<div id="modal"></div>