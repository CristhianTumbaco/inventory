<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/StatusTransaction.php';
require_once RUTA . '/Modelos/DAO/StatusTransactionDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class StatusTransactionController
{
    private $statusTransactionDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->statusTransactionDAO = new StatusTransactionDAO();
        $this->clase = 'Estados';
    }
    public function consultar()
    {
        $scripts = [
            'funciones.js',
            'tabla.js',
            'manage.js?menu=estados'
        ];
        $data = $this->statusTransactionDAO->consult(true);
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'manage.js?menu=estados'
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->statusTransactionDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'manage.js?menu=estados'
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
            if ($this->statusTransactionDAO->repeated(array(trim($_POST['name'])))) {
                $add = new StatusTransaction();
                $add->setName(trim($_POST['name']));
                $this->statusTransactionDAO->create($add);
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
            $add = new StatusTransaction();
            $add->setId($_POST['id']);
            $add->setName($_POST['name']);
            $add->setStatus($_POST['status']);
            $this->statusTransactionDAO->update($add);
            $_SESSION['ok'] = "Registro modificado";
        }
        redirect($this->clase);
    }
}
$controller = new StatusTransactionController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
