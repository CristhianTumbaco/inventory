<?php

class PresentationsDAO
{
    private $conexion = null;
    private $tabla = "presentations";
    private $clase = "Presentations";
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
    public function consultData()
    {
        $array = array();
        $sql = "SELECT id, name, reference_weight_lb FROM $this->tabla WHERE status=1";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, $array);
        return $resulset;
    }
    public function create(Presentations $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `name`, `description`, `reference_weight_lb`) VALUES (NULL, ?, ?, ?)";
        $array = array(
            $data->getName(),
            $data->getDescription(),
            $data->getReference_weight_lb()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function update(Presentations $data)
    {
        $sql = "UPDATE `$this->tabla` SET `name`=?, `description`=?, `reference_weight_lb`=?, `status`=? WHERE `id`=?";
        $array = array(
            $data->getName(),
            $data->getDescription(),
            $data->getReference_weight_lb(),
            $data->getStatus(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
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
    public function repeated($data)
    {
        $sql = "SELECT * FROM $this->tabla WHERE name=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, $data, $this->clase);
        if (sizeof($result) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
