<?php 

/**
 * Facturalusa Opencart Customers Database Table Class
 */
class FacturalusaOpencartCustomersDB
{   
    /**
     * Holds the model facturalusa items
     * 
     * @param   ModelFacturalusaCustomers
     */
    private $modelFacturalusaCustomers;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        $path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog

        require_once($path . 'model/extension/module/facturalusa_customers.php');
        
        $this->modelFacturalusaCustomers = new ModelExtensionModuleFacturalusaCustomers($registry);
    }

    /**
     * Inserts a new row into DB
     * 
     * @param   Integer
     * @param   Integer
     */
    public function insert($customerId, $facturalusaId)
    {
        $this->modelFacturalusaCustomers->insert($customerId, $facturalusaId);
    }

    /**
     * Returns an existing row in DB
     * 
     * @param   Integer
     */
    public function get($customerId)
    {
        return $this->modelFacturalusaCustomers->get($customerId);
    }
}