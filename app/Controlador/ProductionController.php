<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Controlador/StockProcessController.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class ProductionController
{
    private $stockProcessController = null;
    private $clase = null;

    public function __construct()
    {
        $this->stockProcessController = new StockProcessController();
        $this->clase = 'Produccion';
    }
    public function consultar()
    {
        $scripts = [
            'produccion.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION
        ];
        require HEADER;
        require_once RUTA . '/Vistas/' . $this->clase . '/' . $this->clase . '.php';
        require FOOTER;
    }
    public function create()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                throw new Exception('Método no permitido');
            }
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (empty($data)) {
                throw new Exception('Datos inválidos');
            }
            $this->stockProcessController->productionProcess($data);
            $_SESSION['ok'] = 'Producto generado';
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
$controller = new ProductionController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
