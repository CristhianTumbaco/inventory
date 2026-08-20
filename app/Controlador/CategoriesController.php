<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Categories.php';
require_once RUTA . '/Modelos/DAO/CategoriesDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class CategoriesController
{
    private $categoriesDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->categoriesDAO = new CategoriesDAO();
        $this->clase = 'Categorias';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.js',
            'tabla.js',
            'manage.js?menu=categorias'
        ];
        $data = $this->categoriesDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'manage.js?menu=categorias'
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->categoriesDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'manage.js?menu=categorias'
                ];
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
        if (isset($_POST['name']) && $_POST['name'] != null) {
            if ($this->categoriesDAO->repeated(array(trim($_POST['name'])))) {
                $add = new Categories();
                $add->setName(trim($_POST['name']));
                $this->categoriesDAO->create($add);
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
            isset($_POST['id'], $_POST['name'], $_POST['status'])
            && $_POST['id'] != null
            && $_POST['name'] != null
            && $_POST['status'] != null
        ) {
            $add = new Categories();
            $add->setId($_POST['id']);
            $add->setName($_POST['name']);
            $add->setStatus($_POST['status']);
            $this->categoriesDAO->update($add);
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
                $category = new Categories();
                $category->setName(trim($data['name']));

                if ($this->categoriesDAO->repeated(array($category->getName()))) {
                    $idCategory = $this->categoriesDAO->create($category);
                    if ($category != null) {
                        echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente', 'data' => ['id' => (int)$idCategory, 'name' => $category->getName()]]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al crear la categoría']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'La categoría ya existe']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
}
$controller = new CategoriesController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
