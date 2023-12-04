<?php

namespace App\Common\Error;

enum CategoryError : string{
	case CATEGORY_NOT_FOUND = 'Category not found';
	case CATEGORY_ALREADY_EXISTS = 'Category already exists';
	case CATEGORY_NOT_ALLOWED = 'You are not allowed to modify this category';
}
