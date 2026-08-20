<?php

class InventoryPackagesDAO
{
    private $conexion = null;
    private $tabla = "inventory_packages";
    private $clase = "InventoryPackages";
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
    public function create(InventoryPackages $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `batch_id`, `product_id`, `subproduct_id`, `location_id`, `presentation_id`, `package_weight_lb`, `package_count`, `quantity_lb`, `units_per_package`, `status`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getBatch_id(),
            $data->getProduct_id(),
            $data->getSubproduct_id(),
            $data->getLocation_id(),
            $data->getPresentation_id(),
            $data->getPackage_weight_lb(),
            $data->getPackage_count(),
            $data->getQuantity_lb(),
            $data->getUnits_per_package(),
            $data->getStatus()
        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }
    public function update(InventoryPackages $data)
    {
        $sql = "UPDATE `$this->tabla` SET `status`=? WHERE `id`=?";
        $array = array(
            $data->getStatus(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function increaseStock(InventoryPackages $data)
    {
        $sql = "UPDATE `$this->tabla` SET `package_count`= `package_count` + ?,`quantity_lb`=`quantity_lb` + ?  WHERE `id`=?";
        $array = array(
            $data->getPackage_count(),
            $data->getQuantity_lb(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function decreaseStock(InventoryPackages $data)
    {
        $sql = "UPDATE `$this->tabla` SET `package_count`= ?,`quantity_lb`= ?  WHERE `id`=?";
        $array = array(
            $data->getPackage_count(),
            $data->getQuantity_lb(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function updateLocation($locationId, $id)
    {
        $sql = "UPDATE `$this->tabla` SET `location_id`=  ?  WHERE `id`=?";
        $result = $this->conexion->ejecutarActualizacion($sql, array($locationId, $id));
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
    public function validatedId($data)
    {
        $sql = "SELECT id id
        FROM $this->tabla
        WHERE batch_id = ?
        AND product_id = ?
        AND subproduct_id = ?
        AND location_id = ?
        AND presentation_id = ?
        AND package_weight_lb = ?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, $data);
        if (sizeof($result) > 0) {
            return $result;
        } else {
            return null;
        }
    }
    public function validatedIdUnit($data)
    {
        $sql = "SELECT id
            FROM $this->tabla
            WHERE batch_id = ?
            AND product_id = ?
            AND subproduct_id = ?
            AND location_id = ?
            AND presentation_id = ?
            AND package_weight_lb = ?";
        $params = array_slice($data, 0, 6);
        if ($data[6] === null) {
            $sql .= " AND units_per_package IS NULL";
        } else {
            $sql .= " AND units_per_package = ?";
            $params[] = $data[6];
        }
        $result = $this->conexion->ejecutarConsultaAssoc($sql, $params);
        return sizeof($result) > 0 ? $result : null;
    }
    public function manage($id)
    {
        $sql = "SELECT a.id id,
        c.id productoId,
        c.`name` producto,
        f.id subproductoId,
        f.`name` subproducto,
        d.id ubicacionId,
        d.`name` ubicacion,
        e.`name` presentacion,
        a.package_weight_lb,
        a.package_count,
        a.quantity_lb,
        a.units_per_package,
        b.id batchId,
        b.batch_code,
        date_format(b.production_date,'%d/%m/%Y') production_date,
        date_format(b.expiration_date,'%d/%m/%Y') expiration_date
        FROM inventory_packages a
        INNER JOIN batches b ON a.batch_id=b.id
        INNER JOIN products c ON a.product_id=c.id
        INNER JOIN subproducts f ON a.subproduct_id=f.id
        INNER JOIN locations d ON a.location_id=d.id
        INNER JOIN presentations e ON a.presentation_id=e.id
        WHERE a.subproduct_id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function syncStock($data)
    {
        $sql = "SELECT
        SUM(quantity_lb) quantity,
        SUM(package_count) package_count
        FROM inventory_packages
        WHERE product_id = ?
        AND subproduct_id = ?
        AND location_id = ?
        AND presentation_id = ?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, $data);
        return $result;
    }
}
