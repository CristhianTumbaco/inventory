<?php

class Purchases
{
    private $id;
    private $supplier_id;
    private $user_id;
    private $invoice_number;
    private $purchase_date;
    private $subtotal;
    private $tax;
    private $total;
    private $payment_method_id;
    private $status_id;
    private $notes;
    private $created_at;
    private $updated_at;

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

    public function getSupplier_id()
    {
        return $this->supplier_id;
    }

    public function setSupplier_id($supplier_id)
    {
        $this->supplier_id = $supplier_id;
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

    public function getInvoice_number()
    {
        return $this->invoice_number;
    }

    public function setInvoice_number($invoice_number)
    {
        $this->invoice_number = $invoice_number;
        return $this;
    }

    public function getPurchase_date()
    {
        return $this->purchase_date;
    }

    public function setPurchase_date($purchase_date)
    {
        $this->purchase_date = $purchase_date;
        return $this;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    public function getPayment_method_id()
    {
        return $this->payment_method_id;
    }

    public function setPayment_method_id($payment_method_id)
    {
        $this->payment_method_id = $payment_method_id;
        return $this;
    }

    public function getStatus_id()
    {
        return $this->status_id;
    }

    public function setStatus_id($status_id)
    {
        $this->status_id = $status_id;
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