<?php

namespace App\Repositories\Interfaces;

interface NotificationRepository
{
    public function insertNotification($course_id, $type, $parent_review_id);
}