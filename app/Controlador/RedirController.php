<?php
function redirect($dato)
{
    header("Location: " . RUTA_WEB . "/" . $dato . "/");
    exit();
}
