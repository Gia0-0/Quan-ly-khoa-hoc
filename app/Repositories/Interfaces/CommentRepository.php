<?php

namespace App\Repositories\Interfaces;

interface CommentRepository
{
    public function getListCommentByCourseId($params): array;

    public function getDetailCommentByParentId($parentId, $params): array;

    public function createComment($params): bool;

    public function updateComment($id, $params): bool;

    public function deleteComment($id): bool;
}
