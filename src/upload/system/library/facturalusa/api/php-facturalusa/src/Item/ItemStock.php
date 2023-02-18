<?php

namespace Facturalusa\Item;

class ItemStock
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
     * Creates a new item stock
     * https://facturalusa.pt/documentacao/api#artigos-stock-movimentos-criar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('items/stock/movements/create', 'POST', $params);
    }

    /**
     * Updates an existing item stock
     * https://facturalusa.pt/documentacao/api#artigos-stock-movimentos-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("items/stock/movements/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing item stock
     * https://facturalusa.pt/documentacao/api#artigos-stock-movimentos-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("items/stock/movements/{$id}/delete", 'POST');
    }

    /**
     * Returns the current stock
     * https://facturalusa.pt/documentacao/api#artigos-stock-actual
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function current($id)
    {
        return $this->facturalusa->request("items/{$id}/stock/actual", 'POST');
    }
}