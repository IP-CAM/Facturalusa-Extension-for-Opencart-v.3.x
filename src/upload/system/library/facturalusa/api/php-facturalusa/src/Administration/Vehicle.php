<?php

namespace Facturalusa\Administration;

class Vehicle
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
     * Creates a new vehicle
     * https://facturalusa.pt/documentacao/api#administracao-veiculos-criar
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/vehicles/create', 'POST', $params);
    }

    /**
     * Updates an existing vehicle
     * https://facturalusa.pt/documentacao/api#administracao-veiculos-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/vehicles/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing vehicle
     * https://facturalusa.pt/documentacao/api#administracao-veiculos-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/vehicles/{$id}/delete", 'POST');
    }

    /**
     * Finds a vehicle
     * https://facturalusa.pt/documentacao/api#administracao-veiculos-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/vehicles/find', 'POST', $params);
    }

    /**
     * Lists the vehicles
     * https://facturalusa.pt/documentacao/api#administracao-veiculos-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/vehicles/list', 'POST', $params);
    }
}