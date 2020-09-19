<?php

namespace TaskinBirtan\LaravelParasut;

trait Customer {

    protected $customerModel = [];
    protected $createCustomerModel = [];

    protected $attributes = [];


    protected $customerTypes = [
        'customer',
        'supplier'
    ];
    public function setFilterName($name)
    {
        $this->customerModel['filter[name]'] = $name;
        return $this;
    }

    public function setCustomerName($customerName)
    {
        $this->attributes['name'] = $customerName;
        return $this;
    }
    public function setCustomerAccountType($type = 'customer')
    {
        $this->attributes['account_type'] = $type;
        return $this;
    }

    public function getCustomerAttributes()
    {
        return $this->attributes;
    }

    public function getCustomerModel()
    {
        return $this->customerModel;
    }

}
