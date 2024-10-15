<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseError($message, $code = 400): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => 'failed',
            'message' => $message,
        ], $code);
    }

    protected function responseSuccessWithData($data, $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'data' => $data,
        ], $code);
    }

    protected function responseSuccessWithMessage($message, $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
        ], $code);
    }
}
