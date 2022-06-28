<?php

namespace Facturalusa\Sale;

class Sale
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
     * Creates a new sale
     * https://facturalusa.pt/documentacao/api#vendas
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        $this->facturalusa->request('sales/create', 'POST', $params);
    }

    /**
     * Updates an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function update($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-eliminar
     * 
     * @param   Integer id
     */
    public function delete($id)
    {
        $this->facturalusa->request("sales/{$id}/delete", 'POST');
    }

    /**
     * Cancels an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-cancelar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function cancel($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/cancel", 'POST', $params);
    }

    /**
     * Duplicates an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-duplicar
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function duplicate($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/duplicate", 'POST', $params);
    }

    /**
     * Creates a receipt based on an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-recibo
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function receipt($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/receipt", 'POST', $params);
    }

    /**
     * Creates a credit note of an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-nota-credito
     * 
     * @param   Integer id
     */
    public function creditNote($id)
    {
        $this->facturalusa->request("sales/{$id}/credit_note", 'POST');
    }

    /**
     * Creates a debit note of an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-nota-debito
     * 
     * @param   Integer id
     */
    public function debitNote($id)
    {
        $this->facturalusa->request("sales/{$id}/debit_note", 'POST');
    }

    /**
     * Downloads the sale document
     * https://facturalusa.pt/documentacao/api#vendas-download
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function download($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/download", 'POST', $params);
    }

    /**
     * Sends an email 
     * https://facturalusa.pt/documentacao/api#vendas-enviar-email
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function sendEmail($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/send_email", 'POST', $params);
    }

    /**
     * Sends an sms
     * https://facturalusa.pt/documentacao/api#vendas-enviar-sms
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function sendSms($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/send_sms", 'POST', $params);
    }

    /**
     * Signs a document
     * https://facturalusa.pt/documentacao/api#vendas-assinar-digitalmente
     * 
     * @param   Integer id
     * @param   Array   params
     */
    public function sign($id, $params)
    {
        $this->facturalusa->request("sales/{$id}/sign", 'POST', $params);
    }

    /**
     * Creates a debit note of an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-gerar-ref-multibanco
     * 
     * @param   Integer id
     */
    public function generateMBReference($id)
    {
        $this->facturalusa->request("sales/{$id}/generate_mbref", 'POST');
    }

    /**
     * Allows to calculate in-real-time the totals of a document
     * https://facturalusa.pt/documentacao/api#vendas-sumario
     * 
     * @param   Array   params
     */
    public function summary($params)
    {
        $this->facturalusa->request('sales/summary', 'POST', $params);
    }

    /**
     * Finds a sale
     * https://facturalusa.pt/documentacao/api#vendas-procurar
     * 
     * @param   Array   params
     */
    public function find($params)
    {
        $this->facturalusa->request('sales/find', 'POST', $params);
    }

    /**
     * Lists the sales
     * https://facturalusa.pt/documentacao/api#vendas-lista
     * 
     * @param   Array   params
     */
    public function list($params = [])
    {
        $this->facturalusa->request('sales/list', 'POST', $params);
    }
}