<?php

namespace App\Enums;

enum StockActionType: int
{
    case INITIAL = 0;
    case INCOMING = 1;
    case OUTGOING = 2;
}