<?php

namespace EdituraEDU\UBLRenderer;


use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;

class UBLRenderer
{
    private ParsedUBLInvoice $invoice;

    public function __construct(string $ublContent, bool $useDefaultTemplates = true)
    {

        //$this->invoice = $serializer->deserialize($ublContent, Invoice::class, "xml");
        var_dump($this->invoice);
    }

}