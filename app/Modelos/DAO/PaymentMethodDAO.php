<?php

class PaymentMethodDAO
{
    private $conexion = null;
    private $tabla = "payment_method";
    private $clase = "PaymentMethod";
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
    public function consultActive()
    {
        $array = array();
        $sql = "SELECT id, name FROM $this->tabla WHERE status=1";
        $resulset = $this->conexion->ejecutarConsultaClase($sql, $array, $this->clase);
        return $resulset;
    }
    public function create(PaymentMethod $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `name`) VALUES (NULL, ?)";
        $array = array(
            $data->getName()
        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }
    public function update(PaymentMethod $data)
    {
        $sql = "UPDATE `$this->tabla` SET `name`=?, `status`=? WHERE `id`=?";
        $array = array(
            $data->getName(),
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
