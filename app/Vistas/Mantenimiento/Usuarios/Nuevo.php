<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb user-select-none">
        <li class="breadcrumb-item"><a href="#">Mantenimiento</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $this->clase ?></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
    </ol>
</nav>

<div class="card shadow-lg w-50 m-auto">
    <div class="card-body p-4">
        <div class="d-flex user-select-none mb-3">
            <div class="d-flex align-items-center justify-content-center">
                <span class="material-symbols-outlined text-dark h1">add_circle</span>
                <div class="ms-2 h4 fw-semibold">Registrar nuevo</div>
            </div>
        </div>
        <form action="<?= RUTA_WEB . "/" . $this->clase ?>/save" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="fw-bold">Nombre del usuario</label>
                <input class="form-control" name="name" placeholder="Ingrese el nombre" required />
            </div>
            <div class="mb-2">
                <label class="fw-bold">Correo del usuario</label>
                <input type="email" class="form-control" name="email" placeholder="Ingrese el nombre" required />
            </div>
            <div class="mb-2">
                <label class="fw-bold">Contraseña</label>
                <div class="input-group mb-2">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese la contraseña" autocomplete="off" required>
                    <button class="input-group-text btn btn-primary rounded-0 d-flex aligns-item-center" type="button" onclick="mostrarPassword()"><span class="material-symbols-outlined icon">visibility_off</span></button>
                </div>
            </div>
            <div class="mb-2">
                <label class="fw-bold">Rol del usuario</label>
                <select class="form-select" name="rol" required>
                    <option value="1">Supervisor</option>
                    <option value="0">Administrador</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>