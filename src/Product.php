<?php

namespace TaskinBirtan\LaravelParasut;

trait Product
{

    protected $createProductModel = [];
    protected $createProductModelAttributes = [];
    protected $productQuery = [];


    public function setProductQuery($name, $value)
    {
        $this->productQuery[$name] = $value;
        return $this;
    }

    public function getProductQuery()
    {
        return $this->productQuery;
    }

    public function setProductType($type = 'products')
    {
        $this->createProductModel['type'] = $type;
        return $this;
    }
    public function setProductAttribute($type, $value)
    {
        $this->createProductModelAttributes[$type] = $value;
        return $this;
    }

    public function getProductAttributes()
    {
        return $this->createProductModelAttributes;
    }

    public function getProductQueryData()
    {

        return
        [
            'type' => $this->createProductModel['type'],
            'attributes' => $this->getProductAttributes()
        ];
    }



}
