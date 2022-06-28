<?php 

namespace Facturalusa;

interface FacturalusaResponse 
{
    public function fail();
    public function success();
    public function response();
}