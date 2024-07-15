<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

use Exception;

class AdditionalDocumentReference extends UBLDeserializable
{
    public ?string $id = null;
    public ?string $documentType = null;
    public ?string $documentTypeCode = null;
    public ?string $documentDescription = null;
    public ?AttachedFile $attachment = null;

    public static function XMLDeserialize(\Sabre\Xml\Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != \XMLReader::END_ELEMENT || $reader->depth > $depth) {
            if ($reader->nodeType == \XMLReader::ELEMENT) {
                switch ($reader->localName) {
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next();
                        break;
                    case "DocumentType":
                        $instance->documentType = $reader->readString();
                        $reader->next();
                        break;
                    case "DocumentTypeCode":
                        $instance->documentTypeCode = $reader->readString();
                        $reader->next();
                        break;
                    case "DocumentDescription":
                        $instance->documentDescription = $reader->readString();
                        $reader->next();
                        break;
                    case "Attachment":
                        $parsed = $reader->parseCurrentElement();
                        $instance->attachment = $parsed["value"];
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
        return self::CAC_SCHEMA."AdditionalDocumentReference";
    }

    public static function GetTestXML(): string
    {
        return '<cac:AdditionalDocumentReference '.self::NS_DEFINTIONS.'>
                    <cbc:ID>1</cbc:ID>
                    <cbc:DocumentType>Invoice</cbc:DocumentType>
                    <cbc:DocumentTypeCode>380</cbc:DocumentTypeCode>
                    <cbc:DocumentDescription>Invoice</cbc:DocumentDescription>
                    '.AttachedFile::GetTestXML().'
                </cac:AdditionalDocumentReference>';
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if($instance==null)
        {
            $reason="Instance is null";
            return false;
        }
        if(!($instance instanceof AdditionalDocumentReference))
        {
            $reason="Instance is not of type AdditionalDocumentReference";
            return false;
        }
        if($instance->id!="1")
        {
            $reason="ID is not 1";
            return false;
        }
        if($instance->documentType!="Invoice")
        {
            $reason="DocumentType is not Invoice";
            return false;
        }
        if($instance->documentTypeCode!="380")
        {
            $reason="DocumentTypeCode is not 380";
            return false;
        }
        if($instance->documentDescription!="Invoice")
        {
            $reason="DocumentDescription is not Invoice";
            return false;
        }
        if(!AttachedFile::TestDefaultValues($instance->attachment, $reason))
        {
            return false;
        }
        return true;
    }
}