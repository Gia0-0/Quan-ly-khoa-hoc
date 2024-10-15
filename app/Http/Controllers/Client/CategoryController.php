<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private Category $categories;
    private User $user;

    public function __construct(Category $categories, User $user)
    {
        $this->categories = $categories;
        $this->user = $user;
    }

    public function getAllCategoryActive(): JsonResponse
    {
        $data = $this->categories
            ->where('parent_id', 0)
            ->where('status', 1)
            ->with('children')
            ->get();
        return $this->responseSuccessWithData($data, 200);
    }

    public function getCategoryNameBySlug($slug)
    {
        $category = $this->categories->where('slug', $slug)->first(['parent_id', 'category_name', 'id']);
        if (isset($category)) {
            return $this->responseSuccessWithData($category);
        }
        return $this->responseError(trans('messages.not_found'), 404);
    }

    public function getListCategoryByParentId($parentId)
    {
        $response = [];
        $total_courses = 0;
        if ($parentId == 0) {
            $categories = $this->categories
                ->where('parent_id', $parentId)
                ->with('children', function ($query) {
                    $query->withCount('courses');
                })
                ->get();
            if (count($categories) === 0) {
                return $this->responseError(trans('messages.not_found'), 404);
            }
            foreach ($categories as $category) {
                $courses_count = 0;
                foreach ($category->children as $children) {
                    $total_courses += $children->courses_count;
                    $courses_count += $children->courses_count;
                }
                $category['courses_count'] = $courses_count;
            }
        } else {
            $categories = $this->categories->with('children')->withCount('courses')->where('parent_id', $parentId)->get();
            if (count($categories) === 0) {
                return $this->responseError(trans('messages.not_found'), 404);
            }
            foreach ($categories as $children) {
                $total_courses += $children->courses_count;
            }
        }
        $response['total_courses'] = $total_courses;
        $response['categories'] = $categories;

        return $this->responseSuccessWithData($response);
    }


    public function postCreateCategory(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            // true == 1
            // false == 0
            $validator = Validator::make(
                $params,
                [
                    'category_name' => 'required|string|unique:categories',
                    'status' => 'required|integer|between:0,1',
                    'parent_id' => 'required|integer'
                ]
            );


            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 400);
            }


            $data = [
                'category_name' => $params['category_name'],
                'status' => $params['status'],
                'parent_id' => $params['parent_id']
            ];

            $result = $this->categories->create($data);
            DB::commit();
            if (!$result) {
                return $this->responseError(trans('messages.add_error'), 400);
            }
            return $this->responseSuccessWithMessage(trans('messages.add_success'), 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return $this->responseError($e, 500);
        }
    }

    public function putUpdateCategory(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            // lấy request body
            $params = $request->all();

            // lấy id trong request
            $id = $params['id'];

            $validator = Validator::make($params,
                [
                    'category_name' => 'required|string|unique:categories,category_name,' . $id,
                    'status' => 'required|integer|between:0,1',
                    'parent_id' => 'required|integer'
                ]
            );
            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 200);
            }


            // tìm category by id
            $category = $this->categories->find($id, ['*']);
            if (isset($category)) {
                $data = [
                    'category_name' => $params['category_name'],
                    'status' => $params['status'],
                    'parent_id' => $params['parent_id']
                ];
                $result = $category->update($data);
                DB::commit();
                if (!$result) {
                    return $this->responseError(trans('messages.update_error'), 400);
                }
                return $this->responseSuccessWithMessage(trans('messages.update_success'), 404);
            }
            return $this->responseError(trans('messages.not_found'), 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return $this->responseError($e, 500);
        }
    }

    public function deleteCategory($id): JsonResponse
    {
        $category = $this->categories->find($id, ['*']);
        if (isset($category)) {
            $result = $category->delete();
            if (!$result) {
                return $this->responseError(trans('messages.delete_error'), 400);
            }
            return $this->responseSuccessWithMessage(trans('messages.delete_success'), 200);
        }
        return $this->responseError(trans('messages.not_found'), 404);
    }

    public function getAllCategoryChildren()
    {
        $category = $this->categories->where('parent_id', '<>', 0)->get(['id', 'category_name']);
        if (isset($category)) {
            return $this->responseSuccessWithData($category);
        }
        return $this->responseError(trans('messages.not_found'), 404);
    }
}
