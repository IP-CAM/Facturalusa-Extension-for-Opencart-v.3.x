<?php

namespace Facturalusa\Administration;

class DocumentType
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
     * Updates an existing document type
     * https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("administration/documentstypes/{$id}/update", 'POST', $params);
    }

    /**
     * Finds a document type
     * https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('administration/documentstypes/find', 'POST', $params);
    }

    /**
     * Lists the documentstypes
     * https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('administration/documentstypes/list', 'POST', $params);
    }

    /**
     * Communicates a serie by document type to Autoridade Tributária Services
     * https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-comunicar-serie
     * 
     * @param   Integer id
     * @param   Integer serieId
     * @param   Array   params
     * 
     * @return  Array
     */
    public function communicateSerie($id, $serieId, $params)
    {
        return $this->facturalusa->request("administration/documentstypes/{$id}/{$serieId}/communicate", 'POST', $params);
    }
}