<?php 

class FacturalusaCustomer
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
     * Holds the Facturalusa Customers Database Class
     * 
     * @param   FacturalusaDocumentsDB
     */
    private $facturalusaCustomersDB;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        require_once('FacturalusaHelper.php');
        require_once('database/FacturalusaCustomersDB.php');
        require_once('api/php-facturalusa/src/FacturalusaClient.php');
        require_once('api/php-facturalusa/src/Customer/Customer.php');

        $this->registry = $registry;
        $this->config = $this->registry->get('config'); 
        $this->path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog
        $this->facturalusaClient = new \Facturalusa\FacturalusaClient($this->config->get('module_facturalusa_general_api_token'));
        $this->facturalusaHelper = new FacturalusaHelper($this->registry);
        $this->facturalusaCustomersDB = new FacturalusaCustomersDB($this->registry);
    }
    
    /**
     * Finds the Facturalusa ID associated with the Order Customer
     * 
     * @param   Array   Order data
     * 
     * @return  Mixed   Facturalusa ID|Facturalusa Error
     */
    public function find($order = [])
    {
        $facturalusaId = $this->findById($order);

        // Try to find by email then
        if ($facturalusaId === false)
        {
            $facturalusaId = $this->findByEmail($order['email']);

            // Found by email, but does not exists in Opencart DB, therefore saves
            if ($facturalusaId)
                $this->facturalusaCustomersDB->insert($order['customer_id'], $facturalusaId);
        }

        // Customer was found
        if ($facturalusaId !== false)
        {
            // Should or should not update all the time the customer information
            if ($this->config->get('module_facturalusa_customer_update'))
                $this->update($facturalusaId, $order);

            return $facturalusaId;
        }

        // Time to create a new customer
        $response = $this->create($order);

        if (!$response || $response->status == false)
            return $response->message;

        return $response->data->id;
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
        $customer = $this->facturalusaCustomersDB->get($order['customer_id']);

        // Already in the database
        if ($customer)
            return $customer['facturalusa_id'];

        return false;
    }

    /**
     * Finds a certain customer by email
     * https://facturalusa.pt/documentacao/api#clientes-procurar
     * 
     * @param   String
     * 
     * @return  Mixed
     */
    public function findByEmail($email)
    {
        $facturalusa = new \Facturalusa\FacturalusaClient($this->config->get('module_facturalusa_general_api_token'));
        $customer = (new \Facturalusa\Customer\Customer($facturalusa))->find(['value' => $email, 'search_in' => 'Email']);

        if ($facturalusa->fail())
            return false;

        $rows = $facturalusa->response()->data;

        if (count($rows) <= 0)
            return false;

        // Always the first row
        return $rows[0]->id;
    }

    /**
     * Creates a new customer
     * https://facturalusa.pt/documentacao/api#clientes
     * 
     * @param   Array   Order data
     * 
     * @return  FacturalusaResponse
     */
    public function create($order = [])
    {
        $vatNumber = $this->facturalusaHelper->findVatNumber($order);
        $customerType = $this->facturalusaHelper->findCustomerType($order);
        $addresses = $this->facturalusaHelper->findAddresses($order);
        $paymentMethod = $this->facturalusaHelper->findPaymentMethod($order);

        $code = '';
        $prefix = $this->config->get('module_facturalusa_customer_prefix');
        $vatType = $this->config->get('module_facturalusa_other_vat_type');

        // Adds the prefix to the code if exists
        if ($prefix)
        {
            $code = $prefix . time();

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

        (new \Facturalusa\Customer\Customer($this->facturalusaClient))->create($params);

        if ($this->facturalusaClient->success())
            $this->facturalusaCustomersDB->insert($order['customer_id'], $this->facturalusaClient->response()->data->id);

        return $this->facturalusaClient->response();
    }

    /**
     * Updates an existing customer
     * https://facturalusa.pt/documentacao/api#clientes-actualizar
     * 
     * @param   Integer Facturalusa ID
     * @param   Array   Order data
     * 
     * @return  FacturalusaResponse
     */
    public function update($id, $order = [])
    {
        $vatNumber = $this->facturalusaHelper->findVatNumber($order);
        $customerType = $this->facturalusaHelper->findCustomerType($order);
        $addresses = $this->facturalusaHelper->findAddresses($order);
        $paymentMethod = $this->facturalusaHelper->findPaymentMethod($order);

        $code = '';
        $prefix = $this->config->get('module_facturalusa_customer_prefix');
        $vatType = $this->config->get('module_facturalusa_other_vat_type');

        // Adds the prefix to the code if exists
        if ($prefix)
        {
            $code = $prefix . time();

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

        (new \Facturalusa\Customer\Customer($this->facturalusaClient))->update($id, $params);

        return $this->facturalusaClient->response();
    }
}