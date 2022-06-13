<?php

class ModelExtensionModuleFacturalusaCustomers extends Model 
{
    /**
     * Returns the table name with the prefix from database
     * 
     * @return  String
     */
    private function table()
    {
        return DB_PREFIX . 'facturalusa_customers';
    }
    
    /**
     * Returns a single customer based upon the customer id
     * 
     * @param   Integer
     * 
     * @return  Array
     */
    public function get($customerId)
    {
        $sql = "SELECT * FROM {$this->table()} WHERE customer_id = {$customerId}";
        
        return $this->db->query($sql)->row;
    }

    /**
     * Inserts a new customer
     * 
     * @param   Integer
     * @param   Integer
     */
    public function insert($customerId, $facturalusaId)
    {
        $sql = "INSERT INTO {$this->table()} (customer_id, facturalusa_id) VALUES ({$customerId}, {$facturalusaId})";

        $this->db->query($sql);
    }
}
