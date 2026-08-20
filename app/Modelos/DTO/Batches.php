<?php

class Batches
{
    private $id;
    private $batch_code;
    private $batch_type;
    private $parent_batch_id;
    private $purchase_id;
    private $purchase_detail_id;
    private $supplier_id;
    private $category_id;
    private $product_id;
    private $subproduct_id;
    private $production_date;
    private $expiration_date;
    private $entry_temperature;
    private $notes;
    private $created_at;

    function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getBatch_code()
    {
        return $this->batch_code;
    }

    public function setBatch_code($batch_code)
    {
        $this->batch_code = $batch_code;
        return $this;
    }

    public function getBatch_type()
    {
        return $this->batch_type;
    }

    public function setBatch_type($batch_type)
    {
        $this->batch_type = $batch_type;
        return $this;
    }

    public function getParent_batch_id()
    {
        return $this->parent_batch_id;
    }

    public function setParent_batch_id($parent_batch_id)
    {
        $this->parent_batch_id = $parent_batch_id;
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

    public function getPurchase_detail_id()
    {
        return $this->purchase_detail_id;
    }

    public function setPurchase_detail_id($purchase_detail_id)
    {
        $this->purchase_detail_id = $purchase_detail_id;
        return $this;
    }

    public function getSupplier_id()
    {
        return $this->supplier_id;
    }

    public function setSupplier_id($supplier_id)
    {
        $this->supplier_id = $supplier_id;
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

    public function getProduction_date()
    {
        return $this->production_date;
    }

    public function setProduction_date($production_date)
    {
        $this->production_date = $production_date;
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

    public function getEntry_temperature()
    {
        return $this->entry_temperature;
    }

    public function setEntry_temperature($entry_temperature)
    {
        $this->entry_temperature = $entry_temperature;
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