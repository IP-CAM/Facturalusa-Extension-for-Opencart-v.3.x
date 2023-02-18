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
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/paymentconditions/create', 'POST', $params);
    }

    /**
     * Updates an existing payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/paymentconditions/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/paymentconditions/{$id}/delete", 'POST');
    }

    /**
     * Finds a payment condition
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/paymentconditions/find', 'POST', $params);
    }

    /**
     * Lists the paymentconditions
     * https://facturalusa.pt/documentacao/api#administracao-condicoespagamento-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/paymentconditions/list', 'POST', $params);
    }
}