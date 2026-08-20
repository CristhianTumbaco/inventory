<?php

class ProductsDAO
{
    private $conexion = null;
    private $tabla = "products";
    private $clase = "Products";
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
        $sql = "SELECT id, name FROM $this->tabla WHERE status=?";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, array(1));
        return $resulset;
    }
    public function consultData()
    {
        $array = array();
        $sql = "SELECT id, name,category_id FROM $this->tabla WHERE status=1";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, $array);
        return $resulset;
    }
    public function create(Products $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `category_id`, `name`, `description`) VALUES (NULL, ?, ?, ?)";
        $array = array(
            $data->getCategory_id(),
            $data->getName(),
            $data->getDescription()
        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }
    public function update(Products $data)
    {
        $sql = "UPDATE `$this->tabla` SET `name`=?, `description`=?, `status`=? WHERE `id`=?";
        $array = array(
            $data->getName(),
            $data->getDescription(),
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
