<?php

class InventoryMovementsDAO
{
    private $conexion = null;
    private $tabla = "inventory_movements";
    private $clase = "InventoryMovements";
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
    public function create(InventoryMovements $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `product_id`, `subproduct_id`, `batch_id`, `location_id`, `presentation_id`, `user_id`, `movement_type`, `quantity`, `package_count`, `product_condition`, `origin_type`, `origin_id`, `temperature_received`, `expiration_date`, `notes`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getProduct_id(),
            $data->getSubproduct_id(),
            $data->getBatch_id(),
            $data->getLocation_id(),
            $data->getPresentation_id(),
            $data->getUser_id(),
            $data->getMovement_type(),
            $data->getQuantity(),
            $data->getPackage_count(),
            $data->getProduct_condition(),
            $data->getOrigin_type(),
            $data->getOrigin_id(),
            $data->getTemperature_received(),
            $data->getExpiration_date(),
            $data->getNotes()
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
