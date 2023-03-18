<?php 

class FacturalusaOpencartHelper 
{
    /**
     * Gets the vat number in a order
     * 
     * @param   Object
     * @param   Array   Order data
     * 
     * @return  String
     */
    public static function getVatNumber($config, $order = [])
    {
        require_once('connector/FacturalusaConnectorHelper.php');

        $vatNumberField = $config->get('module_facturalusa_customer_vatnumber_field');

        // If not sent, always returns the Consumidor Final
        if (!$vatNumberField || empty($order))
            return FacturalusaConnectorHelper::CONSUMIDOR_FINAL;

        $vatNumber = null;

        if (isset($order['custom_field']) && $order['custom_field'])
        {
            $customFields = is_array($order['custom_field']) ? $order['custom_field'] : unserialize($order['custom_field']);

            foreach ($customFields as $field => $value)
            {
                if ($field == $vatNumberField)
                {
                    $vatNumber = $value;
                    break;
                }
            }
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
            {
                if ($field == $vatNumberField)
                {
                    $vatNumber = $value;
                    break;
                }
            }
        }

        if ($vatNumber)
        {
            $vatNumber = trim($vatNumber);
            $vatNumber = str_replace(' ', '', $vatNumber);
            $vatNumber = str_replace('PT', '', $vatNumber);

            return $vatNumber;
        }

        return FacturalusaConnectorHelper::CONSUMIDOR_FINAL;
    }

    /**
     * Gets the shipping applied to the order
     * 
     * @param   Object
     * @param   Array   Order totals data
     * 
     * @return  Array
     */
    public static function getShipping($config, $orderTotals = [])
    {
        $shipping = 
        [
            'mode' => $config->get('module_facturalusa_other_shipping_name'),
            'value' => 0,
            'vat' => $config->get('module_facturalusa_other_shipping_vat')
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
            $vatType = $config->get('module_facturalusa_other_vat_type');

            if ($vatType == 'IVA incluído')
            {
                $shipping['value'] = $shipping['value'] * (1 + ($shipping['vat'] / 100));
                $shipping['value'] = number_format($shipping['value'], 2);
            }
        }

        return $shipping;
    }

    /**
     * Gets both billing & delivery addresses of an order
     * 
     * @param   Array   Order data
     * 
     * @return  Array
     */
    public static function getAddresses($order = [])
    {
        require_once('connector/FacturalusaConnectorHelper.php');
        
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
            'country' => FacturalusaConnectorHelper::getCountry($order['payment_country']),
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
                'country' => FacturalusaConnectorHelper::getCountry($order['shipping_country']),
                'address' => trim($order['shipping_address_1']),
                'city' => trim($order['shipping_city']),
                'postal_code' => trim($postalCode),
            ];
        }

        return $addresses;
    }

    /**
     * Gets the voucher applied to the order. Returns as percentage %
     * 
     * @param   Array   Order totals data
     * 
     * @return  Array
     */
    public static function getVoucher($orderTotals = [])
    {
        $netTotal = 0;
        $value = 0;

        if ($orderTotals)
        {
            foreach ($orderTotals as $total)
            {
                if ($total['code'] == 'sub_total')
                    $netTotal = $total['value'];

                if ($total['code'] == 'coupon')
                    $value = abs($total['value']); // It's always negative in DB
            }
        }

        return ['net_total' => $netTotal, 'value' => $value];
    }
}