<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use App\Repositories\Interfaces\NotificationRepository;

class PaymentController extends Controller
{
    private Order $order;
    private Course $course;
    private OrderItem $orderItem;
    private Payment $payment;
    private User $user;
    public function __construct(Order $order, OrderItem $orderItem, Course $course, Payment $payment, User $user, protected NotificationRepository $notificationRepository)
    {
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->course = $course;
        $this->payment = $payment;
        $this->user = $user;
    }
    // Xác định URl thanh toán momo
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    public function createPaymentMomo(Request $request)
    {
        try {
            DB::beginTransaction();
            $param = $request->all();
            $validator = Validator::make(
                $param,
                [
                    'course_id' => 'required|array',
                    'course_id.*' => 'required|integer',
                    'note' => 'required|string'
                ]
            );

            if ($validator->fails()) {
                return $this->responseError(trans($validator->errors()->first()), 400);
            }

            $courseIds = $param['course_id'];
            $course = Course::whereIn('id', $courseIds)->get();
            $totalPrice = $course->sum('price');
            $orderCode = time() . "";

            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";


            $partnerCode = 'MOMOBKUN20180529';
            $accessKey = 'klm05TvNBzhg7h7j';
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
            $orderInfo = "ThanhtoanquaMoMo";
            $amount = $totalPrice;
            $redirectUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
            $ipnUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
            $extraData = "";

            $requestId = time() . "";
            $requestType = "payWithATM";
            //before sign HMAC SHA256 signature
            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderCode . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
            // dd($rawHash);
            $signature = hash_hmac("sha256", $rawHash, $secretKey);
            $data = array(
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                "storeId" => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderCode,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            );
            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);  // decode json
            // dd($jsonResult);
            if (isset($jsonResult['payUrl'])) {
                $user_id = auth('api')->user()->id;
                $order = Order::create([
                    'user_id' => $user_id,
                    'order_code' => $orderCode,
                    'total_price' => $totalPrice
                ]);
                if ($order) {
                    foreach ($courseIds as $courseId) {
                        $course = Course::find($courseId);
                        $orderItem = $this->orderItem->create([
                            'order_id' => $order->id,
                            'course_id' => $courseId,
                            'total_price' => ($course->price - $course->price_sale)
                        ]);
                    }
                }

                $payment = $this->payment->create([
                    'order_id' => $order->id,
                    'partner_code' => $jsonResult['partnerCode'],
                    'request_id' => $jsonResult['requestId'],
                    'note' => $param['note'],
                    'message' => $jsonResult['message'],
                    'pay_url' => $jsonResult['payUrl'],
                    'signature' => $jsonResult['signature']
                ]);

                if ($order && $orderItem && $payment) {
                    DB::commit();
                    $course_id = $orderItem->course_id;
                    $notificationData = $this->notificationRepository->insertNotification($course_id, 'review', null);
                    return response()->json([
                        'status' => 'success',
                        'message' => trans('messages.add_success'),
                        'notificationData' => $notificationData,
                        'jsonResult' => $jsonResult
                    ], 200);
                }
                DB::commit();
                return $this->responseError(trans('messages.payment_error'), 400);
            } else {
                DB::commit();
                return $this->responseError(trans('messages.payment_error'), 400);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->responseError($e->getMessage(), 400);
        }
    }

    public function sendEmail(Request $request, $user_id, Course $course, $totalPrice, $orderCode, $priceSale)
    {
        $param = $request->all();
        $validator = Validator::make($param, [

        ]);

        if ($validator->fails()) {
            return $this->responseError(trans($validator->errors()->first()), 422);
        }


        $user = $this->user->whereIn('id', $user_id);
        $email = $user->email;
        $token = Crypt::encrypt([
            'email' => $email,
            'expire_time' => Carbon::parse(Carbon::now())->addMinutes(30)
        ]);

        $mailData = [
            'courses' => $course,
            'total_price' => $totalPrice,
            'order_code' => $orderCode,
            'price_sale' => $priceSale
        ];
        Mail::to($email)->send(new OrderMail($mailData));
        return $this->responseSuccessWithMessage(trans('messages.send_mail_confirm_payment_success'), 200);
    }
}

