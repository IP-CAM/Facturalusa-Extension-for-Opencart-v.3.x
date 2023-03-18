<?php 

/**
 * Facturalusa Opencart Items Database Table Class
 */
class FacturalusaOpencartItemsDB
{   
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
        $path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog

        require_once($path . 'model/extension/module/facturalusa_items.php');
        
        $this->modelFacturalusaItems = new ModelExtensionModuleFacturalusaItems($registry);
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