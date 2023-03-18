<?php

class ModelExtensionModuleFacturalusaSales extends Model 
{
    /**
     * Returns the table name with the prefix from database
     * 
     * @return  String
     */
    private function table()
    {
        return DB_PREFIX . 'facturalusa_sales';
    }
    
    /**
     * Gets a document by order id
     * 
     * @param   Integer
     * 
     * @return  Array
     */
    public function get($orderId)
    {
        $sql = "SELECT * FROM {$this->table()} WHERE order_id = {$orderId}";

        return $this->db->query($sql)->row;
    }
}
