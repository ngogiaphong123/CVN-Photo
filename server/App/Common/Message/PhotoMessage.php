<?php

namespace App\Common\Message;

enum PhotoMessage: string {
case UPLOAD_PHOTOS_SUCCESSFULLY = "Upload photos successfully";
case DELETE_PHOTO_SUCCESSFULLY = "Delete photo successfully";
case GET_USER_PHOTOS_SUCCESSFULLY = "Get user photos successfully";
case GET_PHOTO_SUCCESSFULLY = "Get photo successfully";
case UPDATE_PHOTO_SUCCESSFULLY = "Update photo successfully";
}
