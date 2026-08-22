<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/InventoryStock.php';
require_once RUTA . '/Modelos/DAO/InventoryStockDAO.php';
require_once RUTA . '/Modelos/DTO/InventoryPackages.php';
require_once RUTA . '/Modelos/DAO/InventoryPackagesDAO.php';
require_once RUTA . '/Modelos/DTO/Locations.php';
require_once RUTA . '/Modelos/DAO/LocationsDAO.php';
require_once RUTA . '/Modelos/DTO/Products.php';
require_once RUTA . '/Modelos/DAO/ProductsDAO.php';
require_once RUTA . '/Controlador/StockProcessController.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class StockController
{
    private $inventoryStockDAO = null;
    private $inventoryPackagesDAO = null;
    private $locationsDAO = null;
    private $productsDAO = null;
    private $stockProcessController = null;
    private $clase = null;

    public function __construct()
    {
        $this->inventoryStockDAO = new InventoryStockDAO();
        $this->inventoryPackagesDAO = new InventoryPackagesDAO();
        $this->locationsDAO = new LocationsDAO();
        $this->productsDAO = new ProductsDAO();
        $this->stockProcessController = new StockProcessController();
        $this->clase = 'Stock';
    }
    public function consultar()
    {
        $scripts = [
            'stock.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION
        ];
        require HEADER;
        require_once RUTA . '/Vistas/' . $this->clase . '/' . $this->clase . '.php';
        require FOOTER;
    }
    public function administrar()
    {
        $scripts = [
            'Sortable.min.js',
            'stockadmin.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
        ];
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->formatManage($id);
            if ($data != null) {
                $batchColors = [];
                $bootstrapColors = [
                    'primary',
                    'success',
                    'danger',
                    'warning',
                    'info',
                    'secondary',
                    'dark'
                ];
                $colorIndex = 0;
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
    public function stats()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $stats = $this->inventoryStockDAO->stats($filters);
                echo json_encode(['success' => true, 'data' => $stats[0]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function status()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $locations = $this->locationsDAO->consultActive();
            $products = $this->productsDAO->consultActive();
            echo json_encode(['success' => true, 'data' => array("locations" => $locations, "products" => $products)]);
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
                $data = $this->formatList($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function production()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (!empty($data)) {
                $production = $this->inventoryPackagesDAO->manage($data['subproductId']);
                echo json_encode(['success' => true, 'data' => $production]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron datos']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    private function formatList($filters)
    {
        $toneladas = 2200;
        $rows = $this->inventoryStockDAO->list($filters);
        $products = [];
        foreach ($rows as $row) {
            $productKey = $row['productoId'];
            // PRODUCTO
            if (!isset($products[$productKey])) {
                $products[$productKey] = [
                    'productoId' => $row['productoId'],
                    'producto' => $row['producto'],
                    'totalCantidad' => 0,
                    'totalToneladas' => 0,
                    'totalPaquetes' => 0,
                    'details' => []
                ];
            }
            $products[$productKey]['totalCantidad'] += (float)$row['cantidad'];
            $products[$productKey]['totalToneladas'] += ($row['cantidad'] / $toneladas);
            $products[$productKey]['totalPaquetes'] += (float)$row['paquetes'];
            $subproductKey = $row['subproductoId'];
            // SUBPRODUCTO
            if (!isset($products[$productKey]['details'][$subproductKey])) {
                $products[$productKey]['details'][$subproductKey] = [
                    'subproductoId' => $row['subproductoId'],
                    'subproducto' => $row['subproducto'],
                    'totalCantidad' => 0,
                    'totalToneladas' => 0,
                    'totalPaquetes' => 0,
                    'details' => []
                ];
            }
            $products[$productKey]['details'][$subproductKey]['totalCantidad'] += (float)$row['cantidad'];
            $products[$productKey]['details'][$subproductKey]['totalToneladas'] += ($row['cantidad'] / $toneladas);
            $products[$productKey]['details'][$subproductKey]['totalPaquetes'] += (float)$row['paquetes'];
            // UBICACION + PRESENTACION
            $products[$productKey]['details'][$subproductKey]['details'][] = [
                'ubicacionId' => $row['ubicacionId'],
                'ubicacion' => $row['ubicacion'],
                'presentacionId' => $row['presentacionId'],
                'presentacion' => $row['presentacion'],
                'cantidad' => (float)$row['cantidad'],
                'toneladas' => round($row['cantidad'] / $toneladas, 3),
                'paquetes' => (float)$row['paquetes'],
                'unidades' => $row['unidades'],
                'ultima_actualizacion' => $row['ultima_actualizacion']
            ];
        }
        // Limpiar índices para JSON
        foreach ($products as &$product) {
            $product['totalToneladas'] = round($product['totalToneladas'], 3);
            foreach ($product['details'] as &$subproduct) {
                $subproduct['totalToneladas'] = round($subproduct['totalToneladas'], 3);
                $subproduct['details'] = array_values($subproduct['details']);
            }
            $product['details'] = array_values($product['details']);
        }
        return array_values($products);
    }
    public function formatManage($id)
    {
        $data = $this->inventoryPackagesDAO->manage($id);
        $locations = $this->locationsDAO->consultActive();
        //$location_id['id'];
        //$location_id['name'];
        if (!empty($data) && is_array($data)) {
            $products = [];
            foreach ($data as $row) {
                $productKey = $row['subproductoId'];
                // PRODUCTO
                if (!isset($products[$productKey])) {
                    $products[$productKey] = [
                        'productoId' => $row['productoId'],
                        'producto' => $row['producto'],
                        'subproductoId' => $row['subproductoId'],
                        'subproducto' => $row['subproducto'],
                        'locations' => []
                    ];
                }
                $locationsKey = $row['ubicacionId'];
                if (!isset($products[$productKey]['locations'][$locationsKey])) {
                    $products[$productKey]['locations'][$locationsKey] = [
                        'ubicacionId' => $row['ubicacionId'],
                        'ubicacion' => $row['ubicacion'],
                        'packages' => []
                    ];
                }
                $packageId = $row['id'];
                if (!isset($products[$productKey]['locations'][$locationsKey]['packages'][$packageId])) {
                    $products[$productKey]['locations'][$locationsKey]['packages'][$packageId] = [
                        'id' => $row['id'],
                        'presentacion' => $row['presentacion'],
                        'package_weight_lb' => $row['package_weight_lb'],
                        'package_count' => $row['package_count'],
                        'quantity_lb' => $row['quantity_lb'],
                        'units_per_package' => $row['units_per_package'],
                        'batch_code' => $row['batch_code'],
                        'production_date' => $row['production_date'],
                        'expiration_date' => $row['expiration_date']
                    ];
                }
            }
            foreach ($products as &$product) {
                foreach ($locations as $location) {
                    $locationId = $location['id'];
                    if (!isset($product['locations'][$locationId])) {
                        $product['locations'][$locationId] = [
                            'ubicacionId' => $location['id'],
                            'ubicacion' => $location['name'],
                            'packages' => []
                        ];
                    }
                }
            }
            foreach ($products as &$product) {
                foreach ($product['locations'] as &$subproduct) {
                    $subproduct['packages'] = array_values($subproduct['packages']);
                }
                $product['locations'] = array_values($product['locations']);
            }
            return array_values($products);
        } else {
            return null;
        }
    }
    public function move()
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
            $this->stockProcessController->movePackage(
                (int)$data['packageId'],
                (int)$data['oldLocationId'],
                (int)$data['ubicacionId'],
                (float)$data['quantityToMove'],
                (string)$data['condition'],
                ($data['temperature']) !== '' ? $data['temperature'] : null,
                ($data['expiration_date']) !== '' ? $data['expiration_date'] : null
            );
            $_SESSION['ok'] = 'Paquete movido correctamente';
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function decrease()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                throw new Exception('Método no permitido');
            }
            $json_data = file_get_contents('php://input');
            $decrease = json_decode($json_data, true);
            if (empty($decrease)) {
                throw new Exception('Datos inválidos');
            }
            $this->stockProcessController->decreaseProcess($decrease, 'merma', 'dañado', 'stock', null);
            $_SESSION['ok'] = 'Merma registrada';
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
$controller = new StockController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
