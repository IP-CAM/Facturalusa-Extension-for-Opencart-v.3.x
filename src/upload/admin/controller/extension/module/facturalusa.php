<?php 

class ControllerExtensionModuleFacturalusa extends Controller 
{
    /**
     * Holds the errors in Validation process
     * 
     * @var Array
     */
    private $error = [];

    /**
     * Shows the settings page of Facturalusa
     * 
     * @return  Json
     */
    public function index()
    {
        $this->load->language('extension/module/facturalusa');
        $this->load->model('setting/setting');

        $this->document->setTitle('Facturalusa - Configurações');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate())
        {
            $this->model_setting_setting->editSetting('module_facturalusa', $this->request->post);
            $this->session->data['success'] = 'Actualizado com sucesso!';
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

		$data['breadcrumbs'] = 
        [
            [
                'text' => $this->language->get('text_home'),
			    'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('text_extension'),
			    'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/facturalusa', 'user_token=' . $this->session->data['user_token'], true)
            ],
        ];

        if (isset($this->request->post['module_facturalusa_status']))
            $data['module_facturalusa_status'] = $this->request->post['module_facturalusa_status'];
        else
            $data['module_facturalusa_status'] = $this->config->get('module_facturalusa_status');

        if (isset($this->request->post['module_facturalusa_general_api_token']))
            $data['module_facturalusa_general_api_token'] = $this->request->post['module_facturalusa_general_api_token'];
        elseif ($this->config->get('module_facturalusa_general_api_token'))
            $data['module_facturalusa_general_api_token'] = $this->config->get('module_facturalusa_general_api_token');
        else
            $data['module_facturalusa_general_api_token'] = ''; 

        if (isset($this->request->post['module_facturalusa_document_type']))
            $data['module_facturalusa_document_type'] = $this->request->post['module_facturalusa_document_type'];
        elseif ($this->config->get('module_facturalusa_document_type'))
            $data['module_facturalusa_document_type'] = $this->config->get('module_facturalusa_document_type');
        else
            $data['module_facturalusa_document_type'] = 'Factura Recibo';

        if (isset($this->request->post['module_facturalusa_document_serie']))
            $data['module_facturalusa_document_serie'] = $this->request->post['module_facturalusa_document_serie'];
        elseif ($this->config->get('module_facturalusa_document_serie'))
            $data['module_facturalusa_document_serie'] = $this->config->get('module_facturalusa_document_serie');
        else
            $data['module_facturalusa_document_serie'] = '';

        if (isset($this->request->post['module_facturalusa_document_status']))
            $data['module_facturalusa_document_status'] = $this->request->post['module_facturalusa_document_status'];
        elseif ($this->config->get('module_facturalusa_document_status'))
            $data['module_facturalusa_document_status'] = $this->config->get('module_facturalusa_document_status');
        else
            $data['module_facturalusa_document_status'] = 'Terminado';

        if (isset($this->request->post['module_facturalusa_document_automatic']))
            $data['module_facturalusa_document_automatic'] = $this->request->post['module_facturalusa_document_automatic'];
        else
            $data['module_facturalusa_document_automatic'] = $this->config->get('module_facturalusa_document_automatic');

        if (isset($this->request->post['module_facturalusa_document_create_order_status']))
            $data['module_facturalusa_document_create_order_status'] = $this->request->post['module_facturalusa_document_create_order_status'];
        elseif ($this->config->get('module_facturalusa_document_create_order_status'))
            $data['module_facturalusa_document_create_order_status'] = $this->config->get('module_facturalusa_document_create_order_status');
        else
            $data['module_facturalusa_document_create_order_status'] = '';

        if (isset($this->request->post['module_facturalusa_document_cancel_order_status']))
            $data['module_facturalusa_document_cancel_order_status'] = $this->request->post['module_facturalusa_document_cancel_order_status'];
        elseif ($this->config->get('module_facturalusa_document_cancel_order_status'))
            $data['module_facturalusa_document_cancel_order_status'] = $this->config->get('module_facturalusa_document_cancel_order_status');
        else
            $data['module_facturalusa_document_cancel_order_status'] = '';

        if (isset($this->request->post['module_facturalusa_document_force_send_email']))
            $data['module_facturalusa_document_force_send_email'] = $this->request->post['module_facturalusa_document_force_send_email'];
        else
            $data['module_facturalusa_document_force_send_email'] = $this->config->get('module_facturalusa_document_force_send_email');

        if (isset($this->request->post['module_facturalusa_document_force_send_sms']))
            $data['module_facturalusa_document_force_send_sms'] = $this->request->post['module_facturalusa_document_force_send_sms'];
        else
            $data['module_facturalusa_document_force_send_sms'] = $this->config->get('module_facturalusa_document_force_send_sms');

        if (isset($this->request->post['module_facturalusa_document_force_sign']))
            $data['module_facturalusa_document_force_sign'] = $this->request->post['module_facturalusa_document_force_sign'];
        else
            $data['module_facturalusa_document_force_sign'] = $this->config->get('module_facturalusa_document_force_sign');

        if (isset($this->request->post['module_facturalusa_document_send_email_through']))
            $data['module_facturalusa_document_send_email_through'] = $this->request->post['module_facturalusa_document_send_email_through'];
        elseif ($this->config->get('module_facturalusa_document_send_email_through'))
            $data['module_facturalusa_document_send_email_through'] = $this->config->get('module_facturalusa_document_send_email_through');
        else
            $data['module_facturalusa_document_send_email_through'] = '';

        if (isset($this->request->post['module_facturalusa_document_email_copyto']))
            $data['module_facturalusa_document_email_copyto'] = $this->request->post['module_facturalusa_document_email_copyto'];
        elseif ($this->config->get('module_facturalusa_document_email_copyto'))
            $data['module_facturalusa_document_email_copyto'] = $this->config->get('module_facturalusa_document_email_copyto');
        else
            $data['module_facturalusa_document_email_copyto'] = '';

        if (isset($this->request->post['module_facturalusa_document_language']))
            $data['module_facturalusa_document_language'] = $this->request->post['module_facturalusa_document_language'];
        elseif ($this->config->get('module_facturalusa_document_language'))
            $data['module_facturalusa_document_language'] = $this->config->get('module_facturalusa_document_language');
        else
            $data['module_facturalusa_document_language'] = 'Auto';

        if (isset($this->request->post['module_facturalusa_customer_update']))
            $data['module_facturalusa_customer_update'] = $this->request->post['module_facturalusa_customer_update'];
        else
            $data['module_facturalusa_customer_update'] = $this->config->get('module_facturalusa_customer_update');

        if (isset($this->request->post['module_facturalusa_customer_prefix']))
            $data['module_facturalusa_customer_prefix'] = $this->request->post['module_facturalusa_customer_prefix'];
        elseif ($this->config->get('module_facturalusa_customer_prefix'))
            $data['module_facturalusa_customer_prefix'] = $this->config->get('module_facturalusa_customer_prefix');
        else
            $data['module_facturalusa_customer_prefix'] = '';

        if (isset($this->request->post['module_facturalusa_customer_vatnumber_field']))
            $data['module_facturalusa_customer_vatnumber_field'] = $this->request->post['module_facturalusa_customer_vatnumber_field'];
        elseif ($this->config->get('module_facturalusa_customer_vatnumber_field'))
            $data['module_facturalusa_customer_vatnumber_field'] = $this->config->get('module_facturalusa_customer_vatnumber_field');
        else
            $data['module_facturalusa_customer_vatnumber_field'] = '';

        if (isset($this->request->post['module_facturalusa_item_create']))
            $data['module_facturalusa_item_create'] = $this->request->post['module_facturalusa_item_create'];
        else
            $data['module_facturalusa_item_create'] = $this->config->get('module_facturalusa_item_create');

        if (isset($this->request->post['module_facturalusa_item_prefix']))
            $data['module_facturalusa_item_prefix'] = $this->request->post['module_facturalusa_item_prefix'];
        elseif ($this->config->get('module_facturalusa_item_prefix'))
            $data['module_facturalusa_item_prefix'] = $this->config->get('module_facturalusa_item_prefix');
        else
            $data['module_facturalusa_item_prefix'] = '';

        if (isset($this->request->post['module_facturalusa_item_type']))
            $data['module_facturalusa_item_type'] = $this->request->post['module_facturalusa_item_type'];
        elseif ($this->config->get('module_facturalusa_item_type'))
            $data['module_facturalusa_item_type'] = $this->config->get('module_facturalusa_item_type');
        else
            $data['module_facturalusa_item_type'] = 'Mercadorias';

        if (isset($this->request->post['module_facturalusa_item_unit']))
            $data['module_facturalusa_item_unit'] = $this->request->post['module_facturalusa_item_unit'];
        elseif ($this->config->get('module_facturalusa_item_unit'))
            $data['module_facturalusa_item_unit'] = $this->config->get('module_facturalusa_item_unit');
        else
            $data['module_facturalusa_item_unit'] = '';

        if (isset($this->request->post['module_facturalusa_item_vat']))
            $data['module_facturalusa_item_vat'] = $this->request->post['module_facturalusa_item_vat'];
        elseif ($this->config->get('module_facturalusa_item_vat') !== '' && !is_null($this->config->get('module_facturalusa_item_vat')))
            $data['module_facturalusa_item_vat'] = $this->config->get('module_facturalusa_item_vat');
        else
            $data['module_facturalusa_item_vat'] = '';

        if ($data['module_facturalusa_item_vat'])
            $data['module_facturalusa_item_vat'] = str_replace(',', '.', $data['module_facturalusa_item_vat']);

        if (isset($this->request->post['module_facturalusa_item_vat_exemption']))
            $data['module_facturalusa_item_vat_exemption'] = $this->request->post['module_facturalusa_item_vat_exemption'];
        elseif ($this->config->get('module_facturalusa_item_vat_exemption'))
            $data['module_facturalusa_item_vat_exemption'] = $this->config->get('module_facturalusa_item_vat_exemption');
        else
            $data['module_facturalusa_item_vat_exemption'] = '18';

        if (isset($this->request->post['module_facturalusa_other_vat_type']))
            $data['module_facturalusa_other_vat_type'] = $this->request->post['module_facturalusa_other_vat_type'];
        elseif ($this->config->get('module_facturalusa_other_vat_type'))
            $data['module_facturalusa_other_vat_type'] = $this->config->get('module_facturalusa_other_vat_type');
        else
            $data['module_facturalusa_other_vat_type'] = 'IVA incluído';

        if (isset($this->request->post['module_facturalusa_other_location_origin']))
            $data['module_facturalusa_other_location_origin'] = $this->request->post['module_facturalusa_other_location_origin'];
        elseif ($this->config->get('module_facturalusa_other_location_origin'))
            $data['module_facturalusa_other_location_origin'] = $this->config->get('module_facturalusa_other_location_origin');
        else
            $data['module_facturalusa_other_location_origin'] = '';

        if (isset($this->request->post['module_facturalusa_other_shipping_name']))
            $data['module_facturalusa_other_shipping_name'] = $this->request->post['module_facturalusa_other_shipping_name'];
        elseif ($this->config->get('module_facturalusa_other_shipping_name'))
            $data['module_facturalusa_other_shipping_name'] = $this->config->get('module_facturalusa_other_shipping_name');
        else
            $data['module_facturalusa_other_shipping_name'] = '';

        if (isset($this->request->post['module_facturalusa_other_shipping_vat']))
            $data['module_facturalusa_other_shipping_vat'] = $this->request->post['module_facturalusa_other_shipping_vat'];
        elseif ($this->config->get('module_facturalusa_other_shipping_vat') !== '' && !is_null($this->config->get('module_facturalusa_other_shipping_vat')))
            $data['module_facturalusa_other_shipping_vat'] = (string)$this->config->get('module_facturalusa_other_shipping_vat');
        else
            $data['module_facturalusa_other_shipping_vat'] = '';

        if ($data['module_facturalusa_other_shipping_vat'])
            $data['module_facturalusa_other_shipping_vat'] = str_replace(',', '.', $data['module_facturalusa_other_shipping_vat']);

        if (isset($this->request->post['module_facturalusa_other_decimal_places_prices']))
            $data['module_facturalusa_other_decimal_places_prices'] = $this->request->post['module_facturalusa_other_decimal_places_prices'];
        elseif ($this->config->get('module_facturalusa_other_decimal_places_prices'))
            $data['module_facturalusa_other_decimal_places_prices'] = $this->config->get('module_facturalusa_other_decimal_places_prices');
        else
            $data['module_facturalusa_other_decimal_places_prices'] = 2;

        if (isset($this->request->post['module_facturalusa_other_decimal_places_quantities']))
            $data['module_facturalusa_other_decimal_places_quantities'] = $this->request->post['module_facturalusa_other_decimal_places_quantities'];
        elseif ($this->config->get('module_facturalusa_other_decimal_places_quantities'))
            $data['module_facturalusa_other_decimal_places_quantities'] = $this->config->get('module_facturalusa_other_decimal_places_quantities');
        else
            $data['module_facturalusa_other_decimal_places_quantities'] = 2;

        if (isset($this->request->post['module_facturalusa_send_email_pt_subject']))
            $data['module_facturalusa_send_email_pt_subject'] = $this->request->post['module_facturalusa_send_email_pt_subject'];
        elseif ($this->config->get('module_facturalusa_send_email_pt_subject'))
            $data['module_facturalusa_send_email_pt_subject'] = $this->config->get('module_facturalusa_send_email_pt_subject');
        else
            $data['module_facturalusa_send_email_pt_subject'] = '';

        if (isset($this->request->post['module_facturalusa_send_email_pt_message']))
            $data['module_facturalusa_send_email_pt_message'] = $this->request->post['module_facturalusa_send_email_pt_message'];
        elseif ($this->config->get('module_facturalusa_send_email_pt_message'))
            $data['module_facturalusa_send_email_pt_message'] = $this->config->get('module_facturalusa_send_email_pt_message');
        else
            $data['module_facturalusa_send_email_pt_message'] = '';

        if (isset($this->request->post['module_facturalusa_send_email_en_subject']))
            $data['module_facturalusa_send_email_en_subject'] = $this->request->post['module_facturalusa_send_email_en_subject'];
        elseif ($this->config->get('module_facturalusa_send_email_en_subject'))
            $data['module_facturalusa_send_email_en_subject'] = $this->config->get('module_facturalusa_send_email_en_subject');
        else
            $data['module_facturalusa_send_email_en_subject'] = '';

        if (isset($this->request->post['module_facturalusa_send_email_en_message']))
            $data['module_facturalusa_send_email_en_message'] = $this->request->post['module_facturalusa_send_email_en_message'];
        elseif ($this->config->get('module_facturalusa_send_email_en_message'))
            $data['module_facturalusa_send_email_en_message'] = $this->config->get('module_facturalusa_send_email_en_message');
        else
            $data['module_facturalusa_send_email_en_message'] = '';
        
        $this->load->model('localisation/order_status');
        $this->load->model('customer/custom_field');

        // Loads the order status & custom fields available for this store
		$data['order_status'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['custom_fields'] = $this->model_customer_custom_field->getCustomFields();

        if (isset($this->error['warning']))
            $data['error_warning'] = $this->error['warning'];
        else
            $data['error_warning'] = '';

        $data['action'] = $this->url->link('extension/module/facturalusa', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/facturalusa', $data));
    }

    /**
     * Validates the settings form
     * 
     * @return  Boolean
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/facturalusa'))
            $this->error['warning'] = $this->language->get('error_permission');

        if ($this->request->post['module_facturalusa_document_email_copyto'])
        {
            $emails = $this->request->post['module_facturalusa_document_email_copyto'];

            $cc = explode(',', $emails);
            $cc = array_map('trim', $cc);

            foreach ($cc as $email)
            {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    $this->error['warning'] = 'Um ou mais emails introduzidos são inválidos';
            }
        }

		return !$this->error;
    }

    /**
     * Creates a new document
     * 
     * @param   Request
     * 
     * @return  Json
     */
    public function create()
    {
        require_once(DIR_SYSTEM . 'library/facturalusa/FacturalusaDocument.php');

        $orderId = $this->request->get['order_id'];
        $facturalusaDocument = new FacturalusaDocument($this->registry);
        $response = $facturalusaDocument->create($orderId);

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
    }

    /**
     * Cancels an existing document
     * 
     * @param   Request
     * 
     * @return  Json
     */
    public function cancel()
    {
        require_once(DIR_SYSTEM . 'library/facturalusa/FacturalusaDocument.php');

        $orderId = $this->request->get['order_id'];
        $facturalusaDocument = new FacturalusaDocument($this->registry);
        $response = $facturalusaDocument->cancel($orderId);

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
    }

    /**
     * Downloads the document and shows it to the user
     * 
     * @param   Request
     * 
     * @return  Json
     */
    public function download()
    {
        require_once(DIR_SYSTEM . 'library/facturalusa/FacturalusaDocument.php');

        $orderId = $this->request->get['order_id'];
        $facturalusaDocument = new FacturalusaDocument($this->registry);
        $response = $facturalusaDocument->download($orderId);

        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
    }

    /**
     * Initializes the installation of the plugin
     */
    public function install()
    {
        $this->load->model('setting/setting');
        $this->load->model('setting/event');
        $this->load->model('extension/module/facturalusa');

        $this->model_setting_setting->editSetting('module_facturalusa', 
        [            
            'module_facturalusa_status' => 0,
            'module_facturalusa_general_api_token' => '',
            'module_facturalusa_document_type' => 'Factura Recibo',
            'module_facturalusa_document_serie' => '',
            'module_facturalusa_document_status' => 'Terminado',
            'module_facturalusa_document_automatic' => 1,
            'module_facturalusa_document_create_order_status' => '',
            'module_facturalusa_document_cancel_order_status' => '',
            'module_facturalusa_document_force_send_email' => 1,
            'module_facturalusa_document_force_send_sms' => 0,
            'module_facturalusa_document_force_sign' => 0,
            'module_facturalusa_document_send_email_through' => 'Facturalusa',
            'module_facturalusa_document_email_copyto' => '',
            'module_facturalusa_document_language' => 'Auto',
            'module_facturalusa_customer_update' => 1,
            'module_facturalusa_customer_prefix' => '',
            'module_facturalusa_customer_vatnumber_field' => '',
            'module_facturalusa_item_create' => 1,
            'module_facturalusa_item_prefix' => '',
            'module_facturalusa_item_type' => 'Mercadorias',
            'module_facturalusa_item_unit' => '',
            'module_facturalusa_item_vat' => '',
            'module_facturalusa_item_vat_exemption' => 18,
            'module_facturalusa_other_vat_type' => 'IVA incluído',
            'module_facturalusa_other_location_origin' => '',
            'module_facturalusa_other_shipping_name' => '',
            'module_facturalusa_other_shipping_vat' => '',
            'module_facturalusa_other_decimal_places_prices' => 2,
            'module_facturalusa_other_decimal_places_quantities' => 2,
            'module_facturalusa_send_email_pt_subject' => '',
            'module_facturalusa_send_email_pt_message' => '',
            'module_facturalusa_send_email_en_subject' => '',
            'module_facturalusa_send_email_en_message' => '',
        ]);

        $this->model_extension_module_facturalusa->install();

        $this->model_setting_event->addEvent('facturalusa_order_edit', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/facturalusa/eventSync');
        $this->model_setting_event->addEvent('facturalusa_order_add', 'catalog/model/checkout/order/addOrder/after', 'extension/module/facturalusa/eventSync');
    }

    /**
     * Uninstalls the Facturalusa Plugin
     */
    public function uninstall()
    {
        $this->load->model('setting/setting');
        $this->load->model('setting/event');
        $this->load->model('extension/module/facturalusa');

        $this->model_setting_setting->deleteSetting('module_facturalusa');
        $this->model_extension_module_facturalusa->uninstall();

        $this->model_setting_event->deleteEventByCode('facturalusa_order_edit');
        $this->model_setting_event->deleteEventByCode('facturalusa_order_add');
    }
}