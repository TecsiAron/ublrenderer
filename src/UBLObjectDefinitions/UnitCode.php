<?php

namespace EdituraEDU\UBLRenderer\UBLObjectDefinitions;

enum UnitCode: string
{
    case INVALID = "COULD_NOT_PARSE";
    case UNIT = 'C62';
    case PIECE = 'H87';

    case ARE = 'ARE';
    case HECTARE = 'HAR';

    case SQUARE_METRE = 'MTK';
    case SQUARE_KILOMETRE = 'KMK';
    case SQUARE_FOOT = 'FTK';
    case SQUARE_YARD = 'YDK';
    case SQUARE_MILE = 'MIK';

    case LITRE = 'LTR';

    case SECOND = 'SEC';
    case MINUTE = 'MIN';
    case HOUR = 'HUR';
    case DAY = 'DAY';
    case MONTH = 'MON';
    case YEAR = 'ANN';
}