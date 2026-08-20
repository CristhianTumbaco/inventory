<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Batches.php';
require_once RUTA . '/Modelos/DAO/BatchesDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class TraceabilityController
{
    private $batchesDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->batchesDAO = new BatchesDAO();
        $this->clase = 'Trazabilidad';
    }
    public function consultar()
    {
        $scripts = [
            'trazabilidad.js',
            'funciones.js',
            'tabla.js'
        ];
        require HEADER;
        require_once RUTA . '/Vistas/' . $this->clase . '/' . $this->clase . '.php';
        require FOOTER;
    }
    public function list()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $data = $this->batchesDAO->list($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function resume()
    {
        $scripts = [
            'trazabilidad1.js',
            'funciones.js'
        ];
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->formatResumeHistory($id);
            if (is_array($data) && !empty($data)) {
                $types = [
                    'entrada' => [
                        'bg' => 'bg-success-subtle',
                        'text' => 'text-success',
                        'icon' => 'arrow_downward',
                    ],
                    'salida' => [
                        'bg' => 'bg-primary-subtle',
                        'text' => 'text-primary',
                        'icon' => 'arrow_left_alt',
                    ],
                    'traslado' => [
                        'bg' => 'bg-purple-subtle',
                        'text' => 'text-purple',
                        'icon' => 'compare_arrows',
                    ],
                    'merma' => [
                        'bg' => 'bg-danger-subtle',
                        'text' => 'text-danger',
                        'icon' => 'warning',
                    ],
                    'produccion' => [
                        'bg' => 'bg-info-subtle',
                        'text' => 'text-info',
                        'icon' => 'factory',
                    ],
                ];
                $months = [
                    1 => 'ENE',
                    2 => 'FEB',
                    3 => 'MAR',
                    4 => 'ABR',
                    5 => 'MAY',
                    6 => 'JUN',
                    7 => 'JUL',
                    8 => 'AGO',
                    9 => 'SEP',
                    10 => 'OCT',
                    11 => 'NOV',
                    12 => 'DIC'
                ];
                $conditions = [
                    'congelado' => ['class' => 'bg-primary-subtle text-primary', 'text' => 'Congelado'],
                    'descongelado' => ['class' => 'bg-warning-subtle text-warning', 'text' => 'Descongelado'],
                    'fresco' => ['class' => 'bg-success-subtle text-success', 'text' => 'Fresco'],
                    'dañado' => ['class' => 'bg-danger-subtle text-danger', 'text' => 'Dañado'],
                ];
                require HEADER;
                //echo json_encode($data);
                require_once RUTA . '/Vistas/' . $this->clase . '/Resumen.php';
                require FOOTER;
            } else {
                redirect($this->clase);
            }
        } else {
            redirect($this->clase);
        }
    }
    private function formatResumeHistory($id)
    {
        $response = null;
        $batch = $this->batchesDAO->consultxId($id);
        if (!empty($batch)) {
            $batch = $batch[0];
            $response = [
                "id" => (int)$batch['id'],
                "batch_code" => $batch['batch_code'],
                "product" => $batch['product'],
                "subproductId" => $batch['subproductId'],
                "subproduct" => $batch['subproduct'],
                "presentation" => $batch['presentation'],
                "package_count" => (float)$batch['package_count'],
                "quantity_lb" => (float)$batch['quantity_lb'],
                "production_date" => $batch['production_date'],
                "expiration_date" => $batch['expiration_date'],
                "notes" => $batch['notes'],
                "history" => []
            ];
            $movements = $this->batchesDAO->consultMovements($id);
            if (!empty($movements)) {
                foreach ($movements as $movement) {
                    $date = date('Y-m-d', strtotime($movement['created']));
                    if (!isset($response['history'][$date])) {
                        $response['history'][$date] = [];
                    }
                    $reference = [];
                    if ($movement['origin_id'] != null) {
                        switch ($movement['origin_type']) {
                            case 'purchase':
                                $reference = $this->purchase($movement['origin_id']);
                                break;
                            case 'sale':
                                $reference = $this->sale($movement['origin_id']);
                                break;
                            case 'production':
                                if ($movement['movement_type'] != 'entrada') {
                                    $reference = $this->production($movement['origin_id']);
                                } else {
                                    $reference = $this->productionOrigen($movement['origin_id']);
                                }
                                break;
                        }
                    }
                    $response['history'][$date][] = [
                        "id" => $movement['id'],
                        "hour" => date('H:i', strtotime($movement['created'])),
                        "movement_type" => $movement['movement_type'],
                        "description" => match ($movement['movement_type']) {
                            'entrada' => 'Ingreso al inventario',
                            'traslado' => 'Traslado de ubicación',
                            'produccion' => 'Consumido en producción',
                            'merma' => 'Merma registrada',
                            'salida' => 'Salida por venta',
                            default => ucfirst($movement['movement_type'])
                        },
                        "location" => $movement['locations'],
                        "presentation" => $movement['presentation'],
                        "package_count" => (float)$movement['package_count'],
                        "quantity" => (float)$movement['quantity'],
                        "product_condition" => $movement['product_condition'],
                        "origin_type" => $movement['origin_type'],
                        "origin_id" => (int)$movement['origin_id'],
                        "reference" => $reference
                    ];
                }
                $response['history'] = array_map(
                    fn($date, $events) => [
                        "date" => $date,
                        "events" => $events
                    ],
                    array_keys($response['history']),
                    $response['history']
                );
            }
        }
        return $response;
    }
    private function production($id)
    {
        $response = [];
        $data = $this->batchesDAO->production($id);
        if (!empty($data)) {
            $response = array(
                "text" => "Se crearon los siguientes lotes",
                "data" => $data
            );
        }
        return $response;
    }
    private function productionOrigen($id)
    {
        $response = [];
        $data = $this->batchesDAO->productionOrigen($id);
        if (!empty($data)) {
            $response = array(
                "text" => "Lotes consumidos en la producción",
                "data" => $data
            );
        }
        return $response;
    }
    private function purchase($id)
    {
        $response = [];
        $data = $this->batchesDAO->purchase($id);
        if (!empty($data)) {
            $response = array(
                "text" => "Se realizo la siguiente compra",
                "data" => $data
            );
        }
        return $response;
    }
    private function sale($id)
    {
        $response = [];
        $data = $this->batchesDAO->sale($id);
        if (!empty($data)) {
            $response = array(
                "text" => "Se realizo la siguiente venta",
                "data" => $data
            );
        }
        return $response;
    }
}
$controller = new TraceabilityController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
