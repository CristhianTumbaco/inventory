<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Users.php';
require_once RUTA . '/Modelos/DAO/UsersDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
require_once RUTA . '/Controlador/RuteoController.php';

class UsersController
{
    private $usersDAO = null;
    private $clase = null;

    public function __construct()
    {
        $this->usersDAO = new UsersDAO();
        $this->clase = 'Usuarios';
    }
    public function consultar()
    {
        $scripts = [
            'users.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION,
            'tabla.min.js?v=' . VERSION
        ];
        $data = $this->usersDAO->consult();
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Vista.php';
        require FOOTER;
    }
    public function nuevo()
    {
        $scripts = [
            'users.min.js?v=' . VERSION,
            'funciones.min.js?v=' . VERSION
        ];
        require HEADER;
        require_once RUTA . '/Vistas/Mantenimiento/' . $this->clase . '/Nuevo.php';
        require FOOTER;
    }
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if ($id != null) {
            $data = $this->usersDAO->consultId($id);
            if (!empty($data) && is_array($data)) {
                $data = $data[0];
                $scripts = [
                    'users.min.js?v=' . VERSION
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
            isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['rol'])
            && $_POST['name'] != null
            && $_POST['email'] != null
            && $_POST['password'] != null
            && $_POST['rol'] != null
        ) {
            if ($this->usersDAO->repeated(array(trim($_POST['email'])))) {
                $add = new Users();
                $add->setName(trim($_POST['name']));
                $add->setEmail(trim($_POST['email']));
                $add->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
                $add->setRol(trim($_POST['rol']));
                $this->usersDAO->create($add);
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
            isset($_POST['id'], $_POST['name'], $_POST['email'], $_POST['rol'], $_POST['status'])
            && $_POST['id'] != null
            && $_POST['name'] != null
            && $_POST['email'] != null
            && $_POST['rol'] != null
            && $_POST['status'] != null
        ) {
            $add = new Users();
            $add->setId($_POST['id']);
            $add->setName(trim($_POST['name']));
            $add->setEmail(trim($_POST['email']));
            if (isset($_POST['password']) && $_POST['password'] != null) {
                $add->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
            } else {
                $add->setPassword(null);
            }
            $add->setRol(trim($_POST['rol']));
            $add->setStatus($_POST['status']);
            $this->usersDAO->update($add);
            $_SESSION['ok'] = "Registro modificado";
        }
        redirect($this->clase);
    }
}
$controller = new UsersController();
$accion = $_GET['a'] ?? null;
$id = $_GET['id'] ?? null;
if ($accion != null) {
    controladorrutas($controller, $accion, [$id]);
}
