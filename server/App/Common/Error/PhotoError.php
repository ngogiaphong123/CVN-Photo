<?php

namespace App\Common\Error;

enum PhotoError: string {
	case PHOTO_NOT_FOUND = "Photo not found";
	case INVALID_DATE = "Invalid date";
    case INVALID_PAGE_OR_LIMIT = "Invalid page or limit";
}
