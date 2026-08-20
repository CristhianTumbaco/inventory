<?php

class SaleDetailsDAO
{
    private $conexion = null;
    private $tabla = "sale_details";
    private $clase = "SaleDetails";
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
    public function create(SaleDetails $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `sale_id`, `category_id`, `product_id`, `subproduct_id`, `batch_id`, `quantity`, `unit_cost`, `total_cost`, `presentation_id`, `package_count`, `package_weight_lb`, `units_per_package`, `notes`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getSale_id(),
            $data->getCategory_id(),
            $data->getProduct_id(),
            $data->getSubproduct_id(),
            $data->getBatch_id(),
            $data->getQuantity(),
            $data->getUnit_cost(),
            $data->getTotal_cost(),
            $data->getPresentation_id(),
            $data->getPackage_count(),
            $data->getPackage_weight_lb(),
            $data->getUnits_per_package(),
            $data->getNotes(),

        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }

    public function update(SaleDetails $data)
    {
        $sql = "UPDATE `$this->tabla` SET `batch_id`=?, `quantity`=?, `unit_cost`=?, `total_cost`=?, `notes`=?, `presentation_id`=?, `package_count`=?, `package_weight_lb`=? WHERE `id`=?";
        $array = array(
            $data->getBatch_id(),
            $data->getQuantity(),
            $data->getUnit_cost(),
            $data->getTotal_cost(),
            $data->getNotes(),
            $data->getPresentation_id(),
            $data->getPackage_count(),
            $data->getPackage_weight_lb(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function updateBatch($idbatch, $id)
    {
        $sql = "UPDATE `$this->tabla` SET `batch_id`=? WHERE `id`=?";
        $result = $this->conexion->ejecutarActualizacion($sql, array($idbatch, $id));
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
        $sql = "SELECT * FROM $this->tabla WHERE sale_id=? AND product_id=? AND subproduct_id=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, $data, $this->clase);
        if (sizeof($result) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
