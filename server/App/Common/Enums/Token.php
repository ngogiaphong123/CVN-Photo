<?php

namespace App\Common\Enums;

enum Token: string
{
    case ACCESS_TOKEN = 'accessToken';
    case REFRESH_TOKEN = 'refreshToken';
}
