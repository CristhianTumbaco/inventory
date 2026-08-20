<?php

class LocationTemperatureLogs
{
    private $id;
    private $location_id;
    private $temperature;
    private $humidity;
    private $scale;
    private $sensor_id;
    private $recorded_at;

    function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getLocation_id()
    {
        return $this->location_id;
    }

    public function setLocation_id($location_id)
    {
        $this->location_id = $location_id;
        return $this;
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function getHumidity()
    {
        return $this->humidity;
    }

    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;
        return $this;
    }

    public function getScale()
    {
        return $this->scale;
    }

    public function setScale($scale)
    {
        $this->scale = $scale;
        return $this;
    }

    public function getSensor_id()
    {
        return $this->sensor_id;
    }

    public function setSensor_id($sensor_id)
    {
        $this->sensor_id = $sensor_id;
        return $this;
    }

    public function getRecorded_at()
    {
        return $this->recorded_at;
    }

    public function setRecorded_at($recorded_at)
    {
        $this->recorded_at = $recorded_at;
        return $this;
    }
}
