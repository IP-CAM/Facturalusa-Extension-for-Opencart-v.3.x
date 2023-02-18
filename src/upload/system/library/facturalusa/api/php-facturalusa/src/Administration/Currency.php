<?php

namespace Facturalusa\Administration;

class Currency
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
     * Creates a new currency
     * https://facturalusa.pt/documentacao/api#administracao-moedas-criar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/currencies/create', 'POST', $params);
    }

    /**
     * Updates an existing currency
     * https://facturalusa.pt/documentacao/api#administracao-moedas-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/currencies/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing currency
     * https://facturalusa.pt/documentacao/api#administracao-moedas-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/currencies/{$id}/delete", 'POST');
    }

    /**
     * Finds a currency
     * https://facturalusa.pt/documentacao/api#administracao-moedas-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/currencies/find', 'POST', $params);
    }

    /**
     * Lists the currencies
     * https://facturalusa.pt/documentacao/api#administracao-moedas-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/currencies/list', 'POST', $params);
    }
}