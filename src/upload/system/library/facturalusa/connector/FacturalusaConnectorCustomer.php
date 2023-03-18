<?php 

final class FacturalusaConnectorCustomer 
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
        require_once($path . '../../api/php-facturalusa/src/Customer/Customer.php');
		
		$this->facturalusaClient = new \Facturalusa\FacturalusaClient($apiToken);
	}
	
	/**
	 * Creates or updates an existing customer
	 * https://facturalusa.pt/documentacao/api#clientes
	 * https://facturalusa.pt/documentacao/api#clientes-actualizar
	 *
	 * @param	Integer
	 * @param	Array
	 * 
	 * @return	Array
	 */
	public function createOrUpdate($id = null, $params)
	{
		$customer = new \Facturalusa\Customer\Customer($this->facturalusaClient);

		if ($id)
			$response = $customer->update($id, $params);
		else
			$response = $customer->create($params);

		return $response;
	}
	
	/**
	 * Finds a customer through email
	 * https://facturalusa.pt/documentacao/api#clientes-procurar
	 *
	 * @param	String
	 * 
	 * @return	Array
	 */
	public function findByEmail($email)
	{
		$customer = new \Facturalusa\Customer\Customer($this->facturalusaClient);
		$response = $customer->find(['value' => $email, 'search_in' => 'Email']);

		return $response;
	}
	
	/**
	 * Finds a customer through vat number
	 * https://facturalusa.pt/documentacao/api#clientes-procurar
	 *
	 * @param	String
	 * 
	 * @return	Array
	 */
	public function findByVatNumber($vatNumber)
	{
		$customer = new \Facturalusa\Customer\Customer($this->facturalusaClient);
		$response = $customer->find(['value' => $vatNumber, 'search_in' => 'Vat Number']);

		return $response;
	}
}