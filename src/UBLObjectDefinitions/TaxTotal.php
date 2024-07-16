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

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class TaxTotal extends UBLDeserializable
{
    public ?string $TaxAmount = null;
    public ?string $TaxAmountCurrency = null;
    /**
     * @var TaxSubTotal[] $TaxSubtotals
     */
    public array $TaxSubtotals = [];

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new TaxTotal();
        $depth = $reader->depth;
        $testXML = self::GetTestXML();
        $cLark = $reader->getClark();
        $reader->read(); // Move one child down
        //TODO check if currencyID is needed
        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "TaxAmount":
                        $parsed= $reader->parseCurrentElement();
                        $instance->TaxAmount = $parsed["value"];
                        if (isset($parsed["attributes"]["currencyID"]))
                        {
                            $instance->TaxAmountCurrency = $parsed["attributes"]["currencyID"];
                        }
                        break;
                    case "TaxSubtotal":
                        $parsed = $reader->parseCurrentElement();
                        $instance->TaxSubtotals[] = $parsed["value"];
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading TaxTotal.");
            }
        }

        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CAC_SCHEMA . "TaxTotal";
    }


    public static function GetTestXML(): string
    {
        return '<cac:TaxTotal ' . self::NS_DEFINTIONS . '>
                    <cbc:TaxAmount currencyID="USD">0.00</cbc:TaxAmount>
                    ' . TaxSubTotal::GetTestXML() . TaxSubTotal::GetTestXML() . '
                </cac:TaxTotal>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof TaxTotal))
        {
            $reason = "Instance is not of type TaxTotal";
            return false;
        }
        if ($instance->TaxAmount != "0.00")
        {
            $reason = "TaxAmount is not 0.00 it is: " . $instance->TaxAmount;
            return false;
        }
        if (count($instance->TaxSubtotals) != 2)
        {
            $reason = "TaxSubTotals count is not 2";
            return false;
        }
        if (!TaxSubTotal::TestDefaultValues($instance->TaxSubtotals[0], $reason))
        {
            $reason = "First TaxSubTotal failed with reason: " . $reason;
            return false;
        }
        return true;
    }

    public function GetAmmount(): ?string
    {
        if(!isset($this->TaxAmount) || empty($this->TaxAmount))
        {
            return null;
        }
        return $this->TaxAmount. " " . $this->GetCurrency($this->TaxAmountCurrency);
    }
}