<?php

namespace EdituraEDU\UBLRenderer;


use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;

class UBLRenderer
{
    private ParsedUBLInvoice $invoice;

    public function __construct(string $ublContent, bool $useDefaultTemplates = true)
    {
        if(!MappingsManager::$Initialized)
        {
            MappingsManager::Init();
        }
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($ublContent);
        $this->invoice=ParsedUBLInvoice::XMLDeserialize($reader);
    }

    public function CreateHTML():string
    {
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/Template');
        $twig = new \Twig\Environment($loader);
        $twig->load("default.html.twig");
        return $twig->render('default.html.twig', ['invoice' => $this->invoice]);
    }

}