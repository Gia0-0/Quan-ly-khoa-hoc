<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    private Course $course;
    private Category $category;

    private Chapter $chapter;
    private UserDetail $userDetail;
    private Lesson $lesson;

    public function __construct(Course $course, Category $category, Chapter $chapter, UserDetail $userDetail, Lesson $lesson)
    {
        $this->course = $course;
        $this->category = $category;
        $this->chapter = $chapter;
        $this->userDetail = $userDetail;
        $this->lesson = $lesson;
    }
    public function index(Request $request): JsonResponse
    {
        $query = $request->all();
    
        $validator = Validator::make($query, [
            'page_size' => 'nullable|integer',
            'page_index' => 'nullable|integer',
            'search' => 'nullable|string',
            'user_id' => 'nullable|array',
            'user_id.*' => 'nullable|integer|exists:users,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'level' => ['nullable', 'array'],
            'level.*' => ['nullable', 'string', Rule::in(['beginner', 'intermediate', 'expert', 'all'])],
            'status' => ['nullable', 'array'],
            'status.*' => ['nullable', 'string', Rule::in(['disable', 'available', 'upcoming'])],
            'price_from' => 'nullable|decimal:0|min:0',
            'price_to' => 'nullable|decimal:0|min:0',
        ]);
    
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
    
        $queryBuilder = $this->course->with(['chapters.lessons', 'courseInfo', 'userCourses.userDetail:*', 'category.children:*'])
            ->select('*')
            ->selectRaw('(SELECT COUNT(*) FROM chapters WHERE chapters.course_id = courses.id) AS course_count')
            ->selectRaw('(SELECT COUNT(*) AS lesson_count FROM lessons WHERE lessons.chapter_id IN (SELECT id FROM chapters WHERE chapters.course_id = courses.id)) AS lesson_count')
            ->selectRaw('(SELECT COUNT(*) AS video_count FROM lessons WHERE lessons.type = "video" AND lessons.chapter_id IN (SELECT id FROM chapters WHERE chapters.course_id = courses.id)) AS video_count')
            ->selectRaw('(SELECT COUNT(*) AS text_count FROM lessons WHERE lessons.type = "text" AND lessons.chapter_id IN (SELECT id FROM chapters WHERE chapters.course_id = courses.id)) AS text_count');
    
        // Apply filters to the query builder
        if (isset($query['search'])) {
            $queryBuilder->whereRaw("CONCAT(`course_name`) LIKE ?", ['%' . $query['search'] . '%']);
        }
    
        if (isset($query['status'])) {
            $queryBuilder->whereIn('status', $query['status']);
        }
    
        if (isset($query['level'])) {
            $queryBuilder->whereIn('level', $query['level']);
        }
    
        if (!empty($query['user_id'])) {
            $queryBuilder->whereIn('user_id', $query['user_id']);
        }
    
        if (isset($query['category_id'])) {
            $categoryId = $query['category_id'];
            $category = $this->category->find($categoryId);
    
            if ($category && $category->parent_id == 0) {
                $categories = $this->category->with('children')->where('parent_id', $categoryId)->get();
                $categoryIdsChild = $categories->pluck('id')->toArray();
                $queryBuilder->whereIn('category_id', $categoryIdsChild);
            } else {
                $queryBuilder->where('category_id', $categoryId);
            }
        }
    
        if (isset($query['price_from']) && isset($query['price_to'])) {
            $queryBuilder->whereBetween('price', [$query['price_from'], $query['price_to']]);
        }
    
        // Handle pagination
        $pageSize = $query['page_size'] ?? 12;
        $pageIndex = $query['page_index'] ?? 1;
    
        $coursesPaginated = $queryBuilder->paginate($pageSize, ['*'], 'page', $pageIndex);
    
        // Calculate total durations
        $coursesPaginated->getCollection()->transform(function ($course) {
            $totalCourseDuration = $course->chapters->reduce(function ($carry, $chapter) {
                return $carry + $chapter->lessons->sum('duration');
            }, 0);
    
            $course->totalDuration = $totalCourseDuration;
            return $course;
        });
    
        return $this->responseSuccessWithData($coursesPaginated, 200);
    }
    

    public function store(Request $request): JsonResponse
    {
        $param = $request->all();

        $validator = Validator::make(
            $param,
            [
                'category_id' => 'required|integer',
                'user_id' => 'required|integer',
                'course_name' => 'required|string|unique:courses,course_name',
                'course_description' => 'string|nullable',
                'price' => 'required|numeric',
                'price_sale' => 'numeric|nullable',
                'image_path' => 'string|nullable',
                'image_name' => 'string|nullable',
                'slug' => 'required|string',
                'level' => [
                    'required',
                    'string',
                    Rule::in(['beginner', 'intermediate', 'expert', 'all'])
                ],
                'status' => [
                    'required',
                    'string',
                    Rule::in(['disable', 'available', 'upcoming'])
                ],
                'course_info' => 'required|array',
                'course_info.*.course_id' => 'required|integer',
                'course_info.*.title' => 'required|string',
                'chapters' => 'required|array',
                'chapters.*.status' => [
                    'required',
                    'string',
                    Rule::in(['disable', 'enable'])
                ],
                'chapters.*.priority' => 'required|integer',
                'chapters.*.chapter_title' => 'required|string',
                'chapters.*.lessons' => 'required|array',
                    'chapters.*.lessons.*.type' => [
                        'required',
                        'string',
                        Rule::in(['video', 'text'])
                    ],
                    'chapters.*.lessons.*.status' => [
                        'required',
                        Rule::in(0, 1)
                    ],
                    'chapters.*.lessons.*.priority' => 'required|integer',
                    'chapters.*.lessons.*.lesson_title' => 'required|string',
                    'chapters.*.lessons.*.video_url' => 'required_if:chapters.*.lessons.*.type,video|string|nullable',
                    'chapters.*.lessons.*.content' => 'required_if:chapters.*.lessons.*.type,text|string|nullable',
                    'chapters.*lessons.*.is_public' => [
                        'required',
                        Rule::in(0, 1)
                    ],
                    'chapters.*lessons.*.duration' => 'required|integer'
            ]
        );

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {
            DB::beginTransaction();

            // Create course
            $course = $this->course->create([
                'category_id' => $param['category_id'],
                'user_id' => $param['user_id'],
                'course_name' => $param['course_name'],
                'course_description' => $param['course_description'],
                'slug' => $param['slug'],
                'price' => $param['price'],
                'price_sale' => $param['price_sale'],
                'image_path' => $param['image_path'],
                'image_name' => $param['image_name'],
                'level' => $param['level'],
                'status' => $param['status']
            ]);

            if (!$course) {
                DB::rollBack();
                return $this->responseError(trans('messages.add_error'), 400);
            }

            foreach ($param['course_info'] as $courseInfoData) {
                $courseInfo = $course->courseInfo()->create([
                    'course_id' => $courseInfoData['course_id'],
                    'title' => $courseInfoData['title']
                ]);

                if (!$courseInfo) {
                    DB::rollBack();
                    return $this->responseError(trans('messages.add_error'), 400);
                }
            }

            // Create chapters and lessons
            if (!empty($param['chapters'])) {
                foreach ($param['chapters'] as $chapterData) {
                    $chapter = $course->chapters()->create([
                        'course_id' => $chapterData['course_id'],
                        'priority' => $chapterData['priority'],
                        'chapter_title' => $chapterData['chapter_title'],
                        'status' => $chapterData['status']
                    ]);
                    if (!$chapter) {
                        DB::rollBack();
                        return $this->responseError(trans('messages.add_error'), 400);
                    }
                    // = gán
                    // ==  so sánh giá trị
                    // === so sánh giá trị và địa chỉ bộ nhớ
                    if (!empty($param['lessons'])) {
                        foreach ($chapterData['lessons'] as $lessonData) {
                            $lesson = $chapter->lesson()->create([
                                'type' => $lessonData['type'],
                                'status' => $lessonData['status'],
                                'priority' => $lessonData['priority'],
                                'lesson_title' => $lessonData['lesson_title'],
                                'video_url' => $lessonData['type'] = 'video' ? $lessonData['video_url'] : null,
                                'duration' => $lessonData['duration'],
                                'content' => $lessonData['type'] === 'text' ? $lessonData['content'] : null,
                                'is_public' => $lessonData['is_public']
                            ]);
                            if (!$lesson) {
                                DB::rollBack();
                                return $this->responseError(trans('messages.add_error'), 400);
                            }
                        }
                    }
                }
            }
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.add_success'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function show($id): JsonResponse
    {
        $course = $this->course->with(['chapters.lessons', 'courseInfo', 'userCourses.userDetail:*', 'category.children:*'])->find($id);

        if (!$course) {
            return $this->responseError(trans('messages.not_found'), 404);
        }

        $totalCourseDuration = 0;

        $totalChapter = count($course->chapters);
        $totalLesson = 0;
        $totalVideo = 0;
        $totalText = 0;
        $totalUser = 0;

        foreach ($course->chapters as $chapter) {
            $totalChapterDuration = 0;

            foreach ($chapter->lessons as $lesson) {
                $totalChapterDuration += $lesson->duration;

                if ($lesson->type === 'video') {
                    $totalVideo++;
                } elseif ($lesson->type === 'text') {
                    $totalText++;
                }
            }

            $chapter->totalDuration = $totalChapterDuration;

            $totalCourseDuration += $totalChapterDuration;
        }

        $totalUser = $this->userDetail->whereHas('userDetails', function ($query) use ($id) {
            $query->where('course_id', $id);
        })->count();

        $data = [
            'course' => $course,
            'totalChapter' => $totalChapter,
            'totalLesson' => $totalLesson,
            'totalVideo' => $totalVideo,
            'totalText' => $totalText,
            'totalUser' => $totalUser,
            'totalCourseDuration' => $totalCourseDuration
        ];

        return $this->responseSuccessWithData($data, 200);
    }



    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        $param = $request->all();

        $validator = Validator::make(
            $param,
            [
                'category_id' => 'required|integer',
                'user_id' => 'required|integer',
                'course_name' => 'required|string|unique:courses,course_name',
                'course_description' => 'string|nullable',
                'price' => 'required|numeric',
                'price_sale' => 'numeric|nullable',
                'image_path' => 'string|nullable',
                'image_name' => 'string|nullable',
                'level' => [
                    'required',
                    'string',
                    Rule::in(['beginner', 'intermediate', 'expert', 'all'])
                ],
                'status' => [
                    'required',
                    'string',
                    Rule::in(['disable', 'available', 'upcoming'])
                ],
            ]
        );

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first(), 400);
        }
        try {
            $course = $this->course->find($id);
            if (isset($course)) {
                $data = ([
                    'category_id' => $param['category_id'],
                    'user_id' => $param['user_id'],
                    'course_name' => $param['course_name'],
                    'course_description' => $param['course_description'],
                    'price' => $param['price'],
                    'price_sale' => $param['price_sale'],
                    'image_path' => $param['image_path'],
                    'image_name' => $param['image_name'],
                    'level' => $param['level'],
                    'status' => $param['status'],
                ]);
                $result = $this->course->where('id', $id)->update($data);

                if (!$result) {
                    DB::rollBack();
                    return $this->responseError(trans('messages.update_error'), 400);
                }
                DB::commit();
                return $this->responseSuccessWithMessage(trans('messages.update_success'), 200);
            }
            return $this->responseError(trans('messages.not_found'), 404);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['chapters.lessons', 'courseInfo'])->find($id);

            if (!$course) {
                return $this->responseError(trans('messages.not_found'), 404);
            }

            foreach ($course->chapters as $chapter) {
                $chapter->lessons()->delete();
            }

            $course->chapters()->delete();
            optional($course->courseInfo)->delete();

            $result = $course->delete();

            if (!$result) {
                DB::rollBack();
                return $this->responseError(trans('messages.delete_error'), 400);
            }

            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.delete_success'), 200);


        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }
}

