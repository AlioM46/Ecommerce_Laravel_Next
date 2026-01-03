<?php

namespace App\Enums;

enum UserRole: int
{
    case USER = 1;
    case ADMIN = 2;
    case OWNER = 3;
}


enum enProductsOrderedBy : int
{
      case  Latest = 1;
      case  LowToHigh = 2;
      case  HighToLow = 3;
      case  MostLiked = 4;
      case  Discounted = 5;
}