<?php 

/**
 * Facturalusa Helper class with some utilities to be used in multiple places
 */
class FacturalusaHelper
{
    /**
     * Holds the consumidor final vat number
     * 
     * @var String
     */
    const CONSUMIDOR_FINAL = '999999990';

    /**
     * Holds the customer type Particular
     * 
     * @var String
     */
    const C_PARTICULAR = 'Particular';

    /**
     * Holds the customer type Empresarial
     */
    const C_EMPRESARIAL = 'Empresarial';

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
     * Constructor
     * 
     * @param   Object
     */
    public function __construct($registry = [])
    {
        $this->registry = $registry;
        $this->config = $this->registry->get('config');
    }

    /**
     * Finds the vat number in a order
     * 
     * @param   Array   Order data
     * 
     * @return  String
     */
    public function findVatNumber($order = [])
    {
        $vatNumberField = $this->config->get('module_facturalusa_customer_vatnumber_field');

        // If not sent, always returns the Consumidor Final
        if (!$vatNumberField || empty($order))
            return self::CONSUMIDOR_FINAL;

        if (isset($order['custom_field']) && $order['custom_field'])
        {
            $customFields = is_array($order['custom_field']) ? $order['custom_field'] : unserialize($order['custom_field']);

            foreach ($customFields as $field => $value)
                if ($field == $vatNumberField)
                    return $value;
        }

        /**
         * User can add the VAT field to three locations:
         * 
         * 1) Account
         * 2) Payment address
         * 3) Affiliate <- Doesn't matter
         * 
         * Usually people would add it to the Account information, but in some ocasions it was noticed that they added
         * to the Payment address information. This would lead to the VAT Number never been found.
         * 
         * So, after checking for the VAT Number in the Account (custom_field) it also checks in the Payment Address (payment_custom_field).
         */
        if (isset($order['payment_custom_field']) && $order['payment_custom_field'])
        {
            $customFields = is_array($order['payment_custom_field']) ? $order['payment_custom_field'] : unserialize($order['payment_custom_field']);

            foreach ($customFields as $field => $value)
                if ($field == $vatNumberField)
                    return $value;
        }

        return self::CONSUMIDOR_FINAL;
    }

    /**
     * Finds the Customer Type (Particular or Empresarial) in a order
     * 
     * @param   Array   Order data
     * 
     * @return  String
     */
    public function findCustomerType($order = [])
    {
        $vatNumber = $this->findVatNumber();
        $fullname = $order['firstname'] . ' ' . $order['lastname'];
        $fullname = trim($fullname);
        $fullname = mb_strtolower($fullname);

        // "... Unipessoal", "... Lda" or something related means it's a company
        if (preg_match('/unipessoal|unip.|.lda|lda.|, lda| lda|/i', $fullname))
            return self::C_EMPRESARIAL;

        if ($vatNumber == '999999990')
            return self::C_PARTICULAR;

        /**
         * Tanto o NIF como NIPC são constituídos por 9 digítos, sendo que o primeiro tem significados diferentes:
         *  - 1 ou 2 (pessoa singular ou empresário em nome individual)
         *  - 5 (pessoa colectiva)
         *  - 6 (pessoa colectiva pública)
         *  - 9 (pessoa colectiva irregular ou número provisório)
         * 
         * https://www.nif.pt/nif-das-empresas/
         */
        if ($vatNumber[0] == 1 || $vatNumber[0] == 2)
            return self::C_PARTICULAR;
        
        if ($vatNumber[0] == 5 || $vatNumber[0] == 6)
            return self::C_EMPRESARIAL;

        return self::C_PARTICULAR;
    }

    /**
     * Returns the Addresses in the order
     * 
     * @param   Array   Order data
     * 
     * @return  Array
     */
    public function findAddresses($order = [])
    {
        $addresses = 
        [
            'billing' => 
            [
                'country' => '',
                'address' => '',
                'city' => '',
                'postal_code' => ''
            ],
            'delivery' => 
            [
                'country' => '',
                'address' => '',
                'city' => '',
                'postal_code' => ''
            ]
        ];

        $address = $order['payment_address_1'] . ' ' . $order['payment_address_2'];
        $postalCode = $order['payment_postcode'];

        if ($order['payment_company'])
            $address = $order['payment_company'] . ' - ' . $address;

        if (!$postalCode)
            $postalCode = '0000-000';

        $addresses['billing'] = 
        [
            'country' => trim($order['payment_country']),
            'address' => trim($address),
            'city' => trim($order['payment_city']),
            'postal_code' =>  trim($postalCode),
        ];


        if ($order['shipping_address_1'] != $order['payment_address_1'])
        {
            $postalCode = $order['shipping_postcode'];

            if (!$postalCode)
                $postalCode = '0000-000';

            $addresses['delivery'] = 
            [
                'country' => trim($order['shipping_country']),
                'address' => trim($order['shipping_address_1']),
                'city' => trim($order['shipping_city']),
                'postal_code' => trim($postalCode),
            ];
        }

        return $addresses;
    }

    /**
     * Returns the payment method
     * 
     * @param   Array   Order data
     * 
     * @return  String
     */
    public function findPaymentMethod($order = [])
    {
        switch ($order['payment_code'])
        {
            case 'cod': 
                $paymentMethod = 'Numerário';
                break;
            case 'bank_transfer': 
                $paymentMethod = 'Transferência bancária';
                break;
            case 'hipaycc':
            case 'paypal':
            case 'eupago_cc':
            case 'stripe':
            case 'braintree':
                $paymentMethod = 'Cartão crédito';
                break;
            default:
                $paymentMethod = 'Outros meios';
                break;
        }

        return $paymentMethod;
    }

    /**
     * Returns the shipping applied to the order
     * 
     * @param   Array   Order totals data
     * 
     * @return  Array
     */
    public function findShipping($orderTotals = [])
    {
        $shipping = 
        [
            'mode' => $this->config->get('module_facturalusa_other_shipping_name'),
            'value' => 0,
            'vat' => $this->config->get('module_facturalusa_other_shipping_vat')
        ];

        if ($orderTotals)
        {
            foreach ($orderTotals as $total)
            {
                if ($total['value'] <= 0)
                    continue;

                if ($total['code'] != 'shipping')
                    continue;

                $shipping['value'] = number_format($total['value'], 2);
                break;
            }
        }

        /**
         * In Opencart only the items prices can have already the Vat included. The shipping value never has and it's
         * always added in the checkout. So, if the user decides to use "IVA incluído" the value of the shipping would always
         * be wrong, as Facturalusa would guess that the Shipping Value already has the Vat included, which it does not.
         * 
         * So, when the current Vat Type is "IVA incluído" it should always add Vat Rate to the Shipping Value.
         */
        if ($shipping['vat'] && (is_numeric($shipping['vat']) || is_float($shipping['vat'])))
        {
            $vatType = $this->config->get('module_facturalusa_other_vat_type');

            if ($vatType == 'IVA incluído')
            {
                $shipping['value'] = $shipping['value'] * (1 + ($shipping['vat'] / 100));
                $shipping['value'] = number_format($shipping['value'], 2);
            }
        }

        return $shipping;
    }

    /**
     * Returns the coupon (in percentage) applied to the order
     * 
     * @param   Array   Order totals data
     * 
     * @return  Array
     */
    public function findCoupon($orderTotals = [])
    {
        $subTotal = 0;
        $coupon = 0;

        if ($orderTotals)
        {
            foreach ($orderTotals as $total)
            {
                if ($total['code'] == 'sub_total')
                    $subTotal = $total['value'];

                if ($total['code'] == 'coupon')
                    $coupon = abs($total['value']); // It's always negative in DB
            }
        }
        
        /**
         * Sub total = 1.74
         * Coupon = -0.174 -> Apply abs() function -> 0.174
         * 
         * Percentage = (0.174 / 1.74) = 0.1
         * Percentage = 0.1 * 100 = 10%
         */
        if ($coupon > 0)
        {
            $coupon = ($coupon / $subTotal) * 100;
            $coupon = number_format($coupon, 2);
        }

        return $coupon;
    }
    
    /**
     * Finds the vat (0, 6, 13, 23) of the item ordered
     * 
     * @param   Array   Order item data
     * 
     * @return  Array
     */
    public function findVatForItem($orderItem = [])
    {
        /**
         * 'price' => contains the unit price multiplied by the quantity
         * 'tax' => contains the tax for the unit price
         * 
         * 'total' = 105
         * 'tax' => 24.15
         * 
         * = 129.15
         */
        $totalPlusTax = $orderItem['price'] + $orderItem['tax'];
        // -> 129.15 / 105 = 1.23
        $tax = $totalPlusTax / $orderItem['price'];
        // -> 1.23 - 1 = 0.23
        $tax -= 1;
        // -> 0.23 * 100 = 23
        $tax *= 100;

        return number_format($tax, 2, '.', '');
    }

    /**
     * Finds the language to be applied in the document
     * 
     * @param   Array   Order data
     * 
     * @return  String  Either PT or EN
     */
    public function findLanguage($order = [])
    {
        $language = $this->config->get('module_facturalusa_document_language');
        $country = $order['payment_country'];

        if (in_array($language, ['PT', 'EN']))  
            return $language;

        if (!$country)
            return 'PT';

        // Auto, figure out by the country
        switch ($country)
        {
            case 'Portugal':
            case 'Brazil':
            case 'Angola':
            case 'Mozambique':
            case 'Guinea-Bissau':
            case 'Guinea':
            case 'East Timor':
            case 'Cape Verde':
            case 'Sao Tome and Principe':
                $language = 'PT';
                break;
            default:
                $language = 'EN';
                break;
        }

        return $language;
    }

    /**
     * Logs any data into a log.file
     * 
     * @param   Exception
     */
    public function log(\Exception $e)
    {
        $directory = DIR_SYSTEM . 'storage/logs';
        $filename = sprintf('facturalusa-%s.log', date('Ymd'));
        $path = $directory . '/' . $filename;
        $data = date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . ' -> ' . $e->getLine() . PHP_EOL;

        file_put_contents($path, $data, FILE_APPEND);
    }
}