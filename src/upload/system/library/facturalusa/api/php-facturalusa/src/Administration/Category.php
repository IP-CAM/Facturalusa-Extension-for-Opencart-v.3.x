<?php

namespace Facturalusa\Administration;

class Category
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
     * Creates a new category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-criar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/categories/create', 'POST', $params);
    }

    /**
     * Updates an existing category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/categories/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/categories/{$id}/delete", 'POST');
    }

    /**
     * Finds a category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/categories/find', 'POST', $params);
    }

    /**
     * Lists the categories
     * https://facturalusa.pt/documentacao/api#administracao-categorias-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/categories/list', 'POST', $params);
    }
}