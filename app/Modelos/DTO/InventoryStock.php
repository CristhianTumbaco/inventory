<?php

class InventoryStock
{
    private $id;
    private $product_id;
    private $subproduct_id;
    private $location_id;
    private $presentation_id;
    private $quantity;
    private $package_count;
    private $updated_at;

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

    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
