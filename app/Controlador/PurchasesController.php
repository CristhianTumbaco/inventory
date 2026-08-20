<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Purchases.php';
require_once RUTA . '/Modelos/DAO/PurchasesDAO.php';
require_once RUTA . '/Modelos/DTO/PurchaseDetails.php';
require_once RUTA . '/Modelos/DAO/PurchaseDetailsDAO.php';
require_once RUTA . '/Modelos/DTO/Suppliers.php';
require_once RUTA . '/Modelos/DAO/SuppliersDAO.php';
require_once RUTA . '/Modelos/DTO/Batches.php';
require_once RUTA . '/Modelos/DAO/BatchesDAO.php';
require_once RUTA . '/Modelos/DTO/StatusTransaction.php';
require_once RUTA . '/Modelos/DAO/StatusTransactionDAO.php';
require_once RUTA . '/Modelos/DTO/PaymentMethod.php';
require_once RUTA . '/Modelos/DAO/PaymentMethodDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class PurchasesController
{
    private $purchasesDAO = null;
    private $purchaseDetailsDAO = null;
    private $suppliersDAO = null;
    private $batchesDAO = null;
    private $statusTransactionDAO = null;
    private $paymentMethodDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->purchasesDAO = new PurchasesDAO();
        $this->purchaseDetailsDAO = new PurchaseDetailsDAO();
        $this->suppliersDAO = new SuppliersDAO();
        $this->batchesDAO = new BatchesDAO();
        $this->statusTransactionDAO = new StatusTransactionDAO();
        $this->paymentMethodDAO = new PaymentMethodDAO();
        $this->clase = 'Compras';
    }
    public function consultar()
    {
        $scripts = [
            'comprasview.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION
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
                $data = $this->purchasesDAO->reportTable($filters);
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
                $totalPurchases = $this->purchasesDAO->totalPurchases($filters);
                $statusPurchases = $this->purchasesDAO->statusPurchases($filters);
                $response = array(
                    "totalPurchases" => $totalPurchases[0],
                    "statusPurchases" => $statusPurchases
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
            'compras.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION
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
            'compras.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION
        ];
        $styles = [
            'compras.min.css?v=' . VERSION
        ];
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $compra = $this->formatResumePurchase($id);
            if (is_array($compra) && !empty($compra)) {
                $status = $this->statusTransactionDAO->consultActive();
                $colores = [
                    'Pagada'   => 'text-success',
                    'Pendiente'  => 'text-warning',
                    'Anulada'   => 'text-danger'
                ];
                $claseEstado = $colores[$compra['estado']] ?? 'text-secondary';
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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            $products = $data['products'] ?? [];
            if (!empty($data)) {
                $purchase = new Purchases();
                $purchase->setSupplier_id($data['supplierId']);
                $purchase->setUser_id($_SESSION['ctid']);
                $purchase->setInvoice_number(trim($data['invoice_number']));
                $purchase->setPurchase_date($data['purchase_date']);
                $purchase->setSubtotal($data['subtotal'] !== '' ? $data['subtotal'] : null);
                $purchase->setTax($data['taxes'] !== '' ? $data['taxes'] : null);
                $purchase->setTotal($data['total'] !== '' ? $data['total'] : null);
                $purchase->setPayment_method_id($data['payment_method']);
                $purchase->setStatus_id($data['status']);
                $purchase->setNotes(trim($data['notes']) !== '' ? trim($data['notes']) : null);
                $idPurchase = $this->purchasesDAO->create($purchase);
                if ($idPurchase != null) {
                    foreach ($products as $product) {
                        $purcharseDetail = new PurchaseDetails();
                        $purcharseDetail->setPurchase_id($idPurchase);
                        $purcharseDetail->setCategory_id($product['categoryId']);
                        $purcharseDetail->setProduct_id($product['productId']);
                        $purcharseDetail->setSubproduct_id($product['subproductId']);
                        $purcharseDetail->setQuantity($product['quantity']);
                        $purcharseDetail->setUnit_cost($product['price']);
                        $purcharseDetail->setTotal_cost($product['total']);
                        $purcharseDetail->setPresentation_id($product['presentationId']);
                        $purcharseDetail->setPackage_count($product['presentationQuantity']);
                        $purcharseDetail->setPackage_weight_lb($product['presentationReference'] !== '' ? $product['presentationReference'] : null);
                        $purcharseDetail->setUnits_per_package($product['unidadesVal'] !== '' ? $product['unidadesVal'] : null);
                        $purcharseDetail->setExpiration_date($product['expiration_date'] !== '' ? $product['expiration_date'] : null);
                        $purcharseDetail->setNotes(trim($product['notes']) !== '' ? $product['notes'] : null);
                        $idPurchaseDetail = $this->purchaseDetailsDAO->create($purcharseDetail);
                        if ($idPurchaseDetail != null) {
                            $batch = new Batches();
                            $batch->setBatch_code("CMP-" . date('Y') . "-" . str_pad($idPurchaseDetail, 5, "0", STR_PAD_LEFT));
                            $batch->setBatch_type('purchase');
                            $batch->setPurchase_id($idPurchase);
                            $batch->setPurchase_detail_id($idPurchaseDetail);
                            $batch->setSupplier_id($data['supplierId']);
                            $batch->setCategory_id($product['categoryId']);
                            $batch->setProduct_id($product['productId']);
                            $batch->setSubproduct_id($product['subproductId']);
                            $batch->setProduction_date($data['purchase_date']);
                            $batch->setExpiration_date($product['expiration_date'] !== '' ? $product['expiration_date'] : null);
                            $this->batchesDAO->create($batch);
                        }
                    }
                    echo json_encode(['success' => true, 'message' => 'Compra registrada']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Ocurrio un error al guardar']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function report()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $data = $this->purchasesDAO->report($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    private function formatResumePurchase($id)
    {
        $response = null;
        $data = $this->purchasesDAO->consultDetailId($id);
        if (is_array($data) && !empty($data)) {
            $resultado = [];
            foreach ($data as $item) {
                $key = $item['codigo'];
                if (!isset($resultado[$key])) {
                    $resultado[$key] = [
                        'id' => $item['codigo'],
                        'factura' => $item['factura'],
                        'fecha_compra' => $item['fecha_compra'],
                        'subtotal' => $item['subtotal'],
                        'impuestos' => $item['impuestos'],
                        'total_compra' => $item['total_compra'],
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
                $idPurchase = $data['id'];
                $status = $data['status'];
                $response = $this->purchasesDAO->updateStatus($status, $idPurchase);
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
$controller = new PurchasesController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
