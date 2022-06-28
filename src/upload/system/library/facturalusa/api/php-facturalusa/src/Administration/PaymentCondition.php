<?php

namespace Facturalusa\Administration;

class PaymentCondition
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
     * Creates a new payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-criar
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/paymentconditions/create', 'POST', $params);
    }

    /**
     * Updates an existing payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/paymentconditions/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/paymentconditions/{$id}/delete", 'POST');
    }

    /**
     * Finds a payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/paymentconditions/find', 'POST', $params);
    }

    /**
     * Lists the paymentconditions
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/paymentconditions/list', 'POST', $params);
    }
}