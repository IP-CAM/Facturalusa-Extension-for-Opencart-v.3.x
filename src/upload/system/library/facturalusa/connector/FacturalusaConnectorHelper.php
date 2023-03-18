<?php 

final class FacturalusaConnectorHelper
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
	 * Gets the customer type (Particular or Empresarial)
	 * 
	 * @param	String
	 * @param	String
	 * 
	 * @return	String
	 */
	public static function getCustomerType($companyName = null, $vatNumber = null)
	{
		if ($companyName)
		{
			$companyName = trim($companyName);
        	$companyName = mb_strtolower($companyName);

			 // "... Unipessoal", "... Lda" or something related means it's a company
			 if (preg_match('/unipessoal|unip.|.lda|lda.|, lda| lda|/i', $companyName))
				return self::C_EMPRESARIAL;
		}

		/**
         * Tanto o NIF como NIPC são constituídos por 9 digítos, sendo que o primeiro tem significados diferentes:
         *  - 1 ou 2 (pessoa singular ou empresário em nome individual)
         *  - 5 (pessoa colectiva)
         *  - 6 (pessoa colectiva pública)
         *  - 9 (pessoa colectiva irregular ou número provisório)
         * 
         * https://www.nif.pt/nif-das-empresas/
         */
		if ($vatNumber)
		{
			$vatNumber = mb_strtoupper($vatNumber); // Because of people who write "pt000", instead of "PT000"
			$vatNumber = trim($vatNumber); 

			if (self::isPortugueseVatNumber($vatNumber))
			{
				$firstDigit = $vatNumber[0];

				if (in_array($firstDigit, [1, 2, 3]))
					return self::C_PARTICULAR;

				if (in_array($firstDigit, [5, 6]))
					return self::C_EMPRESARIAL;
			}			
		}

		return self::C_PARTICULAR;
	}

	/**
	 * Get the payment method based on the payment code
	 * 
	 * @param	String
	 * 
	 * @return	String
	 */
	public static function getPaymentMethod($paymentCode)
	{
		$paymentCode = mb_strtolower($paymentCode);

		$isCash = strpos($paymentCode, 'cod') !== false || 
			strpos($paymentCode, 'currency') !== false || 
			strpos($paymentCode, 'money') !== false || 
			strpos($paymentCode, 'cash') !== false ||
			strpos($paymentCode, 'dinheiro') !== false;

		$isBankTransfer = strpos($paymentCode, 'bank') !== false || 
			strpos($paymentCode, 'transfer') !== false || 
			strpos($paymentCode, 'wire') !== false ||
			strpos($paymentCode, 'transferência') !== false ||
			strpos($paymentCode, 'bancária') !== false ||
			strpos($paymentCode, 'iban') !== false;

		$isCreditCard = strpos($paymentCode, 'credit') !== false || 
			strpos($paymentCode, 'card') !== false || 
			strpos($paymentCode, 'paypal') !== false ||
			strpos($paymentCode, 'stripe') !== false ||
			strpos($paymentCode, 'braintree') !== false ||
			strpos($paymentCode, 'cartão') !== false ||
			strpos($paymentCode, 'crédito') !== false;

		if ($isCash)
			return 'Numerário';

		if ($isBankTransfer)
			return 'Transferência bancária';

		if ($isCreditCard)
			return 'Cartão crédito';

		return 'Outros meios';
	}

	/**
	 * Gets the item vat % based on the unit price & total vat
	 * 
	 * @param	Double
	 * @param	Double
	 * 
	 * @return	Double
	 */
	public static function getItemVat($price, $totalVat)
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
        $totalPlusTax = $price + $totalVat;
        // -> 129.15 / 105 = 1.23
        $tax = $totalPlusTax / $price;
        // -> 1.23 - 1 = 0.23
        $tax -= 1;
        // -> 0.23 * 100 = 23
        $tax *= 100;

        return number_format($tax, 2, '.', '');
	}

	/**
	 * Gets the language to issue the document based on the country
	 * 
	 * @param	String
	 * 
	 * @return	String
	 */
	public static function getLanguage($country)
	{
		$country = mb_strtolower($country);
		$country = trim($country);

		$isPortuguese = strpos($country, 'portugal') !== false || 
			strpos($country, 'brazil') !== false || 
			strpos($country, 'brasil') !== false ||
			strpos($country, 'angola') !== false ||
			strpos($country, 'mozambique') !== false ||
			strpos($country, 'guiné') !== false ||
			strpos($country, 'guinea') !== false ||
			strpos($country, 'guinea-bissau') !== false ||
			strpos($country, 'guinea bissau') !== false ||
			strpos($country, 'east timor') !== false ||
			strpos($country, 'timor') !== false ||
			strpos($country, 'cabo verde') !== false ||
			strpos($country, 'cape verde') !== false ||
			strpos($country, 'são tomé') !== false ||
			strpos($country, 'sao tome and principe') !== false;

		if ($isPortuguese)
			return 'PT';

		return 'EN';
	}

	/**
	 * Gets the percentage discount based on the net total & total discount applied, for example, through coupon.
	 * 
	 * @param	Double
	 * @param	Double
	 * 
	 * @return	String
	 */
	public static function getPercentageDiscount($netTotal, $totalDiscount)
	{
		$totalDiscount = abs($totalDiscount); // Could be negative

		/**
         * Sub total = 1.74
         * total discount = -0.174 -> Apply abs() function -> 0.174
         * 
         * Percentage = (0.174 / 1.74) = 0.1
         * Percentage = 0.1 * 100 = 10%
         */
		if ($totalDiscount > 0)
		{
			$percentageDiscount = ($totalDiscount / $netTotal) * 100;
			$percentageDiscount = number_format($percentageDiscount, 2, '.', '');

			return $percentageDiscount;
		}

		return 0;
	}

	/**
	 * Gets the correct country based on the store countries list. Some countries doesn't exists in Facturalusa.
	 * 
	 * @param	String
	 * 
	 * @return	String
	 */
	public static function getCountry($country)
	{
		switch ($country)
		{
			case 'France, Metropolitan':
				$country = 'France';
				break;
			case 'South Korea':
				$country = 'Korea, Republic of';
				break;
		}

		return $country;
	}

	/**
     * Returns if the VAT Number is valid (only if it is Portuguese)
     * Original source: http://www.portugal-a-programar.pt/topic/9968-php-validar-bi-e-nif/page__st__20
     *
     * @param	String
     *
     * @return	Boolean
     */
	public static function isPortugueseVatNumber($vatNumber)
	{
        if (!is_numeric($vatNumber) || strlen($vatNumber) != 9)
            return false;

        $lastNumber = (int)substr($vatNumber, -1);
        $vatNumber = (int)substr($vatNumber, 0, -1);
        $value = 0;

        foreach (array_reverse(str_split($vatNumber)) as $i => $d)
            $value += ($i + 2) * $d;

        return (($value + $lastNumber) % 11 == 0) || ($lastNumber == 0 && ($value + 10) % 11 == 0);
	}

	/**
	 * Logs any errors into a file
	 * 
	 * @param	String
	 * @param	Exception
	 */
	public static function log($folder, \Exception $e)
	{
		$directory = $folder;
        $filename = sprintf('facturalusa-%s.log', date('Ymd'));
        $path = $directory . '/' . $filename;
        $data = sprintf('%s: %s -> line:%s %s', date('Y-m-d H:i:s'), $e->getMessage(), $e->getLine(), PHP_EOL);

        file_put_contents($path, $data, FILE_APPEND);
	}
}