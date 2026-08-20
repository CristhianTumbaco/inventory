<?php

class InventoryPackages
{
    private $id;
    private $batch_id;
    private $product_id;
    private $subproduct_id;
    private $location_id;
    private $presentation_id;
    private $package_weight_lb;
    private $package_count;
    private $quantity_lb;
    private $units_per_package;
    private $status;
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

    public function getBatch_id()
    {
        return $this->batch_id;
    }

    public function setBatch_id($batch_id)
    {
        $this->batch_id = $batch_id;
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

    public function getPackage_weight_lb()
    {
        return $this->package_weight_lb;
    }

    public function setPackage_weight_lb($package_weight_lb)
    {
        $this->package_weight_lb = $package_weight_lb;
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

    public function getQuantity_lb()
    {
        return $this->quantity_lb;
    }

    public function setQuantity_lb($quantity_lb)
    {
        $this->quantity_lb = $quantity_lb;
        return $this;
    }

    public function getUnits_per_package()
    {
        return $this->units_per_package;
    }

    public function setUnits_per_package($units_per_package)
    {
        $this->units_per_package = $units_per_package;
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
