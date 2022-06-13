<?php 

/**
 * Facturalusa Items Database Table Class
 */
class FacturalusaItemsDB
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
     * @param   ModelFacturalusaItems
     */
    private $modelFacturalusaItems;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        $this->registry = $registry;        
        $this->path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog
        
        require_once($this->path . 'model/extension/module/facturalusa_items.php');

        // Initializes the models old way
        $this->modelFacturalusaItems = new ModelExtensionModuleFacturalusaItems($this->registry);
    }

    /**
     * Inserts a new row into DB
     * 
     * @param   Integer
     * @param   Integer
     */
    public function insert($productId, $facturalusaId)
    {
        $this->modelFacturalusaItems->insert($productId, $facturalusaId);
    }

    /**
     * Returns an existing row in DB
     * 
     * @param   Integer
     */
    public function get($productId)
    {
        return $this->modelFacturalusaItems->get($productId);
    }

    /**
     * Finds the appropriate VAT %
     * 
     * @param   Integer
     */
    public function findVat($productId)
    {
        return $this->modelFacturalusaItems->findVat($productId);
    }
}