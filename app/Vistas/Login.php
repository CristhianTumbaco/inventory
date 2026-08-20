<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo NOMBRE; ?> - Iniciar Sesión</title>
    <link rel="shortcut icon" type="image/png" href="<?php echo RUTA_WEB; ?>/assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="<?php echo RUTA_WEB; ?>/assets/css/styles.min.css" />
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper user-select-none" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0 shadow-lg">
                            <div class="card-body">
                                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="<?php echo RUTA_WEB; ?>/assets/images/logos/logo.png" width="180" alt="">
                                </a>
                                <p class="text-center">Sistema de Inventario</p>
                                <form action="<?php echo RUTA_WEB; ?>/Logear/" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" name="usuario" class="form-control" autocomplete="off" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Contraseña</label>
                                        <input type="password" name="clave" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Iniciar Sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo RUTA_WEB; ?>/assets/js/sweetalert2.js"></script>
    <script src="<?php echo RUTA_WEB; ?>/assets/js/notify.min.js"></script>
    <script src="<?php echo RUTA_WEB; ?>/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo RUTA_WEB; ?>/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<script>Notify(2,'" . $_SESSION['error'] . "')</script>";
        unset($_SESSION['error']);
    }
    ?>
</body>

</html>