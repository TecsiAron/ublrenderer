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
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UBLRenderer
{
    private string $UBLContent;
    private static ?ParsedUBLInvoice $CurrentInvoice = null;
    private bool $useDefaultTemplate;

    /**
     * @param string|null $ublContent XML content of the UBL invoice
     * @param bool $useDefaultTemplate if set to false the CreateHTML method will not use the default template and will have to be called with a custom Twig environment
     */
    public function __construct(?string $ublContent = null, bool $useDefaultTemplate = true)
    {
        if(!MappingsManager::$Initialized)
        {
            MappingsManager::Init();
        }
        $this->useDefaultTemplate = $useDefaultTemplate;
        $this->UBLContent = $ublContent;
    }

    /**
     * @param ParsedUBLInvoice $invoice
     * @param Environment|null $twig if the constructor was called with useDefaultTemplate=false, this parameter must be set
     * @return string HTML content of the invoice
     * @throws UBLRenderException if ParsedUBLInvoice::CanRender() returns false
     * @throws LoaderError if the default template cannot be loaded
     * @throws RuntimeError on Twig runtime errors
     * @throws SyntaxError on Twig syntax errors
     */
    public function CreateHTML(ParsedUBLInvoice $invoice, ?Environment $twig=null):string
    {
        /**
         * @var ParsedUBLInvoice $invoice
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        if($twig != null && $this->useDefaultTemplate)
        {
            throw new Exception("Cannot use custom Twig environment with default template");
        }
        if($twig==null && !$this->useDefaultTemplate)
        {
            throw new Exception("Cannot use default template when UBLRenderer::useDefaultTemplate is false");
        }
        $canRender = $invoice->CanRender();
        if($canRender!==true)
        {
            throw new UBLRenderException("Invoice cannot be rendered", $canRender);
        }
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

    /**
     * Calls ParsedUBLInvoice::XMLDeserialize() and returns the result
     * @return ParsedUBLInvoice
     * @throws Exception on any XML parsing error
     */
    public function ParseUBL(): ParsedUBLInvoice
    {
        /*$document=new DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML($this->UBLContent);*/
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($this->UBLContent);
        /**
         * @var ParsedUBLInvoice $invoice
         * @noinspection PhpRedundantVariableDocTypeInspection
         */
        $invoice=ParsedUBLInvoice::XMLDeserialize($reader);
        return $invoice;
    }

    /**
     * Parses the UBL content, creates the HTML and writes it to the specified files using the specified writers
     * Will not work if constructor was called with useDefaultTemplate=false
     * @param IInvoiceWriter[] $writers
     * @param ParsedUBLInvoice|null $invoice If null ParseUBL() will be called (default value is null)
     * @param string|null $htmlContent If null CreateHTML() will be called (default value is null)
     * @return void
     * @throws Exception
     */
    public function WriteFiles(array $writers, ?ParsedUBLInvoice $invoice=null,?string $htmlContent=null)
    {
        if($invoice==null)
        {
            $invoice = $this->ParseUBL();
        }
        if($htmlContent==null)
        {
            $htmlContent = $this->CreateHTML($invoice);
        }
        foreach ($writers as $w)
        {
            $w->WriteContent($htmlContent, $invoice);
        }
    }

    /**
     * Parses the UBL content, creates the HTML and writes it to the specified file using the specified writer
     * Will not work if constructor was called with useDefaultTemplate=false
     * @param IInvoiceWriter $writer HTMLFileWriter will be used by default
     * @param ParsedUBLInvoice|null $invoice If null ParseUBL() will be called (default value is null)
     * @param string|null $htmlContent If null CreateHTML() will be called (default value is null)
     * @return void
     * @throws Exception
     */
    public function WriteFile(IInvoiceWriter $writer = new HTMLFileWriter(), ?ParsedUBLInvoice $invoice=null,?string $htmlContent=null )
    {
        if($invoice==null)
        {
            $invoice = $this->ParseUBL();
        }
        if($htmlContent==null)
        {
            $htmlContent = $this->CreateHTML($invoice);
        }
        $writer->WriteContent($htmlContent, $invoice);
    }

    /**
     * FOR INTERNAL USE ONLY, $CurrentInvoice is only set during rendering and is unset immediately after
     * Use ParseUBL() and/or CreateHTML() if you need a reference to the invoice!
     * @return ParsedUBLInvoice
     * @throws Exception
     */
    public static function GetCurrentInvoice(): ParsedUBLInvoice
    {
        if(self::$CurrentInvoice == null)
        {
            throw new Exception("Bad execution order, no invoice is currently being processed.");
        }
        return self::$CurrentInvoice;
    }

    /**
     * Tries to load the UBL file from a standard ANAF ZIP archive.
     * Assumes 2 files in the zip out of witch one is names semnatura_<index_here>.xml and the UBL being names <same_index_here>.xml
     * @param string $zipPath
     * @return ParsedUBLZIP
     * @throws Exception
     */
    public static function LoadUBLFromZip(string $zipPath): ParsedUBLZIP
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
                $signature=$zip->getFromName($filename);
                $zip->close();
                return new ParsedUBLZIP($content, $signature);
            }
        }

        $zip->close();
        throw new Exception("Invalid ZIP file format: could not find valid signature file (semnatura_*.xml)!");
    }

    /**
     * @deprecated
     */
    private function Dedup(array $reasons):array
    {
        $deduped=[];
        foreach ($reasons as $reason)
        {
            if(!in_array($reason, $deduped))
            {
                $deduped[]=$reason;
            }
        }
        return $deduped;
    }

}