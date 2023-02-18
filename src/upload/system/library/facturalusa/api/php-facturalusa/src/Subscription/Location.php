<?php

namespace Facturalusa\Subscription;

class Location
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
     * Creates a new location
     * https://facturalusa.pt/documentacao/api#subscricao-locais-criar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('subscription/locations/create', 'POST', $params);
    }

    /**
     * Updates an existing location
     * https://facturalusa.pt/documentacao/api#subscricao-locais-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("subscription/locations/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing location
     * https://facturalusa.pt/documentacao/api#subscricao-locais-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("subscription/locations/{$id}/delete", 'POST');
    }

    /**
     * Finds a location
     * https://facturalusa.pt/documentacao/api#subscricao-locais-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('subscription/locations/find', 'POST', $params);
    }

    /**
     * Lists the locations
     * https://facturalusa.pt/documentacao/api#subscricao-locais-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('subscription/locations/list', 'POST', $params);
    }
}