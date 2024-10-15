<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Interfaces\NotificationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Course;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DbNotificationImpl extends DbRepository implements NotificationRepository
{
    public function __construct(Notification                      $model,
                                private readonly Course           $course,
                                private readonly User             $user,
                                private readonly NotificationUser $notificationUser)
    {
        parent::__construct($model);
    }

    public function insertNotification($course_id, $type, $parent_review_id): JsonResponse|array|null
    {
        try {
            DB::beginTransaction();

            if ($course_id) {
                $course = $this->course->find($course_id);
                if (!$course) {
                    return null;
                }
                // Thêm thông báo cho người dùng đã tham gia khóa học khi tạo khóa học hoặc trả lời
                if ($type === 'add_lessons' || $type === 'reply') {
                    $users = $course->userCourses()->pluck('user_id');
                    $notification = $this->model->where('type', $type)->first();
                    if ($notification) {
                        $data = [];
                        foreach ($users as $user_id) {
                            $data = [
                                'notification_id' => $notification->id,
                                'user_id' => $user_id
                            ];
//                            $notification_user = $this->notificationUser->create([
//                                'notification_id' => $notification->id,
//                                'user_id' => $user_id
//                            ]);
//                            if (!$notification_user) {
//                                DB::rollBack();
//                                return null;
//                            }
                        }
                        $this->model->createMany($data);
                    }
                    if ($type === 'reply' && $parent_review_id) {
                        $review = Review::where('id', $parent_review_id)->get();
                        $user_id = $review->user_id;
                        $notification = $this->notification->where('type', $type)->first();
                        if ($notification) {
                            $notification_user = NotificationUser::create([
                                'notification_id' => $notification->id,
                                'user_id' => $user_id
                            ]);
                            if (!$notification_user) {
                                DB::rollBack();
                                return null;
                            }
                        }
                    }
                } // Thêm thông báo cho người tạo khóa học khi có đánh giá hoặc thanh toán
                else if (($type === 'review' && !$parent_review_id) || $type === 'payment') {
                    $user_id = $course->user_id;
                    $notification = $this->notification->where('type', $type)->first();
                    if ($notification) {
                        $notification_user = $this->notificationUser->create([
                            'notification_id' => $notification->id,
                            'user_id' => $user_id
                        ]);
                        if (!$notification_user) {
                            DB::rollBack();
                            return null;
                        }
                    }
                }
            } else {
                return null;
            }

            // Lấy thông tin của Notification và User thông qua mối quan hệ notificationUsers
            $notification = Notification::where('type', $type)->first();
            $subquery = DB::table('notification_users')
                ->select('user_id')
                ->where('notification_id', $notification->id)
                ->groupBy('user_id')
                ->havingRaw('COUNT(user_id) > 0');

            $users = DB::table('users')
                ->joinSub($subquery, 'sub', function ($join) {
                    $join->on('users.id', '=', 'sub.user_id');
                })
                ->get();

            DB::commit();

            return [
                'users' => $users,
                'notification' => $notification
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
