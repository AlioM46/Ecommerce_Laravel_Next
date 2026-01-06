<?php


namespace App\Enums;

enum enOrderStatus: int
{
    case Pending   = 1;
    case Paid      = 2;
    case Shipped   = 3;
    case Completed = 4;
    case Cancelled = 5;
}
