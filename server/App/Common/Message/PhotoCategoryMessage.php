<?php

namespace App\Common\Message;

enum PhotoCategoryMessage: string {
	case ADD_PHOTO_TO_CATEGORY_SUCCESSFULLY = "Add photo to category successfully";
	case REMOVE_PHOTO_FROM_CATEGORY_SUCCESSFULLY = "Remove photo from category successfully";
}
