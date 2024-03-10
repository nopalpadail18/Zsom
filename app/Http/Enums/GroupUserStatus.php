<?php

namespace App\Http\Enums;

enum GroupUserStatus: string
{
    case PANDING = 'pending';
    case APPROVED = 'approved';
}
