<?php

namespace Facturalusa\Customer;

class CustomerOpenAccounts
{
    /**
     * Holds the Facturalusa Client
     * 
     * @var FacturalusaClient
     */
    private $facturalusa;

    /**
     * Initializes the constructor
     * 
     * @param   Facturalusa
     */
    public function __construct(\Facturalusa\FacturalusaClient $facturalusa)
    {
        $this->facturalusa = $facturalusa;
    }

    /**
     * Gets the list of open accounts for an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-contas-aberto-singular
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function find($id)
    {
        return $this->facturalusa->request("customers/{$id}/accounts_open", 'POST');
    }

    /**
     * Allows the download of a PDF file about the list of open accounts for an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-contas-aberto-download
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function download($id, $params = [])
    {
        return $this->facturalusa->request("customers/{$id}/accounts_open/download", 'POST', $params);
    }

    /**
     * Sends an email to the customer with all the open accounts
     * https://facturalusa.pt/documentacao/api#clientes-contas-aberto-enviar-email
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sendEmail($id, $params = [])
    {
        return $this->facturalusa->request("customers/{$id}/accounts_open/send_email", 'POST', $params);
    }

    /**
     * Returns all the open accounts from all customers
     * https://facturalusa.pt/documentacao/api#clientes-contas-aberto-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request("customers/accounts_open", 'POST', $params);
    }
}