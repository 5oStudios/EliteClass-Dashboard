<?php

namespace App\Http\Controllers\Api;

use App\BBL;
use App\Cart;
use App\Order;
use App\Coupon;
use App\Course;
use App\Currency;
use App\Wishlist;
use App\CourseClass;
use App\BundleCourse;
use App\CourseChapter;
use App\CourseProgress;
use App\OfflineSession;
use App\PaymentGateway;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\SessionEnrollment;
use App\Mail\SendOrderMail;
use App\WalletTransactions;
use Illuminate\Http\Request;
use App\Notifications\UserEnroll;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;

class KnetPaymentController extends Controller
{
    public function knetPaymentCreate(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:KNET',
        ], [
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Payment method is invalid',
        ]);

        $msg =  NULL;
        $auth = Auth::guard('api')->user();

        Cart::validatecartitem($auth->id);  // validate cart items

        if ($msg) {
            return response()->json(array("errors" => ["message" => [$msg]]), 422);
        }

        $currency = Currency::where('default', '1')->first();

        $carts = Cart::where('user_id', $auth->id)->get();

        $total_amount = 0;
        $coupon_discount = 0;
        $pay_amount = 0;

        foreach ($carts as $c) {
            //cart item price i.e. offer_price
            $total_amount = $total_amount + $c->offer_price;

            //for coupon discount total
            // $coupon_discount = $coupon_discount + $c->disamount;
            $coupon_discount = ($c->disamount > $c->offer_price) ? ($coupon_discount + $c->offer_price) : ($coupon_discount + $c->disamount);
        }

        foreach ($carts as $c) {

            if ($coupon_discount != 0) {
                $pay_amount = ($total_amount - $coupon_discount) > 0 ? ($total_amount - $coupon_discount) : 0;
            } else {

                $pay_amount = $total_amount;
            }
        }

        if ($pay_amount < 1) {
            return response()->json(array("errors" => ["message" => ['Atleast 1 KWD is required to pay via card']]), 422);
        }

        // Get payment charges against payment method
        $payment_method = $request->payment_method;
        $payment = PaymentGateway::where('payment_method', $request->payment_method)->first();

        if ($payment->type == 'fixed') {
            $payment_charges = $payment->charges;
        } elseif ($payment->type == 'percentage') {
            $payment_charges = ($payment->charges / 100) * $pay_amount;
        }

        // This is being used as order trackID in orders table as order_id
        $today = date('Ymd');
        $orderIds = Order::where('order_id', 'like', $today . '%')->pluck('order_id');
        do {
            $trackId = $today . rand(1000000, 9999999);
        } while ($orderIds->contains($trackId));

        foreach ($carts as $cart) {

            $cart_item  = $cart->course_id ? Course::find($cart->course_id) : ($cart->bundle_id ? BundleCourse::find($cart->bundle_id) : ($cart->meeting_id ? BBL::find($cart->meeting_id) : ($cart->chapter_id ? CourseChapter::find($cart->chapter_id) : OfflineSession::find($cart->offline_session_id))));

            $created_orders[] = Order::create([
                'title' => $cart_item->_title(),
                'price' => $cart_item->price,
                'discount_price' => $cart_item->discount_price,
                'user_id' => $auth->id,
                'instructor_id' => $cart_item->_instructor(),
                'course_id' => $cart->course_id ?? NULL,
                'chapter_id' => $cart->chapter_id ?? NULL,
                'bundle_id' => $cart->bundle_id ?? NULL,
                'meeting_id' => $cart->meeting_id ?? NULL,
                'bundle_course_id' => $cart->bundle_id ? $cart_item->course_id : NULL,
                'offline_session_id' => $cart->offline_session_id ?? NULL,
                'order_id' => $trackId,
                'transaction_id' => 0,
                'payment_method' => $payment_method,
                'total_amount' => $cart->installment == 1 ? $cart->price : $cart->offer_price,
                'paid_amount' => 0,
                'installments' => $cart->installment,
                'coupon_discount' => 0,
                'coupon_id' => NULL,
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                // 'duration' => $duration,
                'enroll_start' => $cart_item->_enrollstart(),
                'enroll_expire' => $cart_item->_enrollexpire(),
                // 'instructor_revenue' => $instructor_payout,
                'status' => '0',
            ]);
        }

        // KNET Payload Starts HERE
        try {

            $TranAmount = $pay_amount + $payment_charges;

            $TranTrackid = current($created_orders)->order_id;

            $TranportalId = config('app.tranportal_id'); // Defined in config/app.php
            $ReqTranportalId = "id=" . $TranportalId;

            $ReqTranportalPassword = "password=" . config('app.tranportal_password'); // Defined in config/app.php
            $ReqAmount = "amt=" . $TranAmount;

            $ReqTrackId = "trackid=" . $TranTrackid;
            $ReqCurrency = "currencycode=414";

            $ReqLangid = "langid=USA";
            $ReqAction = "action=1";

            $ResponseUrl = env('API_URL') . '/user/knet/payment/response';
            $ReqResponseUrl = "responseURL=" . $ResponseUrl;

            $ErrorUrl = env('API_URL') . '/user/knet/payment/error';
            $ReqErrorUrl = "errorURL=" . $ErrorUrl;

            $ReqUdf1 = "udf1=" . $auth->fname . " " . $auth->lname;
            $ReqUdf2 = "udf2=" . $payment_charges;
            $ReqUdf3 = "udf3=";
            $ReqUdf4 = "udf4=";
            $ReqUdf5 = "udf5=";

            $param = $ReqTranportalId . "&" . $ReqTranportalPassword . "&" . $ReqAction . "&" . $ReqLangid . "&" . $ReqCurrency . "&" . $ReqAmount . "&" . $ReqResponseUrl . "&" . $ReqErrorUrl . "&" . $ReqTrackId . "&" . $ReqUdf1 . "&" . $ReqUdf2 . "&" . $ReqUdf3 . "&" . $ReqUdf4 . "&" . $ReqUdf5;

            $termResourceKey = config('app.term_resource_key'); // Defined in config/app.php
            $param = $this->encryptAES($param, $termResourceKey) . "&tranportalId=" . $TranportalId . "&responseURL=" . $ResponseUrl . "&errorURL=" . $ErrorUrl;

            $invoice =  [
                "invoiceId" => current($created_orders)->order_id,
                "invoiceURL" => "https://kpaytest.com.kw/kpg/PaymentHTTP.htm?param=paymentInit" . "&trandata=" . $param,
            ];

            return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(array("errors" => ["message" => [$e->getMessage()]]), 422);
        }
    }


    public function knetPaymentResponse(Request $request)
    {
        $ResErrorText = $request->ErrorText;     //Error Text/message
        $ResErrorNo = $request->Error;           //Error Number
        $ResTranData = $request->trandata;

        $queryParams = [
            'ErrorText' => $ResErrorText,
            'ErrorNo' => $ResErrorNo,
            'TranData' => $ResTranData
        ];

        $termResourceKey = config('app.term_resource_key'); // Defined in config/app.php

        if ($ResErrorText == null && $ResErrorNo == null && $ResTranData != null) {

            Log::channel('KNETPayment')->info(['Encrypted Response TranData: ', $ResTranData]);

            //Decryption logice starts
            $decryptedResp = $this->decrypt($ResTranData, $termResourceKey);

            // Convert Query string to JSON array e.g $decryptedResp is a query string dta
            parse_str($decryptedResp, $output);
            $jsonArrayResp = json_encode($output);

            Log::channel('KNETPayment')->info($jsonArrayResp);
            $response = json_decode($jsonArrayResp);

            $orders = Order::where([['order_id', $response->trackid], ['status', '0']])->get();
            // if ($response->result == 'NOT CAPTURED' || $response->result == 'CAPTURED') {
            if ($response->result == 'CAPTURED') {

                if ($orders->isNotEmpty()) {

                    $carts = Cart::where('user_id', $orders->first()->user_id)->get();

                    /** Create wallet transcation history */
                    $wallet_transaction = WalletTransactions::firstOrCreate(
                        [
                            'user_id' => $orders->first()->user_id,
                            'transaction_id' => $response->tranid,
                            'invoice_id' => $response->paymentid,
                        ],
                        [
                            'wallet_id' => $orders->first()->user->wallet->id,
                            'payment_method' => 'KNET',
                            'total_amount' => $response->amt,
                            'payment_charges' => $response->udf2,
                            'currency' => $orders->first()->currency,
                            'currency_icon' => $orders->first()->currency_icon,
                            'type' => 'Debit',
                            'detail' => 'Cart items purchased',
                            'invoice_data' => $jsonArrayResp,
                            // 'invoice_id' => $response->paymentid,
                        ]
                    );

                    foreach ($orders as $key => $created_order) {

                        // Decrement number of maxuage coupon
                        if ($created_order->coupon_id) {
                            $coupon = Coupon::find($created_order->coupon_id);

                            if ($coupon->maxusage > 0) {
                                $coupon->decrement('maxusage');
                            }
                        }

                        // Remove items from wishlists
                        if ($created_order->course_id) {
                            Wishlist::where(['course_id' => $created_order->course_id, 'user_id' => $orders->first()->user_id])->delete();
                        } elseif ($created_order->bundle_id) {
                            Wishlist::where(['bundle_id' => $created_order->bundle_id, 'user_id' => $orders->first()->user_id])->delete();
                        } elseif ($created_order->meeting_id) {
                            Wishlist::where(['meeting_id' => $created_order->meeting_id, 'user_id' => $orders->first()->user_id])->delete();
                            BBL::find($created_order->meeting_id)->increment('order_count'); // Increment numbers of participants has been enrolled after successfull order

                            // Session Enrollment
                            SessionEnrollment::create([
                                'meeting_id' => $created_order->meeting_id,
                                'offline_session_id' => NULL,
                                'user_id' => $created_order->user_id,
                                'status' => '1',
                            ]);
                        } elseif ($created_order->offline_session_id) {
                            Wishlist::where(['offline_session_id' => $created_order->offline_session_id, 'user_id' => $orders->first()->user_id])->delete();
                            OfflineSession::find($created_order->offline_session_id)->increment('order_count'); // Decrement numbers of participants can enrolled after one successfull order

                            // Session Enrollment
                            SessionEnrollment::create([
                                'meeting_id' => NULL,
                                'offline_session_id' => $created_order->offline_session_id,
                                'user_id' => $created_order->user_id,
                                'status' => '1',
                            ]);
                        }

                        if ($created_order->chapter_id || $created_order->course_id || $created_order->bundle_id) {
                            $courses = $created_order->course_id ? [$created_order->course_id] : ($created_order->chapter_id ? [$created_order->chapter->course_id] : $created_order->bundle_course_id);

                            foreach ($courses as $c) {
                                $chapters = CourseClass::select('id')->where('course_id', $c)->pluck('id');
                                CourseProgress::firstOrCreate(
                                    [
                                        'user_id' => $orders->first()->user_id,
                                        'course_id' => $c,
                                    ],
                                    [
                                        'progress' => 0,
                                        'mark_chapter_id' => [],
                                        'all_chapter_id' => $chapters,
                                        'status' => '1'
                                    ]
                                );
                            }
                        }

                        $created_order->paid_amount = ($carts[$key]->offer_price - $carts[$key]->disamount) > 0 ? $carts[$key]->offer_price - $carts[$key]->disamount : 0;
                        $created_order->coupon_discount =  ($carts[$key]->offer_price - $carts[$key]->disamount) > 0 ? $carts[$key]->disamount : $carts[$key]->offer_price;
                        $created_order->coupon_id = $carts[$key]->coupon_id ?? NULL;
                        $created_order->transaction_id = $wallet_transaction->id;
                        $created_order->status = 1;
                        $created_order->save();

                        if ($created_order->installments && ($created_order->bundle_id || $created_order->course_id)) {

                            $inst = $created_order->bundle_id ? $created_order->bundle->installments : $created_order->courses->installments;
                            $pay_amount = $created_order->paid_amount + $created_order->coupon_discount;

                            OrderInstallment::create([
                                'order_id' => $created_order->id,
                                'user_id' => $created_order->user_id,
                                'transaction_id' => $wallet_transaction->id,
                                'payment_method' => 'KNET',
                                'total_amount' => $created_order->paid_amount,
                                'coupon_discount' => $created_order->coupon_discount,
                                'coupon_id' => $created_order->coupon_id ?? NULL,
                                'currency' => $created_order->currency,
                                'currency_icon' => $created_order->currency_icon,
                            ]);

                            foreach ($inst as $index => $i) {
                                OrderPaymentPlan::create([
                                    'order_id' => $created_order->id,
                                    'wallet_trans_id' => ($pay_amount >= $i->amount) ? $wallet_transaction->id : null,
                                    'created_by' => $created_order->user_id,
                                    'amount' => $i->amount,
                                    'due_date' => $i->due_date,
                                    'installment_no' => $index + 1,
                                    'payment_date' => $pay_amount >= $i->amount ?  now() : null,
                                    'status' => $pay_amount >= $i->amount ? 'Paid' : null,
                                ]);
                                $pay_amount = $pay_amount >= $i->amount ? $pay_amount - $i->amount : 0;
                            }
                        }

                        if (count($created_order->user->device_tokens) > 0 && $created_order->user->notifications) {
                            Notification::send($created_order->user, new UserEnroll($created_order));
                        }
                    }

                    //Delete user carts
                    foreach ($carts as $cart) {
                        $cart->delete();
                    }

                    Log::channel('KNETPayment')->info('KNET Payment Successfull');
                    try {

                        /* sending email */
                        $x = 'Purchased Successfully.';
                        $order = $orders->first();
                        
                        if($order->user->test_user == '0'){
                            Mail::to($order->user->email)->send(new SendOrderMail($x, $order, $response));
                        }

                    } catch (\Swift_TransportException $e) {
                        
                    }
                    
                    // $url = config('app.front-end-url') . '/user/success?success=1&message=Purchased successfully';
                } else {
                    Log::channel('KNETPayment')->info('Order does not exist');
                    // $url = config('app.front-end-url') . '/user/cart?success=0&message=Payment failed';
                }
   
            } else {

                try {
                    /* sending email */
                    $x = 'Payment Failed.';
                    $order = $orders->first();

                    if($order->user->test_user == '0'){
                        Mail::to($order->user->email)->send(new SendOrderMail($x, $order, $response));
                    }

                } catch (\Swift_TransportException $e) {
                    
                }
                    
                // $url = config('app.front-end-url') . '/user/cart?success=0&message=Payment failed';
            }
        } else {
            Log::channel('KNETPayment')->info([$ResErrorText, $ResErrorNo]);
            // $url = config('app.front-end-url') . '/user/cart?success=0&message=Payment failed';
        }

        // return Redirect::to($url);
        return redirect()->route('knet.payment.detail', $queryParams);
    }


    public function knetPaymentError(Request $request)
    {
        Log::channel('KNETPayment')->info('KNET Payment Error');
        Log::channel('KNETPayment')->info([$request->ErrorText, $request->Error]);

        $errorText = $request->ErrorText;     //Error Text/message
        $errorNo = $request->Error; 
        $resTranData = $request->trandata;
        
        $termResourceKey = config('app.term_resource_key'); // Defined in config/app.php
        
        //Decryption logice starts
        $decryptedResp = $this->decrypt($resTranData, $termResourceKey);
        
        // Convert Query string to JSON array e.g $decryptedResp is a query string dta
        parse_str($decryptedResp, $output);
        $jsonArrayResp = json_encode($output);
        
        $data = json_decode($jsonArrayResp);
        
        return view('admin.knet.transaction.response', compact('data', 'errorText', 'errorNo'));

        // return redirect(config('app.front-end-url') . '/user/cart?success=0&message=Payment failed');
    }


    public function knetPaymentDetail(Request $request)
    {
        $errorText = $request->ErrorText;     //Error Text/message
        $errorNo = $request->Error; 
        $resTranData = $request->TranData;

        $termResourceKey = config('app.term_resource_key'); // Defined in config/app.php
        
        //Decryption logice starts
        $decryptedResp = $this->decrypt($resTranData, $termResourceKey);
        
        // Convert Query string to JSON array e.g $decryptedResp is a query string dta
        parse_str($decryptedResp, $output);
        $jsonArrayResp = json_encode($output);
        
        $data = json_decode($jsonArrayResp);
        
        return view('admin.knet.transaction.response', compact('data', 'errorText', 'errorNo'));
    }


    // AES Encryption Method
    function encryptAES($str, $key)
    {
        $str = $this->pkcs5_pad($str);
        $encrypted = openssl_encrypt($str, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $key);
        $encrypted = base64_decode($encrypted);
        $encrypted = unpack('C*', ($encrypted));
        $encrypted = $this->byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    function pkcs5_pad($text)
    {
        $blocksize = 16;
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    function byteArray2Hex($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
    }


    // Decryption Method for AES Algorithm
    function decrypt($code, $key)
    {
        $code =  $this->hex2ByteArray(trim($code));
        $code = $this->byteArray2String($code);
        $iv = $key;
        $code = base64_encode($code);
        $decrypted = openssl_decrypt($code, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
        return $this->pkcs5_unpad($decrypted);
    }

    function hex2ByteArray($hexString)
    {
        $string = hex2bin($hexString);
        return unpack('C*', $string);
    }

    function byteArray2String($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        return join($chars);
    }

    function pkcs5_unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}
