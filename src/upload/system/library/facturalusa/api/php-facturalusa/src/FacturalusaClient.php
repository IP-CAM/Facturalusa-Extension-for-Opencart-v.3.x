<?php

namespace Facturalusa;

require_once('FacturalusaResponse.php');

class FacturalusaClient Implements FacturalusaResponse
{
    /**
     * Holds the API URL
     * 
     * @var String
     */
    const API_URL = 'https://facturalusa.pt/api/v1/';

    /**
     * Holds the CURL Headers
     * 
     * @var Array
     */
    const CURL_HEADERS = 
    [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    /**
     * Holds the API Access Token
     * 
     * @var String
     */
    private $apiToken;

    /**
     * Holds the http code
     * 
     * @var Integer
     */
    private $httpcode;

    /**
     * Holds the response message
     * 
     * @var Array
     */
    private $response;

    /**
     * Initializes the constructor
     * 
     * @param   String  Sets the API token
     */
    public function __construct($apiToken = '')
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Executes a request to the API
     *
     * @param   String  Action
     * @param   String  Type of request (POST, GET, PUT)
     * @param   Array   List of params to send
     */
    public function request($action, $type, $params = [])
    {
        // Append the API token
        $params['api_token'] = $this->apiToken;

        $ch = curl_init(self::API_URL . $action);

		curl_setopt($ch, CURLOPT_HTTPHEADER, self::CURL_HEADERS);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $this->httpcode = $httpcode;
        $this->response = json_decode($response);

        return $this->response;
    }

    /**
     * Returns if the request has failed
     * 
     * @return  Boolean
     */
    public function fail()
    {
        return $this->httpcode != 200;
    }

    /**
     * Returns if the request has succeeded
     * 
     * @return  Boolean
     */
    public function success()
    {
        return $this->httpcode == 200;
    }

    /**
     * Returns the request response
     * 
     * @return  Array
     */
    public function response()
    {
        return $this->response;
    }
}