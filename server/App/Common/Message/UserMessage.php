<?php

namespace App\Common\Message;

enum UserMessage: string
{
    case UPLOAD_AVATAR_SUCCESSFULLY = "Upload avatar successfully";
    case UPDATE_PROFILE_SUCCESSFULLY = "Update profile successfully";
}
