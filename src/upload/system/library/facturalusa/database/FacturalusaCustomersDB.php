<?php 

/**
 * Facturalusa Customers Database Table Class
 */
class FacturalusaCustomersDB
{   
    /**
     * Holds the Opencart Registry class
     * 
     * @param   Object
     */
    private $registry;

    /**
     * Holds the directory path
     * 
     * @param   String
     */
    private $path;

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
        $this->registry = $registry;        
        $this->path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog
        
        require_once($this->path . 'model/extension/module/facturalusa_customers.php');

        // Initializes the models old way
        $this->modelFacturalusaCustomers = new ModelExtensionModuleFacturalusaCustomers($this->registry);
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