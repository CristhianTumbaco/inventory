<?php

class PurchaseDetails
{
    private $id;
    private $purchase_id;
    private $category_id;
    private $product_id;
    private $subproduct_id;
    private $batch_id;
    private $quantity;
    private $unit_cost;
    private $total_cost;
    private $presentation_id;
    private $package_count;
    private $package_weight_lb;
    private $units_per_package;
    private $expiration_date;
    private $notes;

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

    public function getPurchase_id()
    {
        return $this->purchase_id;
    }

    public function setPurchase_id($purchase_id)
    {
        $this->purchase_id = $purchase_id;
        return $this;
    }

    public function getCategory_id()
    {
        return $this->category_id;
    }

    public function setCategory_id($category_id)
    {
        $this->category_id = $category_id;
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

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnit_cost()
    {
        return $this->unit_cost;
    }

    public function setUnit_cost($unit_cost)
    {
        $this->unit_cost = $unit_cost;
        return $this;
    }

    public function getTotal_cost()
    {
        return $this->total_cost;
    }

    public function setTotal_cost($total_cost)
    {
        $this->total_cost = $total_cost;
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

    public function getPackage_count()
    {
        return $this->package_count;
    }

    public function setPackage_count($package_count)
    {
        $this->package_count = $package_count;
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

    public function getUnits_per_package()
    {
        return $this->units_per_package;
    }

    public function setUnits_per_package($units_per_package)
    {
        $this->units_per_package = $units_per_package;
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
}
