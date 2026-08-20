<?php

class LocationsDAO
{
    private $conexion = null;
    private $tabla = "locations";
    private $clase = "Locations";
    function __construct()
    {
        $this->conexion = Conexion::getConexion();
    }
    public function consult()
    {
        $sql = "SELECT * FROM $this->tabla";
        $resulset = $this->conexion->ejecutarConsultaClase($sql, array(), $this->clase);
        return $resulset;
    }
    public function consultActive()
    {
        $sql = "SELECT id, name FROM $this->tabla WHERE status=?";
        $resulset = $this->conexion->ejecutarConsultaAssoc($sql, array(1));
        return $resulset;
    }
    public function create(Locations $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `name`, `description`) VALUES (NULL, ?, ?)";
        $array = array(
            $data->getName(),
            $data->getDescription()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function update(Locations $data)
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
    public function updateTemp(Locations $data)
    {
        $sql = "UPDATE `$this->tabla` SET `temperature`=?, `update_temp`=current_timestamp() WHERE `id`=?";
        $array = array(
            $data->getTemperature(),
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
    public function list($filters)
    {
        $filterData = $this->buildFilters($filters);
        $sql = "SELECT b.name, a.temperature, a.scale, a.humidity, a.recorded_at
        FROM location_temperature_logs a
        INNER JOIN locations b ON a.location_id=b.id";
        if (!empty($filterData['where'])) {
            $sql .= " WHERE " . implode(' AND ', $filterData['where']);
        }
        $sql .= " ORDER BY a.recorded_at DESC";
        return $this->conexion->ejecutarConsultaAssoc(
            $sql,
            $filterData['params']
        );
    }
    private function buildFilters($filters)
    {
        $where = [];
        $params = [];
        // Período
        switch ($filters['period'] ?? 'month') {
            case 'today':
                $where[] = "DATE(a.recorded_at) = CURDATE()";
                break;
            case 'week':
                $where[] = "YEARWEEK(a.recorded_at) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $where[] = "DATE_FORMAT(a.recorded_at,'%Y-%m') = DATE_FORMAT(CURDATE(),'%Y-%m')";
                break;
            case 'last_month':
                $where[] = "DATE_FORMAT(a.recorded_at,'%Y-%m') =
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
                    $where[] = "DATE(a.recorded_at) BETWEEN ? AND ?";
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
