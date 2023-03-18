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

    /**
     * Inserts a new document 
     * 
     * @param   Array
     */
    public function insert($data)
    {
        $sql = "INSERT INTO {$this->table()} ";
        $sql .= "(order_id, facturalusa_id, issue_date, number, document_saft_initials, document_description, serie, customer_name, customer_vat_number, net_total, total_vat, grand_total, status, error) ";
        $sql .= "VALUES ({$data['order_id']}, {$data['facturalusa_id']}, '{$data['issue_date']}', {$data['number']}, '{$data['document_saft_initials']}', '{$data['document_description']}', '{$data['serie']}', ";
        $sql .= "'{$data['customer_name']}', '{$data['customer_vat_number']}', {$data['net_total']}, {$data['total_vat']}, {$data['grand_total']}, '{$data['status']}', '{$data['error']}');";

        $this->db->query($sql);
    }

    /**
     * Updates the status of canceled to True in a document by order id
     * 
     * @param   Integer
     */
    public function update($data)
    {
        $sql = "UPDATE {$this->table()} SET facturalusa_id = {$data['facturalusa_id']}, issue_date = '{$data['issue_date']}', number = {$data['number']}, document_saft_initials = '{$data['document_saft_initials']}', ";
        $sql .= "document_description = '{$data['document_description']}', serie = '{$data['serie']}', customer_name = '{$data['customer_name']}', customer_vat_number = '{$data['customer_vat_number']}', ";
        $sql .= "net_total = {$data['net_total']}, total_vat = {$data['total_vat']}, grand_total = {$data['grand_total']}, status = '{$data['status']}', is_canceled = {$data['is_canceled']}, error = '{$data['error']}' ";
        $sql .= "WHERE order_id = {$data['order_id']}";
        
        $this->db->query($sql);
    }

    /**
     * Deletes a document by order id
     * 
     * @param   Integer
     * 
     * @return  Array
     */
    public function delete($orderId)
    {
        $sql = "DELETE FROM {$this->table()} WHERE order_id = {$orderId}";

        $this->db->query($sql);
    }

    /**
     * Returns if a document exists order id
     * 
     * @param   Integer
     * 
     * @return  Boolean
     */
    public function exists($orderId)
    {
        $sql = "SELECT * FROM {$this->table()} WHERE order_id = {$orderId}";

        return $this->db->query($sql)->num_rows > 0;
    }

    /**
     * Returns if a document exists order id
     * 
     * @param   Integer
     * 
     * @return  Boolean
     */
    public function addHistory($orderId, $orderStatusId, $comment)
    {
        $prefix = DB_PREFIX;
        $dateAdded = date('Y-m-d H:i:s');
        $sql = "INSERT INTO {$prefix}order_history (order_id, order_status_id, comment, date_added) VALUES ({$orderId}, {$orderStatusId}, '{$comment}', '{$dateAdded}');";

        $this->db->query($sql);
    }
}
