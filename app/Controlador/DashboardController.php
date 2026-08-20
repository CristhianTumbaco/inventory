<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DAO/DashboardDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class DashboardController
{
    private $dashboardDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->dashboardDAO = new DashboardDAO();
        $this->clase = 'Dashboard';
    }
    public function consultar()
    {
        $scripts = [
            'dashboard.min.js?v=' . VERSION,
            'graphPurchase.min.js?v=' . VERSION,
            'graphSales.min.js?v=' . VERSION,
            'graphInventory.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            '/assets/libs/apexcharts.js',
        ];
        require HEADER;
        require_once RUTA . '/Vistas/' . $this->clase . '/' . $this->clase . '.php';
        require FOOTER;
    }
    public function analytics()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                throw new Exception('Método no permitido');
            }
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (empty($filters)) {
                throw new Exception('No se recibieron filtros');
            }
            $response = null;
            $module = $filters['module'];
            if ($module == 'purchase') {
                $response = $this->purchase($filters);
            } elseif ($module == 'sales') {
                $response = $this->sales($filters);
            } elseif ($module == 'inventory') {
                $response = $this->inventory($filters);
            }

            echo json_encode(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    private function purchase($filters)
    {
        $response = null;
        $stats = $this->dashboardDAO->statPurchases($filters);
        $graphDay = $this->dashboardDAO->graphPurchaseDay($filters);
        $graphSupplier = $this->dashboardDAO->graphPurchaseSupplier($filters);
        $graphProducts = $this->dashboardDAO->graphPurchaseProducts($filters);
        $graphTaxes = $this->dashboardDAO->graphPurchaseTaxes($filters);
        $current = $stats['current']['total'];
        $previous = $stats['previous']['total'];
        $percentage = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
        $stats['previous']['percentage'] = round($percentage, 2);
        $response = array(
            "stats" => $stats,
            "graphDay" => $graphDay,
            "graphSupplier" => $graphSupplier,
            "graphProducts" => $graphProducts,
            "graphTaxes" => $graphTaxes[0]
        );
        return $response;
    }
    private function sales($filters)
    {
        $response = null;
        $stats = $this->dashboardDAO->statSales($filters);
        $graphDay = $this->dashboardDAO->graphSaleDay($filters);
        $graphSupplier = $this->dashboardDAO->graphSaleSupplier($filters);
        $graphProducts = $this->dashboardDAO->graphSaleProducts($filters);
        $graphTaxes = $this->dashboardDAO->graphSaleTaxes($filters);
        $current = $stats['current']['total'];
        $previous = $stats['previous']['total'];
        $percentage = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
        $stats['previous']['percentage'] = round($percentage, 2);
        $response = array(
            "stats" => $stats,
            "graphDay" => $graphDay,
            "graphSupplier" => $graphSupplier,
            "graphProducts" => $graphProducts,
            "graphTaxes" => $graphTaxes[0]
        );
        return $response;
    }
    private function inventory($filters)
    {
        $response = null;
        $stock = $this->dashboardDAO->statStock();
        $inputOutput = $this->dashboardDAO->statInOut($filters);
        $expired = $this->dashboardDAO->statExpired();
        $temperature = $this->dashboardDAO->statTemperature();
        $graphDay = $this->dashboardDAO->graphInventoryDay($filters);
        $graphTotal = $this->dashboardDAO->graphInventoryTotal($filters);
        $graphLocation = $this->dashboardDAO->graphInventoryLocations();
        $graphProducts = $this->dashboardDAO->graphInventoryProducts();
        $dataExpired = $this->dashboardDAO->dataExpired();
        $response = array(
            "stats" => array(
                "stock" => $stock[0],
                "inOut" => $inputOutput,
                "expired" => $expired[0],
                "temperature" => $temperature,
            ),
            "graphDay" => $graphDay,
            "graphTotal" => $graphTotal[0],
            "graphLocation" => $graphLocation,
            "graphProducts" => $graphProducts,
            "dataExpired" => $dataExpired,
        );
        return $response;
    }
}
$controller = new DashboardController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
