<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private User $user;

    private Cart $cart;

    public function __construct(Cart $cart, User $user)
    {
        $this->user = $user;
        $this->cart = $cart;
    }

    public function index(Request $request): JsonResponse
    {
        $params = $request->query();
        $validator = Validator::make($params, [
            'page_size' => 'nullable|integer',
            'page_index' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 200);
        }

        // Sử dụng toán tử ?? để cung cấp giá trị mặc định nếu key không tồn tại
        $pageSize = $params['page_size'] ?? 10; // Mặc định 10 nếu không có 'page_size'
        $pageIndex = $params['page_index'] ?? 1; // Mặc định 1 nếu không có 'page_index'

        $carts = auth('api')->user()->cart()->with('course')->orderBy('created_at', 'desc')->paginate($pageSize, ['*'], 'page', $pageIndex);

        return $this->responseSuccessWithData([
            'carts' => $carts->items(),
            'total' => $carts->total(),
            'page_index' => $carts->currentPage(),
            'page_size' => $carts->perPage(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            if (empty($user)) {
                return $this->responseError(trans('messages.auth.not_login'), 401);
            }
            $params = $request->all();
            $validator = Validator::make($params, [
                'course_ids' => 'required|array',
                'course_ids.*' => 'required|integer|exists:courses,id',
            ]);
            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 422);
            }
            DB::beginTransaction();

            $data = [];
            foreach ($params['course_ids'] as $course_id) {
                $findCourseInCart = $this->cart->where('user_id', $user->id)->where('course_id', $course_id)->first();
                if (empty($findCourseInCart)) {
                    $data[] = [
                        'user_id' => $user->id,
                        'course_id' => $course_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            $this->cart->insert($data);
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.add_success'));
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

            $user = auth('api')->user();
            if (empty($user)) {
                return $this->responseError(trans('messages.auth.not_login'), 401);
            }
            $findCourseInCart = $this->cart->where('user_id', $user->id)->where('id', $id)->first();
            if (empty($findCourseInCart)) {
                return $this->responseError(trans('messages.not_found'), 500);
            }
            $findCourseInCart->delete();

            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.delete_success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
