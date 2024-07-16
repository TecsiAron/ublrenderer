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
use Exception;
use Sabre\Xml\Reader;
use XMLReader;


class ParsedUBLInvoice extends UBLDeserializable
{
    protected string $UBLVersionID;
    protected string $customizationID = '1.0';
    protected ?string $id = null;
    protected ?bool $copyIndicator = null;
    protected ?DateTime $issueDate = null;
    protected ?InvoiceTypeCode $invoiceTypeCode = InvoiceTypeCode::INVOICE;
    protected ?string $note = null;
    protected ?DateTime $taxPointDate = null;
    protected ?DateTime $dueDate = null;
    protected ?PaymentTerms $paymentTerms = null;
    protected ?Party $accountingSupplierParty = null;
    protected ?Party $accountingCustomerParty = null;
    protected ?Party $payeeParty = null;
    protected ?string $supplierAssignedAccountID = null;
    protected ?PaymentMeans $paymentMeans = null;
    protected ?TaxTotal $taxTotal = null;
    protected ?LegalMonetaryTotal $legalMonetaryTotal = null;
    /** @var InvoiceLine[]|null $invoiceLines */
    protected ?array $invoiceLines = null;
    /** @var AllowanceCharge[]|null $allowanceCharges */
    protected ?array $allowanceCharges = null;
    /** @var AdditionalDocumentReference[] $additionalDocumentReference */
    protected array $additionalDocumentReferences = [];
    protected ?string $documentCurrencyCode = null;
    protected ?string $buyerReference = null;
    protected ?string $accountingCostCode = null;
    protected ?InvoicePeriod $invoicePeriod = null;
    protected ?Delivery $delivery = null;
    protected ?OrderReference $orderReference = null;
    protected ?ContractDocumentReference $contractDocumentReference = null;

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
                        $instance->customizationID = $reader->readString();
                        $reader->next();
                        break;
                    case "ID":
                        $instance->id = $reader->readString();
                        $reader->next();
                        break;
                    case "CopyIndicator":
                        $instance->copyIndicator = $reader->readString() === 'true';
                        $reader->next();
                        break;
                    case "IssueDate":
                        $instance->issueDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "InvoiceTypeCode":
                        $instance->invoiceTypeCode = InvoiceTypeCode::tryFrom($reader->readString()) ?? InvoiceTypeCode::INVALID;
                        $reader->next();
                        break;
                    case "Note":
                        $instance->note = $reader->readString();
                        $reader->next();
                        break;
                    case "TaxPointDate":
                        $instance->taxPointDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "DueDate":
                        $instance->dueDate = DateTime::createFromFormat("Y-m-d", $reader->readString());
                        $reader->next();
                        break;
                    case "PaymentTerms":
                        $parsed = $reader->parseCurrentElement();
                        $instance->paymentTerms = $parsed["value"];
                        break;
                    case "AccountingSupplierParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->accountingSupplierParty = $parsed["value"][0]["value"];
                        break;
                    case "AccountingCustomerParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->accountingCustomerParty = $parsed["value"][0]["value"];
                        break;
                    case "PayeeParty":
                        $parsed = $reader->parseCurrentElement();
                        $instance->payeeParty = $parsed["value"][0]["value"];
                        break;
                    case "SupplierAssignedAccountID":
                        $instance->supplierAssignedAccountID = $reader->readString();
                        $reader->next();
                        break;
                    case "PaymentMeans":
                        $parsed = $reader->parseCurrentElement();
                        $instance->paymentMeans = $parsed["value"];
                        break;
                    case "TaxTotal":
                        $parsed = $reader->parseCurrentElement();
                        $instance->taxTotal = $parsed["value"];
                        break;
                    case "LegalMonetaryTotal":
                        $parsed = $reader->parseCurrentElement();
                        $instance->legalMonetaryTotal = $parsed["value"];
                        break;
                    case "InvoiceLine":
                        $parsed = $reader->parseCurrentElement();
                        $instance->invoiceLines[] = $parsed["value"];
                        break;
                    case "AllowanceCharge":
                        $parsed = $reader->parseCurrentElement();
                        $instance->allowanceCharges[] = $parsed["value"];
                        break;
                    case "AdditionalDocumentReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->additionalDocumentReferences[] = $parsed["value"];
                        break;
                    case "DocumentCurrencyCode":
                        $instance->documentCurrencyCode = $reader->readString();
                        $reader->next();
                        break;
                    case "BuyerReference":
                        $instance->buyerReference = $reader->readString();
                        $reader->next();
                        break;
                    case "AccountingCostCode":
                        $instance->accountingCostCode = $reader->readString();
                        $reader->next();
                        break;
                    case "InvoicePeriod":
                        $parsed = $reader->parseCurrentElement();
                        $instance->invoicePeriod = $parsed["value"];
                        break;
                    case "Delivery":
                        $parsed = $reader->parseCurrentElement();
                        $instance->delivery = $parsed["value"];
                        break;
                    case "OrderReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->orderReference = $parsed["value"];
                        break;
                    case "ContractDocumentReference":
                        $parsed = $reader->parseCurrentElement();
                        $instance->contractDocumentReference = $parsed["value"];
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

    public function GetUBLVersionID(): string
    {
        return $this->UBLVersionID;
    }

    public function SetUBLVersionID(string $UBLVersionID): void
    {
        $this->UBLVersionID = $UBLVersionID;
    }

    public function GetCustomizationID(): string
    {
        return $this->customizationID;
    }

    public function SetCustomizationID(string $customizationID): void
    {
        $this->customizationID = $customizationID;
    }

    public function GetID(): ?string
    {
        return $this->id;
    }

    public function SetID(?string $id): void
    {
        $this->id = $id;
    }

    public function GetIssueDate(): ?DateTime
    {
        return $this->issueDate;
    }

    public function SetIssueDate(?DateTime $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    public function GetCopyIndicator(): ?bool
    {
        return $this->copyIndicator;
    }

    public function SetCopyIndicator(?bool $copyIndicator): void
    {
        $this->copyIndicator = $copyIndicator;
    }

    public function GetTaxPointDate(): ?DateTime
    {
        return $this->taxPointDate;
    }

    public function SetTaxPointDate(?DateTime $taxPointDate): void
    {
        $this->taxPointDate = $taxPointDate;
    }

    public function GetInvoiceTypeCode(): ?InvoiceTypeCode
    {
        return $this->invoiceTypeCode;
    }

    public function SetInvoiceTypeCode(?InvoiceTypeCode $invoiceTypeCode): void
    {
        $this->invoiceTypeCode = $invoiceTypeCode;
    }

    public function GetDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    public function SetDueDate(?DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function GetAllowanceCharges(): ?array
    {
        return $this->allowanceCharges;
    }

    public function SetAllowanceCharges(?array $allowanceCharges): void
    {
        $this->allowanceCharges = $allowanceCharges;
    }

    public function GetPaymentTerms(): ?PaymentTerms
    {
        return $this->paymentTerms;
    }

    public function SetPaymentTerms(?PaymentTerms $paymentTerms): void
    {
        $this->paymentTerms = $paymentTerms;
    }

    public function GetAccountingSupplierParty(): ?Party
    {
        return $this->accountingSupplierParty;
    }

    public function SetAccountingSupplierParty(?Party $accountingSupplierParty): void
    {
        $this->accountingSupplierParty = $accountingSupplierParty;
    }

    public function GetAccountingCostCode(): ?string
    {
        return $this->accountingCostCode;
    }

    public function SetAccountingCostCode(?string $accountingCostCode): void
    {
        $this->accountingCostCode = $accountingCostCode;
    }

    public function GetAdditionalDocumentReferences(): array
    {
        return $this->additionalDocumentReferences;
    }

    public function SetAdditionalDocumentReferences(array $additionalDocumentReferences): void
    {
        $this->additionalDocumentReferences = $additionalDocumentReferences;
    }

    public function GetDocumentCurrencyCode(): ?string
    {
        return $this->documentCurrencyCode;
    }

    public function SetDocumentCurrencyCode(string $documentCurrencyCode): void
    {
        $this->documentCurrencyCode = $documentCurrencyCode;
    }

    public function GetBuyerReference(): ?string
    {
        return $this->buyerReference;
    }

    public function SetBuyerReference(?string $buyerReference): void
    {
        $this->buyerReference = $buyerReference;
    }

    public function GetOrderReference(): ?OrderReference
    {
        return $this->orderReference;
    }

    public function SetOrderReference(?OrderReference $orderReference): void
    {
        $this->orderReference = $orderReference;
    }

    public function GetContractDocumentReference(): ?ContractDocumentReference
    {
        return $this->contractDocumentReference;
    }

    public function SetContractDocumentReference(?ContractDocumentReference $contractDocumentReference): void
    {
        $this->contractDocumentReference = $contractDocumentReference;
    }

    public function GetAccountingCustomerParty(): ?Party
    {
        return $this->accountingCustomerParty;
    }

    public function SetAccountingCustomerParty(?Party $accountingCustomerParty): void
    {
        $this->accountingCustomerParty = $accountingCustomerParty;
    }

    public function GetPayeeParty(): ?Party
    {
        return $this->payeeParty;
    }

    public function SetPayeeParty(?Party $payeeParty): void
    {
        $this->payeeParty = $payeeParty;
    }

    public function GetSupplierAssignedAccountID(): ?string
    {
        return $this->supplierAssignedAccountID;
    }

    public function SetSupplierAssignedAccountID(?string $supplierAssignedAccountID): void
    {
        $this->supplierAssignedAccountID = $supplierAssignedAccountID;
    }

    public function GetPaymentMeans(): ?PaymentMeans
    {
        return $this->paymentMeans;
    }

    public function SetPaymentMeans(?PaymentMeans $paymentMeans): void
    {
        $this->paymentMeans = $paymentMeans;
    }

    public static function GetNamespace(): string
    {
        return "{urn:oasis:names:specification:ubl:schema:xsd:Invoice-2}Invoice";
    }

    public function GetTaxTotal(): ?TaxTotal
    {
        return $this->taxTotal;
    }

    public function SetTaxTotal(?TaxTotal $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function GetLegalMonetaryTotal(): ?LegalMonetaryTotal
    {
        return $this->legalMonetaryTotal;
    }

    public function SetLegalMonetaryTotal(?LegalMonetaryTotal $legalMonetaryTotal): void
    {
        $this->legalMonetaryTotal = $legalMonetaryTotal;
    }

    /**
     * @return ?InvoiceLine[]
     */
    public function GetInvoiceLines(): ?array
    {
        return $this->invoiceLines;
    }

    /**
     * @param InvoiceLine[]|null $invoiceLines
     * @return void
     */
    public function SetInvoiceLines(?array $invoiceLines): void
    {
        $this->invoiceLines = $invoiceLines;
    }

    public function HasSupplierAccountInfo():bool
    {
        return isset($this->paymentMeans->PayeeFinancialAccount);
    }

    public function HasAnyItemIDs():bool
    {
        if(empty($this->invoiceLines))
        {
            return false;
        }
        $count=count($this->invoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(isset($this->invoiceLines[$i]->Item->SellersItemIdentification) || isset($this->invoiceLines[$i]->Item->BuyersItemIdentification))
            {
                return true;
            }
        }
        return false;
    }

    public function AllItemsHaveShortUnitCodeMapped():bool
    {
        if(empty($this->invoiceLines))
        {
            return false;
        }
        $count=count($this->invoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(!$this->invoiceLines[$i]->HasShortMappedUnitCode())
            {
                return false;
            }
        }
        return false;
    }

    public function CanShowUnitCodeDetails():bool
    {
        if(empty($this->invoiceLines))
        {
            return false;
        }
        if($this->AllItemsHaveShortUnitCodeMapped())
        {
            return false;
        }
        $count=count($this->invoiceLines);
        $foundSomeDetails=false;
        for($i=0;$i<$count;$i++)
        {
            if($this->invoiceLines[$i]->HasMappedUnitCode())
            {
               $foundSomeDetails=true;
               break;
            }
        }
        return $foundSomeDetails;
    }

    public function GetLineNumber(InvoiceLine $line):?int
    {
        if(empty($this->invoiceLines))
        {
            return null;
        }
        $count=count($this->invoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if($this->invoiceLines[$i]===$line)
            {
                return $i+1;
            }
        }
        return null;
    }

    public static function GetTestXML(): string
    {
        return file_get_contents(dirname(__FILE__) . "/../efactura_sample_invoice.xml");
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
        if ($instance->GetUBLVersionID() != "2.1")
        {
            $reason = "UBLVersionID is not 2.1";
            return false;
        }
        if ($instance->GetCustomizationID() != "urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.0")
        {
            $reason = "CustomizationID is not urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.0";
            return false;
        }
        if ($instance->GetDocumentCurrencyCode() != "RON")
        {
            $reason = "DocumentCurrencyCode is not RON";
            return false;
        }
        if ($instance->GetInvoiceTypeCode() != InvoiceTypeCode::INVOICE)
        {
            $reason = "InvoiceTypeCode is not INVOICE";
            return false;
        }
        if ($instance->GetIssueDate()->format("Y-m-d") != "2022-05-31")
        {
            $reason = "IssueDate is not 2022-05-31";
            return false;
        }
        if ($instance->GetDueDate()->format("Y-m-d") != "2022-05-31")
        {
            $reason = "DueDate is not 2022-05-31";
            return false;
        }
        if ($instance->GetAccountingSupplierParty() == null)
        {
            $reason = "AccountingSupplierParty is null";
            return false;
        }
        if ($instance->GetAccountingCustomerParty() == null)
        {
            $reason = "AccountingCustomerParty is null";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->Name != "Seller SRL")
        {
            $reason = "AccountingSupplierParty name is not Seller SRL";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->Name != "Buyer name")
        {
            $reason = "AccountingCustomerParty name is not Buyer name";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PartyIdentificationId != "123456")
        {
            $reason = "AccountingCustomerParty partyIdentificationId is not 123456";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->PostalAddress->StreetName != "line1")
        {
            $reason = "AccountingSupplierParty postalAddress streetName is not line1";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PostalAddress->StreetName != "BD DECEBAL NR 1 ET1")
        {
            $reason = "AccountingCustomerParty postalAddress streetName is not BD DECEBAL NR 1 ET1";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->PostalAddress->CityName != "SECTOR1")
        {
            $reason = "AccountingSupplierParty postalAddress cityName is not SECTOR1";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PostalAddress->CityName != "ARAD")
        {
            $reason = "AccountingCustomerParty postalAddress cityName is not ARAD";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->PostalAddress->PostalZone != "013329")
        {
            $reason = "AccountingSupplierParty postalAddress postalZone is not 013329";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PostalAddress->PostalZone != "123456")
        {
            $reason = "AccountingCustomerParty postalAddress postalZone is not 123456";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->PostalAddress->CountrySubentity != "RO-B")
        {
            $reason = "AccountingSupplierParty postalAddress countrySubentity is not RO-B";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PostalAddress->CountrySubentity != "RO-AR")
        {
            $reason = "AccountingCustomerParty postalAddress countrySubentity is not RO-AR";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->PartyTaxScheme->CompanyId != "RO1234567890")
        {
            $reason = "AccountingSupplierParty partyTaxScheme companyID is not RO1234567890";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PartyTaxScheme->CompanyId != "RO987456123")
        {
            $reason = "AccountingCustomerParty partyTaxScheme companyId is not RO987456123";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->PartyTaxScheme->TaxScheme->ID != "VAT")
        {
            $reason = "AccountingSupplierParty partyTaxScheme taxScheme id is not VAT";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->PartyTaxScheme->TaxScheme->ID != "VAT")
        {
            $reason = "AccountingCustomerParty partyTaxScheme taxScheme id is not VAT";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->LegalEntity->RegistrationName != "Seller SRL")
        {
            $reason = "AccountingSupplierParty partyLegalEntity registrationName is not Seller SRL";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->LegalEntity->RegistrationName != "Buyer SRL")
        {
            $reason = "AccountingCustomerParty partyLegalEntity registrationName is not Buyer SRL";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->LegalEntity->CompanyLegalForm != "J40/12345/1998")
        {
            $reason = "AccountingSupplierParty partyLegalEntity companyLegalForm is not J40/12345/1998";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->LegalEntity->CompanyID != "J02/321/2010")
        {
            $reason = "AccountingCustomerParty partyLegalEntity companyLegalForm is not J02/321/2010";
            return false;
        }
        if ($instance->GetAccountingSupplierParty()->Contact->ElectronicMail != "mail@seller.com")
        {
            $reason = "AccountingSupplierParty contact electronicMail is not mail@seller.com";
            return false;
        }
        if ($instance->GetAccountingCustomerParty()->Contact != null)
        {
            $reason = "AccountingCustomerParty contact contact is not empty";
            return false;
        }
        if ($instance->GetPaymentMeans()->PaymentMeansCode != PaymentMeansCode::DebitTransfer)
        {
            $reason = "PaymentMeans paymentMeansCode is not 31(DebitTransfer)";
            return false;
        }
        if ($instance->GetPaymentMeans()->PayeeFinancialAccount->ID != "RO80RNCB0067054355123456")
        {
            $reason = "PaymentMeans payeeFinancialAccount id is not RO80RNCB0067054355123456";
            return false;
        }
        if ($instance->GetTaxTotal()->TaxAmount != 6598592.6)
        {
            $reason = "TaxTotal taxAmount is not 6598592.6";
            return false;
        }
        if ($instance->GetTaxTotal()->TaxSubtotals[0]->TaxableAmount !== "696.12")
        {
            $reason = "TaxTotal taxSubtotals[0] taxableAmount is not 696.12 (" . $instance->GetTaxTotal()->TaxSubtotals[0]->TaxableAmount . ")";
            echo json_encode($instance->GetTaxTotal());
            return false;
        }
        if ($instance->GetTaxTotal()->TaxSubtotals[0]->TaxAmount !== "34.79")
        {
            $reason = "TaxTotal taxSubtotals[0] taxAmount is not 34.79";
            return false;
        }
        if ($instance->GetTaxTotal()->TaxSubtotals[0]->TaxableAmount !== "696.12")
        {
            $reason = "TaxTotal taxSubtotals[0] taxableAmount is not 696.12";
            return false;
        }
        if ($instance->GetTaxTotal()->TaxSubtotals[0]->TaxCategory->ID != "S")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory id is not S";
            return false;
        }
        if ($instance->GetTaxTotal()->TaxSubtotals[0]->TaxCategory->Percent !== "5.00")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory percent is not 5.00";
            return false;
        }
        if ($instance->GetTaxTotal()->TaxSubtotals[0]->TaxCategory->TaxScheme->ID != "VAT")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory taxScheme id is not VAT";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->LineExtensionAmount !== "34741984.11")
        {
            $reason = "LegalMonetaryTotal lineExtensionAmount is not 34741984.11";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->TaxExclusiveAmount !== "34741984.11")
        {
            $reason = "LegalMonetaryTotal taxExclusiveAmount is not 34741984.11";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->TaxInclusiveAmount !== "41340576.71")
        {
            $reason = "LegalMonetaryTotal taxInclusiveAmount is not 41340576.71";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->PayableAmount !== "41340576.71")
        {
            $reason = "LegalMonetaryTotal payableAmount is not 41340576.71";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->LineExtensionCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal lineExtensionCurrencyID is not RON";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->TaxExclusiveCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal taxExclusiveCurrencyID is not RON";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->TaxInclusiveCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal taxInclusiveCurrencyID is not RON";
            return false;
        }
        if ($instance->GetLegalMonetaryTotal()->PayableCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal payableCurrencyID is not RON";
            return false;
        }
        if ($instance->GetInvoiceLines() == null)
        {
            $reason = "InvoiceLines is null";
            return false;
        }
        if (count($instance->GetInvoiceLines()) != 35)
        {
            $reason = "InvoiceLines count is not 35";
            return false;
        }
        /** @var InvoiceLine $line */
        $line = $instance->GetInvoiceLines()[0];
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
}