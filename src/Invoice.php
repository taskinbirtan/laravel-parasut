<?php

namespace TaskinBirtan\LaravelParasut;

trait Invoice
{

    protected $customerModel = [];
    protected $createInvoiceModel = [];

    protected $invoiceAttributes = [];
    protected $invoiceRelationship = [];

    protected $invoiceProductData = [];

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
                break;
            case 'supplier':
                $this->invoiceRelationship['supplier'] = [];
                $this->invoiceRelationship['supplier']['data'] = [];
                $this->invoiceRelationship['supplier']['data']['id'] = $id;
                $this->invoiceRelationship['supplier']['data']['type'] = "contacts";
                break;
            case 'details':
                $arr = [];
                $arr['attributes'] = [];
                $arr['attributes']['quantity'] = $data['quantity'];
                $arr['attributes']['unit_price'] = $data['unit_price'];
                $arr['attributes']['vat_rate'] = $data['vat_rate'];

                $this->invoiceRelationship['details'] = [];

                $this->invoiceRelationship['details']['data'] = [];

                $this->invoiceRelationship['details']['data'][] = [
                    'type' => 'sales_invoice_details',
                    'attributes' => [
                        'quantity' => $data['quantity'],
                        'unit_price' => $data['unit_price'],
                        'vat_rate' => $data['vat_rate']
                    ],

                    'relationships' => [
                        'product' => $this->invoiceProductData
                    ]

                ];

                break;
        }

        return $this;

    }

    public function setInvoiceProduct($id)
    {
        $data = [
            'data' => [
                'id' => $id,
                "type" => "products"
            ]
        ];
        $this->invoiceProductData = $data;

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
