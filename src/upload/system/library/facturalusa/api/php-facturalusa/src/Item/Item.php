<?php

namespace Facturalusa\Item;

class Item
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
     * Creates a new item
     * https://facturalusa.pt/documentacao/api#artigos
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('items/create', 'POST', $params);
    }

    /**
     * Updates an existing item
     * https://facturalusa.pt/documentacao/api#artigos-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("items/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing item
     * https://facturalusa.pt/documentacao/api#artigos-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("items/{$id}/delete", 'POST');
    }

    /**
     * Deletes the image from the item
     * https://facturalusa.pt/documentacao/api#artigos-eliminar-imagem
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function deleteImage($id)
    {
        return $this->facturalusa->request("items/{$id}/delete_image", 'POST');
    }

    /**
     * Finds a item
     * https://facturalusa.pt/documentacao/api#artigos-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('items/find', 'POST', $params);
    }

    /**
     * Lists the items
     * https://facturalusa.pt/documentacao/api#artigos-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('items/list', 'POST', $params);
    }
}