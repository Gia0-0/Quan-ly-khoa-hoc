<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiscountCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class DiscountCategoryController extends Controller
{
    protected Category $category;
    protected DiscountCategory $discountCategory;
    public function __construct(Category $category, DiscountCategory $discountCategory)
    {
        $this->category = $category;
        $this->discountCategory = $discountCategory;
    }

    public function store(Request $request, $discount_id)
    {
        $param = $request->all();
        $validator = Validator::make(
            $param,
            [
                'category_id' => 'required|integer|min:0'
            ]
        );

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }

        $category = $this->category->find($param['category_id']);
        if (!$category) {
            return $this->responseError(trans('messages.category_not_found'), 404);
        }

        $categories = $this->category->where('parent_id', $param['category_id'])->get();

        if ($categories->isEmpty()) {
            return $this->responseError(trans('messages.not_found'), 404);
        }

        DB::beginTransaction();
        try {
            foreach ($categories as $value1) {
                $result = $this->discountCategory->create([
                    'discount_id' => $discount_id,
                    'category_id' => $value1->id
                ]);

                if (!$result) {
                    return $this->responseError(trans('messages.add_error'), 400);
                }
            }
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.add_success'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 400);
        }
    }
}
