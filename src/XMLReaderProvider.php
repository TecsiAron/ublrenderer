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

namespace EdituraEDU\UBLRenderer;

use EdituraEDU\UBLRenderer\UBLObjectDefinitions\AdditionalDocumentReference;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\Address;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\AllowanceCharge;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\AttachedFile;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ClassifiedTaxCategory;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\Contact;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ContractDocumentReference;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\Country;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\Delivery;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\DeliveryLocation;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\InvoiceItem;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\InvoiceLine;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\InvoicePeriod;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ItemPrice;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\LegalEntity;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\LegalMonetaryTotal;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\OrderReference;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\ParsedUBLInvoice;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\Party;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\PartyTaxScheme;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\PayeeFinancialAccount;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\PaymentMeans;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\PaymentTerms;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\PostalAddress;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\SettlementPeriod;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\TaxCategory;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\TaxScheme;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\TaxSubTotal;
use EdituraEDU\UBLRenderer\UBLObjectDefinitions\TaxTotal;
use Sabre\Xml\Reader;

class XMLReaderProvider
{
    public const CLASSES = [
        PartyTaxScheme::class,
        TaxScheme::class,
        TaxCategory::class,
        TaxSubTotal::class,
        TaxTotal::class,
        AllowanceCharge::class,
        AttachedFile::class,
        AdditionalDocumentReference::class,
        Country::class,
        Address::class,
        PostalAddress::class,
        ClassifiedTaxCategory::class,
        Contact::class,
        ContractDocumentReference::class,
        LegalEntity::class,
        Party::class,
        DeliveryLocation::class,
        Delivery::class,
        InvoiceItem::class,
        ItemPrice::class,
        InvoicePeriod::class,
        InvoiceLine::class,
        LegalMonetaryTotal::class,
        OrderReference::class,
        PayeeFinancialAccount::class,
        PaymentMeans::class,
        SettlementPeriod::class,
        PaymentTerms::class,
        ParsedUBLInvoice::class
    ];

    public static function CreateReader(): Reader
    {
        $reader = new Reader();

        for ($i = 0; $i < count(self::CLASSES); $i++)
        {
            $getNS = [self::CLASSES[$i], 'GetNamespace'];
            $namespace = $getNS();
            $reader->elementMap[$namespace] = [self::CLASSES[$i], 'XMLDeserialize'];
        }
        return $reader;
    }
}