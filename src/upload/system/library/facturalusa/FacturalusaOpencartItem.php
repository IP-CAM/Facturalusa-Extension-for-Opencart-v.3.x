<?php 

class FacturalusaOpencartItem
{
    /**
     * Holds the Opencart Config class
     * 
     * @param   Object
     */
    private $config;

    /**
     * Holds the Opencart Facturalusa Items Database Class
     * 
     * @param   FacturalusaOpencartItemsDB
     */
    private $FacturalusaOpencartItemsDB;

    /**
     * Holds the Facturalusa Connector Item Class
     * 
     * @param   FacturalusaConnectorItem
     */
    private $FacturalusaConnectorItem;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        $path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog

        require_once('connector/FacturalusaConnectorItem.php');
        require_once('database/FacturalusaOpencartItemsDB.php');
        require_once($path . 'model/checkout/order.php');
        
        $this->config = $registry->get('config'); 
        $this->FacturalusaConnectorItem = new FacturalusaConnectorItem($this->config->get('module_facturalusa_general_api_token'));
        $this->FacturalusaOpencartItemsDB = new FacturalusaOpencartItemsDB($registry);
    }

    /**
     * Loops through all order items & creates or returns the existing Facturalusa ID
     * 
     * @param   Array   Order items data
     * 
     * @return  Array
     */
    public function all($orderItems = [])
    {
        foreach ($orderItems as $key => $orderItem)
        {
            list($status, $itemId) = $this->find($orderItem);

            if (!$status)
                return [$status, $itemId];

            // Just append
            $orderItems[$key]['facturalusa_id'] = $itemId;
        }

        return [true, $orderItems];
    }
    
    /**
     * Finds the Facturalusa ID associated with the Order Item
     * 
     * @param   Array   Order item data
     * 
     * @return  Array
     */
    public function find($orderItem = [])
    {
        // First tries to see if the item was already sync to Facturalusa 
        $facturalusaId = $this->findById($orderItem);

        if ($facturalusaId !== false)
            return [true, $facturalusaId];

        // Try to find by reference then
        $response = $this->FacturalusaConnectorItem->findByReference($orderItem['model']);

        // The findByReference function always returns multiple rows
        if ($response->data && count($response->data) == 1)
        {
            // Grabs the first result
            $facturalusaId = $response->data[0]->id;

            // Found by reference, but does not exists in Opencart DB, therefore saves
            $this->FacturalusaOpencartItemsDB->insert($orderItem['product_id'], $facturalusaId);

            return [true, $facturalusaId];
        }

        $params = $this->get($orderItem);

        // Creates the item in Facturalusa
        $response = $this->FacturalusaConnectorItem->create($params);

        // Double checks to see if the item was well created
        if (!$response || $response->status == false)
            return [false, $response->message];

        // Found by reference, but does not exists in Opencart DB, therefore saves
        $this->FacturalusaOpencartItemsDB->insert($orderItem['product_id'], $response->data->id);

        return [true, $response->data->id];
    }

    /**
     * Finds a certain item by ID
     * 
     * @param   Array   Order item data
     * 
     * @return  Mixed   False|Facturalusa ID
     */
    public function findById($orderItem = [])
    {
        $item = $this->FacturalusaOpencartItemsDB->get($orderItem['product_id']);

        // Already in the database
        if ($item)
            return $item['facturalusa_id'];

        return false;
    }

    /**
     * Gets the item data
     * 
     * @param   Array   Order item data
     * 
     * @return  Array
     */
    public function get($orderItem = [])
    {
        $unit = $this->config->get('module_facturalusa_item_unit');
        $vat = $this->config->get('module_facturalusa_item_vat');
        $vatExemption = $this->config->get('module_facturalusa_item_vat_exemption');
        $type = $this->config->get('module_facturalusa_item_type');
        $prefix = $this->config->get('module_facturalusa_item_prefix');

        if (!$unit)
            $unit = 'Unidades';

        if (!$vat)
            $vat = $this->FacturalusaOpencartItemsDB->findVat($orderItem['product_id']);
            
        $reference = $orderItem['model'];

        // Adds the prefix to the reference if exists
        if ($prefix)
            $reference = $prefix . $orderItem['model'];

        $params = [];
        $params['reference'] = $reference;
        $params['description'] = $orderItem['name'];
        $params['unit'] = $unit;
        $params['vat'] = $vat;
        $params['vat_exemption'] = $vatExemption;
        $params['type'] = $type;

        return $params;
    }
}