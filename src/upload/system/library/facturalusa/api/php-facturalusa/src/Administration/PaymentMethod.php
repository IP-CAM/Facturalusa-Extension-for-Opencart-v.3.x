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
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/paymentmethods/create', 'POST', $params);
    }

    /**
     * Updates an existing payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/paymentmethods/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/paymentmethods/{$id}/delete", 'POST');
    }

    /**
     * Finds a payment method
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/paymentmethods/find', 'POST', $params);
    }

    /**
     * Lists the paymentmethods
     * https://facturalusa.pt/documentacao/api#administracao-formaspagamento-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/paymentmethods/list', 'POST', $params);
    }
}