<?php

namespace App\Common\Error;

enum AuthError: string {
	case EMAIL_ALREADY_EXISTS = "Email already exists";
	case EMAIL_DOES_NOT_EXIST = "Email does not exist";
	case PASSWORD_IS_INCORRECT = "Password is incorrect";
	case USER_DOES_NOT_EXIST = "User does not exist";
	case REFRESH_TOKEN_IS_INVALID = "Refresh token is invalid";
	case ACCESS_TOKEN_EXPIRED = "Access token expired";
	case ACCESS_TOKEN_IS_INVALID = "Access token is invalid";
}
