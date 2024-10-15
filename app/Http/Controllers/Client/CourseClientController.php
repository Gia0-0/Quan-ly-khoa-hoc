<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Wishlist;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class CourseClientController extends Controller
{
    private Course $course;
    private Category $category;
    private User $user;
    private Chapter $chapter;
    private Lesson $lesson;
    private UserDetail $userDetail;

    public function __construct(Course $course, Category $category, User $user, Chapter $chapter, UserDetail $userDetail, Lesson $lesson)
    {
        $this->course = $course;
        $this->category = $category;
        $this->user = $user;
        $this->chapter = $chapter;
        $this->userDetail = $userDetail;
        $this->lesson = $lesson;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $query = $request->all();
            $validator = Validator::make($query, [
                'page_size' => 'nullable|integer',
                'page_index' => 'nullable|integer',
                'search' => 'nullable|string',
                'user_id' => 'nullable|integer|exists:users,id',
                'category_id' => 'nullable|integer|exists:categories,id',
                'level' => [
                    'nullable',
                    'array'
                ],
                'level.*' => [
                    'nullable',
                    'string',
                    Rule::in(['beginner', 'intermediate', 'expert', 'all'])
                ],
                'status' => [
                    'nullable',
                    'array'
                ],
                'status.*' => [
                    'nullable',
                    'string',
                    Rule::in(['available', 'upcoming'])
                ],
                'price_from' => 'nullable|decimal:0|min:0',
                'price_to' => 'nullable|decimal:0|min:0',
            ]);

            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 200);
            }

            $tokenExists = $request->hasHeader('Authorization');
            $user_id = -1;
            if ($tokenExists) {
                $user = auth('api')->user();
                if (isset($user)) {
                    $user_id = $user->id;
                }
            }
            $courses = $this->course->with(['chapters.lessons', 'courseInfo', 'userCourses.userDetail:*', 'category:*', 'user.userDetail', 'tagCourses:*'])->select('*')
                ->selectRaw('(SELECT COUNT(*) FROM chapters WHERE chapters.course_id = courses.id) AS total_chapter')
                ->selectRaw('(SELECT COUNT(*) AS lesson_count FROM lessons WHERE lessons.chapter_id IN (SELECT id FROM chapters WHERE chapters.course_id = courses.id)) AS lesson_count')
                ->selectRaw('(SELECT COUNT(*) AS video_count FROM lessons WHERE lessons.type = "video" AND lessons.chapter_id IN (SELECT id FROM chapters WHERE chapters.course_id = courses.id)) AS video_count')
                ->selectRaw('(SELECT COUNT(*) AS text_count FROM lessons WHERE lessons.type = "text" AND lessons.chapter_id IN (SELECT id FROM chapters WHERE chapters.course_id = courses.id)) AS text_count');

            if (isset($user_id)) {
                $courses = $courses->selectRaw('(SELECT IF(COUNT(*) > 0, 1, 0) FROM user_courses WHERE user_id = ' . $user_id . ' AND course_id = courses.id) AS bought')
                    ->selectRaw('(SELECT IF(COUNT(*) > 0, 1, 0) FROM wishlists WHERE user_id = ' . $user_id . ' AND course_id = courses.id) AS inWishlist')
                    ->selectRaw('(SELECT IF(COUNT(*) > 0, 1, 0) FROM carts WHERE user_id = ' . $user_id . ' AND course_id = courses.id) AS inCart');
            }

            if (isset($query['category_id'])) {
                $categoryId = $query['category_id'];
                $category = $this->category->find($categoryId);
                if ($category && $category->parent_id == 0) {
                    $categories = $category->with('children')->where('parent_id', $categoryId)->get();
                    // Danh sách id khóa học con
                    $categoryIdsChild = $categories->pluck('id')->toArray();
                    $courses = $courses->whereIn('category_id', $categoryIdsChild);
                } else {
                    $courses = $courses->where('category_id', $categoryId);
                }
            }

            if (isset($query['search'])) {
                $courses = $courses->whereRaw("CONCAT(`course_name`) LIKE '%" . $query['search'] . "%'");
            }

            if (isset($query['status'])) {
                $courses = $courses->whereIn('status', $query['status']);
            }
            if (isset($query['level'])) {
                $courses = $courses->whereIn('level', $query['level']);
            }

            if (isset($query['user_id'])) {
                $courses = $courses->where('user_id', $query['user_id']);
            }

            if (isset($query['price_from']) && isset($query['price_to'])) {
                $courses = $courses->whereRaw("price >= " . $query["price_from"] . " and price <= " . $query["price_to"]);
            }


            $pageSize = $query['page_size'] ?? 12;
            $pageIndex = $query['page_index'] ?? 1;
            $coursesTest = $courses->paginate($pageSize, ['*'], 'page', $pageIndex);
            $coursesTest->getCollection()->transform(function ($course) {
                $totalCourseDuration = 0;

                foreach ($course->chapters as $chapter) {
                    $totalChapterDuration = 0;

                    foreach ($chapter->lessons as $lesson) {
                        $totalChapterDuration += $lesson->duration;
                    }

                    $chapter->totalDuration = $totalChapterDuration;
                    $totalCourseDuration += $totalChapterDuration;
                }

                $course->totalDuration = $totalCourseDuration;

                return $course;
            });
            DB::commit();
            return $this->responseSuccessWithData($coursesTest, 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }


    public function show(Request $request, $slug): JsonResponse
    {
        try {
            DB::beginTransaction();
            $id = $this->course->where('slug', $slug)->value('id');
            $course = $this->course->with(['chapters.lessons', 'courseInfo', 'userCourses.userDetail:*', 'category:*', 'user.userDetail', 'tagCourses:*'])->find($id);
            $course_id = ($course) ? $course->id : -1;
            $total_chapter = $this->chapter->whereHas('course', function ($query) use ($id) {
                $query->where('course_id', $id);
            })->count();
            $total_lesson = $course->chapters->sum(function ($chapter) {
                return $chapter->lessons->count();
            });
            $total_video = $course->chapters->sum(function ($chapter) {
                return $chapter->lessons->where('type', 'video')->count();
            });
            $total_content = $course->chapters->sum(function ($chapter) {
                return $chapter->lessons->where('type', 'text')->count();
            });
            $total_user = $course->userCourses->count();
            $userIdCourse = $this->user->whereHas('userCourses', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })->first();
            $wishlist = $this->user->whereHas('wishlistCourses', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })->first();
            $cart = $this->user->whereHas('cartCourses', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })->first();
            $totalCourseDuration = 0;

            if (!is_null($course)) {
                $totalCourseDuration = 0;
                foreach ($course->chapters as $chapter) {
                    if ($chapter) {
                        $totalChapterDuration = 0;
                        foreach ($chapter->lessons as $lesson) {
                            $totalChapterDuration += $lesson->duration;
                        }
                        $chapter->totalDuration = $totalChapterDuration;
                        $totalCourseDuration += $totalChapterDuration;
                    }
                }
                $course->totalDuration = $totalCourseDuration;
            }
            $tokenExists = $request->hasHeader('Authorization');
            $bought = null;
            $inWishlist = null;
            $inCart = null;
            if ($tokenExists) {
                $user = auth('api')->user();
                if (!is_null($user)) {
                    $bgt = (!is_null($bought)) ? $userIdCourse->id : null;
                    $wList = (!is_null($wishlist)) ? $wishlist->id : null;
                    $crt = (!is_null($cart)) ? $cart->id : null;
                    $bought = $bgt == $user->id && !is_null($bgt != null);
                    $inWishlist = $wList == $user->id && !is_null($wList != null);
                    $inCart = $crt == $user->id && !is_null($crt != null);
                }
            }
            $data = [
                'course' => $course,
                'total_chapter' => $total_chapter,
                'total_lesson' => $total_lesson,
                'total_video' => $total_video,
                'total_content' => $total_content,
                'total_user' => $total_user,
                'bought' => $bought,
                'inWishlist' => $inWishlist,
                'inCart' => $inCart
            ];
            DB::commit();
            if (!$course) {
                return $this->responseError('Khóa học không tồn tại', 200);
            }
            return $this->responseSuccessWithData($data, 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function getPreview($id)
    {
        $lessons = $this->lesson
            ->whereHas('chapter', function ($query) use ($id) {
                $query->where('course_id', $id);
            })
            ->where('is_public', 1)
            ->get(['video_url', 'lesson_title', 'duration']);
        return $this->responseSuccessWithData($lessons, 200);
    }
}
