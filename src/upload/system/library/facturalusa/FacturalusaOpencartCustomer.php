<?php 

class FacturalusaOpencartCustomer
{
    /**
     * Holds the Opencart Config class
     * 
     * @param   Object
     */
    private $config;

    /**
     * Holds the Opencart Facturalusa Customers Database Class
     * 
     * @param   FacturalusaOpencartCustomersDB
     */
    private $FacturalusaOpencartCustomersDB;

    /**
     * Holds the Facturalusa Connector Customer Class
     * 
     * @param   FacturalusaConnectorCustomer
     */
    private $FacturalusaConnectorCustomer;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {      
        require_once('FacturalusaOpencartHelper.php');
        require_once('connector/FacturalusaConnectorCustomer.php');
        require_once('connector/FacturalusaConnectorHelper.php');
        require_once('database/FacturalusaOpencartCustomersDB.php');

        $this->config = $registry->get('config'); 
        $this->FacturalusaConnectorCustomer = new FacturalusaConnectorCustomer($this->config->get('module_facturalusa_general_api_token'));
        $this->FacturalusaOpencartCustomersDB = new FacturalusaOpencartCustomersDB($registry);
    }
    
    /**
     * Finds the Facturalusa ID associated with the Order Customer. This function is responsible
     * for creating & updating existing customers in Facturalusa.
     * 
     * @param   Array   Order data
     * 
     * @return  Array
     */
    public function find($order = [])
    {
        // First tries to see if the customer was already sync to Facturalusa 
        $facturalusaId = $this->findById($order);

        // Grabs the customer vat number
        $vatNumber = FacturalusaOpencartHelper::getVatNumber($this->config, $order);

        if ($facturalusaId === false)
        {
            // Starts by trying to find the customer through Vat Number
            if ($vatNumber != FacturalusaConnectorHelper::CONSUMIDOR_FINAL)
            {
                $response = $this->FacturalusaConnectorCustomer->findByVatNumber($vatNumber);

                // The findByVatNumber function always returns multiple rows
                if ($response->data && count($response->data) > 0)
                {
                    // Grabs the first result
                    $facturalusaId = $response->data[0]->id;

                    // Found by email, but does not exists in Opencart DB, therefore saves
                    $this->FacturalusaOpencartCustomersDB->insert($order['customer_id'], $facturalusaId);
                }
            }

            // Tries to find through email
            if ($facturalusaId === false)
            {
                $response = $this->FacturalusaConnectorCustomer->findByEmail($order['email']);

                // The «findByEmail» function always returns multiple rows
                if ($response->data && count($response->data) > 0)
                {
                    // Grabs the first result
                    $facturalusaId = $response->data[0]->id;

                    // Found by email, but does not exists in Opencart DB, therefore saves
                    $this->FacturalusaOpencartCustomersDB->insert($order['customer_id'], $facturalusaId);
                }
            }
        }

        // Grabs all the parameters / data from the customer in opencart
        $params = $this->get($order);

        if ($facturalusaId)
        {
            // Should or should not update all the time the customer information
            if ($this->config->get('module_facturalusa_customer_update'))
                $this->FacturalusaConnectorCustomer->createOrUpdate($facturalusaId, $params);

            return [true, $facturalusaId];
        }

        // Creates a new customer in Facturalusa
        $response = $this->FacturalusaConnectorCustomer->createOrUpdate(null, $params);

        // Double checks to see if the customer was well created
        if (!$response || $response->status == false)
            return [false, $response->message];

        // Inserts into DB
        $this->FacturalusaOpencartCustomersDB->insert($order['customer_id'], $response->data->id);

        return [true, $response->data->id];
    }

    /**
     * Finds a certain customer by ID
     * 
     * @param   Array   Order data
     * 
     * @return  Integer
     */
    public function findById($order = [])
    {
        $customer = $this->FacturalusaOpencartCustomersDB->get($order['customer_id']);

        // Already in the database
        if ($customer)
            return $customer['facturalusa_id'];

        return false;
    }

    /**
     * Gets the customer data
     * 
     * @param   Array   Order data
     * 
     * @return  Array
     */
    public function get($order = [])
    {
        $vatNumber = FacturalusaOpencartHelper::getVatNumber($this->config, $order); 
        $customerType = FacturalusaConnectorHelper::getCustomerType($order['payment_firstname'], $vatNumber);  
        $paymentMethod = FacturalusaConnectorHelper::getPaymentMethod($order['payment_code']);   
        $addresses = FacturalusaOpencartHelper::getAddresses($order);
        
        $code = '';
        $prefix = $this->config->get('module_facturalusa_customer_prefix');
        $vatType = $this->config->get('module_facturalusa_other_vat_type');

        // Adds the prefix to the code if exists
        if ($prefix)
        {
            $code = $prefix . time();

            // Guest
            if ($order['customer_id'] && $order['customer_id'] != '0')
                $code = $prefix . $order['customer_id'];
        }

        $params = [];
        $params['code'] = $code;
        $params['name'] = trim($order['firstname'] . ' ' . $order['lastname']);
        $params['vat_number'] = $vatNumber;
        $params['country'] = $addresses['billing']['country'];
        $params['address'] = $addresses['billing']['address'];
        $params['city'] = $addresses['billing']['city'];
        $params['postal_code'] = $addresses['billing']['postal_code'];
        $params['email'] = $order['email'];
        $params['mobile'] = $order['telephone'];
        $params['type'] = $customerType;
        $params['vat_type'] = $vatType;
        $params['payment_method'] = $paymentMethod;
        $params['payment_condition'] = 'Pronto pagamento';
        $params['receive_sms'] = true;
        $params['receive_emails'] = true;

        if ($addresses['delivery'] && $addresses['delivery']['address'])
        {
            $params['addresses'][] = 
            [
                'country' => $addresses['delivery']['country'],
                'address' => $addresses['delivery']['address'],
                'city' => $addresses['delivery']['city'],
                'postal_code' => $addresses['delivery']['postal_code'],
            ];

            $params['addresses'] = json_encode($params['addresses']);
        }

        return $params;
    }
}