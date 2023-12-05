<?php

namespace App\Common\Message;

enum CategoryMessage: string {
	case CREATE_SUCCESSFULLY = "Create category successfully";
	case UPDATE_SUCCESSFULLY = "Update category successfully";
	case GET_CATEGORY_PHOTO_SUCCESSFULLY = "Get category photos successfully";
	case GET_USER_CATEGORY_SUCCESSFULLY = "Get user categories successfully";
	case DELETE_SUCCESSFULLY = "Delete category successfully";

}
