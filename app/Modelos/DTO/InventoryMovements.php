<?php

class InventoryMovements
{
    private $id;
    private $product_id;
    private $subproduct_id;
    private $batch_id;
    private $location_id;
    private $presentation_id;
    private $user_id;
    private $movement_type;
    private $quantity;
    private $package_count;
    private $product_condition;
    private $origin_type;
    private $origin_id;
    private $temperature_received;
    private $expiration_date;
    private $notes;
    private $created_at;

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

    public function getProduct_id()
    {
        return $this->product_id;
    }

    public function setProduct_id($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    public function getSubproduct_id()
    {
        return $this->subproduct_id;
    }

    public function setSubproduct_id($subproduct_id)
    {
        $this->subproduct_id = $subproduct_id;
        return $this;
    }

    public function getBatch_id()
    {
        return $this->batch_id;
    }

    public function setBatch_id($batch_id)
    {
        $this->batch_id = $batch_id;
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

    public function getPresentation_id()
    {
        return $this->presentation_id;
    }

    public function setPresentation_id($presentation_id)
    {
        $this->presentation_id = $presentation_id;
        return $this;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getMovement_type()
    {
        return $this->movement_type;
    }

    public function setMovement_type($movement_type)
    {
        $this->movement_type = $movement_type;
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPackage_count()
    {
        return $this->package_count;
    }

    public function setPackage_count($package_count)
    {
        $this->package_count = $package_count;
        return $this;
    }

    public function getProduct_condition()
    {
        return $this->product_condition;
    }

    public function setProduct_condition($product_condition)
    {
        $this->product_condition = $product_condition;
        return $this;
    }

    public function getOrigin_type()
    {
        return $this->origin_type;
    }

    public function setOrigin_type($origin_type)
    {
        $this->origin_type = $origin_type;
        return $this;
    }

    public function getOrigin_id()
    {
        return $this->origin_id;
    }

    public function setOrigin_id($origin_id)
    {
        $this->origin_id = $origin_id;
        return $this;
    }

    public function getTemperature_received()
    {
        return $this->temperature_received;
    }

    public function setTemperature_received($temperature_received)
    {
        $this->temperature_received = $temperature_received;
        return $this;
    }

    public function getExpiration_date()
    {
        return $this->expiration_date;
    }

    public function setExpiration_date($expiration_date)
    {
        $this->expiration_date = $expiration_date;
        return $this;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }
}
