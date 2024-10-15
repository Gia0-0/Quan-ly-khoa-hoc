<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CommentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $params = $request->query();
        $validator = Validator::make($params, [
            'page_size' => 'nullable|integer',
            'page_index' => 'nullable|integer',
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        $user = auth('api')->user();
        if (empty($user)) {
            return $this->responseError(trans('messages.auth.not_login'), 401);
        }
        $responseData = $this->commentRepository->getListCommentByCourseId($params);
        return $this->responseSuccessWithData($responseData);
    }

    public function detail($parentId, Request $request): JsonResponse
    {
        $params = $request->query();
        $validator = Validator::make($params, [
            'page_size' => 'nullable|integer',
            'page_index' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        $responseData = $this->commentRepository->getDetailCommentByParentId($parentId, $params);

        return $this->responseSuccessWithData($responseData);
    }

    public function create(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'course_id' => 'required|integer|exists:courses,id',
            'parent_id' => 'required|numeric',
            'depth' => 'required|numeric|between:1,3',
            'content' => 'required|string',
            'image_path' => 'string|nullable',
            'image_name' => 'required_unless:image_path,null|string',
        ]);
        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first(), 422);
        }
        if (!$this->commentRepository->createComment($params)) {
            return $this->responseError(trans('messages.exception'), 500);
        }
        return $this->responseSuccessWithMessage(trans('messages.add_success'));

    }

    public function update($id, Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'image_path' => 'string',
            'image_name' => 'required_unless:image_path,null|string',
            'content' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }
        if (!$this->commentRepository->updateComment($id, $params)) {
            return $this->responseError(trans('messages.exception'), 500);
        }
        return $this->responseSuccessWithMessage(trans('messages.update_success'));
    }

    public function delete($id): JsonResponse
    {
        if (!$this->commentRepository->deleteComment($id)) {
            return $this->responseError(trans('messages.exception'), 500);
        }
        return $this->responseSuccessWithMessage(trans('messages.delete_success'));
    }
}
