<?php

namespace Facturalusa\Administration;

class Warehouse
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
     * Creates a new warehouse
     * https://facturalusa.pt/documentacao/api#administracao-armazens-criar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/warehouses/create', 'POST', $params);
    }

    /**
     * Updates an existing warehouse
     * https://facturalusa.pt/documentacao/api#administracao-armazens-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/warehouses/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing warehouse
     * https://facturalusa.pt/documentacao/api#administracao-armazens-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/warehouses/{$id}/delete", 'POST');
    }

    /**
     * Finds a warehouse
     * https://facturalusa.pt/documentacao/api#administracao-armazens-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/warehouses/find', 'POST', $params);
    }

    /**
     * Lists the warehouses
     * https://facturalusa.pt/documentacao/api#administracao-armazens-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/warehouses/list', 'POST', $params);
    }
}