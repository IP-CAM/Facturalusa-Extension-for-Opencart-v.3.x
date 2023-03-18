<?php

class ModelExtensionModuleFacturalusa extends Model 
{
    /**
     * Creates all the necessary tables
     */
    public function install()
    {
        $dbPrefix = DB_PREFIX;

        $this->db->query("
			CREATE TABLE IF NOT EXISTS {$dbPrefix}facturalusa_items (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `product_id` int(11) NOT NULL,
                    `facturalusa_id` int(11) NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
		");
        
        $this->db->query("
			CREATE TABLE IF NOT EXISTS {$dbPrefix}facturalusa_customers (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `customer_id` int(11) NOT NULL,
                    `facturalusa_id` int(11) NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
		");
        
        $this->db->query("
			CREATE TABLE IF NOT EXISTS {$dbPrefix}facturalusa_sales (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `order_id` int(11) NOT NULL,
                    `facturalusa_id` int(11) NULL,
                    `issue_date` date NULL,
                    `number` int(11) NULL,
                    `document_saft_initials` VARCHAR(255) NULL,
                    `document_description` VARCHAR(255) NULL,
                    `serie` VARCHAR(255) NULL,
                    `customer_name` VARCHAR(255) NULL,
                    `customer_vat_number` VARCHAR(255) NULL,
                    `net_total` DOUBLE(20,5) NULL,
                    `total_vat` DOUBLE(20,5) NULL,
                    `grand_total` DOUBLE(20,5) NULL,
                    `status` VARCHAR(255) NULL,
                    `is_canceled` TINYINT(1) DEFAULT 0,
                    `error` TEXT NULL,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
		");
    }

    /**
     * Drops all the tables created by Facturalusa Plugin
     */
    public function uninstall()
    {
        $dbPrefix = DB_PREFIX;

        $this->db->query("DROP TABLE IF EXISTS {$dbPrefix}facturalusa_items");
        $this->db->query("DROP TABLE IF EXISTS {$dbPrefix}facturalusa_customers");
        $this->db->query("DROP TABLE IF EXISTS {$dbPrefix}facturalusa_sales");
    }
}
