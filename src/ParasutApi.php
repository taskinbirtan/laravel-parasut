<?php

namespace TaskinBirtan\LaravelParasut;

use GuzzleHttp\Client;

class ParasutApi
{
    use Customer;
    use Invoice;
    use Product;

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

        if (empty($this->client_id) || empty($this->client_secret) || empty($this->redirect_uri) || empty($this->company_id)) {
            throw new \Exception("Paraşüt API sini kullanabilmeniz için env içerisinde zorunlu olarak belirtilmesi gerekli
            olan PARASUT_CLIENT_ID* PARASUT_CLIENT_SECRET* PARASUT_COMPANY_ID* PARASUT_CLIENT_ID* PARASUT_REDIRECT_URI değerlerini belirtiniz (* olanlar zorunludur).");
        }
        $this->http_client = new Client([
            'base_uri' => $this->base_url,

        ]);

    }

    public function login($username, $password)
    {
        $this->response = $this->http_client->request('POST', $this->base_url . '/' . 'oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'username' => $username,
                'password' => $password,
                'redirect_uri' => $this->redirect_uri
            ]
        ]);
        /*TODO cache kullanilarak access_token kayit edilmelidir, refresh token de ayni sekilde ..*/
        if ($this->response->getStatusCode() == 200) {
            return $this->response->getBody();
        } else {
            return json_encode(['isError' => true]);
        }
    }


    public function getCustomers()
    {

        $form_params = $this->getCustomerModel();
        $this->response = $this->http_client->request('GET', $this->version . '/' . $this->company_id . '/' . 'contacts', [
            'headers' => [
                'Authorization' => 'Bearer 2f267dba043c9be0418a3af1419655576118b38b168e9185f6a33fee7b44a675',
            ],
            'form_params' => $form_params

        ]);


        if ($this->response->getStatusCode() == 200) {
            return $this->response->getBody();
        } else {
            return json_encode(['isError' => true]);
        }
    }

    public function createCustomer()
    {
        $this->response = $this->http_client->request('POST', $this->version . '/' . $this->company_id . '/' . 'contacts', [
            'headers' => [
                'Authorization' => 'Bearer 2f267dba043c9be0418a3af1419655576118b38b168e9185f6a33fee7b44a675',

            ],
            'form_params' => [
                'data' => [
                    "type" => 'contacts',
                    "attributes" => $this->getCustomerAttributes()
                ]
            ]
        ]);

        if ($this->response->getStatusCode() == 201) {
            return $this->response->getBody();
        } else {
            return json_encode(['isError' => true]);
        }
    }

    public function createInvoice()
    {


        $this->response = $this->http_client->request('POST', $this->version . '/' . $this->company_id . '/' . 'sales_invoices', [
            'headers' => [
                'Authorization' => 'Bearer 2f267dba043c9be0418a3af1419655576118b38b168e9185f6a33fee7b44a675',
                'Content-type' => 'multipart/form-data'
            ],
            'form_params' => [
                'data' =>
                    [
                        "type" => 'sales_invoices',
                        "attributes" => $this->getInvoiceAttributes(),
                        "relationships" => $this->getInvoiceRelationship()
                    ]

            ]
        ]);

        if ($this->response->getStatusCode() == 201) {
            return $this->response->getBody();
        } else {
            return json_encode(['isError' => true]);
        }
    }

    public function getProducts()
    {
        $form_params = $this->getProductQuery();
        $this->response = $this->http_client->request('GET', $this->version . '/' . $this->company_id . '/' . 'products', [
            'headers' => [
                'Authorization' => 'Bearer 2f267dba043c9be0418a3af1419655576118b38b168e9185f6a33fee7b44a675',
            ],
            'form_params' => $form_params
        ]);
        return $this->response->getBody();

    }

    public function createProduct()
    {
        $data = $this->getProductQueryData();
        $this->response = $this->http_client->request('POST', $this->version . '/' . $this->company_id . '/' . 'products', [
            'headers' => [
                'Authorization' => 'Bearer 2f267dba043c9be0418a3af1419655576118b38b168e9185f6a33fee7b44a675',
            ],
            'form_params' => [
                'data' => $data
            ]
        ]);
        return $this->response->getBody();
    }

}
