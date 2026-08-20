<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Locations.php';
require_once RUTA . '/Modelos/DAO/LocationsDAO.php';
require_once RUTA . '/Modelos/DTO/LocationTemperatureLogs.php';
require_once RUTA . '/Modelos/DAO/LocationTemperatureLogsDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class LocationsController
{
    private $locationsDAO = null;
    private $locationTemperatureLogsDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->locationsDAO = new LocationsDAO();
        $this->locationTemperatureLogsDAO = new LocationTemperatureLogsDAO();
        $this->clase = 'Ubicaciones';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION,
            'manage.min.js?menu=ubicaciones'
        ];
        $data = $this->locationsDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'manage.min.js?menu=ubicaciones'
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->locationsDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'manage.min.js?menu=ubicaciones'
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
        if (isset($_POST['name'], $_POST['description']) && $_POST['name'] != null) {
            if ($this->locationsDAO->repeated(array(trim($_POST['name'])))) {
                $add = new Locations();
                $add->setName(trim($_POST['name']));
                $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
                $this->locationsDAO->create($add);
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
            $add = new Locations();
            $add->setId($_POST['id']);
            $add->setName($_POST['name']);
            $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
            $add->setStatus($_POST['status']);
            $this->locationsDAO->update($add);
            $_SESSION['ok'] = "Registro modificado";
        }
        redirect($this->clase);
    }
    public function temperatura()
    {
        $scripts = [
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION,
            'temperature.min.js?v=' . VERSION,
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Temperatura/Temperatura.php';
        require FOOTER;
    }
    public function list()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $filters = json_decode($json_data, true);
            if (!empty($filters)) {
                $data = $this->locationsDAO->list($filters);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se recibieron filtros']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }
    public function create()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            if (!empty($data)) {
                $location = new LocationTemperatureLogs();
                $location->setLocation_id($data['location']);
                $location->setTemperature(trim($data['temperature']));
                $location->setScale(trim($data['scale']));
                $location->setHumidity(trim($_POST['humidity'] ?? '') !== '' ? trim($_POST['humidity']) : null);
                $this->locationTemperatureLogsDAO->create($location);
                $this->updateTemp($data);
                echo json_encode(['success' => true, 'message' => 'Temperatura registrada']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
    }
    private function updateTemp($data)
    {
        $add = new Locations();
        $add->setId($data['location']);
        $add->setTemperature($data['temperature']);
        $this->locationsDAO->updateTemp($add);
    }
}
$controller = new LocationsController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
