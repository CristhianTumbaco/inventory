<?php

class DashboardDAO
{
    private $conexion = null;
    function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }
    public function statStock()
    {
        $sql = "SELECT COALESCE(SUM(a.quantity), 0) total, COALESCE(SUM(a.package_count), 0) package
        FROM inventory_stock a";
        return $this->conexion->ejecutarConsultaAssoc($sql, array());
    }
    public function statExpired()
    {
        $sql = "SELECT COUNT(*) AS total
        FROM batches
        WHERE expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        return $this->conexion->ejecutarConsultaAssoc($sql, array());
    }
    public function statTemperature()
    {
        $sql = "SELECT name, temperature, update_temp FROM locations WHERE `status`=1";
        return $this->conexion->ejecutarConsultaAssoc($sql, array());
    }
    public function dataExpired()
    {
        $sql = "SELECT
        a.id,
        a.batch_code,
        a.expiration_date,
        b.name producto,
        c.`name` subproducto,
        DATEDIFF(a.expiration_date, CURDATE()) AS dias
        FROM batches a
        INNER JOIN products b ON a.product_id=b.id
        INNER JOIN subproducts c ON a.subproduct_id=c.id
        WHERE a.expiration_date BETWEEN CURDATE()
        AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ORDER BY a.expiration_date ASC";
        return $this->conexion->ejecutarConsultaAssoc($sql, array());
    }
    public function statInOut($filters)
    {
        $filterData = $this->buildFiltersDashboard($filters, 'created_at');
        $sqlInput = "SELECT COALESCE(SUM(p.quantity), 0) total, COALESCE(SUM(p.package_count), 0) package
        FROM inventory_movements p
        WHERE p.movement_type ='entrada'
        AND p.origin_type='purchase'";
        if (!empty($filterData['where'])) {
            $sqlInput .= " AND " . implode(' AND ', $filterData['where']);
        }
        $input = $this->conexion->ejecutarConsultaAssoc(
            $sqlInput,
            $filterData['params']
        );
        $sqlOuput = "SELECT COALESCE(SUM(p.quantity), 0) total, COALESCE(SUM(p.package_count), 0) package
        FROM inventory_movements p
        WHERE p.movement_type ='salida'
        AND p.origin_type='sale'";
        if (!empty($filterData['where'])) {
            $sqlOuput .= " AND " . implode(' AND ', $filterData['where']);
        }
        $output = $this->conexion->ejecutarConsultaAssoc(
            $sqlOuput,
            $filterData['params']
        );
        return array(
            "entrada" => $input[0],
            "salida" => $output[0]
        );
    }
    public function graphInventoryDay($filters)
    {
        $sql = "SELECT
        DATE(p.created_at) AS fecha,
        SUM(CASE
        WHEN p.movement_type = 'entrada' AND p.origin_type='purchase' THEN p.quantity
        ELSE 0
        END) AS entradas,
        SUM(CASE
        WHEN p.movement_type = 'salida' AND p.origin_type='sale' THEN p.quantity
        ELSE 0
        END) AS salidas,
        SUM(CASE
        WHEN p.movement_type = 'merma' AND p.origin_type='stock' THEN p.quantity
        ELSE 0
        END) AS merma,
        SUM(CASE
        WHEN p.movement_type = 'entrada' AND p.origin_type='production' THEN p.quantity
        ELSE 0
        END) AS produccion
        FROM inventory_movements p";
        $filterData = $this->buildFiltersDashboard($filters, 'created_at');
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY DATE(p.created_at)
        ORDER BY fecha ASC";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphInventoryTotal($filters)
    {
        $sql = "SELECT
        SUM(CASE
        WHEN p.movement_type = 'entrada' AND p.origin_type='purchase' THEN p.quantity
        ELSE 0
        END) AS entradas,
        SUM(CASE
        WHEN p.movement_type = 'salida' AND p.origin_type='sale' THEN p.quantity
        ELSE 0
        END) AS salidas,
        SUM(CASE
        WHEN p.movement_type = 'merma' AND p.origin_type='stock' THEN p.quantity
        ELSE 0
        END) AS merma,
        SUM(CASE
        WHEN p.movement_type = 'entrada' AND p.origin_type='production' THEN p.quantity
        ELSE 0
        END) AS produccion
        FROM inventory_movements p";
        $filterData = $this->buildFiltersDashboard($filters, 'created_at');
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphInventoryLocations()
    {
        $sql = "SELECT a.name name, COALESCE(SUM(b.quantity), 0) total
        FROM locations a
        LEFT JOIN inventory_stock b ON a.id=b.location_id
        WHERE a.`status`=1
        GROUP BY a.name";
        return $this->conexion->ejecutarConsultaAssoc($sql, array());
    }
    public function graphInventoryProducts()
    {
        $sql = "SELECT CONCAT(b.name,' / ', c.name) name, COALESCE(SUM(a.quantity),0) total
        FROM inventory_stock a
        INNER JOIN products b ON a.product_id=b.id
        INNER JOIN subproducts c ON a.subproduct_id=c.id
        GROUP BY b.name, c.name";
        return $this->conexion->ejecutarConsultaAssoc($sql, array());
    }
    //VENTAS
    public function graphSaleDay($filters)
    {
        $sql = "SELECT 
        DATE(p.sale_date) AS date,
        COUNT(DISTINCT p.id) AS cantidad,
        COALESCE(SUM(p.total), 0) AS total,
        COALESCE(SUM(p.tax), 0) AS impuestos
        FROM sales p";
        $filterData = $this->buildFiltersDashboard($filters, 'sale_date');
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY DATE(p.sale_date)
        ORDER BY DATE(p.sale_date)";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphSaleSupplier($filters)
    {
        $sql = "SELECT sp.name name, SUM(p.total) total
        FROM sales p
        INNER JOIN suppliers sp ON p.supplier_id=sp.id
        WHERE p.status_id NOT IN(3)";
        $filterData = $this->buildFiltersDashboard($filters, 'sale_date');
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY sp.name";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphSaleProducts($filters)
    {
        $sql = "SELECT 
        CONCAT(b.name,' / ', c.name) name, 
        SUM(a.quantity) total
        FROM sale_details a
        INNER JOIN products b ON a.product_id=b.id
        INNER JOIN subproducts c ON a.subproduct_id=c.id
        INNER JOIN sales p ON a.sale_id=p.id";
        $filterData = $this->buildFiltersDashboard($filters, 'sale_date');
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY b.name, c.name";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphSaleTaxes($filters)
    {
        $sql = "SELECT 
        ROUND((COALESCE(SUM(p.total), 0) /COALESCE(SUM(p.subtotal), 0))*100,2) AS total,
        ROUND((COALESCE(SUM(p.tax), 0) /COALESCE(SUM(p.subtotal), 0))*100,2) AS taxes
        FROM sales p
        WHERE p.status_id NOT IN(3)";
        $filterData = $this->buildFiltersDashboard($filters, 'sale_date');
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function statSales($filters)
    {
        $currentFilters = $this->buildFiltersDashboard($filters, 'sale_date');
        $previousFilters = $this->buildPreviousFiltersDashboard($filters, 'sale_date');
        $sql = "SELECT 
        COUNT(DISTINCT p.id) AS cantidad,
        COALESCE(SUM(p.total), 0) AS total,
        COALESCE(SUM(pd.quantity), 0) AS weight,
        0 AS percentage
        FROM sales p
        INNER JOIN sale_details pd ON p.id = pd.sale_id
        WHERE p.status_id NOT IN(3)";
        $sqlCurrent = $sql;
        if (!empty($currentFilters['where'])) {
            $sqlCurrent .= " AND " . implode(' AND ', $currentFilters['where']);
        }
        $current = $this->conexion->ejecutarConsultaAssoc(
            $sqlCurrent,
            $currentFilters['params']
        );
        $sqlPrevious = $sql;
        if (!empty($previousFilters['where'])) {
            $sqlPrevious .= " AND " . implode(' AND ', $previousFilters['where']);
        }

        $previous = $this->conexion->ejecutarConsultaAssoc(
            $sqlPrevious,
            $previousFilters['params']
        );

        return [
            'current' => $current[0],
            'previous' => $previous[0]
        ];
    }

    //COMPRAS
    public function graphPurchaseDay($filters)
    {
        $sql = "SELECT 
        DATE(p.purchase_date) AS date,
        COUNT(DISTINCT p.id) AS cantidad,
        COALESCE(SUM(p.total), 0) AS total,
        COALESCE(SUM(p.tax), 0) AS impuestos
        FROM purchases p";
        $filterData = $this->buildFiltersDashboard($filters, 'purchase_date');
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY DATE(p.purchase_date)
        ORDER BY DATE(p.purchase_date)";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphPurchaseSupplier($filters)
    {
        $sql = "SELECT sp.name name, SUM(p.total) total
        FROM purchases p
        INNER JOIN suppliers sp ON p.supplier_id=sp.id
        WHERE p.status_id NOT IN(3)";
        $filterData = $this->buildFiltersDashboard($filters, 'purchase_date');
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY sp.name";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphPurchaseProducts($filters)
    {
        $sql = "SELECT 
        CONCAT(b.name,' / ', c.name) name, 
        SUM(a.quantity) total
        FROM purchase_details a
        INNER JOIN products b ON a.product_id=b.id
        INNER JOIN subproducts c ON a.subproduct_id=c.id
        INNER JOIN purchases p ON a.purchase_id=p.id";
        $filterData = $this->buildFiltersDashboard($filters, 'purchase_date');
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        $sql .= " GROUP BY b.name, c.name";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function graphPurchaseTaxes($filters)
    {
        $sql = "SELECT 
        ROUND((COALESCE(SUM(p.total), 0) /COALESCE(SUM(p.subtotal), 0))*100,2) AS total,
        ROUND((COALESCE(SUM(p.tax), 0) /COALESCE(SUM(p.subtotal), 0))*100,2) AS taxes
        FROM purchases p
        WHERE p.status_id NOT IN(3)";
        $filterData = $this->buildFiltersDashboard($filters, 'purchase_date');
        if (!empty($filterData['where'])) {
            $sql .= " AND " . implode(' AND ', $filterData['where']);
        }
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    public function statPurchases($filters)
    {
        $currentFilters = $this->buildFiltersDashboard($filters, 'purchase_date');
        $previousFilters = $this->buildPreviousFiltersDashboard($filters, 'purchase_date');
        $sql = "SELECT 
        COUNT(DISTINCT p.id) AS cantidad,
        COALESCE(SUM(p.total), 0) AS total,
        COALESCE(SUM(pd.quantity), 0) AS weight,
        0 AS percentage
        FROM purchases p
        INNER JOIN purchase_details pd ON p.id = pd.purchase_id
        WHERE p.status_id NOT IN(3)";
        $sqlCurrent = $sql;
        if (!empty($currentFilters['where'])) {
            $sqlCurrent .= " AND " . implode(' AND ', $currentFilters['where']);
        }
        $current = $this->conexion->ejecutarConsultaAssoc(
            $sqlCurrent,
            $currentFilters['params']
        );
        $sqlPrevious = $sql;
        if (!empty($previousFilters['where'])) {
            $sqlPrevious .= " AND " . implode(' AND ', $previousFilters['where']);
        }

        $previous = $this->conexion->ejecutarConsultaAssoc(
            $sqlPrevious,
            $previousFilters['params']
        );

        return [
            'current' => $current[0],
            'previous' => $previous[0]
        ];
    }
    private function buildFiltersDashboard($filters, $campo)
    {
        $where = [];
        $params = [];

        switch ($filters['period'] ?? 'month') {

            case 'today':
                $where[] = "DATE(p.$campo) = CURDATE()";
                break;

            case 'week':
                $where[] = "YEARWEEK(p.$campo, 1) = YEARWEEK(CURDATE(), 1)";
                break;

            case 'month':
                $where[] = "DATE_FORMAT(p.$campo, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')";
                break;

            case 'last_month':
                $where[] = "DATE_FORMAT(p.$campo, '%Y-%m') = 
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
                    $where[] = "DATE(p.$campo) BETWEEN ? AND ?";
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
    private function buildPreviousFiltersDashboard($filters, $campo)
    {
        $where = [];
        $params = [];

        switch ($filters['period'] ?? 'month') {

            case 'today':
                $where[] = "DATE(p.$campo) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;

            case 'week':
                $where[] = "YEARWEEK(p.$campo, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)";
                break;

            case 'month':
                $where[] = "DATE_FORMAT(p.$campo, '%Y-%m') = 
                        DATE_FORMAT(
                            DATE_SUB(CURDATE(), INTERVAL 1 MONTH),
                            '%Y-%m'
                        )";
                break;

            case 'last_month':
                $where[] = "DATE_FORMAT(p.$campo, '%Y-%m') = 
                        DATE_FORMAT(
                            DATE_SUB(CURDATE(), INTERVAL 2 MONTH),
                            '%Y-%m'
                        )";
                break;

            case 'custom':
                if (
                    !empty($filters['dateFrom']) &&
                    !empty($filters['dateTo'])
                ) {
                    $from = new DateTime($filters['dateFrom']);
                    $to = new DateTime($filters['dateTo']);

                    $days = $from->diff($to)->days + 1;

                    $previousTo = clone $from;
                    $previousTo->modify('-1 day');

                    $previousFrom = clone $previousTo;
                    $previousFrom->modify('-' . ($days - 1) . ' days');

                    $where[] = "DATE(p.$campo) BETWEEN ? AND ?";
                    $params[] = $previousFrom->format('Y-m-d');
                    $params[] = $previousTo->format('Y-m-d');
                }
                break;
        }

        return [
            'where' => $where,
            'params' => $params
        ];
    }
}
