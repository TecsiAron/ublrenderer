<?php

namespace EdituraEDU\UBLRenderer;


use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;

class UBLRenderer
{
    private ParsedUBLInvoice $invoice;

    public function __construct(string $ublContent, bool $useDefaultTemplates = true)
    {
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($ublContent);
        $this->invoice=ParsedUBLInvoice::XMLDeserialize($reader);
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/Template');
        $twig = new \Twig\Environment($loader);
        $twig->load("default.html.twig");
        $output = $twig->render('default.html.twig', ['invoice' => $this->invoice]);
        file_put_contents("test.html", $output);
    }

}