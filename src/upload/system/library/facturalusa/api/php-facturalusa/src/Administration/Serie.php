<?php

namespace Facturalusa\Administration;

class Serie
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
     * Creates a new serie
     * https://facturalusa.pt/documentacao/api#administracao-series-criar
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/series/create', 'POST', $params);
    }

    /**
     * Updates an existing serie
     * https://facturalusa.pt/documentacao/api#administracao-series-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/series/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing serie
     * https://facturalusa.pt/documentacao/api#administracao-series-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/series/{$id}/delete", 'POST');
    }

    /**
     * Finds a serie
     * https://facturalusa.pt/documentacao/api#administracao-series-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/series/find', 'POST', $params);
    }

    /**
     * Lists the series
     * https://facturalusa.pt/documentacao/api#administracao-series-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/series/list', 'POST', $params);
    }

    /**
     * Lists the series by document type
     * https://facturalusa.pt/documentacao/api#administracao-series-por-tipo-documento
     * 
     * @param   Array   params
     */
    public function byDocumentType($params = [])
    {
        $this->facturalusa->request('administration/series/by_document_type', 'POST', $params);
    }
}