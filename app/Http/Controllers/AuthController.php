<?php

namespace App\Http\Controllers;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use http\Exception\BadMessageException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private User $user;
    private UserDetail $userDetail;

    public function __construct(User $user, UserDetail $userDetail)
    {
        $this->user = $user;
        $this->userDetail = $userDetail;
    }

    // Register - POST
    public function register(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'family_name' => 'required|string',
            'given_name' => 'required|string',
            'verify_url' => 'required|string|url',
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        try {
            DB::beginTransaction();
            $token = Crypt::encrypt([
                'email' => $params['email'],
                'expire_time' => Carbon::parse(Carbon::now())->addMinutes(30)
            ]);
            $user = $this->user->create([
                'email' => $params['email'],
                'password' => Hash::make($params['password']),
                'user_type' => 'student',
                'status' => 'suspension',
                'active_token' => $token,
                'active_expire' => Carbon::parse(Carbon::now())->addMinutes(30)
            ]);
            if (!$user) {
                DB::commit();
                throw new BadMessageException(trans('messages.auth.register_fail'), 500);
            }
            $userDetail = $this->userDetail->create([
                'user_id' => $user->id,
                'family_name' => $params['family_name'],
                'given_name' => $params['given_name'],
                'slug' => generateUserSlug($params['email'], $params['family_name'], $params['given_name'])
            ]);
            if (!$userDetail) {
                DB::commit();
                throw new BadMessageException(trans('messages.auth.register_fail'), 500);
            }

            $mailData = [
                'logo_url' => env('LOGO_URL', '/'),
                'title' => 'Xác minh email',
                'url' => $params['verify_url'] . $token
            ];

            Mail::to($params['email'])->send(new VerifyMail($mailData));

            DB::commit();
            $data = [
                'email' => $params['email'],
                'password' => $params['password'],
            ];
            return $this->responseSuccessWithData($data);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    // Create Verify Token - POST
    public function createVerifyToken(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'email' => 'required|string|email|max:255',
            'verify_url' => 'required|string|url',
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        try {
            DB::beginTransaction();
            $user = $this->user->where('email', $params['email'])->first();
            if (!$user) {
                DB::commit();
                return $this->responseError(trans('messages.not_found'), 500);
            }
            if ($user->status === 'normal') {
                return $this->responseError(trans('messages.email_is_active'), 500);
            }
            $token = Crypt::encrypt([
                'email' => $params['email'],
                'expire_time' => Carbon::parse(Carbon::now())->addMinutes(30)
            ]);
            $mailData = [
                'logo_url' => env('LOGO_URL', '/'),
                'title' => 'Xác minh email',
                'url' => $params['verify_url'] . $token
            ];
            Mail::to($params['email'])->send(new VerifyMail($mailData));

            $this->user->where('email', $params['email'])->update([
                'active_token' => $token,
                'active_expire' => Carbon::parse(Carbon::now())->addMinutes(30)
            ]);
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.create_token'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    // Verify Account - POST
    public function verifyAccount(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        try {
            $decrypted = Crypt::decrypt($params['token']);
            $userFound = $this->user->where('email', $decrypted['email'])->first();
            if (empty($userFound)) {
                return $this->responseError(trans('messages.invalid_token'), 404);
            } elseif ($decrypted['expire_time']->lessThan(Carbon::now())) {
                return $this->responseError(trans('messages.expire_token'), 400);
            } elseif ($userFound->status === 'normal') {
                return $this->responseError(trans('messages.email_is_active'), 400);
            } elseif ($userFound->active_token !== $params['token']) {
                return $this->responseError(trans('messages.invalid_token'), 404);
            }
            $data = [
                'status' => 'normal',
                'active_token' => null,
                'active_expire' => null
            ];

            $result = $this->user->where('email', $decrypted['email'])->update($data);
            DB::commit();
            if (!$result) {
                return $this->responseError(trans('messages.update_error'), 500);
            }
            return $this->responseSuccessWithMessage(trans('messages.update_success'), 200);

        } catch (DecryptException $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    // Login - POST
    public function login(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'user_type' => [
                'required',
                'string',
                Rule::in(['admin', 'student'])
            ]
        ]);
        if ($validator->fails()) {
            return $this->responseSuccessWithData(trans($validator->errors()->first()), 422);
        }

        try {
            DB::beginTransaction();
            $credentials = [
                'email' => $params['email'],
                'password' => $params['password'],
            ];
            $token = auth('api')->attempt($credentials);
            if ($token) {
                $user = $this->user->where('email', $params['email'])->select('user_type', 'status')->first();
                if ($user->user_type === 'student' and $params['user_type'] === 'admin') {
                    auth('api')->logout();
                    DB::commit();
                    return $this->responseError(trans('messages.unauthenticated'), 401);
                }

                $this->user->where('email', $params['email'])->update([
                    "last_login_time" => Carbon::now()
                ]);
                DB::commit();
                return $this->responseSuccessWithData($token, 200);
            }
            DB::commit();
            return $this->responseError(trans('messages.auth.login_fail'), 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(),
            );
        }
    }

    // Logout - GET
    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return $this->responseSuccessWithMessage(trans("messages.logout_success"));
    }

    // Profile - GET
    public function profile(): JsonResponse
    {
        $user = auth('api')->user()->load('userDetail')->load('cart.course.category')->load('wishlist.course.category');
        return $this->responseSuccessWithData($user);
    }

    // Refresh Token - GET
    public function refreshToken(): JsonResponse
    {
        try {
            $newToken = auth('api')->refresh();
            return $this->responseSuccessWithData($newToken, 200);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    // Forgot Token - GET
    public function forgotPassword(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'email' => 'required|string|email|max:255',
            'reset_url' => 'required|string|url',
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        $token = Crypt::encrypt([
            'email' => $params['email'],
            'expire_time' => Carbon::parse(Carbon::now())->addMinutes(30)
        ]);
        $mailData = [
            'logo_url' => env('LOGO_URL', '/'),
            'title' => 'Đặt lại mật khẩu',
            'url' => $params['verify_url'] . $token
        ];
        Mail::to($params['email'])->send(new VerifyMail($mailData));
        return $this->responseSuccessWithMessage(trans('messages.send_mail_reset_pass_success'));
    }

    // Reset Token - POST
    public function resetPassword(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'password' => 'required|string|confirmed|min:6',
            'token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }
        try {
            DB::beginTransaction();
            $decrypted = Crypt::decrypt($params['token']);
            $userFound = $this->user->where('email', $decrypted['email'])->first();
            if (empty($userFound)) {
                return $this->responseError(trans('messages.invalid_token'), 404);
            } elseif ($decrypted['expire_time']->lessThan(Carbon::now())) {
                return $this->responseError(trans('messages.expire_token'), 400);
            }

            $result = $this->user->where('id', $userFound->id)->update([
                'password' => Hash::make($params['password']),
            ]);
            DB::commit();
            if (!$result) {
                return $this->responseError(trans('messages.update_error'), 500);
            }
            return $this->responseSuccessWithMessage(trans('messages.auth.reset_password'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return $this->responseError($e, 500);
        }
    }

    // Change Password - POST
    public function changePassword(Request $request): JsonResponse
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'id' => 'required|integer',
            'password' => 'required|string|confirmed|min:6',
            'old_password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }

        try {
            DB::beginTransaction();
            $userFound = $this->user->where('id', $params['id'])->first();
            if (empty($userFound)) {
                return $this->responseError(trans('messages.not_found'), 404);
            }
            if (!Hash::check($params['old_password'], $userFound->password)) {
                return $this->responseError(trans('messages.check_password'), 404);
            }
            $result = $this->user->where('id', $userFound->id)->update([
                'password' => Hash::make($params['password']),
            ]);
            DB::commit();
            if (!$result) {
                return $this->responseError(trans('messages.update_error'), 500);
            }
            return $this->responseSuccessWithMessage(trans('messages.auth.reset_password'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }
}
