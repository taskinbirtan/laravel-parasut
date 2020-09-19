<?php

namespace TaskinBirtan\LaravelParasut;

trait Invoice
{

    protected $customerModel = [];
    protected $createInvoiceModel = [];

    protected $invoiceAttributes = [];
    protected $invoiceRelationship = [];

    protected $itemTypes = [
        "invoice",
        "estimate",
        "cancelled",
        "recurring_invoice",
        "recurring_estimate",
        "refund"
    ];

    protected $currencies = [
        "TRL", "USD", "EUR", "GBP"
    ];

    protected $attrValidation = [
        "item_type" => 'enum|required',
        "description" => "string",
        "issue_dates" => "date|required",
        "due_date" => "date",
        "invoice_series" => "string",
        "invoice_id" => "int",
        "currency" => "enum",
        "exchange_rate" => "number",
        "shipment_included" => "boolean"

    ];

    public function getInvoiceModel()
    {
        return $this->customerModel;
    }

    public function addAttribute($type, $value)
    {
        $this->invoiceAttributes[$type] = $value;
        return $this;
    }

    public function setInvoiceRelationship($type, $id = null, $data = null)
    {
        switch ($type) {
            case 'contact':
                $this->invoiceRelationship['contact'] = [];
                $this->invoiceRelationship['contact']['data'] = [];
                $this->invoiceRelationship['contact']['data']['id'] = $id;
                $this->invoiceRelationship['contact']['data']['type'] = "contacts";
//                dd($this->invoiceRelationship);
                break;
            case 'details':

                $arr = new \stdClass();
                $arr->attributes = new \stdClass();
                $arr->attributes->quantity = $data['quantity'];
                $arr->attributes->unit_price = $data['unit_price'];
                $arr->attributes->vat_rate = $data['vat_rate'];


                $this->invoiceRelationship['details'] = [

                        'data' => [
                            [

                                'type' => 'sales_invoice_details',
                                'attributes' => [
                                    'quantity' => 1,
                                    'unit_price' => 100,
                                    'vat_rate' => 0
                                ],

                            ]
                        ]


                ];
                //$this->invoiceRelationship['details'] = json_encode($this->invoiceRelationship['details']);
                //dd($this->invoiceRelationship);

                break;
        }

        return $this;

    }

    public function getInvoiceAttributes()
    {
        return $this->invoiceAttributes;
    }

    public function getInvoiceRelationship()
    {
        return $this->invoiceRelationship;
    }

}
