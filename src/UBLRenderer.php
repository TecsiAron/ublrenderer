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

    public function CreateHTML(ParsedUBLInvoice $invoice):string
    {
        /**
         * @var ParsedUBLInvoice $invoice
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        self::$CurrentInvoice = $invoice;
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/Template');
        $twig = new \Twig\Environment($loader, [
            "strict_variables" => true,
        ]);
        $twig->load("default.html.twig");
        $rendered = $twig->render('default.html.twig', ['invoice' => $invoice]);
        self::$CurrentInvoice = null;
        return $rendered;
    }

    public function ParseUBL(): ParsedUBLInvoice
    {
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($this->UBLContent);
        /**
         * @var ParsedUBLInvoice $invoice
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        $invoice=ParsedUBLInvoice::XMLDeserialize($reader);
        self::$CurrentInvoice = $invoice;
        return $invoice;
    }

    /**
     * @param IInvoiceWriter[] $writer
     * @return void
     */
    public function WriteFiles(array $writer)
    {
        $invoice = $this->ParseUBL();
        $html = $this->CreateHTML($invoice);
        foreach ($writer as $w)
        {
            $w->WriteContent($html, $invoice);
        }
    }

    public function WriteFile(IInvoiceWriter $writer)
    {
        $invoice = $this->ParseUBL();
        $html = $this->CreateHTML($invoice);
        $writer->WriteContent($html, $invoice);
    }

    public static function GetCurrentInvoice(): ParsedUBLInvoice
    {
        if(self::$CurrentInvoice == null)
        {
            throw new Exception("Bad execution order, no invoice is currently being processed.");
        }
        return self::$CurrentInvoice;
    }

    public static function LoadUBLFromZip(string $zipPath): string
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        if($zip->count()!=2)
        {
            throw new Exception("Invalid ZIP file format: must contain exactly 2 files!");
        }
        for($i=0; $i<$zip->count(); $i++)
        {
            $filename = $zip->getNameIndex($i);
            if(str_starts_with( $filename,"semnatura_",) && str_ends_with($filename, ".xml"))
            {
                $invoiceFileName=trim(explode("_", $filename)[1]);
                $content=$zip->getFromName($invoiceFileName);
                if($content===false)
                {
                    throw new Exception("Invalid ZIP file format: could not read invoice file!");
                }
                return $content;
            }
        }

        $zip->close();
        throw new Exception("Invalid ZIP file format: could not find valid signature file (semnatura_*.xml)!");
    }

}