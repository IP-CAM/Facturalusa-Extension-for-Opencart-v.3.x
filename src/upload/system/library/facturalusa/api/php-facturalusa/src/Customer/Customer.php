<?php

namespace Facturalusa\Customer;

class Customer
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
     * Creates a new customer
     * https://facturalusa.pt/documentacao/api#clientes
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('customers/create', 'POST', $params);
    }

    /**
     * Updates an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("customers/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("customers/{$id}/delete", 'POST');
    }

    /**
     * Finds a customer
     * https://facturalusa.pt/documentacao/api#clientes-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('customers/find', 'POST', $params);
    }

    /**
     * Lists the customers
     * https://facturalusa.pt/documentacao/api#clientes-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('customers/list', 'POST', $params);
    }
}