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
        return $this->facturalusa->request('sales/create', 'POST', $params);
    }

    /**
     * Updates an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("sales/{$id}/delete", 'POST');
    }

    /**
     * Cancels an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-cancelar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function cancel($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/cancel", 'POST', $params);
    }

    /**
     * Duplicates an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-duplicar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function duplicate($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/duplicate", 'POST', $params);
    }

    /**
     * Creates a receipt based on an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-recibo
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function receipt($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/receipt", 'POST', $params);
    }

    /**
     * Creates a credit note of an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-nota-credito
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function creditNote($id)
    {
        return $this->facturalusa->request("sales/{$id}/credit_note", 'POST');
    }

    /**
     * Creates a debit note of an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-nota-debito
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function debitNote($id)
    {
        return $this->facturalusa->request("sales/{$id}/debit_note", 'POST');
    }

    /**
     * Downloads the sale document
     * https://facturalusa.pt/documentacao/api#vendas-download
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function download($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/download", 'POST', $params);
    }

    /**
     * Sends an email 
     * https://facturalusa.pt/documentacao/api#vendas-enviar-email
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sendEmail($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/send_email", 'POST', $params);
    }

    /**
     * Sends an sms
     * https://facturalusa.pt/documentacao/api#vendas-enviar-sms
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sendSms($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/send_sms", 'POST', $params);
    }

    /**
     * Signs a document
     * https://facturalusa.pt/documentacao/api#vendas-assinar-digitalmente
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sign($id, $params)
    {
        return $this->facturalusa->request("sales/{$id}/sign", 'POST', $params);
    }

    /**
     * Creates a debit note of an existing sale
     * https://facturalusa.pt/documentacao/api#vendas-gerar-ref-multibanco
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function generateMBReference($id)
    {
        return $this->facturalusa->request("sales/{$id}/generate_mbref", 'POST');
    }

    /**
     * Allows to calculate in-real-time the totals of a document
     * https://facturalusa.pt/documentacao/api#vendas-sumario
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function summary($params)
    {
        return $this->facturalusa->request('sales/summary', 'POST', $params);
    }

    /**
     * Finds a sale
     * https://facturalusa.pt/documentacao/api#vendas-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('sales/find', 'POST', $params);
    }

    /**
     * Lists the sales
     * https://facturalusa.pt/documentacao/api#vendas-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('sales/list', 'POST', $params);
    }
}