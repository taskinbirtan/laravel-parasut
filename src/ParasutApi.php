<?php

namespace TaskinBirtan\LaravelParasut;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ParasutApi
{
    use Customer;
    use Invoice;
    use Product;
    use InvoicePayment;
    use Category;

    protected $base_url = 'https://api.parasut.com';
    protected $version = 'v4';

    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;
    protected $company_id;

    protected $http_client;

    protected $api_access_token;
    protected $response;


    private $options = [
        'Content-Type' => "application/x-www-form-urlencoded"
    ];

    public function __construct($username, $password)
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

//        $parasut_api_token = Cache::get('parasut-api-token', null);
//
//        if(empty($parasut_api_token)) {
//            $this->parasut_api_token = $this->login($username, $password);
//        } else {
//            $this->parasut_api_token = $parasut_api_token;
//        }

        $this->parasut_api_token = $this->login($username, $password);
    }

    public function login($username, $password)
    {
        $parasutApiToken = Cache::remember('parasut-api-token', 6500, function () use ($username, $password) {

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
            return json_decode($this->response->getBody());
        });
        return $parasutApiToken;
    }

    public function getCategories($page = null)
    {
        if (empty($page)) {
            $this->response = $this->http_client->request('GET', $this->version . '/' . $this->company_id . '/' . 'item_categories', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
                ],
                'query' => $this->categoryQueryParameter
            ]);
        } else {
            $this->response = $this->http_client->request('GET', $page, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
                ],
                'query' => $this->categoryQueryParameter
            ]);
        }

        if ($this->response->getStatusCode() == 200) {
            return $this->response->getBody();
        } else {
            return json_encode(['isError' => true]);
        }

    }


    public function getSingleInvoice()
    {
        //dd($this->getInvoiceQueryParameters());
        $this->response = $this->http_client->request('GET', $this->version . '/' . $this->company_id . '/' . 'sales_invoices', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
            ],
            'form_params' => $this->getInvoiceQueryParameters()

        ]);


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
                'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
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
                'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
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
        $param = json_encode(
            [
                'data' =>
                    [
                        "type" => 'sales_invoices',
                        "attributes" => $this->getInvoiceAttributes(),
                        "relationships" => $this->getInvoiceRelationship()
                    ]
            ]
        );
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.parasut.com/v4/" . $this->company_id . "/sales_invoices?=",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => ($param),

            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $this->parasut_api_token->access_token,
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_encode(['isError' => true]);
        } else {

        }
        return (json_decode($response));
    }

    public function createSaleInvoice()
    {
        $param = json_encode(
            [
                'data' =>
                    [
                        "type" => 'purchase_bills',
                        "attributes" => $this->getInvoiceAttributes(),
                        "relationships" => $this->getInvoiceRelationship()
                    ]
            ]
        );
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.parasut.com/v4/" . $this->company_id . "/purchase_bills#basic",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => ($param),

            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $this->parasut_api_token->access_token,
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_encode(['isError' => true]);
        } else {

        }
        return (json_decode($response));
    }

    public function createPurchaseBill()
    {
        $param = json_encode(
            [
                'data' =>
                    [
                        "type" => 'purchase_bills',
                        "attributes" => $this->getInvoiceAttributes(),
                        "relationships" => $this->getInvoiceRelationship()
                    ]
            ]
        );
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.parasut.com/v4/" . $this->company_id . "/purchase_bills#basic",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => ($param),

            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $this->parasut_api_token->access_token,
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return json_encode(['isError' => true]);
        } else {

        }
        return (json_decode($response));
    }

    public function createInvoicePayment($invoice_id)
    {
        $this->response = $this->http_client->request('POST', $this->version . '/' . $this->company_id . '/' . 'sales_invoices/' . $invoice_id . '/' . 'payments', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
            ],
            'form_params' => [
                'data' => [
                    "type" => 'payments',
                    "attributes" => $this->getInvoicePaymentAttributes()
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
                'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
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
                'Authorization' => 'Bearer ' . $this->parasut_api_token->access_token,
            ],
            'form_params' => [
                'data' => $data
            ]
        ]);
        return $this->response->getBody();
    }
}
