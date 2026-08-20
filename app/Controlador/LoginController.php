<?php
require_once dirname(__DIR__). '/config/config.php';
require_once RUTA . '/Modelos/Conexion.php';
require_once RUTA . '/Modelos/DTO/Users.php';
require_once RUTA . '/Modelos/DAO/UsersDAO.php';
require_once RUTA . '/Controlador/RedirController.php';
class LoginController
{
    private $usersDAO = null;
    public function __construct()
    {
        $this->usersDAO = new UsersDAO();
    }
    public function login()
    {
        if (isset($_SESSION['ctuser'], $_SESSION['ctname'], $_SESSION['ctrol'])) {
            redirect('Dashboard');
        } else {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            require_once RUTA . '/Vistas/Login.php';
        }
    }
    public function access()
    {
        $user = trim($_POST['usuario'] ?? '');
        $pass = trim($_POST['clave'] ?? '');
        $csrf = $_POST['csrf_token'] ?? '';
        if ($csrf !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Token CSRF inválido";
            redirect('Login');
        }
        if (empty($user) || empty($pass)) {
            $_SESSION['error'] = "Campos Vacíos";
            redirect('Login');
        }
        $logear = $this->usersDAO->login($user);
        if (!$logear || count($logear) == 0) {
            $_SESSION['error'] = "Usuario no existe";
            redirect('Login');
        }
        $cuenta = $logear[0];
        if (!password_verify($pass, $cuenta->getPassword())) {
            $_SESSION['error'] = "Credenciales no válidas";
            redirect('Login');
        }
        session_regenerate_id(true);
        $_SESSION['ctid'] = $cuenta->getId();
        $_SESSION['ctuser'] = $cuenta->getEmail();
        $_SESSION['ctname'] = $cuenta->getName();
        $_SESSION['ctrol'] = $cuenta->getRol();
        //redirect($_SESSION['ctrol'] == 3 ? 'Otro' : 'Dashboard');
        redirect('Dashboard');
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_regenerate_id(true);
        session_destroy();
        redirect('Login');
    }
}
$controller = new LoginController();
if (isset($_GET) && isset($_GET['a'])) {
    if ($_GET['a'] == 'login') {
        $controller->login();
    } elseif ($_GET['a'] == 'access') {
        $controller->access();
    } elseif ($_GET['a'] == 'logout') {
        $controller->logout();
    } else {
        redirect('Login');
    }
} else {
    redirect('Login');
}
