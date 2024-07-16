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

use DateTime;
use EdituraEDU\UBLRenderer\MappingsManager;
use Exception;
use Sabre\Xml\Reader;
use XMLReader;


class ParsedUBLInvoice extends UBLDeserializable
{
    public string $UBLVersionID;
    public string $CustomizationID = '1.0';
    public ?string $ID = null;
    public ?bool $CopyIndicator = null;
    public ?DateTime $IssueDate = null;
    public ?InvoiceTypeCode $InvoiceTypeCode = InvoiceTypeCode::INVOICE;
    public ?string $Note = null;
    public ?DateTime $TaxPointDate = null;
    public ?DateTime $DueDate = null;
    public ?PaymentTerms $PaymentTerms = null;
    public ?Party $AccountingSupplierParty = null;
    public ?Party $AccountingCustomerParty = null;
    public ?Party $PayeeParty = null;
    public ?string $SupplierAssignedAccountID = null;
    public ?PaymentMeans $PaymentMeans = null;
    public ?TaxTotal $TaxTotal = null;
    public ?LegalMonetaryTotal $LegalMonetaryTotal = null;
    /** @var InvoiceLine[]|null $InvoiceLines */
    public ?array $InvoiceLines = null;
    /** @var AllowanceCharge[]|null $AllowanceCharges */
    public ?array $AllowanceCharges = null;
    /** @var AdditionalDocumentReference[] $additionalDocumentReference */
    public array $AdditionalDocumentReferences = [];
    public ?string $DocumentCurrencyCode = null;
    public ?string $BuyerReference = null;
    public ?string $AccountingCostCode = null;
    public ?InvoicePeriod $InvoicePeriod = null;
    public ?Delivery $Delivery = null;
    public ?OrderReference $OrderReference = null;
    public ?ContractDocumentReference $ContractDocumentReference = null;

    public static function XMLDeserialize(Reader $reader): self
    {
        $instance = new self();
        $depth = $reader->depth;
        $reader->read(); // Move one child down

        while ($reader->nodeType != XMLReader::END_ELEMENT || $reader->depth > $depth)
        {
            if ($reader->nodeType == XMLReader::ELEMENT)
            {
                switch ($reader->localName)
                {
                    case "UBLVersionID":
                        $instance->UBLVersionID = $reader->readString();
                        $reader->next();
                        break;
                    case "CustomizationID":
                        $instance->CustomizationID = $reader->readString();
                        $reader->next();
                        break;
                    case "ID":
                        $instance->ID = $reader->readString();
                        $reader->next();
                        break;
                    case "CopyIndicator":
                        $instance->CopyIndicator = $reader->readString() === 'true';
                        $reader->next();
                        break;
                    case "IssueDate":
                        $instance->IssueDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "InvoiceTypeCode":
                        $instance->InvoiceTypeCode = InvoiceTypeCode::tryFrom($reader->readString()) ?? InvoiceTypeCode::INVALID;
                        $reader->next();
                        break;
                    case "Note":
                        $instance->Note = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxPointDate":
                        $instance->TaxPointDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "DueDate":
                        $instance->DueDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "PaymentTerms":
                        $parsed = $reader->parseCurrentElement();
                        $instance->PaymentTerms = $parsed["value"];
                        break;
                    case "AccountingSupplierParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->AccountingSupplierParty = $parsed["value"][0]["value"];
                        break;
                    case "AccountingCustomerParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->AccountingCustomerParty = $parsed["value"][0]["value"];
                        break;
                    case "PayeeParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->PayeeParty = $parsed["value"][0]["value"];
                        break;
                    case "SupplierAssignedAccountID":
                        $instance->SupplierAssignedAccountID = $reader->readString();
                        $reader->next();
                        break;
                    case "PaymentMeans":
                        $parsed = $reader->parseCurrentElement();
                        $instance->PaymentMeans = $parsed["value"];
                        break;
                    case "TaxTotal":
                        $parsed = $reader->parseCurrentElement();
                        $instance->TaxTotal = $parsed["value"];
                        break;
                    case "LegalMonetaryTotal":
                        $parsed = $reader->parseCurrentElement();
                        $instance->LegalMonetaryTotal = $parsed["value"];
                        break;
                    case "InvoiceLine":
                        $parsed = $reader->parseCurrentElement();
                        $instance->InvoiceLines[] = $parsed["value"];
                        break;
                    case "AllowanceCharge":
                        $parsed = $reader->parseCurrentElement();
                        $instance->AllowanceCharges[] = $parsed["value"];
                        break;
                    case "AdditionalDocumentReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->AdditionalDocumentReferences[] = $parsed["value"];
                        break;
                    case "DocumentCurrencyCode":
                        $instance->DocumentCurrencyCode = $reader->readString();
                        $reader->next();
                        break;
                    case "BuyerReference":
                        $instance->BuyerReference = $reader->readString();
                        $reader->next();
                        break;
                    case "AccountingCostCode":
                        $instance->AccountingCostCode = $reader->readString();
                        $reader->next();
                        break;
                    case "InvoicePeriod":
                        $parsed = $reader->parseCurrentElement();
                        $instance->InvoicePeriod = $parsed["value"];
                        break;
                    case "Delivery":
                        $parsed = $reader->parseCurrentElement();
                        $instance->Delivery = $parsed["value"];
                        break;
                    case "OrderReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->OrderReference = $parsed["value"];
                        break;
                    case "ContractDocumentReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->ContractDocumentReference = $parsed["value"];
                        break;
                }
            }
            if (!$reader->read())
            {
                throw new Exception("Unexpected end of XML file while reading Invoice.");
            }
        }

        return $instance;
    }

    

    public static function GetNamespace(): string
    {
        return "{urn:oasis:names:specification:ubl:schema:xsd:Invoice-2}Invoice";
    }
    public function HasSupplierAccountInfo():bool
    {
        return isset($this->PaymentMeans->PayeeFinancialAccount);
    }

    public function HasAnyItemIDs():bool
    {
        if(empty($this->InvoiceLines))
        {
            return false;
        }
        $count=count($this->InvoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(isset($this->InvoiceLines[$i]->Item->SellersItemIdentification) || isset($this->InvoiceLines[$i]->Item->BuyersItemIdentification))
            {
                return true;
            }
        }
        return false;
    }

    public function AllItemsHaveShortUnitCodeMapped():bool
    {
        if(empty($this->InvoiceLines))
        {
            return false;
        }
        $count=count($this->InvoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(!$this->InvoiceLines[$i]->HasShortMappedUnitCode())
            {
                return false;
            }
        }
        return false;
    }

    public function CanShowUnitCodeDetails():bool
    {
        if(empty($this->InvoiceLines))
        {
            return false;
        }
        if($this->AllItemsHaveShortUnitCodeMapped())
        {
            return false;
        }
        $count=count($this->InvoiceLines);
        $foundSomeDetails=false;
        for($i=0;$i<$count;$i++)
        {
            if($this->InvoiceLines[$i]->HasMappedUnitCode())
            {
               $foundSomeDetails=true;
               break;
            }
        }
        return $foundSomeDetails;
    }

    public function GetLineNumber(InvoiceLine $line):int
    {
        if(empty($this->InvoiceLines))
        {
            throw new Exception("Invoice has no lines");
        }
        $count=count($this->InvoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if($this->InvoiceLines[$i]===$line)
            {
                return $i+1;
            }
        }
        throw new Exception("InvoiceLine instance not found in ParsedUBLInvoice::InvoiceLines");
    }

    public function HasDueDate():bool
    {
        if(isset($this->DueDate))
        {
            return true;
        }
        if(isset($this->PaymentTerms->PaymentDueDate))
        {
            return true;
        }
        if(isset($this->PaymentTerms->SettlementPeriod->EndDate))
        {
            return true;
        }
        return false;
    }

    public function GetDueDate():DateTime
    {
        if(!$this->HasDueDate())
        {
            throw new Exception("Invoice due date not found");
        }
        if(isset($this->DueDate))
        {
            return $this->DueDate;
        }
        if(isset($this->PaymentTerms->PaymentDueDate))
        {
            return $this->PaymentTerms->PaymentDueDate;
        }
        return $this->PaymentTerms->SettlementPeriod->EndDate;
    }

    public function HasNotes()
    {
        if(isset($this->Note) && !empty($this->Note))
        {
            return true;
        }
        $count=count($this->InvoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(isset($this->InvoiceLines[$i]->Note) && !empty($this->InvoiceLines[$i]->Note))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string[]
     */
    public function GetNotes():array
    {
        $result=[];
        if(isset($this->Note) && !empty($this->Note))
        {
            $result[]=$this->Note;
        }
        $count=count($this->InvoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(isset($this->InvoiceLines[$i]->Note) && !empty($this->InvoiceLines[$i]->Note))
            {
                $result[]=$this->InvoiceLines[$i]->Note. " (linia ".$this->GetLineNumber($this->InvoiceLines[$i]).")";
            }
        }
        if(count($result)==0)
        {
            throw new Exception("No notes found in invoice");
        }
        return $result;
    }

    public function HasInvoiceLevelAllowanceCharges():bool
    {
        return isset($this->AllowanceCharges) && !empty($this->AllowanceCharges);
    }

    public function HasOtherInfo():bool
    {
        return (isset($this->OrderReference) && $this->OrderReference->HasValidID())
            || isset($this->PaymentMeans->PaymentMeansCode)
            || $this->HasAttachments()
            || isset($this->ContractDocumentReference);
    }

    /**
     * @return string[]
     */
    public function GetOtherInfo():array
    {
        $result=[];
        if(isset($this->OrderReference))
        {
            if(isset($this->OrderReference->ID) && !empty($this->OrderReference->ID))
            {
                $result[]="Comanda: ".$this->OrderReference->ID;
            }
            else if(isset($this->OrderReference->SalesOrderID) && !empty($this->OrderReference->SalesOrderID))
            {
                $result[]="Comanda: ".$this->OrderReference->SalesOrderID;
            }
        }
        if(isset($this->PaymentMeans->PaymentMeansCode))
        {
            $paymentMeans="Modalitatea preferată de plata: ".$this->PaymentMeans->PaymentMeansCode->value;
            if(MappingsManager::GetInstance()->PaymentMeansCodeHasMapping($this->PaymentMeans->PaymentMeansCode->value))
            {
                $paymentMeans.=" (".MappingsManager::GetInstance()->GetPaymentMeansCodeMapping($this->PaymentMeans->PaymentMeansCode->value).")";
            }
            $result[]=$paymentMeans;
        }
        if($this->HasAttachments())
        {
            $result[]="Există documente atașate in fișierul XML";
        }
        return $result;
    }


    public static function GetTestXML(): string
    {
        return file_get_contents(dirname(__FILE__) . "/../../tests/efactura_sample_invoice.xml");
    }

    public static function TestDefaultValues(?UBLDeserializable $instance, string &$reason): bool
    {
        if ($instance == null)
        {
            $reason = "Instance is null";
            return false;
        }
        if (!($instance instanceof ParsedUBLInvoice))
        {
            $reason = "Instance is not of type ParsedUBLInvoice";
            return false;
        }
        if ($instance->UBLVersionID != "2.1")
        {
            $reason = "UBLVersionID is not 2.1";
            return false;
        }
        if ($instance->CustomizationID != "urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.0")
        {
            $reason = "CustomizationID is not urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.0";
            return false;
        }
        if ($instance->DocumentCurrencyCode != "RON")
        {
            $reason = "DocumentCurrencyCode is not RON";
            return false;
        }
        if ($instance->InvoiceTypeCode != InvoiceTypeCode::INVOICE)
        {
            $reason = "InvoiceTypeCode is not INVOICE";
            return false;
        }
        if ($instance->IssueDate->format("Y-m-d") != "2022-05-31")
        {
            $reason = "IssueDate is not 2022-05-31";
            return false;
        }
        if ($instance->DueDate->format("Y-m-d") != "2022-05-31")
        {
            $reason = "DueDate is not 2022-05-31";
            return false;
        }
        if ($instance->AccountingSupplierParty == null)
        {
            $reason = "AccountingSupplierParty is null";
            return false;
        }
        if ($instance->AccountingCustomerParty == null)
        {
            $reason = "AccountingCustomerParty is null";
            return false;
        }
        if ($instance->AccountingSupplierParty->Name != "Seller SRL")
        {
            $reason = "AccountingSupplierParty name is not Seller SRL";
            return false;
        }
        if ($instance->AccountingCustomerParty->Name != "Buyer name")
        {
            $reason = "AccountingCustomerParty name is not Buyer name";
            return false;
        }
        if ($instance->AccountingCustomerParty->PartyIdentificationId != "123456")
        {
            $reason = "AccountingCustomerParty partyIdentificationId is not 123456";
            return false;
        }
        if ($instance->AccountingSupplierParty->PostalAddress->StreetName != "line1")
        {
            $reason = "AccountingSupplierParty postalAddress streetName is not line1";
            return false;
        }
        if ($instance->AccountingCustomerParty->PostalAddress->StreetName != "BD DECEBAL NR 1 ET1")
        {
            $reason = "AccountingCustomerParty postalAddress streetName is not BD DECEBAL NR 1 ET1";
            return false;
        }
        if ($instance->AccountingSupplierParty->PostalAddress->CityName != "SECTOR1")
        {
            $reason = "AccountingSupplierParty postalAddress cityName is not SECTOR1";
            return false;
        }
        if ($instance->AccountingCustomerParty->PostalAddress->CityName != "ARAD")
        {
            $reason = "AccountingCustomerParty postalAddress cityName is not ARAD";
            return false;
        }
        if ($instance->AccountingSupplierParty->PostalAddress->PostalZone != "013329")
        {
            $reason = "AccountingSupplierParty postalAddress postalZone is not 013329";
            return false;
        }
        if ($instance->AccountingCustomerParty->PostalAddress->PostalZone != "123456")
        {
            $reason = "AccountingCustomerParty postalAddress postalZone is not 123456";
            return false;
        }
        if ($instance->AccountingSupplierParty->PostalAddress->CountrySubentity != "RO-B")
        {
            $reason = "AccountingSupplierParty postalAddress countrySubentity is not RO-B";
            return false;
        }
        if ($instance->AccountingCustomerParty->PostalAddress->CountrySubentity != "RO-AR")
        {
            $reason = "AccountingCustomerParty postalAddress countrySubentity is not RO-AR";
            return false;
        }
        if ($instance->AccountingSupplierParty->PartyTaxScheme->CompanyId != "RO1234567890")
        {
            $reason = "AccountingSupplierParty partyTaxScheme companyID is not RO1234567890";
            return false;
        }
        if ($instance->AccountingCustomerParty->PartyTaxScheme->CompanyId != "RO987456123")
        {
            $reason = "AccountingCustomerParty partyTaxScheme companyId is not RO987456123";
            return false;
        }
        if ($instance->AccountingSupplierParty->PartyTaxScheme->TaxScheme->ID != "VAT")
        {
            $reason = "AccountingSupplierParty partyTaxScheme taxScheme id is not VAT";
            return false;
        }
        if ($instance->AccountingCustomerParty->PartyTaxScheme->TaxScheme->ID != "VAT")
        {
            $reason = "AccountingCustomerParty partyTaxScheme taxScheme id is not VAT";
            return false;
        }
        if ($instance->AccountingSupplierParty->LegalEntity->RegistrationName != "Seller SRL")
        {
            $reason = "AccountingSupplierParty partyLegalEntity registrationName is not Seller SRL";
            return false;
        }
        if ($instance->AccountingCustomerParty->LegalEntity->RegistrationName != "Buyer SRL")
        {
            $reason = "AccountingCustomerParty partyLegalEntity registrationName is not Buyer SRL";
            return false;
        }
        if ($instance->AccountingSupplierParty->LegalEntity->CompanyLegalForm != "J40/12345/1998")
        {
            $reason = "AccountingSupplierParty partyLegalEntity companyLegalForm is not J40/12345/1998";
            return false;
        }
        if ($instance->AccountingCustomerParty->LegalEntity->CompanyID != "J02/321/2010")
        {
            $reason = "AccountingCustomerParty partyLegalEntity companyLegalForm is not J02/321/2010";
            return false;
        }
        if ($instance->AccountingSupplierParty->Contact->ElectronicMail != "mail@seller.com")
        {
            $reason = "AccountingSupplierParty contact electronicMail is not mail@seller.com";
            return false;
        }
        if ($instance->AccountingCustomerParty->Contact != null)
        {
            $reason = "AccountingCustomerParty contact contact is not empty";
            return false;
        }
        if ($instance->PaymentMeans->PaymentMeansCode != PaymentMeansCode::DebitTransfer)
        {
            $reason = "PaymentMeans paymentMeansCode is not 31(DebitTransfer)";
            return false;
        }
        if ($instance->PaymentMeans->PayeeFinancialAccount->ID != "RO80RNCB0067054355123456")
        {
            $reason = "PaymentMeans payeeFinancialAccount id is not RO80RNCB0067054355123456";
            return false;
        }
        if ($instance->TaxTotal->TaxAmount != 6598592.6)
        {
            $reason = "TaxTotal taxAmount is not 6598592.6";
            return false;
        }
        if ($instance->TaxTotal->TaxSubtotals[0]->TaxableAmount !== "696.12")
        {
            $reason = "TaxTotal taxSubtotals[0] taxableAmount is not 696.12 (" . $instance->TaxTotal->TaxSubtotals[0]->TaxableAmount . ")";
            echo json_encode($instance->TaxTotal);
            return false;
        }
        if ($instance->TaxTotal->TaxSubtotals[0]->TaxAmount !== "34.79")
        {
            $reason = "TaxTotal taxSubtotals[0] taxAmount is not 34.79";
            return false;
        }
        if ($instance->TaxTotal->TaxSubtotals[0]->TaxableAmount !== "696.12")
        {
            $reason = "TaxTotal taxSubtotals[0] taxableAmount is not 696.12";
            return false;
        }
        if ($instance->TaxTotal->TaxSubtotals[0]->TaxCategory->ID != "S")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory id is not S";
            return false;
        }
        if ($instance->TaxTotal->TaxSubtotals[0]->TaxCategory->Percent !== "5.00")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory percent is not 5.00";
            return false;
        }
        if ($instance->TaxTotal->TaxSubtotals[0]->TaxCategory->TaxScheme->ID != "VAT")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory taxScheme id is not VAT";
            return false;
        }
        if ($instance->LegalMonetaryTotal->LineExtensionAmount !== "34741984.11")
        {
            $reason = "LegalMonetaryTotal lineExtensionAmount is not 34741984.11";
            return false;
        }
        if ($instance->LegalMonetaryTotal->TaxExclusiveAmount !== "34741984.11")
        {
            $reason = "LegalMonetaryTotal taxExclusiveAmount is not 34741984.11";
            return false;
        }
        if ($instance->LegalMonetaryTotal->TaxInclusiveAmount !== "41340576.71")
        {
            $reason = "LegalMonetaryTotal taxInclusiveAmount is not 41340576.71";
            return false;
        }
        if ($instance->LegalMonetaryTotal->PayableAmount !== "41340576.71")
        {
            $reason = "LegalMonetaryTotal payableAmount is not 41340576.71";
            return false;
        }
        if ($instance->LegalMonetaryTotal->LineExtensionCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal lineExtensionCurrencyID is not RON";
            return false;
        }
        if ($instance->LegalMonetaryTotal->TaxExclusiveCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal taxExclusiveCurrencyID is not RON";
            return false;
        }
        if ($instance->LegalMonetaryTotal->TaxInclusiveCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal taxInclusiveCurrencyID is not RON";
            return false;
        }
        if ($instance->LegalMonetaryTotal->PayableCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal payableCurrencyID is not RON";
            return false;
        }
        if ($instance->InvoiceLines == null)
        {
            $reason = "InvoiceLines is null";
            return false;
        }
        if (count($instance->InvoiceLines) != 35)
        {
            $reason = "InvoiceLines count is not 35";
            return false;
        }
        /** @var InvoiceLine $line */
        $line = $instance->InvoiceLines[0];
        if ($line->ID != "1")
        {
            $reason = "InvoiceLines[0] id is not 1";
            return false;
        }
        if ($line->InvoicedQuantity !== "46396.67")
        {
            $reason = "InvoiceLines[0] invoicedQuantity is not 46396.67";
            return false;
        }
        if ($line->UnitCode != "C62")
        {
            $reason = "InvoiceLines[0] unitCode is not C62";
            return false;
        }
        if ($line->LineExtensionAmount != "334641.38")
        {
            $reason = "InvoiceLines[0] lineExtensionAmount is not 334641.38";
            return false;
        }
        if ($line->LineExtensionAmountCurrencyID != "RON")
        {
            $reason = "InvoiceLines[0] lineExtensionCurrencyID is not RON";
            return false;
        }
        if ($line->AllAllowanceCharges == null)
        {
            $reason = "InvoiceLines[0] allowanceCharges is null";
            return false;
        }
        if (count($line->AllAllowanceCharges) != 2)
        {
            $reason = "InvoiceLines[0] allowanceCharges count is not 2";
            return false;
        }
        if ($line->AllAllowanceCharges[0]->ChargeIndicator !== false)
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] chargeIndicator is not false";
            return false;
        }
        if ($line->AllAllowanceCharges[0]->Amount !== "801.98")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] amount is not 801.98";
            return false;
        }
        if ($line->AllAllowanceCharges[0]->AmountCurrency != "RON")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] amountCurrencyID is not RON";
            return false;
        }
        if ($line->AllAllowanceCharges[1]->BaseAmountCurrency != "RON")
        {
            $reason = "InvoiceLines[0] allowanceCharges[1] baseAmountCurrencyID is not RON";
            return false;
        }
        if ($line->AllAllowanceCharges[1]->BaseAmount !== "354715.84")
        {
            $reason = "InvoiceLines[0] allowanceCharges[1] baseAmount is not 354715.84";
            return false;
        }
        if ($line->AllAllowanceCharges[0]->AllowanceChargeReasonCode !== "95")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] allowanceChargeReasonCode is not 95";
            return false;
        }
        if ($line->AllAllowanceCharges[0]->AllowanceChargeReason != "Discount")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] allowanceChargeReason is not Discount";
            return false;
        }
        if ($line->Item == null)
        {
            $reason = "InvoiceLines[0] item is null";
            return false;
        }
        if ($line->Item->Name != "item name")
        {
            $reason = "InvoiceLines[0] item name is not item name";
            return false;
        }
        if ($line->Item->SellersItemIdentification != "0102")
        {
            $reason = "InvoiceLines[0] item sellersItemIdentification is not 0102";
            return false;
        }
        if ($line->Item->CommodityClassification != "03222000-3")
        {
            $reason = "InvoiceLines[0] item commodityClassification is not 03222000-3";
            return false;
        }
        if ($line->Item->CommodityClassificationListID != "STI")
        {
            $reason = "InvoiceLines[0] item commodityClassificationListID is not STI";
            return false;
        }
        if ($line->Item->ClassifiedTaxCategory == null)
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory is null";
            return false;
        }
        if ($line->Item->ClassifiedTaxCategory->ID != "S")
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory id is not S";
            return false;
        }
        if ($line->Item->ClassifiedTaxCategory->Percent !== "19.00")
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory percent is not 5.00";
            return false;
        }
        if ($line->Item->ClassifiedTaxCategory->TaxScheme->ID != "VAT")
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory taxScheme id is not VAT";
            return false;
        }
        if ($line->Price == null)
        {
            $reason = "InvoiceLines[0] price is null";
            return false;
        }
        if ($line->Price->PriceAmount !== "7.6453")
        {
            $reason = "InvoiceLines[0] price priceAmount is not 46396.67";
            return false;
        }
        if ($line->Price->PriceCurrencyID != "RON")
        {
            $reason = "InvoiceLines[0] price priceAmountCurrencyID is not RON";
            return false;
        }
        if ($line->Price->BaseQuantity != 1)
        {
            $reason = "InvoiceLines[0] price baseQuantity is not 1";
            return false;
        }
        if ($line->Price->UnitCode != "C62")
        {
            $reason = "InvoiceLines[0] price baseQuantityUnitCode is not C62";
            return false;
        }
        return true;
    }

    public function HasAttachments():bool
    {
        return (isset($this->AdditionalDocumentReferences) && !empty($this->AdditionalDocumentReferences)) ||
            isset($this->ContractDocumentReference);
    }
}