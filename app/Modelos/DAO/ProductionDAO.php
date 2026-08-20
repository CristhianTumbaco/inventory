<?php

class ProductionDAO
{
    private $conexion = null;
    private $tabla = "production";
    private $clase = "Production";
    function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }
    public function consult()
    {
        $array = array();
        $sql = "SELECT * FROM $this->tabla";
        $resulset = $this->conexion->ejecutarConsultaClase($sql, $array, $this->clase);
        return $resulset;
    }
    public function create(Production $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `user_id`, `notes`) VALUES (NULL, ?, ?)";
        $array = array(
            $data->getUser_id(),
            $data->getNotes(),
        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }
    public function delete($id)
    {
        $num_filas_afectadas = $this->conexion->ejecutarActualizacion("DELETE FROM $this->tabla WHERE id=?", array($id));
        return $num_filas_afectadas;
    }
    public function consultId($id)
    {
        $sql = "SELECT * FROM $this->tabla WHERE id=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, array($id), $this->clase);
        return $result;
    }
}
