<?php

namespace App\Common\Error;

enum CategoryError : string{
	case CATEGORY_NOT_FOUND = 'Category not found';
	case CATEGORY_ALREADY_EXISTS = 'Category already exists';
	case CATEGORY_NOT_ALLOWED = 'You are not allowed to modify this category';
	case CANNOT_REMOVE_FROM_UNCATEGORIZED = 'Cannot remove photo from Uncategorized category';
	case PHOTO_ALREADY_IN_CATEGORY = 'Photo already in category';
	case PHOTO_NOT_IN_CATEGORY = 'Photo not in category';
}
