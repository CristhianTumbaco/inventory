<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Categories.php';
require_once RUTA . '/Modelos/DAO/CategoriesDAO.php';
require_once RUTA . '/Modelos/DTO/Products.php';
require_once RUTA . '/Modelos/DAO/ProductsDAO.php';
require_once RUTA . '/Modelos/DTO/Subproducts.php';
require_once RUTA . '/Modelos/DAO/SubproductsDAO.php';
require_once RUTA . '/Modelos/DTO/Presentations.php';
require_once RUTA . '/Modelos/DAO/PresentationsDAO.php';
require_once RUTA . '/Modelos/DTO/Locations.php';
require_once RUTA . '/Modelos/DAO/LocationsDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class CatalogsController
{
    private $categoriesDAO = null;
    private $productsDAO = null;
    private $subproductsDAO = null;
    private $presentationsDAO = null;
    private $locationsDAO = null;

    public function __construct()
    {
        $this->categoriesDAO = new CategoriesDAO();
        $this->productsDAO = new ProductsDAO();
        $this->subproductsDAO = new SubproductsDAO();
        $this->presentationsDAO = new PresentationsDAO();
        $this->locationsDAO = new LocationsDAO();
    }
    public function purchase()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $categories = $this->categoriesDAO->consultData();
            if (sizeof($categories) > 0) {
                $products = $this->productsDAO->consultData();
                $subproducts = $this->subproductsDAO->consultData();
                $presentations = $this->presentationsDAO->consultData();
                $subproductsByProduct = [];
                foreach ($subproducts as $subproduct) {
                    $subproductsByProduct[$subproduct['product_id']][] = $subproduct;
                }
                $productsByCategory = [];
                foreach ($products as $product) {
                    $product['subproducts'] = $subproductsByProduct[$product['id']] ?? [];
                    $productsByCategory[$product['category_id']][] = $product;
                }
                $result = [];
                foreach ($categories as $category) {
                    $category['products'] = $productsByCategory[$category['id']] ?? [];
                    $result[] = $category;
                }
                echo json_encode(['success' => true, 'data' => array("categories" => $result, "presentations" => $presentations)]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No hay categorias configuradas']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function production()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $products = $this->productsDAO->consultData();
            $subproducts = $this->subproductsDAO->consultData();
            $subproductsByProduct = [];
            foreach ($subproducts as $subproduct) {
                $subproductsByProduct[$subproduct['product_id']][] = $subproduct;
            }
            $result = [];
            foreach ($products as $product) {
                $product['subproducts'] = $subproductsByProduct[$product['id']] ?? [];
                $result[] = $product;
            }
            echo json_encode(['success' => true, 'data' => array("categories" => $result)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function preproduction()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $categories = $this->categoriesDAO->consultData();
            if (sizeof($categories) > 0) {
                $products = $this->productsDAO->consultData();
                $subproducts = $this->subproductsDAO->consultData();
                $presentations = $this->presentationsDAO->consultData();
                $locations = $this->locationsDAO->consultActive();
                $subproductsByProduct = [];
                foreach ($subproducts as $subproduct) {
                    $subproductsByProduct[$subproduct['product_id']][] = $subproduct;
                }
                $productsByCategory = [];
                foreach ($products as $product) {
                    $product['subproducts'] = $subproductsByProduct[$product['id']] ?? [];
                    $productsByCategory[$product['category_id']][] = $product;
                }
                $result = [];
                foreach ($categories as $category) {
                    $category['products'] = $productsByCategory[$category['id']] ?? [];
                    $result[] = $category;
                }
                echo json_encode(['success' => true, 'data' => array("categories" => $result, "presentations" => $presentations, "locations" => $locations)]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No hay categorias configuradas']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function temperature(){
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $locations = $this->locationsDAO->consultActive();
            echo json_encode(['success' => true, 'data' => array("locations" => $locations)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
}
$controller = new CatalogsController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
