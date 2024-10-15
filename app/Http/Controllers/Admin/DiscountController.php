<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    private Discount $discount;
    public function __construct(Discount $discount)
    {
        $this->discount = $discount;
    }
    public function store(Request $request)
    {
        $param = $request->all();
        $validator = Validator::make($param, [
            'discount_code' => 'required|string|unique:discounts,discount_code',
            'discount_type' => [
                'required',
                'string',
                Rule::in(['percentage', 'fixed'])
            ],
            'discount_value' => 'required|numeric|between:0,99999999.99',
            'start_date' => 'required|date_format:d-m-Y',
            'end_date' => 'required|date_format:d-m-Y',
            'status' => [
                'required',
                'string',
                Rule::in(['active', 'inactive'])
            ]
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {
            DB::beginTransaction();
            $param['start_date'] = Carbon::createFromFormat('d-m-Y', $param['start_date'])->format('Y-m-d');
            $param['end_date'] = Carbon::createFromFormat('d-m-Y', $param['end_date'])->format('Y-m-d');
            $discount = $this->discount->create([
                'discount_code' => $param['discount_code'],
                'discount_type' => $param['discount_type'],
                'discount_value' => $param['discount_value'],
                'start_date' => $param['start_date'],
                'end_date' => $param['end_date'],
                'status' => $param['status'],
            ]);

            if (!$discount) {
                DB::rollBack();
                return $this->responseError(trans('messages.add_error'), 400);
            }
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.add_success'), 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 400);
        }
    }

    function index(Request $request)
    {
        $query = $request->all();
        $validator = Validator::make($query, [
            'page_size' => 'nullable|integer',
            'page_index' => 'nullable|integer',
            'search' => 'nullable|string',
            'value_start' => 'nullable|numeric|between:0,99999999.99',
            'value_end' => 'nullable|numeric|between:0,99999999.99',
            'start_date_from' => 'nullable|date_format:d-m-Y',
            'start_date_to' => 'nullable|date_format:d-m-Y',
            'end_date_from' => 'nullable|date_format:d-m-Y',
            'end_date_to' => 'nullable|date_format:d-m-Y'
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {

            $discount = $this->discount->select(['*']);
            if (isset($query['search'])) {
                $discount = $this->discount->whereRaw("concat(`discount_code`) like '%" . $query['search'] . "%'");
            }
            if (isset($query['value_start']) && isset($query['value_end'])) {
                $discount = $discount->whereRaw("discount_value >= '" . $query["value_start"] . "' and discount_value <= '" . $query["value_end"] . "'");
            }
            if (isset($query['start_date_from']) && isset($query['start_date_to'])) {
                $start_date_from = Carbon::createFromFormat('d-m-Y', $query['start_date_from'])->format('Y-m-d');
                $start_date_to = Carbon::createFromFormat('d-m-Y', $query['start_date_to'])->format('Y-m-d');
                $discount = $discount->whereRaw("start_date >= '" . $start_date_from . "' and start_date <= '" . $start_date_to . "'");
            }
            if (isset($query['end_date_from']) && isset($query['end_date_to'])) {
                $end_date_from = Carbon::createFromFormat('d-m-Y', $query["end_date_from"])->format('Y-m-d');
                $end_date_to = Carbon::createFromFormat('d-m-Y', $query["end_date_to"])->format('Y-m-d');
                $discount = $discount->whereRaw("end_date >= '" . $end_date_from . "' and end_date <= '" . $end_date_to . "'");
            }

            $pageSize = $query['page_size'] ?? 12;
            $pageIndex = $query['page_index'] ?? 1;
            $discount = $discount->paginate($pageSize, ['*'], 'page', $pageIndex);
            DB::commit();
            return $this->responseSuccessWithData($discount, 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $discount = $this->discount->find($id);
        if (!$discount) {
            return $this->responseError(trans('messages.not_found'), 404);
        }
        return $this->responseSuccessWithData($discount, 200);
    }
    public function update($id, Request $request)
    {
        $param = $request->all();
        $validator = Validator::make($param, [
            'discount_code' => 'required|string',
            'discount_type' => [
                'required',
                'string',
                Rule::in(['percentage', 'fixed'])
            ],
            'discount_value' => 'required|numeric|between:0,99999999.99',
            'start_date' => 'required|date_format:d-m-Y',
            'end_date' => 'required|date_format:d-m-Y',
            'status' => [
                'required',
                'string',
                Rule::in(['active', 'inactive'])
            ]
        ]);
        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 400);
        }
        try {
            DB::beginTransaction();
            $discount = $this->discount->find($id);

            if (!$discount) {
                return $this->responseError(trans('messages.not_found'), 404);
            }

            $param['start_date'] = Carbon::createFromFormat('d-m-Y', $param['start_date'])->format('Y-m-d');
            $param['end_date'] = Carbon::createFromFormat('d-m-Y', $param['end_date'])->format('Y-m-d');
            $data = [
                'discount_code' => $param['discount_code'],
                'discount_type' => $param['discount_type'],
                'discount_value' => $param['discount_value'],
                'start_date' => $param['start_date'],
                'end_date' => $param['end_date'],
                'status' => $param['status'],
            ];
            $result = $this->discount->where('id', $id)->update($data);
            if (!$result) {
                DB::rollBack();
                return $this->responseError(trans('messages.update_error'), 400);
            }
            DB::commit();
            return $this->responseSuccessWithMessage(trans('messages.update_success'), 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 400);
        }
    }
    public function destroy($id)
    {
        try {
            $discount = $this->discount->find($id);
            if (!$discount) {
                return $this->responseError(trans('messages.not_found'), 404);
            }
            $result = $discount->delete();
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