<?php

namespace Facturalusa\Administration;

class Price
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
     * Creates a new price
     * https://facturalusa.pt/documentacao/api#administracao-precos-criar
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/prices/create', 'POST', $params);
    }

    /**
     * Updates an existing price
     * https://facturalusa.pt/documentacao/api#administracao-precos-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/prices/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing price
     * https://facturalusa.pt/documentacao/api#administracao-precos-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/prices/{$id}/delete", 'POST');
    }

    /**
     * Finds a price
     * https://facturalusa.pt/documentacao/api#administracao-precos-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/prices/find', 'POST', $params);
    }

    /**
     * Lists the prices
     * https://facturalusa.pt/documentacao/api#administracao-precos-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/prices/list', 'POST', $params);
    }
}