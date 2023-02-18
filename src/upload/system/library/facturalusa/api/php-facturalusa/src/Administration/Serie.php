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
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('administration/series/create', 'POST', $params);
    }

    /**
     * Updates an existing serie
     * https://facturalusa.pt/documentacao/api#administracao-series-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/series/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing serie
     * https://facturalusa.pt/documentacao/api#administracao-series-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("administration/series/{$id}/delete", 'POST');
    }

    /**
     * Communicates a serie to all type of documents in Autoridade Tributária Services
     * https://facturalusa.pt/documentacao/api#administracao-series-comunicar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function communicate($id)
    {
        return $this->facturalusa->request("administration/series/{$id}/communicate", 'POST');
    }

    /**
     * Finds a serie
     * https://facturalusa.pt/documentacao/api#administracao-series-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/series/find', 'POST', $params);
    }

    /**
     * Lists the series
     * https://facturalusa.pt/documentacao/api#administracao-series-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/series/list', 'POST', $params);
    }

    /**
     * Lists the series by document type
     * https://facturalusa.pt/documentacao/api#administracao-series-por-tipo-documento
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function byDocumentType($params = [])
    {
        return $this->facturalusa->request('administration/series/by_document_type', 'POST', $params);
    }
}