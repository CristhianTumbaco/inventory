<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo NOMBRE; ?></title>
    <link rel="shortcut icon" type="image/png" href="<?php echo RUTA_WEB; ?>/assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="<?php echo RUTA_WEB; ?>/assets/css/styles.min.css" />
    <link rel="stylesheet" href="<?php echo RUTA_WEB; ?>/assets/css/add.min.css" />
    <link rel="stylesheet" href="<?php echo RUTA_WEB; ?>/assets/libs/select2/select2.min.css" />
    <link rel="stylesheet" href="<?php echo RUTA_WEB; ?>/assets/libs/select2/select2-bootstrap4.min.css" />
    <link href="<?php echo RUTA_WEB; ?>/assets/libs/datatables/datatables.min.css" rel="stylesheet">
    <?php if (!empty($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <?php
            $href = str_starts_with($style, '/')
                ? RUTA_WEB . $style
                : RUTA_WEB . '/assets/css/' . $style;
            ?>
            <link rel="stylesheet" href="<?= $href ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <aside class="left-sidebar user-select-none">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="<?php echo RUTA_WEB; ?>/Dashboard/" class="text-nowrap logo-img">
                        <img src="<?php echo RUTA_WEB; ?>/assets/images/logos/logo.png" width="60" alt="" />
                        <span class="fw-semibold">INVENTARIO LISSYMAR</span>
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <span class="material-symbols-outlined fs-8">close</span>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="sidebar-item mt-3" id="dashboard">
                            <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Dashboard/" aria-expanded="false">
                                <span>
                                    <span class="material-symbols-outlined">dashboard</span>
                                </span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <?php if ($_SESSION['ctrol'] == 0) { ?>
                            <li class="nav-small-cap">
                                <span class="material-symbols-outlined nav-small-cap-icon fs-4">more_horiz</span>
                                <span class="hide-menu">Compras</span>
                            </li>
                            <li class="sidebar-item" id="compras">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Compras/" aria-expanded="false">
                                    <span class="material-symbols-outlined">shopping_bag</span>
                                    </span>
                                    <span class="hide-menu">Compras</span>
                                </a>
                            </li>
                            <li class="sidebar-item" id="recepcion">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Recepcion/" aria-expanded="false">
                                    <span class="material-symbols-outlined">deployed_code_alert</span>
                                    </span>
                                    <span class="hide-menu">Recepción</span>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="nav-small-cap">
                            <span class="material-symbols-outlined nav-small-cap-icon fs-4">more_horiz</span>
                            <span class="hide-menu">Inventario</span>
                        </li>
                        <li class="sidebar-item" id="stock">
                            <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Stock/" aria-expanded="false">
                                <span>
                                    <span class="material-symbols-outlined">inventory</span>
                                </span>
                                <span class="hide-menu">Stock Actual</span>
                            </a>
                        </li>
                        <?php if ($_SESSION['ctrol'] == 0) { ?>
                            <li class="sidebar-item" id="produccion">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Produccion/" aria-expanded="false">
                                    <span>
                                        <span class="material-symbols-outlined">factory</span>
                                    </span>
                                    <span class="hide-menu">Producción</span>
                                </a>
                            </li>
                            <li class="sidebar-item" id="trazabilidad">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Trazabilidad/" aria-expanded="false">
                                    <span>
                                        <span class="material-symbols-outlined">swap_vert</span>
                                    </span>
                                    <span class="hide-menu">Trazabilidad</span>
                                </a>
                            </li>
                            <li class="sidebar-item" id="temperatura">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Temperatura/" aria-expanded="false">
                                    <span>
                                        <span class="material-symbols-outlined">snowflake</span>
                                    </span>
                                    <span class="hide-menu">Temperatura</span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($_SESSION['ctrol'] == 0) { ?>
                            <li class="nav-small-cap">
                                <span class="material-symbols-outlined nav-small-cap-icon fs-4">more_horiz</span>
                                <span class="hide-menu">Ventas</span>
                            </li>
                            <li class="sidebar-item" id="ventas">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Ventas/" aria-expanded="false">
                                    <span class="material-symbols-outlined">money_bag</span>
                                    </span>
                                    <span class="hide-menu">Ventas</span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($_SESSION['ctrol'] == 0) { ?>
                            <li class="nav-small-cap">
                                <span class="material-symbols-outlined nav-small-cap-icon fs-4">more_horiz</span>
                                <span class="hide-menu">MANTENIMIENTO</span>
                            </li>
                            <li class="sidebar-item" id="usuarios">
                                <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Usuarios/" aria-expanded="false">
                                    <span>
                                        <span class="material-symbols-outlined">account_box</span>
                                    </span>
                                    <span class="hide-menu">Usuarios</span>
                                </a>
                            </li>
                            <li class="sidebar-item" id="parametros">
                                <a class="sidebar-link" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" href="">
                                    <span>
                                        <span class="material-symbols-outlined">design_services </span>
                                    </span>
                                    <span class="hide-menu">Parametros</span>
                                </a>
                            </li>
                            <div class="collapse ms-3" id="collapseExample">
                                <li class="sidebar-item" id="categorias">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Categorias/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Categorias</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="productos">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Productos/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Productos</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="subproductos">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Subproductos/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Subproductos</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="ubicaciones">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Ubicaciones/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Ubicaciones</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="presentaciones">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Presentaciones/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Presentaciones</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="metodopago">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/MetodoPago/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Método de Pago</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="estados">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Estados/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Estados</span>
                                    </a>
                                </li>
                                <li class="sidebar-item" id="proveedores">
                                    <a class="sidebar-link" href="<?php echo RUTA_WEB; ?>/Proveedores/" aria-expanded="false">
                                        <span>
                                            <span class="material-symbols-outlined icon-fill fs-2">circle</span>
                                        </span>
                                        <span class="hide-menu">Proveedores</span>
                                    </a>
                                </li>
                            </div>
                        <?php } ?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header user-select-none">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                                <span class="material-symbols-outlined">menu</span>
                            </a>
                        </li>
                        <li class="nav-item">
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <img src="<?php echo RUTA_WEB; ?>/assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <span class="material-symbols-outlined fs-6">person</span>
                                            <p class="mb-0 fs-3"><?= $_SESSION['ctname'] ?></p>
                                        </a>
                                        <a href="<?php echo RUTA_WEB; ?>/Logout/" class="btn btn-outline-danger mx-3 mt-2 d-block">Salir</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid">
                <!--  Row 1 -->