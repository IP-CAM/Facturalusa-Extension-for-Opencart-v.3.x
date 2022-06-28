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
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/documentstypes/{$id}/update", 'POST', $params);
    }

    /**
     * Finds a document type
     * https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/documentstypes/find', 'POST', $params);
    }

    /**
     * Lists the documentstypes
     * https://facturalusa.pt/documentacao/api#administracao-tiposdocumento-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/documentstypes/list', 'POST', $params);
    }
}