<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Suppliers.php';
require_once RUTA . '/Modelos/DAO/SuppliersDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class SupplierController
{
    private $suppliersDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->suppliersDAO = new SuppliersDAO();
        $this->clase = 'Proveedores';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION,
            'manage.min.js?menu=proveedores'
        ];
        $data = $this->suppliersDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'manage.min.js?menu=proveedores'
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->suppliersDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'manage.min.js?menu=proveedores'
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
        if (
            isset($_POST['identification'], $_POST['name'], $_POST['phone'], $_POST['email'])
            && $_POST['name'] != null
        ) {
            if ($this->suppliersDAO->repeated(array($_POST['identification'], trim($_POST['name'])))) {
                $add = new Suppliers();
                $add->setIdentification(trim($_POST['identification'] ?? '') !== '' ? trim($_POST['identification']) : null);
                $add->setName(trim($_POST['name']));
                $add->setPhone(trim($_POST['phone'] ?? '') !== '' ? trim($_POST['phone']) : null);
                $add->setEmail(trim($_POST['email'] ?? '') !== '' ? trim($_POST['email']) : null);
                $add->setAddress(trim($_POST['address'] ?? '') !== '' ? trim($_POST['address']) : null);
                $this->suppliersDAO->create($add);
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
            isset($_POST['id'], $_POST['identification'], $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['status'])
            && $_POST['id'] != null
            && $_POST['name'] != null
            && $_POST['status'] != null
        ) {
            $add = new Suppliers();
            $add->setId($_POST['id']);
            $add->setIdentification(trim($_POST['identification'] ?? '') !== '' ? trim($_POST['identification']) : null);
            $add->setName(trim($_POST['name']));
            $add->setPhone(trim($_POST['phone'] ?? '') !== '' ? trim($_POST['phone']) : null);
            $add->setEmail(trim($_POST['email'] ?? '') !== '' ? trim($_POST['email']) : null);
            $add->setAddress(trim($_POST['address'] ?? '') !== '' ? trim($_POST['address']) : null);
            $add->setStatus($_POST['status']);
            $this->suppliersDAO->update($add);
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
                $supplier = new Suppliers();
                $supplier->setIdentification((trim($data['identification']) !== '') ? trim($data['identification']) : null);
                $supplier->setName(trim($data['name']));
                $supplier->setPhone((trim($data['phone']) !== '') ? trim($data['phone']) : null);
                $supplier->setEmail((trim($data['email']) !== '') ? trim($data['email']) : null);
                $supplier->setAddress((trim($data['address']) !== '') ? trim($data['address']) : null);
                if ($this->suppliersDAO->repeated(array($supplier->getIdentification(), $supplier->getName()))) {
                    $idSupplier = $this->suppliersDAO->create($supplier);
                    if ($idSupplier != null) {
                        echo json_encode(['success' => true, 'message' => 'Proveedor creado exitosamente', 'data' => ['id' => $idSupplier, 'name' => $supplier->getName()]]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error al crear el proveedor']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'El proveedor ya existe']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
}
$controller = new SupplierController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
