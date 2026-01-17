<?php

namespace App\Enums;

enum ErrorCodeCommon: int
{
    case InvalidToken = 100;
    case MissingToken = 101;
    case ExpiredToken = 102;
}
