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

use EdituraEDU\UBLRenderer\UBLRenderer;
use EdituraEDU\UBLRenderer\XMLReaderProvider;
use Sabre\Xml\Reader;

abstract class UBLDeserializable
{
    public const CBC_SCHEMA = "{urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2}";
    public const CAC_SCHEMA = "{urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2}";
    protected const NS_DEFINTIONS = 'xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"';

    public static abstract function XMLDeserialize(Reader $reader): self;

    public static abstract function GetNamespace(): string;

    public static function TestParse(string &$reason): bool
    {
        $xml = static::GetTestXML();
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($xml, null, LIBXML_NONET | LIBXML_NOERROR);
        $reader->read();
        while ($reader->localName == "#comment")
        {
            $reader->read();
        }
        $clark = $reader->getClark();
        $parsed = $reader->parseCurrentElement();
        $parsedClass = $parsed["value"];
        if ($parsedClass == null || is_array($parsedClass))
        {
            $reason = "Failed to parse " . static::class . " with Clark:" . $clark;
            return false;
        }
        return static::TestDefaultValues($parsedClass, $reason);
    }

    protected function ContainsNull(array $params):bool
    {
        foreach ($params as $param)
        {
            if ($param == null)
            {
                return true;
            }
        }
        return false;
    }

    protected function GetCurrency(?string &$currencyVar): string
    {
        if ($currencyVar == null)
        {
            if(!empty(UBLRenderer::GetCurrentInvoice()->DocumentCurrencyCode))
            {
                return UBLRenderer::GetCurrentInvoice()->DocumentCurrencyCode;
            }
            return "RON";
        }
        return $currencyVar;
    }

    //todo define mandatory non static method CanRender():bool

    protected function DeserializeComplete(): void {}

    public static abstract function GetTestXML(): string;

    public static abstract function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool;
}