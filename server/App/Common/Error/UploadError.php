<?php

namespace App\Common\Error;

enum UploadError : string {
	case FILE_IS_TOO_LARGE = "File is too large";
	case FILE_TYPE_IS_NOT_ALLOWED = "File type is not allowed";
	case FILE_COULD_NOT_BE_UPLOADED = "File could not be uploaded";
}

