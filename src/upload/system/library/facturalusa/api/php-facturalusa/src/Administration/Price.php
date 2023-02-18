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
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/prices/create', 'POST', $params);
    }

    /**
     * Updates an existing price
     * https://facturalusa.pt/documentacao/api#administracao-precos-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/prices/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing price
     * https://facturalusa.pt/documentacao/api#administracao-precos-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/prices/{$id}/delete", 'POST');
    }

    /**
     * Finds a price
     * https://facturalusa.pt/documentacao/api#administracao-precos-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/prices/find', 'POST', $params);
    }

    /**
     * Lists the prices
     * https://facturalusa.pt/documentacao/api#administracao-precos-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/prices/list', 'POST', $params);
    }
}