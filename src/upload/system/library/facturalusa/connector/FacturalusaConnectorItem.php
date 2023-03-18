<?php 

final class FacturalusaConnectorItem
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
        require_once($path . '../../api/php-facturalusa/src/Item/Item.php');

		$this->facturalusaClient = new \Facturalusa\FacturalusaClient($apiToken);
	}
	
	/**
	 * Creates a new item
	 * https://facturalusa.pt/documentacao/api#artigos
	 * 
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function create($params)
	{
		$item = new \Facturalusa\Item\Item($this->facturalusaClient);
		$response = $item->create($params);

		return $response;
	}
	
	/**
	 * Finds a item through reference
	 * https://facturalusa.pt/documentacao/api#artigos-procurar
	 *
	 * @param	String
	 * 
	 * @return	Array
	 */
	public function findByReference($reference)
	{
		$item = new \Facturalusa\Item\Item($this->facturalusaClient);
		$response = $item->find(['value' => $reference, 'search_in' => 'Reference']);

		return $response;
	}
}