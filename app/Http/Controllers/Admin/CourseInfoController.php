<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseInfo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CourseInfoController extends Controller
{
    private CourseInfo $courseInfo;
    public function __construct(CourseInfo $courseInfo) {
        $this->courseInfo = $courseInfo;
    }
    public function update(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            $validator = Validator::make(
                $param,
                [
                    'title' => 'required|string|unique:chapters,chapter_title,' . $id
                ]
            );

            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 400);
            }

            $course_info = $this->courseInfo->find($id);
            if (isset($course_info)) {
                $data = [
                    'course_id' => $param['course_id'],
                    'title' => $param['title'],
                ];
                $result = $this->courseInfo->where('id', $id)->update($data);
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
            $courseInfo = CourseInfo::find($id);

            if (!$courseInfo) {
                return $this->responseError(trans('messages.not_found'), 404);
            }

            $result = $courseInfo->delete();

            if (!$result) {
                return $this->responseError(trans('messages.delete_error'), 400);
            }

            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.delete_success'), 400);


        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 400);
        }
    }
}
