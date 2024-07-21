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
use Exception;
use Sabre\Xml\Reader;

abstract class UBLDeserializable
{
    /**
     * Common Basic Components Schema
     */
    public const CBC_SCHEMA = "{urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2}";
    /**
     * Common Aggregate Components Schema
     */
    public const CAC_SCHEMA = "{urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2}";
    /**
     * XML namespace definitions for both Aggregate and Basic components, helps with GetTestXML methods
     */
    protected const NS_DEFINTIONS = 'xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"';
    /**
     * All implementers must define the method to implement deserialization logic
     * Implementations of this method should (almost) never be called directly
     */
    public static abstract function XMLDeserialize(Reader $reader): self;

    /**
     * All implementers must define the method to get the namespace of the object
     * This is used to create the XML node=>class mapping
     */
    public static abstract function GetNamespace(): string;

    /**
     * Used be ParseTest
     * @see \ParseTest
     * @param string $reason
     * @return bool
     */
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

    /**
     * Used internally by CanRender implementations
     * Checks if an array contains null values
     * @param array $params
     * @return bool
     */
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

    /**
     * Utility method to get the currency for a value, if the passed currency is null, it will default to the DocumentCurrencyCode if possible "RON" otherwise
     * @param string|null $currencyVar
     * @return string
     * @throws Exception if method is called out of order (not during invoice rendering)
     */
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

    /**
     * Converts XML Clark notation to a local name (from {namespace}localName to localName)
     * @param string $nameWithNamespace
     * @return string
     */
    protected function getLocalName(string $nameWithNamespace):string
    {
        return explode("}", $nameWithNamespace)[1];
    }

    /**
     * All implementers must define the method to check if the object can be rendered (contains all info used/required by the default template)
     * @return true|array
     */
    public abstract function CanRender(): true|array;

    /**
     * Optional, implementers can define and call this method after XMLDeserialize is complete
     * @return void
     */
    protected function DeserializeComplete(): void {}

    /**
     * All implementers must define the method to get a test XML string
     * This is used by ParseTest
     * TODO: this should be removed from this class and implementers and should be moved to appropriate places in tests/
     * @see \ParseTest
     * @return string
     */
    public static abstract function GetTestXML(): string;

    /**
     * Implementers should define this method to test if the default values are set correctly after parsing.
     * Called from ParseTest
     * TODO: this should be removed from this class and implementers and should be moved to appropriate places in tests/
     * @see UBLDeserializable::GetTestXML() for default values
     * @param UBLDeserializable|null $instance
     * @param string $reason reason for failure
     * @return bool
     */
    public static abstract function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool;
}