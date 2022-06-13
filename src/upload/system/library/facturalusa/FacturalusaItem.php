<?php 

class FacturalusaItem
{
    /**
     * Holds the Opencart Registry class
     * 
     * @param   Object
     */
    private $registry;

    /**
     * Holds the Opencart Config class
     * 
     * @param   Object
     */
    private $config;

    /**
     * Holds the directory path
     * 
     * @param   String
     */
    private $path;

    /**
     * Holds the Facturalusa Client
     * 
     * @param   FacturalusaClient
     */
    private $facturalusaClient;

    /**
     * Holds the Facturalusa Helper
     * 
     * @param   FacturalusaHelper
     */
    private $facturalusaHelper;

    /**
     * Holds the Facturalusa Items Database Class
     * 
     * @param   FacturalusaItemsDB
     */
    private $facturalusaItemsDB;

    /**
     * Holds the model products
     * 
     * @param   ModelCatalogProduct
     */
    private $modelProducts;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        require_once('FacturalusaHelper.php');
        require_once('database/FacturalusaItemsDB.php');
        require_once('api/php-facturalusa/src/FacturalusaClient.php');
        require_once('api/php-facturalusa/src/Item/Item.php');

        $this->registry = $registry;
        $this->config = $this->registry->get('config'); 
        $this->path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog
        $this->facturalusaClient = new \Facturalusa\FacturalusaClient($this->config->get('module_facturalusa_general_api_token'));
        $this->facturalusaHelper = new FacturalusaHelper($this->registry);
        $this->facturalusaItemsDB = new FacturalusaItemsDB($this->registry);
        
        require_once($this->path . 'model/catalog/product.php');

        // Initializes the models old way
        $this->modelProducts = new ModelCatalogProduct($this->registry);
    }

    /**
     * Loops through all order items & creates or returns the existing Facturalusa ID
     * 
     * @param   Array   Order items data
     * 
     * @return  Mixed   Array|Facturalusa error
     */
    public function all($orderItems = [])
    {
        $hasError = false;

        foreach ($orderItems as $key => $orderItem)
        {
            $facturalusaId = $this->find($orderItem);

            if (!is_numeric($facturalusaId))
            {
                $hasError = $facturalusaId;
                break;
            }

            // Just append
            $orderItems[$key]['facturalusa_id'] = $facturalusaId;
        }

        if ($hasError !== false)
            return $hasError;

        return $orderItems;
    }
    
    /**
     * Finds the Facturalusa ID associated with the Order Item
     * 
     * @param   Array   Order item data
     * 
     * @return  Mixed   Facturalusa ID|Facturalusa error
     */
    public function find($orderItem = [])
    {
        $facturalusaId = $this->findById($orderItem);

        // Try to find by reference then
        if ($facturalusaId === false)
        {
            $facturalusaId = $this->findByReference($orderItem['model']);

            // Found by reference, but does not exists in Opencart DB, therefore saves
            if ($facturalusaId)            
                $this->facturalusaItemsDB->insert($orderItem['product_id'], $facturalusaId);
        }

        // Item was found, return the ID
        if ($facturalusaId !== false)
            return $facturalusaId;

        // Nothing has been found, time to create a new item
        $response = $this->create($orderItem);

        if (!$response || $response->status == false)
            return $response->message;

        return $response->data->id;
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
        $item = $this->facturalusaItemsDB->get($orderItem['product_id']);

        // Already in the database
        if ($item)
            return $item['facturalusa_id'];

        return false;
    }

    /**
     * Finds a certain item by reference
     * https://facturalusa.pt/documentacao/api#artigos-procurar
     * 
     * @param   String
     * 
     * @return  Mixed   False|Facturalusa ID
     */
    public function findByReference($reference)
    {
        $facturalusa = new \Facturalusa\FacturalusaClient($this->config->get('module_facturalusa_general_api_token'));
        $item = (new \Facturalusa\Item\Item($facturalusa))->find(['value' => $reference, 'search_in' => 'Reference']);

        if ($facturalusa->fail())
            return false;

        $rows = $facturalusa->response()->data;

        if (count($rows) <= 0)
            return false;

        // Always the first row
        return $rows[0]->id;
    }

    /**
     * Creates a new item
     * https://facturalusa.pt/documentacao/api#artigos
     * 
     * @param   Array   Order item data
     * 
     * @return  FacturalusaResponse
     */
    public function create($orderItem = [])
    {
        $unit = $this->config->get('module_facturalusa_item_unit');
        $vat = $this->config->get('module_facturalusa_item_vat');
        $vatExemption = $this->config->get('module_facturalusa_item_vat_exemption');
        $type = $this->config->get('module_facturalusa_item_type');
        $prefix = $this->config->get('module_facturalusa_item_prefix');

        if (!$unit)
            $unit = 'Unidades';

        if (!$vat)
            $vat = $this->facturalusaItemsDB->findVat($orderItem['product_id']);
            
        $reference = $orderItem['model'];

        // Adds the prefix to the reference if exists
        if ($prefix)
            $reference = $prefix . $orderItem['model'];

        $params = [];
        $params['reference'] = $reference;
        $params['description'] = $orderItem['name'];
        $params['unit'] = $unit;
        $params['vat'] = $vat;
        $params['vat_exemption '] = $vatExemption;
        $params['type'] = $type;

        (new \Facturalusa\Item\Item($this->facturalusaClient))->create($params);

        if ($this->facturalusaClient->success())
            $this->facturalusaItemsDB->insert($orderItem['product_id'], $this->facturalusaClient->response()->data->id);

        return $this->facturalusaClient->response();
    }
}