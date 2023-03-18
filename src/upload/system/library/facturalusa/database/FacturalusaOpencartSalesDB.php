<?php 

/**
 * Facturalusa Opencart Sales Database Table Class
 */
class FacturalusaOpencartSalesDB
{  
    /**
     * Holds the model facturalusa sales
     * 
     * @param   ModelFacturalusaSales
     */
    private $modelFacturalusaSales;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        $path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog

        require_once($path . 'model/extension/module/facturalusa_sales.php');

        $this->modelFacturalusaSales = new ModelExtensionModuleFacturalusaSales($registry);
    }

    /**
     * Saves the information into DB. This function will figure out if it should insert a new row or updating the existing one
     * 
     * @param   Integer 
     * @param   Mixed   Array|FacturalusaResponse
     */
    public function save($orderId, $data)
    {
        $exists = $this->exists($orderId);
        $fields = $this->fields();

        // If already has data, fills the fields
        if ($exists)
            $fields = $this->fill($orderId, $fields);

        $fields = $this->match($fields, $data);
        $fields['order_id'] = $orderId;

        if ($exists)
            $this->update($fields);
        else
            $this->insert($fields);
    }

    /**
     * Inserts a new row into DB
     * 
     * @param   Array
     */
    public function insert($data)
    {
        $this->modelFacturalusaSales->insert($data);
    }

    /**
     * Updates an existing row in DB
     * 
     * @param   Array
     */
    public function update($data)
    {
        $this->modelFacturalusaSales->update($data);
    }

    /**
     * Deletes an existing row in DB
     * 
     * @param   Integer
     */
    public function delete($orderId)
    {
        $this->modelFacturalusaSales->delete($orderId);
    }

    /**
     * Returns an existing row in DB
     * 
     * @param   Integer
     */
    public function get($orderId)
    {
        return $this->modelFacturalusaSales->get($orderId);
    }

    /**
     * Returns if the order already exists in DB
     * 
     * @param   Integer
     * 
     * @return  Boolean
     */
    public function exists($orderId)
    {
        return $this->modelFacturalusaSales->exists($orderId);
    }

    /**
     * Adds the order history
     * 
     * IMPORTANT: We can't re-use the function addOrderHistory() in OrdersModel, because it calls 
     * another model through the default loading $this->load->model and we, at this point, have no access
     * to this call.
     * 
     * @param   Integer
     * @param   Integer
     * @param   String
     */
    public function addHistory($orderId, $orderStatusId, $comment = '')
    {
        return $this->modelFacturalusaSales->addHistory($orderId, $orderStatusId, $comment);
    }

    /**
     * Matches the fields with the data provided
     * 
     * @param   Array
     * @param   Mixed   Array|FacturalusaResponse
     * 
     * @return  Array
     */
    public function match($fields, $data)
    {
        foreach ($data as $key => $value)
            $fields[$key] = $value;

        return $fields;
    }

    /**
     * Fills the fields with the existing data
     * 
     * @param   Integer
     * @param   Array
     * 
     * @return  Array
     */
    public function fill($orderId, $fields)
    {
        $data = $this->get($orderId);
        $data['error'] = ''; // always clears

        return $this->match($fields, $data);
    }

    /**
     * Returns the fields of the database to be filled with default value
     * 
     * @return  Array
     */
    public function fields()
    {
        return 
        [
            'order_id' => '', 
            'facturalusa_id' => 0, 
            'issue_date' => '',
            'number' => 0,
            'document_saft_initials' => '',
            'document_description' => '',
            'serie' => '',
            'customer_name' => '',
            'customer_vat_number' => '',
            'net_total' => 0,
            'total_vat' => 0,
            'grand_total' => 0,
            'status' => 'Rascunho',
            'is_canceled' => false,
            'error' => '',
        ];
    }
}