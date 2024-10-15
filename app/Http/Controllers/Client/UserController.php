<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $params = $request->all();
        $data = [
            'name' => 'Duc Anh',
            'age' => 24
        ];
        if ($data['age'] < 20) {
            return $this->responseError('Bạn không đủ tuổi', 200);
        }
        return $this->responseSuccessWithData($data, 200);
    }

    public function createUser(Request $request): JsonResponse
    {
        $params = $request->all();
        // {
        //    "name": "ABCD",
        //    "password": "123456"
        //}
        return $this->responseSuccessWithData($params, 200);
    }
}
