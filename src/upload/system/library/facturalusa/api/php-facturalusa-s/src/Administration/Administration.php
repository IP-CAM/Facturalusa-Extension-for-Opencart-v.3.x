<?php

namespace Facturalusa\Administration;

class Administration
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
     * Returns all the information about all the endpoints available in administration
     * https://facturalusa.pt/documentacao/api#administracao
     */
    public function all()
    {
        $this->facturalusa->request('administration/all', 'POST');
    }
}