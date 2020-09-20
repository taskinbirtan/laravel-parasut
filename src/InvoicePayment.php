<?php

namespace TaskinBirtan\LaravelParasut;

trait InvoicePayment
{
    protected $invoicePaymentAttributes = [];

    public function addInvoicePaymentAttribute($type, $value)
    {
        $this->invoicePaymentAttributes[$type] = $value;
        return $this;
    }


    public function getInvoicePaymentAttributes()
    {
        return $this->invoicePaymentAttributes;
    }


}
