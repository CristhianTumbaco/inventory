<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb user-select-none">
        <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
        <li class="breadcrumb-item active" aria-current="page">Parametros</li>
        <li class="breadcrumb-item active" aria-current="page"><?= $this->clase ?></li>
    </ol>
</nav>

<div class="card shadow-lg w-100">
    <div class="card-body p-4">
        <div class="d-flex user-select-none mb-3">
            <div class="d-flex align-items-center justify-content-center">
                <span class="material-symbols-outlined text-dark h1">unknown_document</span>
            </div>
            <div class="ms-2">
                <div class="h4 fw-semibold">Informe de <?= $this->clase ?></div>
                <h6 class="card-subtitle mb-2 text-body-secondary">Gestione y administre los parametros.</h6>
            </div>
            <div class="ms-auto">
                <a class="btn btn-primary d-flex aligns-item-center" href="<?= RUTA_WEB . "/" . $this->clase ?>/Nuevo">
                    <span class="material-symbols-outlined">add</span>
                    <span class="ms-2">Nuevo</span>
                </a>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <div class="position-relative">
                <span class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y ms-3">search</span>
                <input type="text" class="form-control form-control-lg ps-5" id="tablebuscador" placeholder="¿Buscas algo?">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless border" id="table">
                <thead class="bg-light user-select-none">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $item) {
                    ?>
                        <tr>
                            <td><?= $item->getId(); ?></td>
                            <td><?= $item->getName(); ?></td>
                            <td>
                                <?php
                                if ($item->getStatus() == 1) {
                                    echo '<span class="badge bg-success">Activo</span>';
                                } else {
                                    echo '<span class="badge bg-danger">Inactivo</span>';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <a class="material-symbols-outlined h3" title="Editar" href="<?= RUTA_WEB . "/" . $this->clase."/".$item->getId() ?>">edit</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>

            </table>

        </div>
    </div>
</div>