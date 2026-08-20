<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Products.php';
require_once RUTA . '/Modelos/DAO/ProductsDAO.php';
require_once RUTA . '/Modelos/DTO/Subproducts.php';
require_once RUTA . '/Modelos/DAO/SubproductsDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class SubproductsController
{
    private $productsDAO = null;
    private $subproductsDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->productsDAO = new ProductsDAO();
        $this->subproductsDAO = new SubproductsDAO();
        $this->clase = 'Subproductos';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.js',
            'tabla.js',
            'manage.js?menu=subproductos'
        ];
        $products = $this->productsDAO->consult();
        $data = $this->subproductsDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'manage.js?menu=subproductos'
        ];
        $products = $this->productsDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->subproductsDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'manage.js?menu=subproductos'
                ];
                $products = $this->productsDAO->consult();
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
            isset($_POST['product'], $_POST['name'], $_POST['description'])
            && $_POST['product'] != null
            && $_POST['name'] != null
        ) {
            if ($this->subproductsDAO->repeated(array($_POST['product'], trim($_POST['name'])))) {
                $add = new Subproducts();
                $add->setProduct_id($_POST['product']);
                $add->setName(trim($_POST['name']));
                $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
                $this->subproductsDAO->create($add);
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
            $add = new Subproducts();
            $add->setId($_POST['id']);
            $add->setName($_POST['name']);
            $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
            $add->setStatus($_POST['status']);
            $this->subproductsDAO->update($add);
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
                $subproduct = new Subproducts();
                $subproduct->setName(trim($data['name']));
                $subproduct->setProduct_id($data['productId']);
                if ($this->subproductsDAO->repeated(array($subproduct->getProduct_id(), $subproduct->getName()))) {
                    $idSubproduct = $this->subproductsDAO->create($subproduct);
                    if ($idSubproduct != null) {
                        echo json_encode(['success' => true, 'message' => 'Subproducto creado exitosamente', 'data' => ['id' => (int)$idSubproduct, 'name' => $subproduct->getName(), 'product_id' => (int)$subproduct->getProduct_id()]]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al crear el subproducto']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'El subproducto ya existe']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
}
$controller = new SubproductsController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
