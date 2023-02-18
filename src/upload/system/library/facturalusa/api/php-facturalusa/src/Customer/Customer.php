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
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('customers/create', 'POST', $params);
    }

    /**
     * Updates an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("customers/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("customers/{$id}/delete", 'POST');
    }

    /**
     * Finds a customer
     * https://facturalusa.pt/documentacao/api#clientes-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('customers/find', 'POST', $params);
    }

    /**
     * Lists the customers
     * https://facturalusa.pt/documentacao/api#clientes-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('customers/list', 'POST', $params);
    }
}