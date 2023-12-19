<?php

namespace App\Common\Message;

enum AuthMessage: string
{
    case REGISTER_SUCCESSFULLY = "Register successfully";
    case LOGIN_SUCCESSFULLY = "Login successfully";
    case GET_ME_SUCCESSFULLY = "Get me successfully";
    case LOGOUT_SUCCESSFULLY = "Logout successfully";
    case REFRESH_TOKEN_SUCCESSFULLY = "Refresh token successfully";

}
