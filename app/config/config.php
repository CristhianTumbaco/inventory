<?php
ini_set('session.gc_maxlifetime', 7200);
if (!isset($_SESSION)) {
    session_name("inventario");
    session_start();
}
date_default_timezone_set('America/Guayaquil');
define('DBUSER', 'praxmed');
define('DBPWD', 'praxmed');
define('DBHOST', '127.0.0.1');
define('DBNAME', 'seafood_inventory');
define('NOMBRE', 'Inventario');
define('RUTA', dirname(__DIR__));
define('RUTA_WEB', '/inventario/app');
define('HEADER', RUTA . '/Vistas/Plantillas/Header.php');
define('FOOTER', RUTA . '/Vistas/Plantillas/Footer.php');
define('SINACCESO', RUTA . '/Vistas/Plantillas/Nopermitido.php');
