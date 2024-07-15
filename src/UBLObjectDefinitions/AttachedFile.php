<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;
use Sabre\Xml\Reader;
use XMLReader;

class AttachedFile extends UBLDeserializable
{
    public ?string $filePath  = null;
    public ?string $externalReference = null;
    public ?string $externalReferenceMimeType = null;


    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "FilePath":
                        $instance->filePath = $reader->readString();
                        $reader->next();
                        break;
                    case "ExternalReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->externalReference =$parsed["value"];
                        if(isset($parsed["attributes"]["mimeCode"]))
                        {
                            $instance->externalReferenceMimeType = $parsed["attributes"]["mimeCode"];
                        }
                        break;
                }
            }

            if (!$reader->read()) {
                throw new Exception("Invalid XML format");
            }
        }
        return $instance;
    }

    public static function GetNamespace(): string
    {
        return self::CBC_SCHEMA."Attachment";
    }

    public static function GetTestXML(): string
    {
        return '<cbc:Attachment '.self::NS_DEFINTIONS.'>
                    <cbc:FilePath>file.txt</cbc:FilePath>
                    <cbc:ExternalReference>http://example.com/file.txt</cbc:ExternalReference>
                </cbc:Attachment>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance==null)
        {
            $reason="Instance is null";
            return false;
        }
        if(!($instance instanceof AttachedFile))
        {
            $reason="Instance is not of type AttachedFile";
            return false;
        }
        if($instance->filePath!=="file.txt")
        {
            $reason="FilePath is not 'file.txt'";
            return false;
        }
        if($instance->externalReference!=="http://example.com/file.txt")
        {
            $reason="ExternalReference is not 'http://example.com/file.txt'";
            return false;
        }
        return true;
    }
}