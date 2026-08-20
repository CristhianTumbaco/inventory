<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Purchases.php';
require_once RUTA . '/Modelos/DAO/PurchasesDAO.php';
require_once RUTA . '/Modelos/DTO/PurchaseDetails.php';
require_once RUTA . '/Modelos/DAO/PurchaseDetailsDAO.php';
require_once RUTA . '/Modelos/DTO/Batches.php';
require_once RUTA . '/Modelos/DAO/BatchesDAO.php';
require_once RUTA . '/Modelos/DTO/Locations.php';
require_once RUTA . '/Modelos/DAO/LocationsDAO.php';
require_once RUTA . '/Controlador/StockProcessController.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class ReceptionController
{
    private $purchasesDAO = null;
    private $purchaseDetailsDAO = null;
    private $batchesDAO = null;
    private $locationsDAO = null;
    private $stockProcessController = null;
    private $clase = null;

    public function __construct()
    {
        $this->purchasesDAO = new PurchasesDAO();
        $this->purchaseDetailsDAO = new PurchaseDetailsDAO();
        $this->batchesDAO = new BatchesDAO();
        $this->locationsDAO = new LocationsDAO();
        $this->stockProcessController = new StockProcessController();
        $this->clase = 'Recepcion';
    }
    public function consultar()
    {
        $scripts = [
            'recepcionview.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION,
        ];
        require HEADER;
        require_once RUTA . '/Vistas/' . $this->clase . '/' . $this->clase . '.php';
        require FOOTER;
    }
    public function stats()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $stats = $this->purchasesDAO->statsReception($filters);
                echo json_encode(['success' => true, 'data' => $stats[0]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function list()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $data = $this->purchasesDAO->reception($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function administrar()
    {
        $scripts = [
            'recepcion.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION,
        ];
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->formatDataReceptionId($id);
            if (is_array($data) && !empty($data)) {
                $locations = $this->locationsDAO->consultActive();
                $data = $data[0];
                require HEADER;
                require_once RUTA . '/Vistas/' . $this->clase . '/Administrar.php';
                require FOOTER;
            } else {
                redirect($this->clase);
            }
        } else {
            redirect($this->clase);
        }
    }
    public function create()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (is_array($data) && !empty($data)) {
                foreach ($data as $batch) {
                    $idbatch = $batch['id'];
                    $detail_id = $batch['detail_id'];
                    $this->purchaseDetailsDAO->updateBatch($idbatch, $detail_id);
                    $lotes = new Batches();
                    $lotes->setId($idbatch);
                    $lotes->setBatch_code(trim($batch['batch_code']));
                    $lotes->setProduction_date($batch['production_date']);
                    $lotes->setExpiration_date($batch['expiration_date']);
                    $lotes->setEntry_temperature($batch['entry_temperature']);
                    $this->batchesDAO->update($lotes);
                    $this->stockProcessController->reception($batch);
                }
                echo json_encode(['success' => true, 'message' => 'Recepción completada']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ocurrio un error inesperado']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }

    private function formatDataReceptionId($id)
    {
        $response = null;
        $data = $this->purchasesDAO->consultPending($id);
        if (is_array($data) && !empty($data)) {
            $resultado = [];
            foreach ($data as $item) {
                $key = $item['id'];
                if (!isset($resultado[$key])) {
                    $resultado[$key] = [
                        'id' => $item['id'],
                        'invoice_number' => $item['invoice_number'],
                        'purchase_date' => $item['purchase_date'],
                        'notes' => $item['notes'],
                        'user' => $item['user'],
                        'supplier' => $item['supplier'],
                        'products' => []
                    ];
                }
                $resultado[$key]['products'][] = [
                    'idbatch' => $item['idbatch'],
                    'detail_id' => $item['detail_id'],
                    'category' => $item['category'],
                    'product' => $item['product'],
                    'subproduct' => $item['subproduct'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['total_cost'],
                    'presentation' => $item['presentation'],
                    'package_count' => $item['package_count'],
                    'package_weight_lb' => $item['package_weight_lb'],
                    'batch_code' => $item['batch_code'],
                    'production_date' => $item['production_date'],
                    'expiration_date' => $item['expiration_date']
                ];
            }
            $response = array_values($resultado);
        }
        return $response;
    }
}
$controller = new ReceptionController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
