<?php

namespace Facturalusa\Receipt;

class Receipt
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
     * Creates a new receipt
     * https://facturalusa.pt/documentacao/api#recibos
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function create($params)
    {
        return $this->facturalusa->request('receipts/create', 'POST', $params);
    }

    /**
     * Updates an existing receipt
     * https://facturalusa.pt/documentacao/api#recibos-actualizar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function update($id, $params)
    {
        return $this->facturalusa->request("receipts/{$id}/update", 'POST', $params);
    }

    /**
     * Deletes an existing receipt
     * https://facturalusa.pt/documentacao/api#recibos-eliminar
     * 
     * @param   Integer id
     * 
     * @return  Array
     */
    public function delete($id)
    {
        return $this->facturalusa->request("receipts/{$id}/delete", 'POST');
    }

    /**
     * Cancels an existing receipt
     * https://facturalusa.pt/documentacao/api#recibos-cancelar
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function cancel($id, $params)
    {
        return $this->facturalusa->request("receipts/{$id}/cancel", 'POST', $params);
    }

    /**
     * Downloads the receipt document
     * https://facturalusa.pt/documentacao/api#recibos-download
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function download($id, $params)
    {
        return $this->facturalusa->request("receipts/{$id}/download", 'POST', $params);
    }

    /**
     * Sends an email 
     * https://facturalusa.pt/documentacao/api#recibos-enviar-email
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sendEmail($id, $params)
    {
        return $this->facturalusa->request("receipts/{$id}/send_email", 'POST', $params);
    }

    /**
     * Sends an sms
     * https://facturalusa.pt/documentacao/api#recibos-enviar-sms
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sendSms($id, $params)
    {
        return $this->facturalusa->request("receipts/{$id}/send_sms", 'POST', $params);
    }

    /**
     * Signs a document
     * https://facturalusa.pt/documentacao/api#recibos-assinar-digitalmente
     * 
     * @param   Integer id
     * @param   Array   params
     * 
     * @return  Array
     */
    public function sign($id, $params)
    {
        return $this->facturalusa->request("receipts/{$id}/sign", 'POST', $params);
    }

    /**
     * Allows to calculate in-real-time the totals of a document
     * https://facturalusa.pt/documentacao/api#recibos-sumario
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function summary($params)
    {
        return $this->facturalusa->request('receipts/summary', 'POST', $params);
    }

    /**
     * Finds a receipt
     * https://facturalusa.pt/documentacao/api#recibos-procurar
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function find($params)
    {
        return $this->facturalusa->request('receipts/find', 'POST', $params);
    }

    /**
     * Lists the receipts
     * https://facturalusa.pt/documentacao/api#recibos-lista
     * 
     * @param   Array   params
     * 
     * @return  Array
     */
    public function list($params = [])
    {
        return $this->facturalusa->request('receipts/list', 'POST', $params);
    }
}