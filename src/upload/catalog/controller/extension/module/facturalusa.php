<?php

class ControllerExtensionModuleFacturalusa extends Controller 
{
    /**
     * Triggers the sync of the Opencart order into Facturalusa automatically
     * 
     * @param   Array
     * @param   Array
     * @param   Array
     */
    public function eventSync(&$route, &$args, &$output)
    {
        if (!isset($args[0]))
            return;

        require_once('system/library/facturalusa/FacturalusaOpencartSale.php');

        // No response to be output
        $orderId = $args[0];
        $facturalusaSale = new FacturalusaOpencartSale($this->registry);

        if ($facturalusaSale->shouldCreate($orderId))
            $facturalusaSale->create($orderId);
        elseif ($facturalusaSale->shouldCancel($orderId))
            $facturalusaSale->cancel($orderId);
    }
}
