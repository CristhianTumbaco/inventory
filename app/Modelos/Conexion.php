<?php
class Conexion
{ // utilizaremos el patron de disenio singleton
    public static $conexion = null;
    private $db;

    // constructor privado para que solo dentro de esta clase pueda ser utilizado
    private function __construct()
    {
        // se inicializa db con la conexion de tipo PDO a la base de datos (motor mysql)
        $usuario = 'root';
        $contrasenia = '';
        try {
            $this->db = new PDO("mysql:host=" . DBHOST . ":3306; dbname=" . DBNAME, DBUSER, DBPWD);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec("set character set utf8"); // encodificacion de caracteres para que muestre 
            // adecuadamente caracteres  especiales (tildes, dieresis, etc) en el navegador
        } catch (PDOException $e) {
            /*echo "linea del error " . $e->getLine();
            echo "</br>";
            echo "archivo " . $e->getFile();
            echo "</br>";
            die("error " . $e->getMessage());*/
            echo "<h1>No se ha podido establecer la conexión con la base de datos</h1>";
            echo "<br>";
            echo "<h2>De clic <a href='#' onClick='document.location.reload(true)'>aquí</a> para volver a intentar</h2>";
            die("");
        }
    }

    public static function getConexion()
    {
        // si no existe se crea un objeto de esta clase y lo retorna, asi aseguramos 
        // una unica instancia
        if (!isset(self::$conexion)) {
            self::$conexion = new Conexion();
        }
        return self::$conexion;
    }
    // metodo de consulta, sentencias select o stores procedures que ejecuten select
    public function ejecutarConsultaClase($sql, $array, $clase)
    {
        try { //sentencia preparada
            //$sentencia = $this->db->prepare("select *from producto where estado = ?");
            $sentencia = $this->db->prepare($sql);
            //ejecutar la sentencia
            //$sentencia->execute(array(1));
            $sentencia->execute($array);
            //retornar resultados
            //$resultset = $sentencia->fetchAll(PDO::FETCH_CLASS, 'Producto'); //GECTHCLASS crea una instancia de la clase especificada y mapea las columnas de la tabla a los atributos de la instancia
            $resultset = $sentencia->fetchAll(PDO::FETCH_CLASS, $clase);
            return $resultset;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    // metodo de consulta, sentencias select o stores procedures que ejecuten select
    public function ejecutarConsultaAssoc($sql, $array)
    {
        try {
            $sentencia = $this->db->prepare($sql);
            $sentencia->execute($array);
            $resultset = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultset;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    // metodo para modificacion de datos, sentencias INSERT, UPDATE, DELETE
    public function ejecutarActualizacion($sql, $array)
    {
        try {
            $sentencia = $this->db->prepare($sql);
            $sentencia->execute($array);
            $num_filas_afectadas = $sentencia->rowCount();
            //rowCount() obtiene el numero de filas afectadas por la sentencia ejecutada
            return $num_filas_afectadas;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ejecutarultimoId($sql, $array)
    {
        try {
            $sentencia = $this->db->prepare($sql);
            $sentencia->execute($array);
            $num_filas_afectadas = $sentencia->rowCount();
            $ultimoid = $this->db->lastInsertId();
            if ($num_filas_afectadas > 0) {
                return $ultimoid;
            } else {
                return null;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
