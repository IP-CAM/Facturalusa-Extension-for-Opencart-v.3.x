<?php 

/**
 * Facturalusa Document class
 */
class FacturalusaDocument
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
     * Holds the Facturalusa Documents Database Class
     * 
     * @param   FacturalusaDocumentsDB
     */
    private $facturalusaDocumentsDB;

    /**
     * Holds the model orders
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
        require_once('FacturalusaHelper.php');
        require_once('database/FacturalusaDocumentsDB.php');
        require_once('api/php-facturalusa/src/FacturalusaClient.php');
        require_once('api/php-facturalusa/src/Sale/Sale.php');

        $this->registry = $registry;
        $this->config = $this->registry->get('config'); 
        $this->path = defined('DIR_CATALOG') ? DIR_CATALOG : DIR_APPLICATION; // Because it's being called in admin & catalog
        $this->facturalusaClient = new \Facturalusa\FacturalusaClient($this->config->get('module_facturalusa_general_api_token'));
        $this->facturalusaHelper = new FacturalusaHelper($this->registry);
        $this->facturalusaDocumentsDB = new FacturalusaDocumentsDB($this->registry);
        
        require_once($this->path . 'model/checkout/order.php');

        // Initializes the models old way
        $this->modelOrders = new ModelCheckoutOrder($this->registry);
    }

    /**
     * Returns if it should create the document in Facturalusa
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

            $order = $this->modelOrders->getOrder($orderId);
            $createOrderStatus = $this->config->get('module_facturalusa_document_create_order_status');

            if (!in_array($order['order_status_id'], $createOrderStatus))
                return false;

            $document = $this->facturalusaDocumentsDB->get($orderId);

            if ($document && $document['status'] == 'Terminado')
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            $this->facturalusaHelper->log($e);

            return false;
        }
    }   

    /**
     * Returns if it should cancel the existing document in Facturalusa
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

            $document = $this->facturalusaDocumentsDB->get($orderId);

            if (!$document || $document['status'] == 'Rascunho')
                return false;

            return true;
        }
        catch (\Exception $e)
        {
            $this->facturalusaHelper->log($e);

            return false;
        }
    }

    /**
     * Creates a new document in Facturalusa
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
            require_once('FacturalusaCustomer.php');
            require_once('FacturalusaItem.php');

            $order = $this->modelOrders->getOrder($orderId);
            $orderItems = $this->modelOrders->getOrderProducts($orderId);
            $orderTotals = $this->modelOrders->getOrderTotals($orderId);

            $customer = new FacturalusaCustomer($this->registry);
            $item = new FacturalusaItem($this->registry);

            $documentType = $this->config->get('module_facturalusa_document_type');
            $serie = $this->config->get('module_facturalusa_document_serie');
            $documentStatus = $this->config->get('module_facturalusa_document_status');
            $customerId = $customer->find($order);
            $paymentMethod = $this->facturalusaHelper->findPaymentMethod($order);
            $shipping = $this->facturalusaHelper->findShipping($orderTotals);
            $vatNumber = $this->facturalusaHelper->findVatNumber($order);
            $addresses = $this->facturalusaHelper->findAddresses($order);
            $coupon = $this->facturalusaHelper->findCoupon($orderTotals);
            $language = $this->facturalusaHelper->findLanguage($order);
            $decimalPlacesPrices = $this->config->get('module_facturalusa_other_decimal_places_prices');
            $decimalPlacesQuantities = $this->config->get('module_facturalusa_other_decimal_places_quantities');
            $vatType = $this->config->get('module_facturalusa_other_vat_type');
            $sendEmailThrough = $this->config->get('module_facturalusa_document_send_email_through');
            $forceSendEmail = $this->config->get('module_facturalusa_document_force_send_email');
            $forceSendSms = $this->config->get('module_facturalusa_document_force_send_sms');
            $forceSign = $this->config->get('module_facturalusa_document_force_sign');
            
            // Re-updates the variable with new data
            $orderItems = $item->all($orderItems);
            $items = [];

            /**
             * Step 1) Checks if the customer ID exists / was well created
             * 
             * If it's not an number, it's a Facturalusa Response Message Error
             */
            if (!is_numeric($customerId))
            {
                $this->facturalusaDocumentsDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o cliente, razão: ' . $customerId]);
                return $result;
            }

            /**
             * Step 2) Checks if all the items ID exists / were well created
             * 
             * If it's not an array, it's a Facturalusa Response Message Error
             */
            if (!is_array($orderItems))
            {
                $this->facturalusaDocumentsDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o artigo, razão: ' . $orderItems]);
                return $result;
            }

            foreach ($orderItems as $orderItem)
            {
                $price = number_format($orderItem['price'], $decimalPlacesPrices, '.', '');
                $quantity = number_format($orderItem['quantity'], $decimalPlacesQuantities, '.', '');
                $vat = $this->facturalusaHelper->findVatForItem($orderItem);

                $items[] = 
                [
                    'id' => $orderItem['facturalusa_id'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'vat' => $vat
                ];
            }

            $params = [];
            $params['issue_date'] = date('Y-m-d');
            $params['document_type'] = $documentType;
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

            // Applies a percentage coupon to the whole document
            if ($coupon > 0)
                $params['final_discount_global'] = $coupon;

            $params['vat_type'] = $vatType;
            $params['items'] = $items;
            $params['language'] = $language;
            $params['format'] = 'A4';
            $params['force_print'] = true; // Always, no need to not be always
            $params['force_send_email'] = $forceSendEmail && $sendEmailThrough == 'Facturalusa';
            $params['force_send_sms'] = $forceSendSms;
            $params['force_sign'] = $forceSign;
            $params['status'] = $documentStatus;

            /**
             * Step 3) Before creating the document checks the summary
             * 
             * We must grab the summary to avoid creating documents in Facturalusa where the Grand Total is different from the one in OpenCart.
             */
            $response = $this->summary($params);

            if ($response->fail())
            {
                $this->facturalusaDocumentsDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o documento, razão: ' . $response->message]);
                return $result;
            }

            $grandTotal = number_format($response->response()->data->grand_total, 2, '.', '');
            $orderTotal = 0;

            foreach ($orderTotals as $total)
            {
                if ($total['code'] == 'total')
                    $orderTotal = $total['value'];
            }

            $orderTotal = number_format($orderTotal, 2, '.', '');

            /**
             * Step 4) Check if the Grand Total in Facturalusa is different from the one in OpenCart
             */
            if ($grandTotal != $orderTotal)
            {
                $this->facturalusaDocumentsDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o documento, razão: o total geral no Facturalusa e no OpenCart não são iguais. A má configuração do plugin em termos de taxas de IVA, valores de expedição, casas decimais de preços & quantidades, entre outros pode ser a causa.']);
                return $result;
            }

            /**
             * Step 5) Finally creates the document
             */
            (new \Facturalusa\Sale\Sale($this->facturalusaClient))->create($params);

            $response = $this->facturalusaClient;

            /**
             * Step 6) If anything fails during the document creation, saves
             */
            if ($response->fail())
            {
                $this->facturalusaDocumentsDB->save($orderId, ['order_id' => $orderId, 'error' => 'Não foi possível criar o documento, razão: ' . $response->response()->message]);
                return $result;
            }

            $data = $response->response()->data;
            $values = 
            [
                'facturalusa_id' => $data->id,
                'issue_date' => $data->issue_date,
                'number' => $data->number,
                'document_saft_initials' => $data->documenttype->saft_initials,
                'document_description' => $data->documenttype->description,
                'serie' => $data->serie->description,
                'customer_name' => $data->customer_name,
                'customer_vat_number' => $data->customer_vat_number,
                'net_total' => $data->net_total,
                'total_vat' => $data->total_vat,
                'grand_total' => $data->grand_total,
                'status' => $data->status,
            ];

            /**
             * Step 7) Everything went well, saves the document generated in DB
             */
            $this->facturalusaDocumentsDB->save($orderId, $values);

            // Saves if the email has been sent & if should have sent the email
            $emailHasBeenSent = $data->status == 'Terminado' && $forceSendEmail;
            $shouldHaveSentEmail = $emailHasBeenSent;

            /**
             * Step 8) Send the email through OpenCart
             * 
             * Although this is not an advisable solution, users have the possibility of sending the invoice to customer through Opencart.
             * This option should not be used by people with low knowledge of IT, since this might lead to some problems, such as lack of 
             * configurations or just badly configured the email setting.
             * 
             * Usually people will select "force_send_email" and send it through Facturalusa.
             */
            if ($forceSendEmail && $sendEmailThrough == 'OpenCart' && $data->status == 'Terminado')
            {
                $emailHasBeenSent = $this->sendEmailThroughOpenCart($order, $data);
                $shouldHaveSentEmail = true;
            }

            /**
             * Step 9) Saves the history
             */
            $orderStatusId = $order['order_status_id'];
            $comment = "Sincronizado com o Facturalusa, estado: {$data->status}.";
            
            // Saves if the email has been or sent
            if ($shouldHaveSentEmail)
            {
                if ($emailHasBeenSent)
                    $comment .= ' Enviado email para ' . $order['email'];
                else
                    $comment .= ' Não foi possível enviar o email para ' . $order['email'] . '. Verifique as configurações de email.';
            }

            $this->facturalusaDocumentsDB->addHistory($orderId, $orderStatusId, $comment);

            $result = ['status' => true];
        }
        catch (\Exception $e)
        {
            $this->facturalusaHelper->log($e);
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
            $document = $this->facturalusaDocumentsDB->get($orderId);

            $params = [];
            $params['reason'] = 'Anulado via loja online';
            
            (new \Facturalusa\Sale\Sale($this->facturalusaClient))->cancel($document['facturalusa_id'], $params);

            $response = $this->facturalusaClient->response();

            if ($this->facturalusaClient->fail())
            {
                $this->update($orderId, ['error' => 'Não foi possível anular o documento, razão:' . $response->message]);
                return $result;
            }

            // Finally saves everything, which means the document was well canceled
            $this->facturalusaDocumentsDB->save($orderId, ['is_canceled' => true]);

            // Saves history as well
            $comment = 'Documento anulado no Facturalusa. Não foi enviado nenhum email ao cliente, deve-o fazer manualmente se for adequado.';
            $this->facturalusaDocumentsDB->addHistory($orderId, $order['order_status_id'], $comment);
            
            $result = ['status' => true];
        }
        catch (\Exception $e)
        {
            $this->facturalusaHelper->log($e);
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
            $document = $this->facturalusaDocumentsDB->get($orderId);
            $params = [];
            
            (new \Facturalusa\Sale\Sale($this->facturalusaClient))->download($document['facturalusa_id'], $params);

            $response = $this->facturalusaClient->response();

            if ($this->facturalusaClient->fail())
            {
                $result['message'] = $response->message;
                return $result;
            }

            $result = ['status' => true, 'url_file' => $response->url_file];
        }
        catch (\Exception $e)
        {
            $this->facturalusaHelper->log($e);
        }

        return $result;
    }

    /**
     * Returns the summary
     * 
     * @param   Array
     * 
     * @return  FacturalusaClient
     */
    public function summary($params)
    {
        (new \Facturalusa\Sale\Sale($this->facturalusaClient))->summary($params);

        return $this->facturalusaClient;
    }
    
    /**
     * Sends the email to the customer through Opencart
     * 
     * @param   Array   Order data
     * @param   FacturalusaResponse
     */
    public function sendEmailThroughOpenCart($order = [], $data)
    {
        try
        {
            $language = $this->facturalusaHelper->findLanguage($order);
            $subject = $this->config->get('module_facturalusa_send_email_pt_subject');
            $message = $this->config->get('module_facturalusa_send_email_pt_message');
            $cc = $this->config->get('module_facturalusa_document_email_copyto');
    
            if ($language == 'EN')
            {
                $subject = $this->config->get('module_facturalusa_send_email_en_subject');
                $message = $this->config->get('module_facturalusa_send_email_en_message');
            }

            $replace =
            [
                '@data_emissao' => $data->issue_date,
                '@cliente' => $data->customer_name,
                '@contribuinte' => $data->customer_vat_number,
                '@documento' => $data->documenttype->description . ' ' . $data->serie->description . '/' . $data->number,
                '@total_geral' => number_format($data->grand_total, 2, ',', ' ') . $data->currency->symbol,
            ];

            // Replaces the text with the "@" keys defined 
            $subject = str_replace(array_keys($replace), array_values($replace), $subject);
            $message = str_replace(array_keys($replace), array_values($replace), $message);

            $content = [];
            $content['title'] = $order['store_name'];
            $content['store_name'] = $order['store_name'];
		    $content['store_url'] = $order['store_url'];
            $content['logo'] = $order['store_url'] . 'image/' . $this->config->get('config_logo');
            $content['message'] = $message;

            /**
             * The Loader Object
             * https://code.tutsplus.com/tutorials/understand-registry-and-loader-objects-in-opencart--cms-23702
             * 
             * "
             *  The "Loader" object is used to load the different components of OpenCart as required, like model, controller, language, view, library, etc. 
             *  It's important to note here that when the "Loader" object is created, it is stored in the $registry object with "load" as an array key. 
             *  So you can access the $loader object by using a $this­->load call as explained in the above section.
             * "
             * 
             * However, since we are using a custom OpenCart class, we can't use the default convention "$this->registry->load nor $this->load" as the article points.
             * Also, the view is actually defined in the SYSTEM path. In order to make this work, we use the default configuration of twig.
             */
            $loader = new \Twig\Loader\FilesystemLoader(DIR_SYSTEM . 'library/facturalusa/view');
            $twig = new \Twig\Environment($loader);
            $view = $twig->render('facturalusa_document.twig', $content);

            file_put_contents(DIR_SYSTEM . 'storage/logs/x.txt', $view, FILE_APPEND);

            $mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setTo($order['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order['store_name']);
			$mail->setSubject($subject);
			$mail->setHtml($view);
            $mail->setBcc($cc);
			$mail->addAttachment($data->url_file);
			$sent = $mail->send();

            return $sent;
        }
        catch (\Exception $e)
        {
            $this->facturalusaHelper->log($e);
        }

        return false;
    }
}