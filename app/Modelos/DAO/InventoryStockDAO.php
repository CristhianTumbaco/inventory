<?php

class InventoryStockDAO
{
    private $conexion = null;
    private $tabla = "inventory_stock";
    private $clase = "InventoryStock";
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
    public function create(InventoryStock $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `product_id`, `subproduct_id`, `location_id`, `presentation_id`, `quantity`, `package_count`) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getProduct_id(),
            $data->getSubproduct_id(),
            $data->getLocation_id(),
            $data->getPresentation_id(),
            $data->getQuantity(),
            $data->getPackage_count()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function update(InventoryStock $data)
    {
        $sql = "UPDATE `$this->tabla` SET `location_id`=?, `quantity`=? WHERE `id`=?";
        $array = array(
            $data->getLocation_id(),
            $data->getQuantity(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function increaseStock(InventoryStock $data)
    {
        $sql = "UPDATE `$this->tabla` SET `quantity`= `quantity` + ?,`package_count`=`package_count` + ?  WHERE `id`=?";
        $array = array(
            $data->getQuantity(),
            $data->getPackage_count(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function syncStock(InventoryStock $data)
    {
        $sql = "UPDATE `$this->tabla` SET `quantity`=  ?,`package_count`= ?  WHERE `id`=?";
        $array = array(
            $data->getQuantity(),
            $data->getPackage_count(),
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
    public function validatedId($data)
    {
        $sql = "SELECT id id
        FROM $this->tabla
        WHERE product_id = ?
        AND subproduct_id = ?
        AND location_id = ?
        AND presentation_id = ?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, $data);
        if (sizeof($result) > 0) {
            return $result;
        } else {
            return null;
        }
    }
    public function stats($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT 
        SUM(a.quantity) AS total_quantity,
        SUM(a.package_count) AS total_packages,
        COUNT(DISTINCT a.product_id) AS total_items,
        MAX(a.updated_at) AS last_update
        FROM inventory_stock a";
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function list($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT 
        c.id AS productoId,
        c.name AS producto,
        d.id AS subproductoId,
        d.name AS subproducto,
        f.id AS ubicacionId,
        f.name AS ubicacion,
        SUM(a.quantity) AS cantidad,
        p.id AS presentacionId,
        p.name AS presentacion,
        SUM(a.package_count) AS paquetes,
        MAX(a.updated_at) AS ultima_actualizacion
        FROM inventory_stock a
        INNER JOIN products c ON a.product_id = c.id
        INNER JOIN subproducts d ON a.subproduct_id = d.id
        INNER JOIN locations f ON a.location_id = f.id
        INNER JOIN presentations p ON a.presentation_id = p.id";
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY 
        c.id, c.name,
        d.id, d.name,
        f.id, f.name,
        p.id, p.name";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    private function buildFilters($filters)
    {
        $where = [];
        $params = [];

        if (!empty($filters['locations'])) {
            $where[] = "a.location_id = ?";
            $params[] = $filters['locations'];
        }
        if (!empty($filters['products'])) {
            $where[] = "a.product_id = ?";
            $params[] = $filters['products'];
        }
        return [
            'where' => $where,
            'params' => $params
        ];
    }
    public function consultDecrease($data)
    {
        $sql = "SELECT 
        a.id id,
        c.id AS productoId,
        c.name AS producto,
        d.id AS subproductoId,
        d.name AS subproducto,
        f.id AS ubicacionId,
        f.name AS ubicacion,
        a.quantity AS cantidad,
        p.id AS presentacionId,
        p.name AS presentacion,
        a.package_count AS paquetes
        FROM inventory_stock a
        INNER JOIN products c ON a.product_id = c.id
        INNER JOIN subproducts d ON a.subproduct_id = d.id
        INNER JOIN locations f ON a.location_id = f.id
        INNER JOIN presentations p ON a.presentation_id = p.id
        WHERE a.product_id=?
        AND a.subproduct_id=?
        AND a.location_id=?
        AND a.presentation_id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, $data);
        return $result;
    }
}
