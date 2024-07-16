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
    protected string $documentCurrencyCode = 'EUR';
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

    public function getUBLVersionID(): string
    {
        return $this->UBLVersionID;
    }

    public function setUBLVersionID(string $UBLVersionID): void
    {
        $this->UBLVersionID = $UBLVersionID;
    }

    public function getCustomizationID(): string
    {
        return $this->customizationID;
    }

    public function setCustomizationID(string $customizationID): void
    {
        $this->customizationID = $customizationID;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getIssueDate(): ?DateTime
    {
        return $this->issueDate;
    }

    public function setIssueDate(?DateTime $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    public function getCopyIndicator(): ?bool
    {
        return $this->copyIndicator;
    }

    public function setCopyIndicator(?bool $copyIndicator): void
    {
        $this->copyIndicator = $copyIndicator;
    }

    public function getTaxPointDate(): ?DateTime
    {
        return $this->taxPointDate;
    }

    public function setTaxPointDate(?DateTime $taxPointDate): void
    {
        $this->taxPointDate = $taxPointDate;
    }

    public function getInvoiceTypeCode(): ?InvoiceTypeCode
    {
        return $this->invoiceTypeCode;
    }

    public function setInvoiceTypeCode(?InvoiceTypeCode $invoiceTypeCode): void
    {
        $this->invoiceTypeCode = $invoiceTypeCode;
    }

    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getAllowanceCharges(): ?array
    {
        return $this->allowanceCharges;
    }

    public function setAllowanceCharges(?array $allowanceCharges): void
    {
        $this->allowanceCharges = $allowanceCharges;
    }

    public function getPaymentTerms(): ?PaymentTerms
    {
        return $this->paymentTerms;
    }

    public function setPaymentTerms(?PaymentTerms $paymentTerms): void
    {
        $this->paymentTerms = $paymentTerms;
    }

    public function getAccountingSupplierParty(): ?Party
    {
        return $this->accountingSupplierParty;
    }

    public function setAccountingSupplierParty(?Party $accountingSupplierParty): void
    {
        $this->accountingSupplierParty = $accountingSupplierParty;
    }

    public function getAccountingCostCode(): ?string
    {
        return $this->accountingCostCode;
    }

    public function setAccountingCostCode(?string $accountingCostCode): void
    {
        $this->accountingCostCode = $accountingCostCode;
    }

    public function getAdditionalDocumentReferences(): array
    {
        return $this->additionalDocumentReferences;
    }

    public function setAdditionalDocumentReferences(array $additionalDocumentReferences): void
    {
        $this->additionalDocumentReferences = $additionalDocumentReferences;
    }

    public function getDocumentCurrencyCode(): string
    {
        return $this->documentCurrencyCode;
    }

    public function setDocumentCurrencyCode(string $documentCurrencyCode): void
    {
        $this->documentCurrencyCode = $documentCurrencyCode;
    }

    public function getBuyerReference(): ?string
    {
        return $this->buyerReference;
    }

    public function setBuyerReference(?string $buyerReference): void
    {
        $this->buyerReference = $buyerReference;
    }

    public function getOrderReference(): ?OrderReference
    {
        return $this->orderReference;
    }

    public function setOrderReference(?OrderReference $orderReference): void
    {
        $this->orderReference = $orderReference;
    }

    public function getContractDocumentReference(): ?ContractDocumentReference
    {
        return $this->contractDocumentReference;
    }

    public function setContractDocumentReference(?ContractDocumentReference $contractDocumentReference): void
    {
        $this->contractDocumentReference = $contractDocumentReference;
    }

    public function getAccountingCustomerParty(): ?Party
    {
        return $this->accountingCustomerParty;
    }

    public function setAccountingCustomerParty(?Party $accountingCustomerParty): void
    {
        $this->accountingCustomerParty = $accountingCustomerParty;
    }

    public function getPayeeParty(): ?Party
    {
        return $this->payeeParty;
    }

    public function setPayeeParty(?Party $payeeParty): void
    {
        $this->payeeParty = $payeeParty;
    }

    public function getSupplierAssignedAccountID(): ?string
    {
        return $this->supplierAssignedAccountID;
    }

    public function setSupplierAssignedAccountID(?string $supplierAssignedAccountID): void
    {
        $this->supplierAssignedAccountID = $supplierAssignedAccountID;
    }

    public function getPaymentMeans(): ?PaymentMeans
    {
        return $this->paymentMeans;
    }

    public function setPaymentMeans(?PaymentMeans $paymentMeans): void
    {
        $this->paymentMeans = $paymentMeans;
    }

    public static function GetNamespace(): string
    {
        return "{urn:oasis:names:specification:ubl:schema:xsd:Invoice-2}Invoice";
    }

    public function getTaxTotal(): ?TaxTotal
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(?TaxTotal $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function getLegalMonetaryTotal(): ?LegalMonetaryTotal
    {
        return $this->legalMonetaryTotal;
    }

    public function setLegalMonetaryTotal(?LegalMonetaryTotal $legalMonetaryTotal): void
    {
        $this->legalMonetaryTotal = $legalMonetaryTotal;
    }

    /**
     * @return ?InvoiceLine[]
     */
    public function getInvoiceLines(): ?array
    {
        return $this->invoiceLines;
    }

    /**
     * @param InvoiceLine[]|null $invoiceLines
     * @return void
     */
    public function setInvoiceLines(?array $invoiceLines): void
    {
        $this->invoiceLines = $invoiceLines;
    }

    public function hasSupplierAccountInfo():bool
    {
        return isset($this->paymentMeans->payeeFinancialAccount);
    }

    public function hasAnyItemIDs():bool
    {
        if(empty($this->invoiceLines))
        {
            return false;
        }
        $count=count($this->invoiceLines);
        for($i=0;$i<$count;$i++)
        {
            if(isset($this->invoiceLines[$i]->item->sellersItemIdentification) || isset($this->invoiceLines[$i]->item->buyersItemIdentification))
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

    public function getLineNumber(InvoiceLine $line):?int
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
        if ($instance->getUBLVersionID() != "2.1")
        {
            $reason = "UBLVersionID is not 2.1";
            return false;
        }
        if ($instance->getCustomizationID() != "urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.0")
        {
            $reason = "CustomizationID is not urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.0";
            return false;
        }
        if ($instance->getDocumentCurrencyCode() != "RON")
        {
            $reason = "DocumentCurrencyCode is not RON";
            return false;
        }
        if ($instance->getInvoiceTypeCode() != InvoiceTypeCode::INVOICE)
        {
            $reason = "InvoiceTypeCode is not INVOICE";
            return false;
        }
        if ($instance->getIssueDate()->format("Y-m-d") != "2022-05-31")
        {
            $reason = "IssueDate is not 2022-05-31";
            return false;
        }
        if ($instance->getDueDate()->format("Y-m-d") != "2022-05-31")
        {
            $reason = "DueDate is not 2022-05-31";
            return false;
        }
        if ($instance->getAccountingSupplierParty() == null)
        {
            $reason = "AccountingSupplierParty is null";
            return false;
        }
        if ($instance->getAccountingCustomerParty() == null)
        {
            $reason = "AccountingCustomerParty is null";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->name != "Seller SRL")
        {
            $reason = "AccountingSupplierParty name is not Seller SRL";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->name != "Buyer name")
        {
            $reason = "AccountingCustomerParty name is not Buyer name";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->partyIdentificationId != "123456")
        {
            $reason = "AccountingCustomerParty partyIdentificationId is not 123456";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->postalAddress->streetName != "line1")
        {
            $reason = "AccountingSupplierParty postalAddress streetName is not line1";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->postalAddress->streetName != "BD DECEBAL NR 1 ET1")
        {
            $reason = "AccountingCustomerParty postalAddress streetName is not BD DECEBAL NR 1 ET1";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->postalAddress->cityName != "SECTOR1")
        {
            $reason = "AccountingSupplierParty postalAddress cityName is not SECTOR1";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->postalAddress->cityName != "ARAD")
        {
            $reason = "AccountingCustomerParty postalAddress cityName is not ARAD";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->postalAddress->postalZone != "013329")
        {
            $reason = "AccountingSupplierParty postalAddress postalZone is not 013329";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->postalAddress->postalZone != "123456")
        {
            $reason = "AccountingCustomerParty postalAddress postalZone is not 123456";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->postalAddress->countrySubentity != "RO-B")
        {
            $reason = "AccountingSupplierParty postalAddress countrySubentity is not RO-B";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->postalAddress->countrySubentity != "RO-AR")
        {
            $reason = "AccountingCustomerParty postalAddress countrySubentity is not RO-AR";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->partyTaxScheme->companyId != "RO1234567890")
        {
            $reason = "AccountingSupplierParty partyTaxScheme companyID is not RO1234567890";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->partyTaxScheme->companyId != "RO987456123")
        {
            $reason = "AccountingCustomerParty partyTaxScheme companyId is not RO987456123";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->partyTaxScheme->taxScheme->id != "VAT")
        {
            $reason = "AccountingSupplierParty partyTaxScheme taxScheme id is not VAT";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->partyTaxScheme->taxScheme->id != "VAT")
        {
            $reason = "AccountingCustomerParty partyTaxScheme taxScheme id is not VAT";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->legalEntity->registrationName != "Seller SRL")
        {
            $reason = "AccountingSupplierParty partyLegalEntity registrationName is not Seller SRL";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->legalEntity->registrationName != "Buyer SRL")
        {
            $reason = "AccountingCustomerParty partyLegalEntity registrationName is not Buyer SRL";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->legalEntity->companyLegalForm != "J40/12345/1998")
        {
            $reason = "AccountingSupplierParty partyLegalEntity companyLegalForm is not J40/12345/1998";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->legalEntity->companyId != "J02/321/2010")
        {
            $reason = "AccountingCustomerParty partyLegalEntity companyLegalForm is not J02/321/2010";
            return false;
        }
        if ($instance->getAccountingSupplierParty()->contact->electronicMail != "mail@seller.com")
        {
            $reason = "AccountingSupplierParty contact electronicMail is not mail@seller.com";
            return false;
        }
        if ($instance->getAccountingCustomerParty()->contact != null)
        {
            $reason = "AccountingCustomerParty contact contact is not empty";
            return false;
        }
        if ($instance->getPaymentMeans()->paymentMeansCode != PaymentMeansCode::DebitTransfer)
        {
            $reason = "PaymentMeans paymentMeansCode is not 31(DebitTransfer)";
            return false;
        }
        if ($instance->getPaymentMeans()->payeeFinancialAccount->id != "RO80RNCB0067054355123456")
        {
            $reason = "PaymentMeans payeeFinancialAccount id is not RO80RNCB0067054355123456";
            return false;
        }
        if ($instance->getTaxTotal()->taxAmount != 6598592.6)
        {
            $reason = "TaxTotal taxAmount is not 6598592.6";
            return false;
        }
        if ($instance->getTaxTotal()->taxSubtotals[0]->taxableAmount !== "696.12")
        {
            $reason = "TaxTotal taxSubtotals[0] taxableAmount is not 696.12 (" . $instance->getTaxTotal()->taxSubtotals[0]->taxableAmount . ")";
            echo json_encode($instance->getTaxTotal());
            return false;
        }
        if ($instance->getTaxTotal()->taxSubtotals[0]->taxAmount !== "34.79")
        {
            $reason = "TaxTotal taxSubtotals[0] taxAmount is not 34.79";
            return false;
        }
        if ($instance->getTaxTotal()->taxSubtotals[0]->taxableAmount !== "696.12")
        {
            $reason = "TaxTotal taxSubtotals[0] taxableAmount is not 696.12";
            return false;
        }
        if ($instance->getTaxTotal()->taxSubtotals[0]->taxCategory->id != "S")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory id is not S";
            return false;
        }
        if ($instance->getTaxTotal()->taxSubtotals[0]->taxCategory->percent !== "5.00")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory percent is not 5.00";
            return false;
        }
        if ($instance->getTaxTotal()->taxSubtotals[0]->taxCategory->taxScheme->id != "VAT")
        {
            $reason = "TaxTotal taxSubtotals[0] taxCategory taxScheme id is not VAT";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->lineExtensionAmount !== "34741984.11")
        {
            $reason = "LegalMonetaryTotal lineExtensionAmount is not 34741984.11";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->taxExclusiveAmount !== "34741984.11")
        {
            $reason = "LegalMonetaryTotal taxExclusiveAmount is not 34741984.11";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->taxInclusiveAmount !== "41340576.71")
        {
            $reason = "LegalMonetaryTotal taxInclusiveAmount is not 41340576.71";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->payableAmount !== "41340576.71")
        {
            $reason = "LegalMonetaryTotal payableAmount is not 41340576.71";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->lineExtensionCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal lineExtensionCurrencyID is not RON";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->taxExclusiveCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal taxExclusiveCurrencyID is not RON";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->taxInclusiveCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal taxInclusiveCurrencyID is not RON";
            return false;
        }
        if ($instance->getLegalMonetaryTotal()->payableCurrency != "RON")
        {
            $reason = "LegalMonetaryTotal payableCurrencyID is not RON";
            return false;
        }
        if ($instance->getInvoiceLines() == null)
        {
            $reason = "InvoiceLines is null";
            return false;
        }
        if (count($instance->getInvoiceLines()) != 35)
        {
            $reason = "InvoiceLines count is not 35";
            return false;
        }
        /** @var InvoiceLine $line */
        $line = $instance->getInvoiceLines()[0];
        if ($line->id != "1")
        {
            $reason = "InvoiceLines[0] id is not 1";
            return false;
        }
        if ($line->invoicedQuantity !== "46396.67")
        {
            $reason = "InvoiceLines[0] invoicedQuantity is not 46396.67";
            return false;
        }
        if ($line->unitCode != "C62")
        {
            $reason = "InvoiceLines[0] unitCode is not C62";
            return false;
        }
        if ($line->lineExtensionAmount != "334641.38")
        {
            $reason = "InvoiceLines[0] lineExtensionAmount is not 334641.38";
            return false;
        }
        if ($line->lineExtensionAmountCurrencyID != "RON")
        {
            $reason = "InvoiceLines[0] lineExtensionCurrencyID is not RON";
            return false;
        }
        if ($line->allAllowanceCharges == null)
        {
            $reason = "InvoiceLines[0] allowanceCharges is null";
            return false;
        }
        if (count($line->allAllowanceCharges) != 2)
        {
            $reason = "InvoiceLines[0] allowanceCharges count is not 2";
            return false;
        }
        if ($line->allAllowanceCharges[0]->chargeIndicator !== false)
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] chargeIndicator is not false";
            return false;
        }
        if ($line->allAllowanceCharges[0]->amount !== "801.98")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] amount is not 801.98";
            return false;
        }
        if ($line->allAllowanceCharges[0]->amountCurrency != "RON")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] amountCurrencyID is not RON";
            return false;
        }
        if ($line->allAllowanceCharges[1]->baseAmountCurrency != "RON")
        {
            $reason = "InvoiceLines[0] allowanceCharges[1] baseAmountCurrencyID is not RON";
            return false;
        }
        if ($line->allAllowanceCharges[1]->baseAmount !== "354715.84")
        {
            $reason = "InvoiceLines[0] allowanceCharges[1] baseAmount is not 354715.84";
            return false;
        }
        if ($line->allAllowanceCharges[0]->allowanceChargeReasonCode !== "95")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] allowanceChargeReasonCode is not 95";
            return false;
        }
        if ($line->allAllowanceCharges[0]->allowanceChargeReason != "Discount")
        {
            $reason = "InvoiceLines[0] allowanceCharges[0] allowanceChargeReason is not Discount";
            return false;
        }
        if ($line->item == null)
        {
            $reason = "InvoiceLines[0] item is null";
            return false;
        }
        if ($line->item->name != "item name")
        {
            $reason = "InvoiceLines[0] item name is not item name";
            return false;
        }
        if ($line->item->sellersItemIdentification != "0102")
        {
            $reason = "InvoiceLines[0] item sellersItemIdentification is not 0102";
            return false;
        }
        if ($line->item->commodityClassification != "03222000-3")
        {
            $reason = "InvoiceLines[0] item commodityClassification is not 03222000-3";
            return false;
        }
        if ($line->item->commodityClassificationListID != "STI")
        {
            $reason = "InvoiceLines[0] item commodityClassificationListID is not STI";
            return false;
        }
        if ($line->item->classifiedTaxCategory == null)
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory is null";
            return false;
        }
        if ($line->item->classifiedTaxCategory->id != "S")
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory id is not S";
            return false;
        }
        if ($line->item->classifiedTaxCategory->percent !== "19.00")
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory percent is not 5.00";
            return false;
        }
        if ($line->item->classifiedTaxCategory->taxScheme->id != "VAT")
        {
            $reason = "InvoiceLines[0] classifiedTaxCategory taxScheme id is not VAT";
            return false;
        }
        if ($line->price == null)
        {
            $reason = "InvoiceLines[0] price is null";
            return false;
        }
        if ($line->price->priceAmount !== "7.6453")
        {
            $reason = "InvoiceLines[0] price priceAmount is not 46396.67";
            return false;
        }
        if ($line->price->priceCurrencyID != "RON")
        {
            $reason = "InvoiceLines[0] price priceAmountCurrencyID is not RON";
            return false;
        }
        if ($line->price->baseQuantity != 1)
        {
            $reason = "InvoiceLines[0] price baseQuantity is not 1";
            return false;
        }
        if ($line->price->unitCode != "C62")
        {
            $reason = "InvoiceLines[0] price baseQuantityUnitCode is not C62";
            return false;
        }
        return true;
    }
}