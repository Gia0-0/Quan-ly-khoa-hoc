<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function uploadImage(Request $request) {
        $params = $request->all();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:png,jpg,jpeg,gif|max:100000',
            'folder' => [
                'required',
                'string',
                Rule::in(['courses', 'chapters', 'lessons', 'users'])
            ],
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $user = auth('api')->user();
        if (empty($user)) {
            return $this->responseError(trans('messages.auth.not_login'), 401);
        }

        $user_id = $user->id;
        $folder = $params['folder'];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $user_id . '_' . Str::uuid();
            $fileNameOrigin = $image->getClientOriginalName();
            $path = $image->storeAs('images/' . $folder . '/' . $imageName . $fileNameOrigin);

            return response()->json(['message' => 'Tải lên hình ảnh thành công', 'image_path' => $path, 'image_name' => $imageName]);
        }

        return response()->json(['error' => 'No image provided'], 400);
    }

    public function removeImage(Request $request)
    {

    }
}
