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
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/categories/create', 'POST', $params);
    }

    /**
     * Updates an existing category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/categories/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/categories/{$id}/delete", 'POST');
    }

    /**
     * Finds a category
     * https://facturalusa.pt/documentacao/api#administracao-categorias-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/categories/find', 'POST', $params);
    }

    /**
     * Lists the categories
     * https://facturalusa.pt/documentacao/api#administracao-categorias-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/categories/list', 'POST', $params);
    }
}