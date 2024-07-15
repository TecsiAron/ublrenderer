<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use EdituraEDU\UBLRenderer\XMLReaderProvider;
use Sabre\Xml\Reader;

abstract class UBLDeserializable
{
    public const CBC_SCHEMA = "{urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2}";
    public const CAC_SCHEMA = "{urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2}";
    protected const NS_DEFINTIONS='xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"';

    public static abstract function XMLDeserialize(Reader $reader): self;

    public static abstract function GetNamespace(): string;

    public static function TestParse(string &$reason): bool
    {
        $xml= static::GetTestXML();
        $reader = XMLReaderProvider::CreateReader();
        $reader->xml($xml, null, LIBXML_NONET | LIBXML_NOERROR);
        $reader->read();
        while($reader->localName=="#comment")
        {
            $reader->read();
        }
        $clark = $reader->getClark();
        $parsed = $reader->parseCurrentElement();
        $parsedClass = $parsed["value"];
        if($parsedClass==null || is_array($parsedClass))
        {
            $reason="Failed to parse ".static::class. " with Clark:".$clark;
            return false;
        }
        return static::TestDefaultValues($parsedClass, $reason);
    }
    protected function DeserializeComplete():void { }
    public static abstract function GetTestXML():string;

    public static abstract function TestDefaultValues(?UBLDeserializable $instance, string &$reason):bool;
}