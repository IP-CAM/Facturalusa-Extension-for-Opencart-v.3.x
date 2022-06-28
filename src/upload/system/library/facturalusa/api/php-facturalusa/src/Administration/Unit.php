<?php

namespace Facturalusa\Administration;

class Unit
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
     * Creates a new unit
     * https://facturalusa.pt/documentacao/api#administracao-unidades-criar
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/units/create', 'POST', $params);
    }

    /**
     * Updates an existing unit
     * https://facturalusa.pt/documentacao/api#administracao-unidades-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/units/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing unit
     * https://facturalusa.pt/documentacao/api#administracao-unidades-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/units/{$id}/delete", 'POST');
    }

    /**
     * Finds a unit
     * https://facturalusa.pt/documentacao/api#administracao-unidades-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/units/find', 'POST', $params);
    }

    /**
     * Lists the units
     * https://facturalusa.pt/documentacao/api#administracao-unidades-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/units/list', 'POST', $params);
    }
}