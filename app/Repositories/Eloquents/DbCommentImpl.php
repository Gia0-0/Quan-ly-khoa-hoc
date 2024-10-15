<?php

namespace App\Repositories\Eloquents;

use App\Models\Comment;
use App\Models\User;
use App\Repositories\Interfaces\CommentRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DbCommentImpl extends DbRepository implements CommentRepository
{

    public function __construct(
        Comment               $model,
        private readonly User $user)
    {
        parent::__construct($model);
    }

    public function getListCommentByCourseId($params): array
    {
        $data = $this->user->where('id', 1)->get();
        $comments = $this->model
            ->where('course_id', $params['course_id'])
            ->where('parent_id', 0)
            ->where('depth', 1)
            ->with([
                'user:id,email',
                'user.userDetail:id,user_id,family_name,given_name,image_path,image_name'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($params['page_size'], ['*'], 'page', $params['page_index']);

        $comments->getCollection()->transform(function ($comment) {
            $comment->total_children = $this->model->countAllChildren($comment->id);
            return $comment;
        });

        return [
            'comments' => $comments->items(),
            'total' => $comments->total(),
            'page_index' => $comments->currentPage(),
            'page_size' => $comments->perPage(),
        ];
    }

    public function getDetailCommentByParentId($parentId, $params): array
    {
        $comments = $this->model
            ->where('parent_id', $parentId)
            ->with([
                'user:id,email',
                'user.userDetail:id,user_id,family_name,given_name,image_path,image_name'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($params['page_size'], ['*'], 'page', $params['page_index']);

        $comments->getCollection()->transform(function ($comment) {
            $comment->total_children = $this->model->countAllChildren($comment->id);
            return $comment;
        });
        return [
            'comments' => $comments->items(),
            'total' => $comments->total(),
            'page_index' => $comments->currentPage(),
            'page_size' => $comments->perPage(),
        ];
    }

    public function createComment($params): bool
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            if (empty($user)) {
                return false;
            }
            $data = [
                'course_id' => $params['course_id'],
                'parent_id' => $params['parent_id'],
                'depth' => $params['depth'],
                'image_path' => $params['image_path'],
                'image_name' => $params['image_name'],
                'content' => $params['content'],
                'user_id' => $user['id'],
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ];
            $this->model->create($data);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return false;
        }
    }

    public function updateComment($id, $params): bool
    {

        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            if (empty($user)) {
                return false;
            }
            $comment = $this->model->findById($id);
            if (!$comment) {
                return false;
            }
            if ($comment->user_id !== $user['id']) {
                return false;
            }
            $data = ([
                'image_path' => $params['image_path'] ?? $comment->image_path,
                'image_name' => $params['image_name'] ?? $comment->image_name,
                'content' => $params['content'],
                'updated_at' => Carbon::now()
            ]);
            $result = $comment->update($data);
            if (!$result) {
                DB::rollBack();
                return false;
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return false;
        }
    }

    public function deleteComment($id): bool
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            if (empty($user)) {
                return false;
            }
            $comment = $this->model->findById($id);
            if (!$comment) {
                return false;
            }
            if ($comment->user_id !== $user['id']) {
                return false;
            }
            $result = $comment->delete();
            if (!$result) {
                DB::rollBack();
                return false;
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return false;
        }
    }

    private function countAllChildren($commentId): int
    {
        $count = DB::table('comments as parent')
            ->join('comments as child', 'child.parent_id', '=', 'parent.id')
            ->where('parent.id', $commentId)
            ->count();

        $childComments = $this->model->where('parent_id', $commentId)->get();
        foreach ($childComments as $childComment) {
            $count += $this->model->countAllChildren($childComment->id);
        }

        return $count;
    }
}
