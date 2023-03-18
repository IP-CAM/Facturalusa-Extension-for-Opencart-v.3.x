<?php 

class FacturalusaOpencartSale
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
     * Holds the Facturalusa Opencart Sales Database Class
     * 
     * @param   FacturalusaOpencartSalesDB
     */
    private $FacturalusaOpencartSalesDB;

    /**
     * Holds the Facturalusa Connector Sale Class
     * 
     * @param   FacturalusaConnectorSale
     */
    private $FacturalusaConnectorSale;

    /**
     * Holds the model checkout order
     * 
     * @param   ModelCheckoutOrder
     */
    private $modelOrders;

    /**
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        $path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog
        
        require_once('connector/FacturalusaConnectorSale.php');
        require_once('connector/FacturalusaConnectorHelper.php');
        require_once('database/FacturalusaOpencartSalesDB.php');
        require_once('FacturalusaOpencartHelper.php');
        require_once('FacturalusaOpencartCustomer.php');
        require_once('FacturalusaOpencartItem.php');
        require_once($path . 'model/checkout/order.php'); 

        $this->registry = $registry;
        $this->config = $registry->get('config');        
        $this->FacturalusaConnectorSale = new FacturalusaConnectorSale($this->config->get('module_facturalusa_general_api_token'));
        $this->FacturalusaOpencartSalesDB = new FacturalusaOpencartSalesDB($registry); 
        $this->modelOrders = new ModelCheckoutOrder($registry);
    }

    /**
     * Returns if it should create the sale in Facturalusa
     * 
     * @param   Integer
     * 
     * @return  Boolean
     */
    public function shouldCreate($orderId)
    {
        try
        {   
            if (!$this->config->get('module_facturalusa_general_api_token') || !$this->config->get('module_facturalusa_status'))
                return false;

            if (!$this->config->get('module_facturalusa_document_automatic'))
                return false;

            $order = $this->modelOrders->getOrder($orderId);
            $createOrderStatus = $this->config->get('module_facturalusa_document_create_order_status');

            if (!in_array($order['order_status_id'], $createOrderStatus))
                return false;

            $sale = $this->FacturalusaOpencartSalesDB->get($order['order_id']);

            if ($sale && $sale['status'] == 'Terminado')
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            FacturalusaConnectorHelper::log(DIR_SYSTEM . 'storage/logs', $e);

            return false;
        }
    }   

    /**
     * Returns if it should cancel the existing sale in Facturalusa
     * 
     * @param   Integer
     * 
     * @return  Boolean
     */
    public function shouldCancel($orderId)
    {
        try
        {   
            if (!$this->config->get('module_facturalusa_general_api_token') || !$this->config->get('module_facturalusa_status'))
                return false;

            $order = $this->modelOrders->getOrder($orderId);
            $cancelOrderStatus = $this->config->get('module_facturalusa_document_cancel_order_status');

            if (!in_array($order['order_status_id'], $cancelOrderStatus))
                return false;

            $sale = $this->FacturalusaOpencartSalesDB->get($order['order_id']);

            if (!$sale || $sale['status'] == 'Rascunho')
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            FacturalusaConnectorHelper::log(DIR_SYSTEM . 'storage/logs', $e);

            return false;
        }
    }

    /**
     * Creates a new sale in Facturalusa
     * 
     * @param   Integer     Order id
     * 
     * @return  Array
     */
    public function create($orderId)
    {
        $result = ['status' => false, 'message' => 'Não foi possível sincronizar, actualize a página para ver o erro'];

        try
        {       
            // Initializes both Customer & Item for sync purposes
            $customer = new FacturalusaOpencartCustomer($this->registry);
            $item = new FacturalusaOpencartItem($this->registry);

            // Clears initial errors & resets
            $this->FacturalusaOpencartSalesDB->delete($orderId);

            // Grabs the order details
            $order = $this->modelOrders->getOrder($orderId);
            $orderItems = $this->modelOrders->getOrderProducts($orderId);
            $orderTotals = $this->modelOrders->getOrderTotals($orderId);     

            /**
             * Step 1) Finds / creates the customer
             */
            list($status, $customerId) = $customer->find($order);

            if (!$status)
            {
                // $customerId actually holds the string error
                $result['message'] = $customerId;
                $this->FacturalusaOpencartSalesDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o cliente, razão: ' . $customerId]);
                return $result;
            }

            /**
             * Step 2) Finds / creates all the items
             */
            list($status, $orderItems) = $item->all($orderItems);

            if (!$status)
            {
                // $orderItems actually holds the string error
                $result['message'] = $orderItems;
                $this->FacturalusaOpencartSalesDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o artigo, razão: ' . $orderItems]);
                return $result;
            }


            // Defines default content
            $saleType = $this->config->get('module_facturalusa_document_type');
            $serie = $this->config->get('module_facturalusa_document_serie');
            $saleStatus = $this->config->get('module_facturalusa_document_status');
            $paymentMethod = FacturalusaConnectorHelper::getPaymentMethod($order['payment_code']);
            $shipping = FacturalusaOpencartHelper::getShipping($this->config, $orderTotals);
            $vatNumber = FacturalusaOpencartHelper::getVatNumber($this->config, $order);
            $addresses = FacturalusaOpencartHelper::getAddresses($order);
            $voucher = FacturalusaOpencartHelper::getVoucher($orderTotals);
            $language = FacturalusaConnectorHelper::getLanguage($addresses['billing']['country']);
            $decimalPlacesPrices = $this->config->get('module_facturalusa_other_decimal_places_prices');
            $decimalPlacesQuantities = $this->config->get('module_facturalusa_other_decimal_places_quantities');
            $vatType = $this->config->get('module_facturalusa_other_vat_type');
            $forceSendEmail = $this->config->get('module_facturalusa_document_force_send_email');
            $forceSendSms = $this->config->get('module_facturalusa_document_force_send_sms');
            $forceSign = $this->config->get('module_facturalusa_document_force_sign');
            $items = [];

            foreach ($orderItems as $orderItem)
            {
                $price = number_format($orderItem['price'], $decimalPlacesPrices, '.', '');
                $quantity = number_format($orderItem['quantity'], $decimalPlacesQuantities, '.', '');
                $vat = FacturalusaConnectorHelper::getItemVat($orderItem['price'], $orderItem['tax']);
                $details = null;
                $options = $this->modelOrders->getOrderOptions($orderItem['order_id'], $orderItem['order_product_id']);
                $vatExemption = $this->config->get('module_facturalusa_item_vat_exemption');

                // When the vat is higher than zero, we cannot apply any vat exemption
                if ($vat > 0)
                    $vatExemption = 18; // Without exemption

                // Appends the options selected in the product to the details
                if ($options && count($options) > 0)
                {
                    foreach ($options as $option)
                        $details .= $option['value'] . ' / ';

                    $details = rtrim($details, ' / ');
                }

                $items[] = 
                [
                    'id' => $orderItem['facturalusa_id'],
                    'details' => $details,
                    'price' => $price,
                    'quantity' => $quantity,
                    'vat' => $vat,
                    'vat_exemption' => $vatExemption,
                ];
            }

            $params = [];
            $params['issue_date'] = date('Y-m-d');
            $params['document_type'] = $saleType;
            $params['serie'] = $serie;
            $params['customer'] = $customerId;
            $params['vat_number'] = $vatNumber;
            $params['country'] = $addresses['billing']['country'];
            $params['address'] = $addresses['billing']['address'];
            $params['city'] = $addresses['billing']['city'];
            $params['postal_code'] = $addresses['billing']['postal_code'];
            $params['payment_method'] = $paymentMethod;

            if ($addresses['delivery'] && $addresses['delivery']['address'])
            {
                $params['delivery_address_country'] = $addresses['delivery']['country'];
                $params['delivery_address_address'] = $addresses['delivery']['address'];
                $params['delivery_address_city'] = $addresses['delivery']['city'];
                $params['delivery_address_postal_code'] = $addresses['delivery']['postal_code'];

                // Should be filled, but just in case..
                if (!$params['address'])
                {
                    $params['country'] = $addresses['delivery']['country'];
                    $params['address'] = $addresses['delivery']['address'];
                    $params['city'] = $addresses['delivery']['city'];
                    $params['postal_code'] = $addresses['delivery']['postal_code'];
                }
            }

            if ($shipping['value'] > 0)
            {
                $params['shipping_mode'] = $shipping['mode'];
                $params['shipping_value'] = $shipping['value'];
                $params['shipping_vat'] = $shipping['vat'];
            }

            // Applies a percentage voucher to the whole document
            if ($voucher['value'] > 0)
                $params['final_discount_global'] = FacturalusaConnectorHelper::getPercentageDiscount($voucher['net_total'], $voucher['value']);

            $params['vat_type'] = $vatType;
            $params['items'] = $items;
            $params['language'] = $language;
            $params['format'] = 'A4';
            $params['force_print'] = true; // Always, no need to not be always
            $params['force_send_email'] = $forceSendEmail;
            $params['force_send_sms'] = $forceSendSms;
            $params['force_sign'] = $forceSign;
            $params['status'] = $saleStatus;

            /**
             * Step 3) Before creating the document checks the summary
             * 
             * We must grab the summary to avoid creating documents in Facturalusa where the Grand Total is different from the one in OpenCart.
             */
            $response = $this->FacturalusaConnectorSale->summary($params);

            if (!$response->status)
            {
                $this->FacturalusaOpencartSalesDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o documento, razão: ' . $response->message]);
                return $result;
            }

            $grandTotal = number_format($response->data->grand_total, 2, '.', '');
            $orderTotal = 0;

            foreach ($orderTotals as $total)
            {
                if ($total['code'] == 'total')
                {
                    $orderTotal = $total['value'];
                    break;
                }
            }

            $orderTotal = number_format($orderTotal, 2, '.', '');

            /**
             * Step 4) Check if the Grand Total in Facturalusa is different from the one in OpenCart
             */
            if ($grandTotal != $orderTotal)
            {
                $grandTotal = number_format($grandTotal, 2, ',', '');
                $orderTotal = number_format($orderTotal, 2, ',', '');

                $this->FacturalusaOpencartSalesDB->save($orderId, ['order_id' => $orderId, 'error' => "Não foi possível criar o documento, razão: o total geral no Facturalusa ({$grandTotal}€) e no OpenCart ({$orderTotal}€) não são iguais. A má configuração do plugin em termos de taxas de IVA, tipo de IVA, valores de expedição, casas decimais de preços & quantidades, entre outros pode ser a causa."]);
                return $result;
            }

            /**
             * Step 5) Finally creates the document
             */
            $response = $this->FacturalusaConnectorSale->create($params);

            /**
             * Step 6) If anything fails during the document creation, saves
             */
            if (!$response->status)
            {
                $this->FacturalusaOpencartSalesDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o documento, razão: ' . $response->message]);
                return $result;
            }

            $data = $response->data;
            $values = 
            [
                'facturalusa_id' => $data->id,
                'issue_date' => $data->issue_date,
                'number' => $data->number ? $data->number : 0, // Draft mode
                'document_saft_initials' => $data->documenttype->saft_initials,
                'document_description' => $data->documenttype->description,
                'serie' => $data->serie->description,
                'customer_name' => $data->customer_name,
                'customer_vat_number' => $data->customer_vat_number,
                'net_total' => $data->net_total,
                'total_vat' => $data->total_vat,
                'grand_total' => $data->grand_total,
                'status' => $data->status,
                'error' => null, // clears
            ];

            // Step 7) Everything went well, saves the document generated in DB
            $this->FacturalusaOpencartSalesDB->save($orderId, $values);

            // Step 9) Saves the history
            $orderStatusId = $order['order_status_id'];
            $comment = "Sincronizado com o Facturalusa, estado: {$data->status}";
            $this->FacturalusaOpencartSalesDB->addHistory($orderId, $orderStatusId, $comment);

            if ($forceSendEmail)
            {
                $comment = "Enviado email para {$order['email']}";
                $this->FacturalusaOpencartSalesDB->addHistory($orderId, $orderStatusId, $comment);
            }            

            $result = ['status' => true];
        }
        catch (\Exception $e)
        {
            FacturalusaConnectorHelper::log(DIR_SYSTEM . 'storage/logs', $e);
        }

        return $result;
    }

    /**
     * Cancels an existing document in Facturalusa
     * 
     * @param   Integer     Order id
     * 
     * @return  Array
     */
    public function cancel($orderId)
    {
        $result = ['status' => false, 'message' => 'Não foi possível sincronizar, actualize a página para ver o erro'];

        try
        {
            $order = $this->modelOrders->getOrder($orderId);
            $sale = $this->FacturalusaOpencartSalesDB->get($orderId);

            $params = [];
            $params['reason'] = 'Anulado via loja online';
            
            $response = $this->FacturalusaConnectorSale->cancel($sale['facturalusa_id'], $params);

            if (!$response->status)
            {
                $this->update($orderId, ['error' => 'Não foi possível anular o documento, razão:' . $response->message]);
                return $result;
            }

            // Finally saves everything, which means the document was well canceled
            $this->FacturalusaOpencartSalesDB->save($orderId, ['is_canceled' => true]);

            // Saves history as well
            $comment = 'Documento anulado no Facturalusa. Não foi enviado nenhum email ao cliente, deve-o fazer manualmente se for adequado.';
            $this->FacturalusaOpencartSalesDB->addHistory($orderId, $order['order_status_id'], $comment);
            
            $result = ['status' => true];
        }
        catch (\Exception $e)
        {
            FacturalusaConnectorHelper::log(DIR_SYSTEM . 'storage/logs', $e);
        }

        return $result;
    }
    
    /**
     * Downloads an existing document
     * 
     * @param   Integer     Order id
     * 
     * @return  Array
     */
    public function download($orderId)
    {
        $result = ['status' => false, 'message' => 'Não foi possível fazer download', 'url_file' => ''];

        try
        {
            $sale = $this->FacturalusaOpencartSalesDB->get($orderId);
            $params = [];
            
            $response = $this->FacturalusaConnectorSale->download($sale['facturalusa_id'], $params);

            if (!$response->status)
            {
                $result['message'] = $response->message;
                return $result;
            }

            $result = ['status' => true, 'url_file' => $response->url_file];
        }
        catch (\Exception $e)
        {
            FacturalusaConnectorHelper::log(DIR_SYSTEM . 'storage/logs', $e);
        }

        return $result;
    }

    /**
     * Sends through email and existing document
     * 
     * @param   Integer     Order id
     * 
     * @return  Array
     */
    public function sendEmail($orderId)
    {
        $result = ['status' => false, 'message' => 'Não foi possível enviar o email, verfique se o cliente possui um email válido'];

        try
        {
            // Grabs the order details
            $order = $this->modelOrders->getOrder($orderId);

            // Grabs the sale details
            $sale = $this->FacturalusaOpencartSalesDB->get($orderId);

            $params = 
            [
                'to' => json_encode(
                [
                    'name' => $sale['customer_name'],
                    'email' => $order['email'],
                ]),
                'cc' => $this->config->get('module_facturalusa_document_email_copyto'),
            ];

            $response = $this->FacturalusaConnectorSale->sendEmail($sale['facturalusa_id'], $params);

            $emailHasBeenSent = $response->status;

            // Saves history
            if ($emailHasBeenSent)
                $comment = "Enviado email para {$order['email']}";
            else
                $comment = "Não foi possível enviar o email para {$order['email']}";

            $orderStatusId = $order['order_status_id'];
            $this->FacturalusaOpencartSalesDB->addHistory($orderId, $orderStatusId, $comment);

            $result['status'] = $emailHasBeenSent == true;
        }
        catch (\Exception $e)
        {
            FacturalusaConnectorHelper::log(DIR_SYSTEM . 'storage/logs', $e);
        }

        return $result;
    }
}