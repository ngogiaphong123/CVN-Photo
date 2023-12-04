<?php

namespace App\Common\Enums;

enum AuthError : string {
	case EMAIL_ALREADY_EXISTS = "Email already exists";
	case EMAIL_DOES_NOT_EXIST = "Email does not exist";
	case PASSWORD_IS_INCORRECT = "Password is incorrect";
	case USER_DOES_NOT_EXIST = "User does not exist";
	case REFRESH_TOKEN_IS_INVALID = "Refresh token is invalid";
	case ACCESS_TOKEN_EXPIRED = "Access token expired";
}

enum UploadError : string {
	case FILE_IS_TOO_LARGE = "File is too large";
	case FILE_TYPE_IS_NOT_ALLOWED = "File type is not allowed";
	case FILE_COULD_NOT_BE_UPLOADED = "File could not be uploaded";
}
