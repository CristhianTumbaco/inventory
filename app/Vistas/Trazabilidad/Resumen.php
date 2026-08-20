<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Trazabilidad</a></li>
        <li class="breadcrumb-item active" aria-current="page">Historial del Lote</li>
    </ol>
</nav>
<div class="d-flex user-select-none mb-3">
    <div class="d-flex align-items-center justify-content-center">
        <span class="material-symbols-outlined text-dark h1">swap_vert</span>
    </div>
    <div class="ms-2">
        <div class="h4 fw-semibold">Trazabilidad por Lote</div>
        <h6 class="card-subtitle mb-2 text-body-secondary">Consulta el historial completo de movimientos de un lote.</h6>
    </div>
    <div class="ms-auto">
        <a href="<?php echo RUTA_WEB; ?>/Stock/<?= $data['subproductId'] ?>"
            class="btn btn-light border border-1 d-flex align-items-center justify-content-center">
            <span class="material-icons-outlined">arrow_outward</span>
            <span class="ms-2">Ir a Stock</span>
        </a>

    </div>
</div>
<div class="card shadow mb-2">
    <div class="card-body p-2 user-select-none">
        <div class="d-flex">
            <div class="border rounded-4 bg-primary-subtle text-center p-3 d-flex">
                <div class="align-self-center">
                    <div class="fw-bold text-primary h5">LOTE</div>
                    <div class="fw-semibold text-primary h3"><?= $data['batch_code'] ?></div>
                    <div>
                        <?php
                        if ($data['package_count'] > 0) {
                            echo '<span class="badge rounded-pill text-bg-success">Disponible</span>';
                        } else {
                            echo '<span class="badge rounded-pill text-bg-danger">Agotado</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="p-2 w-100">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th>Producto:</th>
                        <td><?= $data['product'] ?></td>
                        <th>Fecha de Producción:</th>
                        <td><?= date_create($data['production_date'])->format('d/m/Y') ?></td>
                    </tr>
                    <tr>
                        <th>Subproducto:</th>
                        <td><?= $data['subproduct'] ?></td>
                        <th>Fecha de Expiración:</th>
                        <td><?= date_create($data['expiration_date'])->format('d/m/Y') ?></td>
                    </tr>
                    <tr>
                        <th>Presentación:</th>
                        <td><?= $data['presentation'] ?></td>
                        <th>Cantidad Actual:</th>
                        <td class="fw-bold text-primary"><?= $data['package_count'] . " " . $data['presentation'] . " (" . $data['quantity_lb'] . " LB)" ?></td>
                    </tr>
                    <tr>
                        <th>Notas:</th>
                        <td colspan="3"><?= $data['notes'] ?? '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <div class="fw-semibold h5">Historial de movimientos</div>
        <div class="text-muted">Se muestran los movimientos agrupoados por fecha.</div>
    </div>
    <div class="p-4">
        <div class="row">
            <?php
            foreach ($data['history'] as $history) {
                $date = $history['date'];
                $dateObj = new DateTime($date);
                $formatted =  $months[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
                $events = $history['events'];
            ?>
                <div class="col-1 text-center">
                    <div class="h4 fw-semibold"><?= $dateObj->format('d') ?></div>
                    <div class="text-muted"><?= $formatted ?></div>
                </div>
                <div class="col-11">
                    <ul class="timeline-widget mb-0 position-relative mb-3">
                        <?php
                        foreach ($events as $event) {
                            $condition = strtolower(trim($event['product_condition'] ?? ''));
                            $type = $types[$event['movement_type']] ?? null;
                        ?>
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-time text-dark flex-shrink-0 text-end fw-bold"><?= $event['hour'] ?></div>
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 border border-dark flex-shrink-0 my-8"></span>
                                    <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                </div>
                                <div class="timeline-desc w-100">
                                    <table class="table table-borderless w-100 mb-0" style="table-layout: fixed;">
                                        <tr>
                                            <td style="width: 38%;">
                                                <?php if ($type): ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="<?= $type['bg'] ?> rounded-4 p-2 d-flex justify-content-center align-items-center">
                                                            <span class="material-symbols-outlined <?= $type['text'] ?>" style="font-size:2rem;">
                                                                <?= $type['icon'] ?>
                                                            </span>
                                                        </div>
                                                        <div class="ms-2">
                                                            <div class="fw-semibold"><?= strtoupper($event['movement_type']) ?></div>
                                                            <div class="text-muted fs-2"><?= $event['description'] ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td style="width: 20%;">
                                                <div class="fw-semibold">Ubicación:</div>
                                                <div><?= $event['location'] ?></div>
                                            </td>
                                            <td style="width: 20%;">
                                                <div class="fw-semibold"><?= $event['package_count'] . " " . $event['presentation'] ?></div>
                                                <div><?= $event['quantity'] ?> LB</div>
                                            </td>
                                            <td style="width: 22%;">
                                                <div class="fw-semibold">Estado del producto:</div>
                                                <div>
                                                    <?php
                                                    if ($condition !== '' && isset($conditions[$condition])) {
                                                        $conditionData = $conditions[$condition];
                                                        echo '<span class="badge ' . $conditionData['class'] . ' rounded-pill">' . $conditionData['text'] . '</span>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary-subtle text-secondary rounded-pill">-</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php if (!empty($event['reference'])): ?>
                                        <div class="pb-2 border-bottom">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="material-symbols-outlined text-muted" style="font-size: 18px;">
                                                    link
                                                </span>

                                                <span class="text-muted">
                                                    <?= htmlspecialchars($event['reference']['text']) ?>:
                                                </span>

                                                <?php foreach ($event['reference']['data'] as $reference): ?>
                                                    <span class="badge bg-light-primary text-primary fw-semibold">
                                                        <?= htmlspecialchars($reference['ref']) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
                <hr>
            <?php
            }
            ?>

        </div>
    </div>
</div>