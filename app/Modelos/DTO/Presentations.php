<?php

class Presentations
{
    private $id;
    private $name;
    private $description;
    private $reference_weight_lb;
    private $status;

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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getReference_weight_lb()
    {
        return $this->reference_weight_lb;
    }

    public function setReference_weight_lb($reference_weight_lb)
    {
        $this->reference_weight_lb = $reference_weight_lb;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
