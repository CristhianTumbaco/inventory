<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Controlador/RedirController.php';

function controladorrutas($controller, $accion, $params = [])
{
    if (isset($_SESSION['ctid'], $_SESSION['ctuser'], $_SESSION['ctname'], $_SESSION['ctrol'])) {
        $metodoProhibido = ['__construct', '__destruct'];
        if (
            method_exists($controller, $accion) &&
            is_callable([$controller, $accion]) &&
            !in_array($accion, $metodoProhibido)
        ) {
            return call_user_func_array([$controller, $accion], $params);
        } else {
            redirect('Login');
        }
    } else {
        redirect('Login');
    }
}
