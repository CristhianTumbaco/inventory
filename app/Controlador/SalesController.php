<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Sales.php';
require_once RUTA . '/Modelos/DAO/SalesDAO.php';
require_once RUTA . '/Modelos/DTO/SaleDetails.php';
require_once RUTA . '/Modelos/DAO/SaleDetailsDAO.php';
require_once RUTA . '/Modelos/DTO/Suppliers.php';
require_once RUTA . '/Modelos/DAO/SuppliersDAO.php';
require_once RUTA . '/Modelos/DTO/StatusTransaction.php';
require_once RUTA . '/Modelos/DAO/StatusTransactionDAO.php';
require_once RUTA . '/Modelos/DTO/PaymentMethod.php';
require_once RUTA . '/Modelos/DAO/PaymentMethodDAO.php';
require_once RUTA . '/Modelos/DTO/InventoryPackages.php';
require_once RUTA . '/Modelos/DAO/InventoryPackagesDAO.php';
require_once RUTA . '/Controlador/StockProcessController.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class SalesController
{
    private $salesDAO = null;
    private $saleDetailsDAO = null;
    private $suppliersDAO = null;
    private $statusTransactionDAO = null;
    private $paymentMethodDAO = null;
    private $inventoryPackagesDAO = null;
    private $stockProcessController = null;
    private $clase = null;

    public function __construct()
    {
        $this->salesDAO = new SalesDAO();
        $this->saleDetailsDAO = new SaleDetailsDAO();
        $this->suppliersDAO = new SuppliersDAO();
        $this->statusTransactionDAO = new StatusTransactionDAO();
        $this->paymentMethodDAO = new PaymentMethodDAO();
        $this->inventoryPackagesDAO = new InventoryPackagesDAO();
        $this->stockProcessController = new StockProcessController();
        $this->clase = 'Ventas';
    }
    public function consultar()
    {
        $scripts = [
            'ventasview.js',
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
                $data = $this->salesDAO->reportTable($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function stats()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $totalSales = $this->salesDAO->totalSales($filters);
                $statusSales = $this->salesDAO->statusSales($filters);
                $response = array(
                    "totalSales" => $totalSales[0],
                    "statusSales" => $statusSales
                );
                echo json_encode(['success' => true, 'data' => $response]);
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
            $status = $this->statusTransactionDAO->consult(false);
            echo json_encode(['success' => true, 'data' => $status]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function nuevo()
    {
        $scripts = [
            'ventas.js',
            'funciones.js'
        ];
        $suppliers = $this->suppliersDAO->consultActive();
        $status = $this->statusTransactionDAO->consultActive();
        $payments = $this->paymentMethodDAO->consultActive();
        require HEADER;
        require_once RUTA . '/Vistas/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function resume()
    {
        $scripts = [
            'ventas.js',
            'funciones.js'
        ];
        $styles = [
            'compras.css'
        ];
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $venta = $this->formatResumePurchase($id);
            if (is_array($venta) && !empty($venta)) {
                $status = $this->statusTransactionDAO->consultActive();
                $colores = [
                    'Pagada'   => 'text-success',
                    'Pendiente'  => 'text-warning',
                    'Anulada'   => 'text-danger'
                ];
                $claseEstado = $colores[$venta['estado']] ?? 'text-secondary';
                require HEADER;
                require_once RUTA . '/Vistas/' . $this->clase . '/Resumen.php';
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
        try {
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                throw new Exception('Método no permitido');
            }
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            $products = $data['products'] ?? [];
            if (empty($data)) {
                throw new Exception('Datos inválidos');
            }
            $sale = new Sales();
            $sale->setSupplier_id($data['supplierId']);
            $sale->setUser_id($_SESSION['ctid']);
            $sale->setInvoice_number(trim($data['invoice_number']) !== '' ? trim($data['invoice_number']) : null);
            $sale->setSale_date($data['sale_date']);
            $sale->setSubtotal($data['subtotal'] !== '' ? $data['subtotal'] : null);
            $sale->setTax($data['taxes'] !== '' ? $data['taxes'] : null);
            $sale->setTotal($data['total'] !== '' ? $data['total'] : null);
            $sale->setPayment_method_id($data['payment_method']);
            $sale->setStatus_id($data['status']);
            $sale->setNotes(trim($data['notes']) !== '' ? trim($data['notes']) : null);
            $idSale = $this->salesDAO->create($sale);
            if ($idSale == null) {
                throw new Exception('Ocurrio un error al guardar');
            }
            foreach ($products as $product) {
                $idPackage = $product['idPackage'];
                $packcage = $this->inventoryPackagesDAO->consultId($idPackage);
                if (!empty($packcage) && is_array($packcage)) {
                    $packcage = $packcage[0];
                    $saleDetail = new SaleDetails();
                    $saleDetail->setSale_id($idSale);
                    $saleDetail->setCategory_id($product['categoryId']);
                    $saleDetail->setProduct_id($packcage->getProduct_id());
                    $saleDetail->setSubproduct_id($packcage->getSubproduct_id());
                    $saleDetail->setBatch_id($packcage->getBatch_id());
                    $saleDetail->setQuantity($product['quantity']);
                    $saleDetail->setUnit_cost($product['price']);
                    $saleDetail->setTotal_cost($product['total']);
                    $saleDetail->setPresentation_id($packcage->getPresentation_id());
                    $saleDetail->setPackage_count($product['inputPackage']);
                    $saleDetail->setPackage_weight_lb($packcage->getPackage_weight_lb());
                    $saleDetail->setUnits_per_package($packcage->getUnits_per_package());
                    $saleDetail->setNotes(null);
                    $idSaleDetail = $this->saleDetailsDAO->create($saleDetail);
                    if ($idSaleDetail != null) {
                        $this->stockProcessController->decreaseProcess(array(array(
                            "id" => $idPackage,
                            "package_count" => $product['inputPackage'],
                            "notes" => null,
                            "weights" => []
                        )), 'salida', null, 'sale', $idSale);
                    }
                }
            }
            echo json_encode(['success' => true, 'message' => 'Venta registrada']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function report()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $data = $this->salesDAO->report($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    private function formatResumePurchase($id)
    {
        $response = null;
        $data = $this->salesDAO->consultDetailId($id);
        if (is_array($data) && !empty($data)) {
            $resultado = [];
            foreach ($data as $item) {
                $key = $item['codigo'];
                if (!isset($resultado[$key])) {
                    $resultado[$key] = [
                        'id' => $item['codigo'],
                        'factura' => $item['factura'],
                        'fecha_venta' => $item['fecha_venta'],
                        'subtotal' => $item['subtotal'],
                        'impuestos' => $item['impuestos'],
                        'total_venta' => $item['total_venta'],
                        'metodo_pago' => $item['metodo_pago'],
                        'estadoId' => $item['estadoId'],
                        'estado' => $item['estado'],
                        'usuario' => $item['usuario'],
                        'proveedor' => $item['proveedor'],
                        'notas' => $item['notas'],
                        'productos' => []
                    ];
                }
                $resultado[$key]['productos'][] = [
                    'categoria' => $item['categoria'],
                    'producto' => $item['producto'],
                    'subproducto' => $item['subproducto'],
                    'cantidad' => $item['cantidad'],
                    'presentacion' => $item['presentacion'],
                    'cantidad_paquete' => $item['cantidad_paquete'],
                    'costo_unitario' => $item['costo_unitario'],
                    'costo_total' => $item['costo_total'],
                    'descripcion' => $item['descripcion']
                ];
            }
            $response = array_values($resultado);
        }
        return $response[0];
    }
    public function update()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (!empty($data)) {
                $idSale = $data['id'];
                $status = $data['status'];
                $response = $this->salesDAO->updateStatus($status, $idSale);
                if ($response > 0) {
                    echo json_encode(['success' => true, 'message' => "Estado actualizado"]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Ocurrio un error"]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
}
$controller = new SalesController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
