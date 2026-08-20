<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Categories.php';
require_once RUTA . '/Modelos/DAO/CategoriesDAO.php';
require_once RUTA . '/Modelos/DTO/Products.php';
require_once RUTA . '/Modelos/DAO/ProductsDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class ProductsController
{
    private $categoriesDAO = null;
    private $productsDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->categoriesDAO = new CategoriesDAO();
        $this->productsDAO = new ProductsDAO();
        $this->clase = 'Productos';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION,
            'manage.min.js?menu=productos'
        ];
        $category = $this->categoriesDAO->consult();
        $data = $this->productsDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'manage.min.js?menu=productos'
        ];
        $category = $this->categoriesDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->productsDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'manage.min.js?menu=productos'
                ];
                $category = $this->categoriesDAO->consult();
                require HEADER;
                require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Editar.php';
                require FOOTER;
            } else {
                redirect($this->clase);
            }
        } else {
            redirect($this->clase);
        }
    }
    public function save()
    {
        if (
            isset($_POST['category'], $_POST['name'], $_POST['description'])
            && $_POST['category'] != null
            && $_POST['name'] != null
        ) {
            if ($this->productsDAO->repeated(array(trim($_POST['name'])))) {
                $add = new Products();
                $add->setCategory_id($_POST['category']);
                $add->setName(trim($_POST['name']));
                $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
                $this->productsDAO->create($add);
                $_SESSION['ok'] = "Registrado correctamente";
            } else {
                $_SESSION['error'] = "Registro Duplicado";
            }
        }
        redirect($this->clase);
    }
    public function update()
    {
        if (
            isset($_POST['id'], $_POST['name'], $_POST['description'], $_POST['status'])
            && $_POST['id'] != null
            && $_POST['name'] != null
            && $_POST['status'] != null
        ) {
            $add = new Products();
            $add->setId($_POST['id']);
            $add->setName($_POST['name']);
            $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
            $add->setStatus($_POST['status']);
            $this->productsDAO->update($add);
            $_SESSION['ok'] = "Registro modificado";
        }
        redirect($this->clase);
    }
    public function create()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (!empty($data)) {
                $product = new Products();
                $product->setName(trim($data['name']));
                $product->setCategory_id($data['categoryId']);
                if ($this->productsDAO->repeated(array($product->getName()))) {
                    $idProduct = $this->productsDAO->create($product);
                    if ($product != null) {
                        echo json_encode(['success' => true, 'message' => 'Producto creado exitosamente', 'data' => ['id' => (int)$idProduct, 'name' => $product->getName(), 'category_id' => (int)$product->getCategory_id()]]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al crear el producto']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'El producto ya existe']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
}
$controller = new ProductsController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
