<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Repositories\Interfaces\NotificationRepository;

class ReviewController extends Controller
{
    private Review $review;

    public function __construct(Review $review, protected NotificationRepository $notificationRepository)
    {
        $this->review = $review;
    }

    public function store(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'course_id' => 'required|integer|exists:courses,id',
            'content' => 'required|string',
            'rating' => [
                'required',
                Rule::in([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5])
            ],
            'parent_review_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }

        try {
            DB::beginTransaction();

            $user = auth('api')->user();
            $findReviewByUser = $this->review->where('user_id', $user->id)->where('course_id', $params['course_id'])->first();

            if (isset($findReviewByUser)) {
                $review = $this->review->create([
                    'course_id' => $params['course_id'],
                    'user_id' => $user->id,
                    'content' => $params['content'],
                    'rating' => $params['rating'],
                    'parent_review_id' => $params['parent_review_id']
                ]);

                if (!$review) {
                    DB::rollBack();
                    return $this->responseError(trans('messages.add_error'), 500);
                }

                DB::commit();
                $course_id = $params['course_id'];
                if ($course_id) {
                    $id = $review->parent_review_id;
                    if ($id === 0) {
                        $notificationData = $this->notificationRepository->insertNotification($course_id, 'review', null);
                        return response()->json([
                            'status' => 'success',
                            'message' => trans('messages.add_success'),
                            'notificationData' => $notificationData
                        ], 200);
                    }
                    else {
                        if ($id !== 0) {
                            $notificationData = $this->notificationRepository->insertNotification($course_id, 'reply', $id);
                            return response()->json([
                                'status' => 'success',
                                'message' => trans('messages.add_success'),
                                'notificationData' => $notificationData
                            ], 200);
                        }
                    }
                }
                

            }

            return $this->responseError(trans('messages.exists'), 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $params = $request->query();
        $validator = Validator::make($params, [
            'id' => 'required|integer',
            'content' => 'required|string',
            'rating' => [
                'required',
                Rule::in([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5])
            ],
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 200);
        }

        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $findReviewByUser = $this->review->where('user_id', $user->id)->where('id', $params['id'])->first();
            if (isset($findReviewByUser)) {
                $data = [
                    'content' => $params['content'],
                    'rating' => $params['rating']
                ];
                $result = $findReviewByUser->update($data);
                DB::commit();
                if (!$result) {
                    return $this->responseSuccessWithMessage(trans('messages.update_error'));
                }
                return $this->responseSuccessWithMessage(trans('messages.update_success'));
            }
            return $this->responseError(trans('messages.not_found'), 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function delete($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $findReviewByUser = $this->review->where('user_id', $user->id)->where('id', $id)->first();
            if (isset($findReviewByUser)) {
                $findReviewByUser->delete();
                DB::commit();
                return $this->responseSuccessWithMessage(trans('messages.delete_success'));
            }
            return $this->responseError(trans('messages.not_found'), 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }

    }
}
