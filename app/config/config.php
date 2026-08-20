<?php
ini_set('session.gc_maxlifetime', 7200);
if (!isset($_SESSION)) {
    session_name("inventario");
    session_start();
}
date_default_timezone_set('America/Guayaquil');
$configPath = '/home/lissymar/db_config.ini';
//$configPath = dirname(__DIR__).'/config/db_config.ini';
if (!file_exists($configPath)) {
    die('Archivo de configuración no encontrado.');
}
$config = parse_ini_file($configPath);
define('VERSION', '1.0');
define('DBUSER', $config['DBUSER']);
define('DBPWD', $config['DBPWD']);
define('DBHOST', $config['DBHOST']);
define('DBNAME', $config['DBNAME']);
define('NOMBRE', 'Inventario');
define('RUTA', dirname(__DIR__));
define('RUTA_WEB', '/app');
define('HEADER', RUTA . '/Vistas/Plantillas/Header.php');
define('FOOTER', RUTA . '/Vistas/Plantillas/Footer.php');
define('SINACCESO', RUTA . '/Vistas/Plantillas/Nopermitido.php');
