<?php

class PurchasesDAO
{
    private $conexion = null;
    private $tabla = "purchases";
    private $clase = "Purchases";
    function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }
    public function consult()
    {
        $array = array();
        $sql = "SELECT * FROM $this->tabla ORDER BY purchase_date DESC";
        $resulset = $this->conexion->ejecutarConsultaClase($sql, $array, $this->clase);
        return $resulset;
    }
    public function create(Purchases $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `supplier_id`, `user_id`, `invoice_number`, `purchase_date`, `subtotal`, `tax`, `total`, `payment_method_id`, `status_id`, `notes`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getSupplier_id(),
            $data->getUser_id(),
            $data->getInvoice_number(),
            $data->getPurchase_date(),
            $data->getSubtotal(),
            $data->getTax(),
            $data->getTotal(),
            $data->getPayment_method_id(),
            $data->getStatus_id(),
            $data->getNotes()
        );
        $result = $this->conexion->ejecutarultimoId($sql, $array);
        return $result;
    }
    public function update(Purchases $data)
    {
        $sql = "UPDATE `$this->tabla` SET `invoice_number`=?, `purchase_date`=?, `subtotal`=?, `tax`=?, `total`=?, `payment_method_id`=?, `status_id`=?, `notes`=? WHERE `id`=?";
        $array = array(
            $data->getInvoice_number(),
            $data->getPurchase_date(),
            $data->getSubtotal(),
            $data->getTax(),
            $data->getTotal(),
            $data->getPayment_method_id(),
            $data->getStatus_id(),
            $data->getId()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function updateStatus($status, $id)
    {
        $sql = "UPDATE `$this->tabla` SET  `status_id`=? WHERE `id`=?";
        $result = $this->conexion->ejecutarActualizacion($sql, array($status, $id));
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
        $sql = "SELECT * FROM $this->tabla WHERE supplier_id=? AND invoice_number=?";
        $result = $this->conexion->ejecutarConsultaClase($sql, $data, $this->clase);
        if (sizeof($result) > 0) {
            return false;
        } else {
            return true;
        }
    }
    public function reception($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT
        p.id codigo,
        p.invoice_number factura,
        s.name proveedor,
        p.purchase_date fecha_compra,
        COUNT(pd.id) cantidad_items,
        SUM(pd.quantity) peso_total,
        DATEDIFF(CURDATE(), p.purchase_date) dias_atraso
        FROM purchases p
        INNER JOIN purchase_details pd ON p.id = pd.purchase_id AND pd.batch_id IS NULL
        INNER JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN status_transaction stp ON p.status_id = stp.id
        WHERE p.status_id NOT IN (3)";
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY
        p.id,
        p.invoice_number,
        s.name,
        p.purchase_date
        ORDER BY p.purchase_date DESC";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function consultPending($id)
    {
        $sql = "SELECT p.id id, 
        p.invoice_number invoice_number, 
        p.purchase_date purchase_date, 
        p.notes notes,
        ur.`name` user,
        s.`name` supplier,
        pd.id detail_id,
        c.`name` category,
        pr.`name` product,
        spr.`name` subproduct,
        pd.quantity quantity,
        pd.unit_cost unit_cost,
        pd.total_cost total_cost,
        pt.`name` presentation,
        pd.package_count package_count,
        pd.package_weight_lb package_weight_lb,
        bt.batch_code batch_code,
        bt.production_date production_date,
        bt.expiration_date expiration_date,
        bt.id idbatch
        FROM purchases p
        INNER JOIN purchase_details pd ON p.id=pd.purchase_id
        INNER JOIN suppliers s ON p.supplier_id=s.id
        INNER JOIN categories c ON pd.category_id=c.id
        INNER JOIN products pr ON pd.product_id=pr.id
        INNER JOIN subproducts spr ON pd.subproduct_id=spr.id
        INNER JOIN batches bt ON p.id=bt.purchase_id AND pd.id=bt.purchase_detail_id
        LEFT JOIN presentations pt ON pd.presentation_id=pt.id
        LEFT JOIN users ur ON p.user_id=ur.id
        WHERE p.id=?
        AND batch_id IS NULL";
        $result = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $result;
    }
    public function consultDetailId($id)
    {
        $sql = "SELECT p.id codigo, 
        p.invoice_number factura, 
        p.purchase_date fecha_compra, 
        p.subtotal subtotal,
        p.tax impuestos,
        p.total total_compra,
        pm.`name` metodo_pago,
        stp.id estadoId,
        stp.`name` estado,
        ur.`name` usuario,
        s.`name` proveedor,
        c.`name` categoria,
        pr.`name` producto,
        spr.`name` subproducto,
        pd.quantity cantidad,
        pt.`name` presentacion,
        pd.package_count cantidad_paquete,
        pd.unit_cost costo_unitario,
        pd.total_cost costo_total,
        pd.notes descripcion,
        p.notes notas
        FROM purchases p
        INNER JOIN purchase_details pd ON p.id=pd.purchase_id
        INNER JOIN suppliers s ON p.supplier_id=s.id
        INNER JOIN categories c ON pd.category_id=c.id
        INNER JOIN products pr ON pd.product_id=pr.id
        INNER JOIN subproducts spr ON pd.subproduct_id=spr.id
        LEFT JOIN presentations pt ON pd.presentation_id=pt.id
        LEFT JOIN users ur ON p.user_id=ur.id
        LEFT JOIN status_transaction stp ON p.status_id=stp.id
        LEFT JOIN payment_method pm ON p.payment_method_id=pm.id
        WHERE p.id=?
        ORDER BY p.purchase_date DESC";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, array($id));
        return $resulset;
    }
    public function report($filters)
    {
        $sql = "SELECT p.id 'Código', 
        p.invoice_number 'Número de Factura', 
        p.purchase_date 'Fecha de Compra', 
        p.subtotal 'Subtotal',
        p.tax 'Impuesto',
        p.total 'Total',
        pm.`name` 'Metodo de Pago',
        stp.`name` 'Estado de la Compra',
        ur.`name` 'Usuario',
        s.identification 'Identificación Proveedor',
        s.`name` 'Proveedor',
        c.`name` 'Categoria',
        pr.`name` 'Producto',
        spr.`name` 'Subproducto',
        pd.quantity 'Cantidad en Libras',
        pt.`name` 'Presentación',
        pd.package_count 'Cantidad de Paquetes',
        pd.unit_cost 'Costo Unitario',
        pd.total_cost 'Costo Total',
        pd.package_weight_lb 'Peso por Paquetes',
        pd.units_per_package 'Unidades por Paquetes',
        pd.notes 'Descripción del Producto',
        p.notes 'Decripción de la Compra'
        FROM purchases p
        INNER JOIN purchase_details pd ON p.id=pd.purchase_id
        INNER JOIN suppliers s ON p.supplier_id=s.id
        INNER JOIN categories c ON pd.category_id=c.id
        INNER JOIN products pr ON pd.product_id=pr.id
        INNER JOIN subproducts spr ON pd.subproduct_id=spr.id
        LEFT JOIN presentations pt ON pd.presentation_id=pt.id
        LEFT JOIN users ur ON p.user_id=ur.id
        LEFT JOIN status_transaction stp ON p.status_id=stp.id
        LEFT JOIN payment_method pm ON p.payment_method_id=pm.id";
        $filterData = $this->buildFilters($filters);
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " ORDER BY p.purchase_date DESC";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, $filterData['params']);
        return $resulset;
    }
    public function statsReception($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT
        COUNT(DISTINCT p.id) compras_pendientes,
        SUM(pd.quantity) cantidad_pendiente,
        MAX(DATEDIFF(CURDATE(), p.purchase_date)) max_dias_atraso,
        COUNT(DISTINCT p.supplier_id) proveedores
        FROM purchases p
        INNER JOIN purchase_details pd ON p.id = pd.purchase_id AND pd.batch_id IS NULL
        WHERE status_id NOT IN (3)";
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function totalPurchases($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT COUNT(*) cantidad, 
        SUM(CASE WHEN status_id = 1 THEN total ELSE 0 END) AS total
        FROM purchases p";
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        return $this->conexion->ejecutarConsultaAssoc($sql, $filterData['params']);
    }
    public function statusPurchases($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT
            s.name estado,
            COUNT(*) cantidad
        FROM purchases p
        INNER JOIN status_transaction s  ON p.status_id = s.id";
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY s.name";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function reportTable($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT
        p.id codigo,
        p.invoice_number factura,
        p.notes notas,
        p.purchase_date fecha_compra,
        s.identification identificacion,
        s.name proveedor,
        p.total total_compra,
        stp.name estado,
        COUNT(pd.id) cantidad_items
        FROM purchases p
        INNER JOIN purchase_details pd
        ON p.id = pd.purchase_id
        INNER JOIN suppliers s
        ON p.supplier_id = s.id
        LEFT JOIN status_transaction stp
        ON p.status_id = stp.id";
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY
        p.id,
        p.invoice_number,
        p.purchase_date,
        s.identification,
        s.name,
        p.total,
        stp.name
        ORDER BY p.purchase_date DESC";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    private function buildFilters($filters)
    {
        $where = [];
        $params = [];
        // Estado
        if (!empty($filters['status'])) {
            $where[] = "p.status_id = ?";
            $params[] = $filters['status'];
        }
        // Período
        switch ($filters['period'] ?? 'month') {
            case 'today':
                $where[] = "DATE(p.purchase_date) = CURDATE()";
                break;
            case 'week':
                $where[] = "YEARWEEK(p.purchase_date) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $where[] = "DATE_FORMAT(p.purchase_date,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m')";
                break;
            case 'last_month':
                $where[] = "DATE_FORMAT(p.purchase_date,'%Y-%m') =
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
                    $where[] = "DATE(p.purchase_date) BETWEEN ? AND ?";
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
}
