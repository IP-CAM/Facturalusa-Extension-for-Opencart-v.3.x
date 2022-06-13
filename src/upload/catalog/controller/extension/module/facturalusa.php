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

        require_once('system/library/facturalusa/FacturalusaDocument.php');

        // No response to be output
        $orderId = $args[0];
        $facturalusaDocument = new FacturalusaDocument($this->registry);

        if ($facturalusaDocument->shouldCreate($orderId))
            $facturalusaDocument->create($orderId);
        elseif ($facturalusaDocument->shouldCancel($orderId))
            $facturalusaDocument->cancel($orderId);
    }
}
