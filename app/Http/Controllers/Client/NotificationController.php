<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    private Notification $notification;
    private User $user;
    private Course $course;
    private NotificationUser $notificationUser;
    public function __construct(Notification $notification, User $user, Course $course, NotificationUser $notificationUser)
    {
        $this->notification = $notification;
        $this->user = $user;
        $this->course = $course;
        $this->notificationUser = $notificationUser;
    }

    public function getNotification(Request $request)
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            $validator = Validator::make($param, [
                'type' => [
                    'required',
                    'string',
                    Rule::in(['review', 'payment', 'create_course', 'reply'])
                ],
                'course_id' => 'nullable|integer|exists:courses,id'
            ]);

            if ($validator->fails()) {
                DB::commit();
                return $this->responseError(trans($validator->errors()->first()), 400);
            }

            // dd($notification);
            // $user = auth('api')->user();
            // $user_id = $user ? $user->id : -1;
            if (isset($param['course_id'])) {
                $course = $this->course->find($param['course_id']);
                if (!$course) {
                    return $this->responseError(trans('messages.not_found'), 404);
                }
                if ($param['type'] === 'create_course' || $param['type'] === 'reply') {
                    $users = $course->userCourses()->get();
                    foreach ($users as $user) {
                        $notification = $this->notification->where('type', $param['type'])->first();
                        if (isset($notification)) {
                            $notification_user = $this->notificationUser->create([
                                'notification_id' => $notification->id,
                                'user_id' => $user->id
                            ]);
                            if (!$notification_user) {
                                DB::commit();
                                return $this->responseError(trans('messages.add_error'), 400);
                            }
                        }
                    }
                }
                if ($param['type'] === 'review' && $param['type'] === 'payment') {

                }

            } else {
                return $this->responseError(trans('messages.not_found'), 404);
            }
            DB::commit();
            return $this->responseSuccessWithData($notification_user);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function insertNotification($course_id, $type)
    {
        try {
            DB::beginTransaction();

            if ($course_id) {
                $course = $this->course->find($course_id);
                if (!$course) {
                    return false;
                }

                // Thêm thông báo cho người dùng đã tham gia khóa học khi tạo khóa học hoặc trả lời
                if ($type === 'create_course' || $type === 'reply') {
                    $users = $course->userCourses()->pluck('user_id');
                    $notification = $this->notification->firstWhere('type', $type);
                    if ($notification) {
                        foreach ($users as $user_id) {
                            $notification_user = $this->notificationUser->create([
                                'notification_id' => $notification->id,
                                'user_id' => $user_id
                            ]);
                            if (!$notification_user) {
                                DB::rollBack();
                                return false;
                            }
                        }
                    }
                }

                // Thêm thông báo cho người tạo khóa học khi có đánh giá hoặc thanh toán
                if ($type === 'review' || $type === 'payment') {
                    $user_id = $course->user_id;
                    $notification = $this->notification->firstWhere('type', $type);
                    if ($notification) {
                        $notification_user = $this->notificationUser->create([
                            'notification_id' => $notification->id,
                            'user_id' => $user_id
                        ]);
                        if (!$notification_user) {
                            DB::rollBack();
                            return false;
                        }
                    }
                }
            } else {
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
}
