<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    private Wishlist $wishlist;

    public function __construct(Wishlist $wishlist)
    {
        $this->wishlist = $wishlist;
    }

    // Get list Wishlist - GET
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
        $wishlists = auth('api')->user()->wishlist()->with('course')->orderBy('created_at', 'desc')->paginate($params['page_size'], ['*'], 'page', $params['page_index']);

        return $this->responseSuccessWithData([
            'wishlists' => $wishlists->items(),
            'total' => $wishlists->total(),
            'page_index' => $wishlists->currentPage(),
            'page_size' => $wishlists->perPage(),
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
                'course_id' => 'required|integer|exists:courses,id',
            ]);
            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 422);
            }
            DB::beginTransaction();

            $findCourseInWishlist = $this->wishlist->where('user_id', $user->id)->where('course_id', $params['course_id'])->first();
            if (empty($findCourseInWishlist)) {
                $this->wishlist->create([
                    'course_id' => $params['course_id'],
                    'user_id' => $user->id
                ]);

                DB::commit();
                return $this->responseSuccessWithMessage(trans('messages.add_success'));
            }
            return $this->responseError(trans('messages.exists'), 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function delete($id): JsonResponse
    {
        try {
            $user = auth('api')->user();
            if (empty($user)) {
                return $this->responseError(trans('messages.auth.not_login'), 401);
            }
            DB::beginTransaction();
            $findCourseInWishlist = $this->wishlist->where('user_id', $user->id)->where('id', $id)->first();
            if (empty($findCourseInWishlist)) {
                return $this->responseError(trans('messages.not_found'), 404);
            }
            $findCourseInWishlist->delete();
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.delete_success'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
