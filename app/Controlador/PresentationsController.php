<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Presentations.php';
require_once RUTA . '/Modelos/DAO/PresentationsDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class PresentationsController
{
    private $presentationsDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->presentationsDAO = new PresentationsDAO();
        $this->clase = 'Presentaciones';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.js',
            'tabla.js',
            'manage.js?menu=presentaciones'
        ];
        $data = $this->presentationsDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'funciones.js',
            'manage.js?menu=presentaciones'
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->presentationsDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'funciones.js',
                    'manage.js?menu=presentaciones'
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
        if (isset($_POST['name'], $_POST['description'], $_POST['reference_weight_lb']) && $_POST['name'] != null) {
            if ($this->presentationsDAO->repeated(array(trim($_POST['name'])))) {
                $add = new Presentations();
                $add->setName(trim($_POST['name']));
                $add->setReference_weight_lb(trim($_POST['reference_weight_lb'] ?? '') !== '' ? trim($_POST['reference_weight_lb']) : null);
                $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
                $this->presentationsDAO->create($add);
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
            isset($_POST['id'], $_POST['name'], $_POST['status'], $_POST['reference_weight_lb'])
            && $_POST['id'] != null
            && $_POST['name'] != null
            && $_POST['status'] != null
        ) {
            $add = new Presentations();
            $add->setId($_POST['id']);
            $add->setName($_POST['name']);
            $add->setReference_weight_lb(trim($_POST['reference_weight_lb'] ?? '') !== '' ? trim($_POST['reference_weight_lb']) : null);
            $add->setDescription(trim($_POST['description'] ?? '') !== '' ? trim($_POST['description']) : null);
            $add->setStatus($_POST['status']);
            $this->presentationsDAO->update($add);
            $_SESSION['ok'] = "Registro modificado";
        }
        redirect($this->clase);
    }
}
$controller = new PresentationsController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
