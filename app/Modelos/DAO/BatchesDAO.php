<?php

class BatchesDAO
{
    private $conexion = null;
    private $tabla = "batches";
    private $clase = "Batches";
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
    public function create(Batches $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `batch_code`, `batch_type`, `parent_batch_id`, `purchase_id`, `purchase_detail_id`, `supplier_id`, `category_id`, `product_id`, `subproduct_id`,  `production_date`, `expiration_date`, `entry_temperature`, `notes`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getBatch_code(),
            $data->getBatch_type(),
            $data->getParent_batch_id(),
            $data->getPurchase_id(),
            $data->getPurchase_detail_id(),
            $data->getSupplier_id(),
            $data->getCategory_id(),
            $data->getProduct_id(),
            $data->getSubproduct_id(),
            $data->getProduction_date(),
            $data->getExpiration_date(),
            $data->getEntry_temperature(),
            $data->getNotes()
        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }
    public function update(Batches $data)
    {
        $sql = "UPDATE `$this->tabla` SET `batch_code`=?, `production_date`=?, `expiration_date`=?, `entry_temperature`=?, `notes`=? WHERE `id`=?";
        $array = array(
            $data->getBatch_code(),
            $data->getProduction_date(),
            $data->getExpiration_date(),
            $data->getEntry_temperature(),
            $data->getNotes(),
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
        $sql = "SELECT * FROM $this->tabla WHERE batch_code=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, $data, $this->clase);
        if (sizeof($result) > 0) {
            return false;
        } else {
            return true;
        }
    }
    public function consultxId($id)
    {
        $sql = "SELECT
        a.id,
        a.batch_code,
        b.name AS product,
        c.id AS subproductId,
        c.name AS subproduct,
        a.production_date,
        a.expiration_date,
        e.name AS presentation,
        SUM(d.package_count) AS package_count,
        SUM(d.quantity_lb) AS quantity_lb,
        a.notes
        FROM batches a
        INNER JOIN products b ON a.product_id = b.id
        INNER JOIN subproducts c ON a.subproduct_id = c.id
        LEFT JOIN inventory_packages d ON a.id = d.batch_id
        LEFT JOIN presentations e ON d.presentation_id = e.id
        WHERE a.id = ?
        GROUP BY
        a.id,
        a.batch_code,
        b.name,
        c.name,
        a.production_date,
        a.expiration_date,
        e.name,
        a.notes";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function consultMovements($id)
    {
        $sql = "SELECT a.id id, 
        a.movement_type movement_type, 
        a.created_at created,
        b.`name` locations, 
        c.`name` presentation,
        a.package_count,
        a.quantity,
        a.product_condition,
        a.origin_type,
        a.origin_id
        FROM inventory_movements a
        INNER JOIN locations b ON a.location_id=b.id
        INNER JOIN presentations c ON a.presentation_id=c.id
        WHERE a.batch_id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function codeBatch()
    {
        $sql = "SELECT CONCAT(
            'PROD-',
            YEAR(CURDATE()),
            '-',
            LPAD(
                COALESCE(
                    MAX(CAST(SUBSTRING_INDEX(batch_code, '-', -1) AS UNSIGNED)),
                    0
                ) + 1,
                5,
                '0'
            )
        ) AS next_batch_code
        FROM batches
        WHERE batch_code LIKE CONCAT('PROD-', YEAR(CURDATE()), '-%')";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array());
        if (sizeof($result) > 0) {
            return $result[0]['next_batch_code'];
        } else {
            return 'PROD-2026-00001';
        }
    }
    public function list($filters)
    {
        $sql = "SELECT a.id, 
        a.batch_code,
        a.batch_type, 
        b.`name` product, 
        c.`name` subproduct, 
        a.production_date, 
        a.expiration_date, 
        a.notes
        FROM batches a
        INNER JOIN products b ON a.product_id=b.id
        INNER JOIN subproducts c ON a.subproduct_id=c.id";
        $filterData = $this->buildFilters($filters);
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " ORDER BY a.production_date ASC";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, $filterData['params']);
        return $resulset;
    }
    private function buildFilters($filters)
    {
        $where = [];
        $params = [];
        // Período
        switch ($filters['period'] ?? 'month') {
            case 'today':
                $where[] = "DATE(a.production_date) = CURDATE()";
                break;
            case 'week':
                $where[] = "YEARWEEK(a.production_date) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $where[] = "DATE_FORMAT(a.production_date,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m')";
                break;
            case 'last_month':
                $where[] = "DATE_FORMAT(a.production_date,'%Y-%m') =
                        DATE_FORMAT(
                            DATE_SUB(CURDATE(), INTERVAL 1 MONTH),
                            '%Y-%m'
                        )";
                break;
            case 'custom':
                if (
                    !empty($filters['dateFrom']) &&
                    !empty($filters['dateTo'])
                ) {
                    $where[] = "DATE(a.production_date) BETWEEN ? AND ?";
                    $params[] = $filters['dateFrom'];
                    $params[] = $filters['dateTo'];
                }
                break;
        }
        return [
            'where' => $where,
            'params' => $params
        ];
    }
    public function production($id)
    {
        $sql = "SELECT DISTINCT b.batch_code ref
        FROM inventory_movements a
        INNER JOIN batches b ON a.batch_id=b.id
        WHERE a.origin_type='production' 
        AND movement_type='entrada'
        AND a.origin_id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function productionOrigen($id)
    {
        $sql = "SELECT DISTINCT b.batch_code ref
        FROM inventory_movements a
        INNER JOIN batches b ON a.batch_id=b.id
        WHERE a.origin_type='production' 
        AND movement_type='produccion'
        AND a.origin_id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function purchase($id)
    {
        $sql = "SELECT invoice_number ref FROM purchases WHERE id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function sale($id)
    {
        $sql = "SELECT invoice_number ref FROM sales WHERE id=?";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
}
