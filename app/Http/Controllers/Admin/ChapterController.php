<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Repositories\Interfaces\NotificationRepository;

class ChapterController extends Controller
{
    private Chapter $chapter;

    public function __construct(Chapter $chapter, protected NotificationRepository $notificationRepository)
    {
        $this->chapter = $chapter;
    }

    public function store(Request $request): JsonResponse
    {
        $param = $request->all();
        $validator = Validator::make(
            $param,
            [
                'status' => [
                    'required',
                    'string',
                    Rule::in(['disable', 'enable'])
                ],
                'priority' => 'required|integer',
                'chapter_title' => 'required|string|unique:chapters,chapter_title,'
            ]
        );

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {
            DB::beginTransaction();
            $result = $this->chapter->create([
                'course_id' => $param['course_id'],
                'status' => $param['status'],
                'priority' => $param['priority'],
                'chapter_title' => $param['chapter_title']
            ]);
            if (!$result) {
                DB::rollBack();
                return $this->responseError(trans('messages.add_error'), 400);
            }
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.add_success'), 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            $validator = Validator::make(
                $param,
                [
                    'status' => [
                        'required',
                        'string',
                        Rule::in(['disable', 'enable'])
                    ],
                    'priority' => 'required|integer',
                    'chapter_title' => 'required|string|unique:chapters,chapter_title,' . $id
                ]
            );

            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 400);
            }

            $chapter = $this->chapter->find($id);
            if (isset($chapter)) {
                $data = [
                    'course_id' => $param['course_id'],
                    'status' => $param['status'],
                    'priority' => $param['priority'],
                    'chapter_title' => $param['chapter_title']
                ];
                $result = $this->chapter->where('id', $id)->update($data);
                DB::commit();
                if ($result) {
                    return $this->responseSuccessWithMessage(trans('messages.update_success'), 200);
                } else {
                    return $this->responseError(trans('messages.update_error'), 400);
                }
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
            $chapter = Chapter::with('lessons')->find($id);

            if (!$chapter) {
                return $this->responseError(trans('messages.not_found'), 404);
            }

            foreach ($chapter->lessons as $lesson) {
                optional($lesson)->delete();
            }

            $result = $chapter->delete();

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
