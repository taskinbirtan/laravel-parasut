<?php

namespace TaskinBirtan\LaravelParasut;

use GuzzleHttp\Client;

class ParasutApi
{

    protected $base_url = 'https://api.parasut.com';
    protected $version = 'v4';

    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;
    protected $company_id;

    protected $http_client;

    protected $response;


    private $options = [
        'Content-Type' => "application/x-www-form-urlencoded"
    ];

    public function __construct()
    {
        $this->client_id = env('PARASUT_CLIENT_ID');
        $this->client_secret = env('PARASUT_CLIENT_SECRET');
        $this->redirect_uri = env('PARASUT_REDIRECT_URI', "urn:ietf:wg:oauth:2.0:oob");
        $this->company_id = env('PARASUT_COMPANY_ID');
        $this->client_id = env('PARASUT_CLIENT_ID');

        if(empty($this->client_id) || empty($this->client_secret) || empty($this->redirect_uri) || empty($this->company_id)) {
            throw new \Exception("Paraşüt API sini kullanabilmeniz için env içerisinde zorunlu olarak belirtilmesi gerekli
            olan PARASUT_CLIENT_ID* PARASUT_CLIENT_SECRET* PARASUT_COMPANY_ID* PARASUT_CLIENT_ID* PARASUT_REDIRECT_URI değerlerini belirtiniz (* olanlar zorunludur).");
        }
        $this->http_client = new Client([
            'base_uri' => $this->base_url,
        ]);

    }

    public function login($username, $password)
    {
        return 'hello';
    }
}
