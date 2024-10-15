<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Repositories\Interfaces\NotificationRepository;

class LessonController extends Controller
{
    private Lesson $lesson;
    public function __construct(Lesson $lesson, protected NotificationRepository $notificationRepository)
    {
        $this->lesson = $lesson;
    }

    public function store(Request $request)
    {
        $param = $request->all();
        $validator = Validator::make(
            $param,
            [
                'chapter_id' => 'required|integer|exists:chapters,id',
                'type' => [
                    'required',
                    'string',
                    Rule::in(['video', 'text'])
                ],
                'status' => 'required|integer|between:0,1',
                'priority' => 'required|string',
                'lesson_title' => 'required|string|unique:lessons,lesson_title',
                'video_url' => 'required_if:type,video|string|nullable',
                'is_public' => [
                    'required',
                    Rule::in(0, 1)
                ],
                'duration' => 'required|integer',
                'content' => 'required_if:type,text|string|nullable',
            ]
        );

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {
            DB::beginTransaction();

            $result = $this->lesson->create([
                'chapter_id' => $param['chapter_id'],
                'type' => $param['type'],
                'status' => $param['status'],
                'priority' => $param['priority'],
                'lesson_title' => $param['lesson_title'],
                'video_url' => $param['type'] = 'video' ? $param['video_url'] : null,
                'duration' => $param['duration'],
                'content' => $param['type'] = 'video' ? $param['video_url'] : null,
                'is_public' => $param['is_public']
            ]);
            if (!$result) {
                DB::rollBack();
                return $this->responseError(trans('messages.add_error'), 400);
            }
            DB::commit();
            $course_id = $result->chapter->course->id;
            $notificationData = $this->notificationRepository->insertNotification($course_id, 'add_lessons', 1);
            return response()->json([
                'status' => 'success',
                'message' => trans('messages.add_success'),
                'notificationData' => $notificationData
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $param = $request->all();
        $validator = Validator::make(
            $param,
            [
                'type' => [
                    'required',
                    'string',
                    Rule::in(['video', 'text'])
                ],
                'status' => 'required|integer|between:0,1',
                'priority' => 'required|string',
                'lesson_title' => 'required|string|unique:lessons,lesson_title' . $id,
                'video_url' => 'required_if:type,video|string|nullable',
                'is_public' => [
                    'required',
                    Rule::in(0, 1)
                ],
                'duration' => 'required|integer',
                'content' => 'required_if:type,text|string|nullable',
            ]
        );

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {
            DB::beginTransaction();

            $lesson = $this->lesson->find($id);
            if (isset($lesson)) {
                $data = [
                    'chapter_id' => $param['chapter_id'],
                    'type' => $param['type'],
                    'status' => $param['status'],
                    'priority' => $param['priority'],
                    'lesson_title' => $param['lesson_title'],
                    'video_url' => $param['type'] = 'video' ? $param['video_url'] : null,
                    'duration' => $param['duration'],
                    'content' => $param['type'] = 'video' ? $param['video_url'] : null,
                    'is_public' => $param['is_public']
                ];
                $lesson = $this->lesson->where('id', $id);
                $result = $lesson->update($data);

                if ($result) {
                    DB::commit();
                    $course_id = $lesson->chapter()->course()->id;
                    $this->notificationRepository->insertNotification($course_id, 'add_lessons', null);
                    return $this->responseSuccessWithMessage(trans('messages.update_success'), 200);
                }
                return $this->responseError(trans('messages.update_error'), 400);
            } else {
                return $this->responseError(trans('messages.not_found'), 404);
            }

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
            $lesson = Lesson::find($id);

            if (!$lesson) {
                return $this->responseError(trans('messages.not_found'), 404);
            }

            $result = $lesson->delete();

            if (!$result) {
                return $this->responseError(trans('messages.delete_error'), 400);
            }

            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.delete_success'), 400);


        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
