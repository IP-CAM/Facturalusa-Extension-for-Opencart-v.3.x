<?php

namespace Facturalusa\Administration;

class Employee
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
     * Creates a new employee
     * https://facturalusa.pt/documentacao/api#administracao-colaboradores-criar
     * 
     * @param   Array   params
     */
    public function create($params)
    {
        $this->facturalusa->request('administration/employees/create', 'POST', $params);
    }

    /**
     * Updates an existing employee
     * https://facturalusa.pt/documentacao/api#administracao-colaboradores-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("administration/employees/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing employee
     * https://facturalusa.pt/documentacao/api#administracao-colaboradores-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("administration/employees/{$id}/delete", 'POST');
    }

    /**
     * Finds a employee
     * https://facturalusa.pt/documentacao/api#administracao-colaboradores-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('administration/employees/find', 'POST', $params);
    }

    /**
     * Lists the employees
     * https://facturalusa.pt/documentacao/api#administracao-colaboradores-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('administration/employees/list', 'POST', $params);
    }
}