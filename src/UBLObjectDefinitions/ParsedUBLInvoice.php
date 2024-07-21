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
use Sabre\Xml\LibXMLException;
use Sabre\Xml\ParseException;
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

    /**
     * This is the only implementation of XMLDeserialize that is called directly
     * @param Reader $reader
     * @return ParsedUBLInvoice
     * @throws LibXMLException
     * @throws ParseException
     * @throws Exception if end of XML is reached before Invoice node is found or if Invoice node does not parse to an assoc array
     * @see UBLDeserializable::XMLDeserialize()
     */
    public static function XMLDeserialize(Reader $reader): ParsedUBLInvoice
    {
        $instance = new self();
        $clark = $reader->getClark();
        while ($clark != "{urn:oasis:names:specification:ubl:schema:xsd:Invoice-2}Invoice")
        {
            $reader->read();
            $clark = $reader->getClark();
            if ($reader->nodeType === XMLReader::NONE)
            {
                throw new Exception("Invalid XML structure for Invoice");
            }
        }
        $parsedInvoice = $reader->parseInnerTree();
        if (!is_array($parsedInvoice))
        {
            throw new Exception("Invalid XML structure for Invoice");
        }
        for ($i = 0; $i < count($parsedInvoice); $i++)
        {
            $parsed = $parsedInvoice[$i];
            if ($parsed["value"] === null)
            {
                continue;
            }
            $localName = $instance->getLocalName($parsed["name"]);
            switch ($localName)
            {
                case "UBLVersionID":
                    $instance->UBLVersionID = $parsed["value"];
                    break;
                case "CustomizationID":
                    $instance->CustomizationID = $parsed["value"];
                    break;
                case "ID":
                    $instance->ID = $parsed["value"];
                    break;
                case "CopyIndicator":
                    $instance->CopyIndicator = $parsed["value"] === 'true';
                    break;
                case "IssueDate":
                    $instance->IssueDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
                case "InvoiceTypeCode":
                    $instance->InvoiceTypeCode = InvoiceTypeCode::tryFrom($parsed["value"]) ?? InvoiceTypeCode::INVALID;
                    break;
                case "Note":
                    $instance->Note = $parsed["value"];
                    break;
                case "TaxPointDate":
                    $instance->TaxPointDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
                case "DueDate":
                    $instance->DueDate = DateTime::createFromFormat("Y-m-d", $parsed["value"]);
                    break;
                case "PaymentTerms":
                    $instance->PaymentTerms = $parsed["value"];
                    break;
                case "AccountingSupplierParty":
                    $instance->AccountingSupplierParty = $parsed["value"][0]["value"];
                    break;
                case "AccountingCustomerParty":
                    $instance->AccountingCustomerParty = $parsed["value"][0]["value"];
                    $instance->AccountingCustomerParty->IsBuyer = true;
                    break;
                case "PayeeParty":
                    $instance->PayeeParty = $parsed["value"][0]["value"];
                    break;
                case "SupplierAssignedAccountID":
                    $instance->SupplierAssignedAccountID = $parsed["value"];
                    break;
                case "PaymentMeans":
                    $instance->PaymentMeans = $parsed["value"];
                    break;
                case "TaxTotal":
                    $instance->TaxTotal = $parsed["value"];
                    break;
                case "LegalMonetaryTotal":
                    $instance->LegalMonetaryTotal = $parsed["value"];
                    break;
                case "InvoiceLine":
                    $instance->InvoiceLines[] = $parsed["value"];
                    break;
                case "AllowanceCharge":
                    $instance->AllowanceCharges[] = $parsed["value"];
                    break;
                case "AdditionalDocumentReference":
                    $instance->AdditionalDocumentReferences[] = $parsed["value"];
                    break;
                case "DocumentCurrencyCode":
                    $instance->DocumentCurrencyCode = $parsed["value"];
                    break;
                case "BuyerReference":
                    $instance->BuyerReference = $parsed["value"];
                    break;
                case "AccountingCostCode":
                    $instance->AccountingCostCode = $parsed["value"];
                    break;
                case "InvoicePeriod":
                    $instance->InvoicePeriod = $parsed["value"];
                    break;
                case "Delivery":
                    $instance->Delivery = $parsed["value"];
                    break;
                case "OrderReference":
                    $instance->OrderReference = $parsed["value"];
                    break;
                case "ContractDocumentReference":
                    $instance->ContractDocumentReference = $parsed["value"];
                    break;
            }
        }
        $instance->DeserializeComplete();
        return $instance;
    }

    /**
     * Checks if AccountingSupplierParty has a valid registration number and if not, tries to set it from the BuyerReference
     * @return void
     */
    protected function DeserializeComplete(): void
    {
        if (isset($this->AccountingSupplierParty))
        {
            if ($this->AccountingCustomerParty->GetRegistrationNumber() == null && isset($this->BuyerReference) && !empty($this->BuyerReference))
            {
                $this->AccountingCustomerParty->ForcedRegistrationNumber = $this->BuyerReference;
            }
        }
    }


    public static function GetNamespace(): string
    {
        return "{urn:oasis:names:specification:ubl:schema:xsd:Invoice-2}Invoice";
    }

    /**
     * Checks for PaymentMeans PayeeFinancialAccount
     * @return bool
     */
    public function HasSupplierAccountInfo(): bool
    {
        return isset($this->PaymentMeans->PayeeFinancialAccount) && !empty($this->PaymentMeans->PayeeFinancialAccount->ID);
    }

    /**
     * Checks if any InvoiceLine has a SellersItemIdentification or BuyersItemIdentification
     * @return bool
     */
    public function HasAnyItemIDs(): bool
    {
        if (empty($this->InvoiceLines))
        {
            return false;
        }
        $count = count($this->InvoiceLines);
        for ($i = 0; $i < $count; $i++)
        {
            if (isset($this->InvoiceLines[$i]->Item->SellersItemIdentification) || isset($this->InvoiceLines[$i]->Item->BuyersItemIdentification))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if any InvoiceLine have a mapped short (max 4 chars by default) unit code
     * @return bool
     */
    public function AllItemsHaveShortUnitCodeMapped(): bool
    {
        if (empty($this->InvoiceLines))
        {
            return false;
        }
        $count = count($this->InvoiceLines);
        for ($i = 0; $i < $count; $i++)
        {
            if (!$this->InvoiceLines[$i]->HasShortMappedUnitCode())
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if any InvoiceLine has a mapped unit code
     * Should be called after AllItemsHaveShortUnitCodeMapped returns false
     * @return bool
     */
    public function CanShowUnitCodeDetails(): bool
    {
        if (empty($this->InvoiceLines))
        {
            return false;
        }
        if ($this->AllItemsHaveShortUnitCodeMapped())
        {
            return false;
        }
        $count = count($this->InvoiceLines);
        $foundSomeDetails = false;
        for ($i = 0; $i < $count; $i++)
        {
            if ($this->InvoiceLines[$i]->HasMappedUnitCode())
            {
                $foundSomeDetails = true;
                break;
            }
        }
        return $foundSomeDetails;
    }

    /**
     * Since allowance charges do generate a line but should not be counted (causing the loop count to be unusable), this method maps actual InvoiceLine instances to their line number
     * @param InvoiceLine $line
     * @return int
     * @throws Exception if Invoice has no lines or if the line is not found in this instance
     */
    public function GetLineNumber(InvoiceLine $line): int
    {
        if (empty($this->InvoiceLines))
        {
            throw new Exception("Invoice has no lines");
        }
        $count = count($this->InvoiceLines);
        for ($i = 0; $i < $count; $i++)
        {
            if ($this->InvoiceLines[$i] === $line)
            {
                return $i + 1;
            }
        }
        throw new Exception("InvoiceLine instance not found in ParsedUBLInvoice::InvoiceLines");
    }

    /**
     * Checks for due date in:
     * DueDate
     * PaymentTerms PaymentDueDate
     * PaymentTerms SettlementPeriod EndDate
     * If at least one is found, returns true
     * @return bool
     */
    public function HasDueDate(): bool
    {
        if (isset($this->DueDate))
        {
            return true;
        }
        if (isset($this->PaymentTerms->PaymentDueDate))
        {
            return true;
        }
        if (isset($this->PaymentTerms->SettlementPeriod->EndDate))
        {
            return true;
        }
        return false;
    }

    /**
     * Should only be called after HasDueDate returns true
     * Returns (in order of priority):
     * DueDate
     * PaymentTerms PaymentDueDate
     * PaymentTerms SettlementPeriod EndDate
     * @return DateTime
     * @throws Exception if HasDueDate returns false
     */
    public function GetDueDate(): DateTime
    {
        if (!$this->HasDueDate())
        {
            throw new Exception("Invoice due date not found");
        }
        if (isset($this->DueDate))
        {
            return $this->DueDate;
        }
        if (isset($this->PaymentTerms->PaymentDueDate))
        {
            return $this->PaymentTerms->PaymentDueDate;
        }
        return $this->PaymentTerms->SettlementPeriod->EndDate;
    }

    /**
     * Checks for non-empty notes
     * @return bool
     */
    public function HasNotes()
    {
        if (isset($this->Note) && !empty($this->Note))
        {
            return true;
        }
        $count = count($this->InvoiceLines);
        for ($i = 0; $i < $count; $i++)
        {
            if (isset($this->InvoiceLines[$i]->Note) && !empty($this->InvoiceLines[$i]->Note))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets all notes from the invoice
     * @return string[]
     * @throws Exception if line note is found, but GetLineNumber fails
     */
    public function GetNotes(): array
    {
        if (!$this->HasNotes())
        {
            return [];
        }
        $result = [];
        if (isset($this->Note) && !empty($this->Note))
        {
            $result[] = $this->Note;
        }
        $count = count($this->InvoiceLines);
        for ($i = 0; $i < $count; $i++)
        {
            if (isset($this->InvoiceLines[$i]->Note) && !empty($this->InvoiceLines[$i]->Note))
            {
                $result[] = $this->InvoiceLines[$i]->Note . " (linia " . $this->GetLineNumber($this->InvoiceLines[$i]) . ")";
            }
        }
        if (count($result) == 0)
        {
            throw new Exception("No notes found in invoice");
        }
        return $result;
    }

    /**
     * Checks for invoice level allowance charges (allowance charges in invoice lines are not counted here)
     * @return bool
     */
    public function HasInvoiceLevelAllowanceCharges(): bool
    {
        return isset($this->AllowanceCharges) && !empty($this->AllowanceCharges);
    }

    /**
     * Checks for:
     * Valid OrderReference ID
     * Valid PaymentMeansCode
     * Attached files
     * ContractDocumentReference
     * Items with long unit codes
     * Returns true if any of the above is true
     * @return bool
     */
    public function HasOtherInfo(): bool
    {
        /*$unitCodeCheck=(!$this->AllItemsHaveShortUnitCodeMapped() && $this->CanShowUnitCodeDetails());
        $orderRefCheck=(isset($this->OrderReference) && $this->OrderReference->HasValidID());
        $paymentMeansCheck=isset($this->PaymentMeans->PaymentMeansCode);
        $attachmentsCheck=$this->HasAttachments();
        $contractDocCheck=isset($this->ContractDocumentReference);*/
        return (isset($this->OrderReference) && $this->OrderReference->HasValidID())
            || isset($this->PaymentMeans->PaymentMeansCode)
            || $this->HasAttachments()
            || isset($this->ContractDocumentReference)
            || (!$this->AllItemsHaveShortUnitCodeMapped() && $this->CanShowUnitCodeDetails());
    }

    /**
     * Gets a string array with additional information about the invoice
     * HasOtherInfo should be called first to check if there is any additional info to show
     * @return string[]
     * @throws Exception on MappingsManager errors
     */
    public function GetOtherInfo(): array
    {
        $result = [];
        if (!$this->AllItemsHaveShortUnitCodeMapped() && $this->CanShowUnitCodeDetails())
        {
            $lineCount = count($this->InvoiceLines);
            for ($i = 0; $i < $lineCount; $i++)
            {
                if ($this->InvoiceLines[$i]->HasMappedUnitCode() && !$this->InvoiceLines[$i]->HasShortMappedUnitCode())
                {
                    $result[] = "Unitate de măsură pentru linia " . ($i + 1) . ": " . $this->InvoiceLines[$i]->UnitCode . " - " . MappingsManager::GetInstance()->GetUnitCodeMapping($this->InvoiceLines[$i]->UnitCode);
                }
            }
        }
        if (isset($this->OrderReference))
        {
            if (isset($this->OrderReference->ID) && !empty($this->OrderReference->ID))
            {
                $result[] = "Comanda: " . $this->OrderReference->ID;
            }
            else if (isset($this->OrderReference->SalesOrderID) && !empty($this->OrderReference->SalesOrderID))
            {
                $result[] = "Comanda: " . $this->OrderReference->SalesOrderID;
            }
        }
        if (isset($this->PaymentMeans->PaymentMeansCode))
        {
            $paymentMeans = "Modalitatea preferată de plata: " . $this->PaymentMeans->PaymentMeansCode->value;
            if (MappingsManager::GetInstance()->PaymentMeansCodeHasMapping($this->PaymentMeans->PaymentMeansCode->value))
            {
                $paymentMeans .= " (" . MappingsManager::GetInstance()->GetPaymentMeansCodeMapping($this->PaymentMeans->PaymentMeansCode->value) . ")";
            }
            $result[] = $paymentMeans;
        }
        if ($this->HasAttachments())
        {
            $result[] = "Există documente atașate in fișierul XML";
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

    /**
     * Checks for:
     * AccountingCustomerParty
     * AccountingSupplierParty
     * ID
     * IssueDate
     * InvoiceLines
     * @see UBLDeserializable::CanRender()
     * @return true|array
     */
    public function CanRender(): true|array
    {
        $result = [];
        $subComponentsOK = true;
        $toCheck = [$this->AccountingCustomerParty, $this->AccountingSupplierParty, $this->ID, $this->IssueDate, $this->InvoiceLines];
        if ($this->AccountingCustomerParty != null)
        {
            $partyResult = $this->AccountingCustomerParty->CanRender();
            if ($partyResult !== true)
            {
                $subComponentsOK = false;
                $result = array_merge($result, $partyResult);
            }
        }
        if ($this->AccountingSupplierParty != null)
        {
            $partyResult = $this->AccountingSupplierParty->CanRender();
            if ($partyResult !== true)
            {
                $subComponentsOK = false;
                $result = array_merge($result, $partyResult);
            }
        }
        if ($this->InvoiceLines != null)
        {
            $lineCount = count($this->InvoiceLines);
            for ($i = 0; $i < $lineCount; $i++)
            {
                $lineResult = $this->InvoiceLines[$i]->CanRender();
                if ($lineResult !== true)
                {
                    $subComponentsOK = false;
                    $result = array_merge($result, $lineResult);
                }
            }
        }
        else
        {
            $lineCount = 0;
        }
        /*if ($this->LegalMonetaryTotal == null)
        {
            $result[] = "[ParsedUBLInvoice]No monetary total";
            $subComponentsOK = false;
        }
        else
        {
            $validationResult = $this->LegalMonetaryTotal->CanRender();
            if ($validationResult !== true)
            {
                $subComponentsOK = false;
                $result = array_merge($result, $validationResult);
            }
        }*/
        if ($subComponentsOK === true)
        {
            if (!$this->ContainsNull($toCheck) || $lineCount == 0)
            {
                return true;
            }
        }
        if ($lineCount == 0)
        {
            $result[] = "[ParsedUBLInvoice] No invoice lines";
        }
        if ($this->AccountingCustomerParty == null)
        {
            $result[] = "[ParsedUBLInvoice]No seller party info";
        }
        if ($this->AccountingSupplierParty == null)
        {
            $result[] = "[ParsedUBLInvoice]No buyer party info";
        }
        return $result;
    }

    /**
     * Checks for:
     * AdditionalDocumentReferences
     * ContractDocumentReference
     * @return bool
     */
    public function HasAttachments(): bool
    {
        return (isset($this->AdditionalDocumentReferences) && !empty($this->AdditionalDocumentReferences)) ||
            isset($this->ContractDocumentReference);
    }
}