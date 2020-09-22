<?php

namespace TaskinBirtan\LaravelParasut;

trait Category
{

    protected $categoryUrl = [
        'get' => "{base_url}/{api_version}/{company_id}/item_categories"
    ];

    protected function setCategoryUrl($url)
    {
        $category_url = str_replace('{base_url}', $this->categoryUrl['get'], $this->base_url);
        $category_url = str_replace('{api_version}', $category_url, $this->version);
        $category_url = str_replace('{company_id}', $category_url, $this->company_id);
        return $category_url;

    }

}
