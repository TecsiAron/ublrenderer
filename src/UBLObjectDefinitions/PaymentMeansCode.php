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

enum PaymentMeansCode: string
{
    case InstrumentNotDefined = '1';
    case AutomatedClearingHouseCredit = '2';
    case AutomatedClearingHouseDebit = '3';
    case ACHDemandDebitReversal = '4';
    case ACHDemandCreditReversal = '5';
    case ACHDemandCredit = '6';
    case ACHDemandDebit = '7';
    case Hold = '8';
    case NationalOrRegionalClearing = '9';
    case InCash = '10';
    case ACHSavingsCreditReversal = '11';
    case ACHSavingsDebitReversal = '12';
    case ACHSavingsCredit = '13';
    case ACHSavingsDebit = '14';
    case BookentryCredit = '15';
    case BookentryDebit = '16';
    case ACHDemandCCDCredit = '17';
    case ACHDemandCCDDebit = '18';
    case ACHDemandCTPCredit = '19';
    case Cheque = '20';
    case BankersDraft = '21';
    case CertifiedBankersDraft = '22';
    case BankCheque = '23';
    case BillOfExchangeAwaitingAcceptance = '24';
    case CertifiedCheque = '25';
    case LocalCheque = '26';
    case ACHDemandCTPDebit = '27';
    case ACHDemandCTXCredit = '28';
    case ACHDemandCTXDebit = '29';
    case CreditTransfer = '30';
    case DebitTransfer = '31';
    case ACHDemandCCDPlusCredit = '32';
    case ACHDemandCCDPlusDebit = '33';
    case ACHPPD = '34';
    case ACHSavingsCCDCredit = '35';
    case ACHSavingsCCDDebit = '36';
    case ACHSavingsCTPCredit = '37';
    case ACHSavingsCTPDebit = '38';
    case ACHSavingsCTXCredit = '39';
    case ACHSavingsCTXDebit = '40';
    case ACHSavingsCCDPlusCredit = '41';
    case PaymentToBankAccount = '42';
    case ACHSavingsCCDPlusDebit = '43';
    case AcceptedBillOfExchange = '44';
    case ReferencedHomeBankingCreditTransfer = '45';
    case InterbankDebitTransfer = '46';
    case HomeBankingDebitTransfer = '47';
    case BankCard = '48';
    case DirectDebit = '49';
    case PaymentByPostgiro = '50';
    case FRNorme697Telereglement = '51';
    case UrgentCommercialPayment = '52';
    case UrgentTreasuryPayment = '53';
    case CreditCard = '54';
    case DebitCard = '55';
    case Bankgiro = '56';
    case StandingAgreement = '57';
    case SEPACreditTransfer = '58';
    case SEPADirectDebit = '59';
    case PromissoryNote = '60';
    case PromissoryNoteSignedByDebtor = '61';
    case PromissoryNoteSignedByDebtorEndorsedByBank = '62';
    case PromissoryNoteSignedByDebtorEndorsedByThirdParty = '63';
    case PromissoryNoteSignedByBank = '64';
    case PromissoryNoteSignedByBankEndorsedByAnotherBank = '65';
    case PromissoryNoteSignedByThirdParty = '66';
    case PromissoryNoteSignedByThirdPartyEndorsedByBank = '67';
    case OnlinePaymentService = '68';
    case BillDrawnByCreditorOnDebtor = '70';
    case BillDrawnByCreditorOnBank = '74';
    case BillDrawnByCreditorEndorsedByAnotherBank = '75';
    case BillDrawnByCreditorOnBankEndorsedByThirdParty = '76';
    case BillDrawnByCreditorOnThirdParty = '77';
    case BillDrawnByCreditorOnThirdPartyAcceptedEndorsedByBank = '78';
    case NotTransferableBankersDraft = '91';
    case NotTransferableLocalCheque = '92';
    case ReferenceGiro = '93';
    case UrgentGiro = '94';
    case FreeFormatGiro = '95';
    case RequestedMethodForPaymentWasNotUsed = '96';
    case ClearingBetweenPartners = '97';
    case MutuallyDefined = 'zzz';

    case INVALID = 'COULD_NOT_PARSE';
}