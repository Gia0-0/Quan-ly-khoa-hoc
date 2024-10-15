<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Order;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Discount;
use App\Models\DiscountCategory;
use App\Models\Category;
use Carbon\Carbon;

class OrderController extends Controller
{
    private Order $order;
    protected $discount;
    protected $discountCategory;
    protected $category;
    protected $course;

    public function __construct(Order $order, Discount $discount, DiscountCategory $discountCategory, Category $category, Course $course)
    {
        $this->order = $order;
        $this->discount = $discount;
        $this->discountCategory = $discountCategory;
        $this->category = $category;
        $this->course = $course;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->all();
        $validator = Validator::make($query, [
            'user_id' => 'required|integer',
            'order_code' => 'required|string',
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'success'])
            ],
            'total_price' => 'required|decimal:2'
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 200);
        }

        $order = Order::with(['orderItems.*'])->get()
            ->selectRaw('Select Count(*) From order_items Where orders.id = order_items.orderId');

        if (isset($query['search'])) {
            $order = $order->whereIn("CONCAT(order_code, status, total_price) LIKE '%" . $query['search'] . "%'");
        }
        $pageSize = $query['page_size'] ?? 12;
        $pageIndex = $query['page_index'] ?? 1;
        $order = $order->paginate($pageSize, );

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        // $currentDate = Carbon::now();

        // // Retrieve all discount categories
        // $discountCategories = $this->discountCategory->select(['*'])->get();

        // foreach ($discountCategories as $discountCategory) {
        //     $discount = $this->discount->find($discountCategory->discount_id);
        //     $category = $this->category->find($discountCategory->category_id);

        //     if ($discount && $category) {
        //         if ($discount->start_date <= $currentDate && $discount->end_date >= $currentDate && $discount->status == 'active') {
        //             $courses = $category->courses;

        //             foreach ($courses as $course) {
        //                 if ($discount->discount_type == 'fixed') {
        //                     $value = $course->price - $discount->discount_value;
        //                 } else {
        //                     $value = $course->price - ($course->price * $discount->discount_value / 100);
        //                 }

        //                 // Update course with discount details
        //                 $course->update([
        //                     'discount_id' => $discount->id,
        //                     'price_sale' => $value
        //                 ]);
        //                 if (!$course) {
        //                     DB::rollBack();
        //                     return $this->responseError(trans('messages.update_error'), 400);
        //                 }
        //             }
        //         }
        //     }
        // }
        // DB::commit();
        // return $this->responseSuccessWithMessage(trans('messages.add_success'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
