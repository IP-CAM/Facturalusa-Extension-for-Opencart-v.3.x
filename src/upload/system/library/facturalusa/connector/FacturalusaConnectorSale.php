<?php 

final class FacturalusaConnectorSale
{
	/**
     * Holds the Facturalusa Client
     * 
     * @param   FacturalusaClient
     */
    private $facturalusaClient;
	
	/**
	 * Constructor function
	 * 
	 * @param	String
	 */
	public function __construct($apiToken)
	{
		$path = realpath(dirname(__FILE__));

		require_once($path . '../../api/php-facturalusa/src/FacturalusaClient.php');
        require_once($path . '../../api/php-facturalusa/src/Sale/Sale.php');
		
		$this->facturalusaClient = new \Facturalusa\FacturalusaClient($apiToken);
	}
	
	/**
	 * Creates a sale
	 * https://facturalusa.pt/documentacao/api#vendas
	 *
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function create($params)
	{
		$sale = new \Facturalusa\Sale\Sale($this->facturalusaClient);
		$response = $sale->create($params);

		return $response;
	}
	
	/**
	 * Cancels an existing sale
	 * https://facturalusa.pt/documentacao/api#vendas-cancelar
	 *
	 * @param	Integer
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function cancel($id, $params)
	{
		$sale = new \Facturalusa\Sale\Sale($this->facturalusaClient);
		$response = $sale->cancel($id, $params);

		return $response;
	}
	
	/**
	 * Downloads an existing sale
	 * https://facturalusa.pt/documentacao/api#vendas-download
	 *
	 * @param	Integer
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function download($id, $params)
	{
		$sale = new \Facturalusa\Sale\Sale($this->facturalusaClient);
		$response = $sale->download($id, $params);

		return $response;
	}
	
	/**
	 * Sends an email of an existing sale
	 * https://facturalusa.pt/documentacao/api#vendas-enviar-email
	 *
	 * @param	Integer
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function sendEmail($id, $params)
	{
		$sale = new \Facturalusa\Sale\Sale($this->facturalusaClient);
		$response = $sale->sendEmail($id, $params);

		return $response;
	}
	
	/**
	 * Downloads the summary
	 * https://facturalusa.pt/documentacao/api#vendas-sumario
	 * 
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function summary($params)
	{
		$sale = new \Facturalusa\Sale\Sale($this->facturalusaClient);
		$response = $sale->summary($params);

		return $response;
	}
}