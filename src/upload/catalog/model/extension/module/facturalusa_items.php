<?php

class ModelExtensionModuleFacturalusaItems extends Model 
{
    /**
     * Returns the table name with the prefix from database
     * 
     * @return  String
     */
    private function table()
    {
        return DB_PREFIX . 'facturalusa_items';
    }
    
    /**
     * Returns a single item based upon the product id
     * 
     * @param   Integer
     * 
     * @return  Array
     */
    public function get($productId)
    {
        $sql = "SELECT * FROM {$this->table()} WHERE product_id = {$productId}";
        $query = $this->db->query($sql);
        
        return $query->row;
    }

    /**
     * Inserts a new item
     * 
     * @param   Integer
     * @param   Integer
     */
    public function insert($productId, $facturalusaId)
    {
        $sql = "INSERT INTO {$this->table()} (product_id, facturalusa_id) VALUES ({$productId}, {$facturalusaId});";

        $this->db->query($sql);
    }

    /**
     * Finds the product vat based on a product id
     * 
     * @param   Integer
     * 
     * @return  Integer
     */
    public function findVat($productId)
    {
        $prefix = DB_PREFIX;
        $vat = 23; // Default

        // Grabs the product first
        $product = $this->db->query("SELECT * FROM {$prefix}product WHERE product_id = {$productId}")->row;
        
        // If no tax associated, always return as 23%
        if (!$product || !isset($product['tax_class_id']))
            return $vat;

        $taxClassId = $product['tax_class_id'];
        $taxRules = $this->db->query("SELECT * FROM {$prefix}tax_rule WHERE tax_class_id = {$taxClassId}")->rows;

        if (count($taxRules) <= 0)
            return $vat;

        foreach ($taxRules as $taxRule)
        {
            $taxRateId = $taxRule['tax_rate_id'];
            $taxRate = $this->db->query("SELECT * FROM {$prefix}tax_rate WHERE tax_rate_id = {$taxRateId}")->row;

            // Only finds rates that are in Percentage
            if (!$taxRate || $taxRate['type'] != 'P')
                continue;

            $rate = $taxRate->rate;
            $rate = number_format(2, $rate, '.', '');

            /**
             * All vats from Portugal (Azores, Madeira & Continental)
             * https://www.economias.pt/valor-do-iva-em-portugal/
             */
            if (!in_array($rate, [0.00, 4.00, 5.00, 6.00, 9.00, 12.00, 13.00, 18.00, 22.00, 23.00]))
                continue;

            $vat = $rate;
            
            break;
        }

        return $vat;
    }
}
