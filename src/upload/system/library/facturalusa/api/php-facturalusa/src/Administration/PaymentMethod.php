<?php

namespace Facturalusa\Administration;

class PaymentMethod
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
     * Creates a new payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-criar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/paymentmethods/create', 'POST', $params);
    }

    /**
     * Updates an existing payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/paymentmethods/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/paymentmethods/{$id}/delete", 'POST');
    }

    /**
     * Finds a payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/paymentmethods/find', 'POST', $params);
    }

    /**
     * Lists the paymentmethods
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/paymentmethods/list', 'POST', $params);
    }
}