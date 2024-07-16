<?php
/*
 *  Copyright [2024] [Tecsi Aron]
 *
 *     Licensed under the Apache License, Version 2.0 (the "License");
 *     you may not use this file except in compliance with the License.
 *     You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *     Unless required by applicable law or agreed to in writing, software
 *     distributed under the License is distributed on an "AS IS" BASIS,
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *     See the License for the specific language governing permissions and
 *     limitations under the License.
 */

namespace EdituraEDU\UBLRenderer;


use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;
use Exception;

class UBLRenderer
{
    private string $UBLContent;
    private static ?ParsedUBLInvoice $CurrentInvoice = null;
    public function __construct(string $ublContent, bool $useDefaultTemplates = true)
    {
        if(!MappingsManager::$Initialized)
        {
            MappingsManager::Init();
        }
        $this->UBLContent = $ublContent;
    }

    public function CreateHTML():string
    {
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($this->UBLContent);
        /**
         * @var ParsedUBLInvoice $invoice
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        $invoice=ParsedUBLInvoice::XMLDeserialize($reader);
        self::$CurrentInvoice = $invoice;
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/Template');
        $twig = new \Twig\Environment($loader);
        $twig->load("default.html.twig");
        $rendered = $twig->render('default.html.twig', ['invoice' => $invoice]);
        self::$CurrentInvoice = null;
        return $rendered;
    }

    public static function GetCurrentInvoice(): ParsedUBLInvoice
    {
        if(self::$CurrentInvoice == null)
        {
            throw new Exception("Bad execution order, no invoice is currently being processed.");
        }
        return self::$CurrentInvoice;
    }

}