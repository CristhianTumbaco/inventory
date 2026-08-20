<?php

class LocationTemperatureLogsDAO
{
    private $conexion = null;
    private $tabla = "location_temperature_logs";
    private $clase = "LocationTemperatureLogs";
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
    public function create(LocationTemperatureLogs $data)
    {
        $sql = "INSERT INTO `$this->tabla` (`id`, `location_id`, `temperature`, `humidity`, `scale`, `sensor_id`) VALUES (NULL, ?, ?, ?, ?, ?)";
        $array = array(
            $data->getLocation_id(),
            $data->getTemperature(),
            $data->getHumidity(),
            $data->getScale(),
            $data->getSensor_id()
        );
        $result = $this->conexion->ejecutarActualizacion($sql, $array);
        return $result;
    }
    public function update(LocationTemperatureLogs $data)
    {
        $sql = "UPDATE `$this->tabla` SET `temperature`=?, `humidity`=?, `scale`=?, `sensor_id`=? WHERE `id`=?";
        $array = array(
            $data->getTemperature(),
            $data->getHumidity(),
            $data->getScale(),
            $data->getSensor_id(),
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
}
