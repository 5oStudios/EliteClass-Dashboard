<?php

namespace App\Http\Controllers\Api;

use App\BBL;
use App\Cart;
use App\Order;
use App\Coupon;
use App\Course;
use App\Wallet;
use App\User;
use App\Currency;
use App\Wishlist;
use App\CartCoupon;
use App\CourseClass;
use App\BundleCourse;
use App\CourseChapter;
use App\Mail\TestMail;
use App\CourseProgress;
use App\OfflineSession;
use App\PaymentGateway;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\SessionEnrollment;
use App\Mail\SendOrderMail;
use App\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Notifications\UserEnroll;
use App\Notifications\WalletTopUp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\PaymentController;

class UPaymentController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:VISA/MASTER,KNET',
        ], [
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Payment method is invalid',
        ]);

        $msg = null;
        $auth = Auth::guard('api')->user();

        $msg = Cart::validatecartitem($auth->id);  // validate cart items

        if ($msg) {
            $resp = [
                'invoiceURL' => null,
                'isExpiredItem' => true,
                'description' => 'This transaction is captured by system beacause it has zero amount to pay because cart item price is zero by using coupon.
                                      Therefore, no need to redirect on payment gateway that\'s why invoiceURL is null.',
            ];

            return response()->json(['IsSuccess' => false, 'Message' => $msg, 'Data' => $resp]);
        }

        $created_orders = [];
        $productName = [];
        $productQty = [];
        $productPrice = [];
        $total_amount = 0;
        $coupon_discount = 0;
        $pay_amount = 0;

        $carts = Cart::where('user_id', $auth->id)->get();

        foreach ($carts as $c) {
            //cart item price i.e. offer_price
            // $total_amount = $total_amount + $c->offer_price;
            if ((is_null($c->offer_type) && $c->offer_price) || $c->installment === 1) {
                $total_amount += $c->offer_price;
            } else {
                //fixed
                if ($c->offer_type == 'fixed') {
                    $total_amount += ($c->price - $c->offer_price);
                }
                //%
                elseif ($c->offer_type == 'percentage') {
                    $total_amount += ($c->price - (($c->offer_price / 100) * $c->price));
                }
            }

            //for coupon discount total
            if ($c->installment == 0 && $c->cartCoupon) {
                $coupon_discount = ($c->cartCoupon->disamount >= $c->offer_price) ? ($coupon_discount + $c->offer_price) : ($coupon_discount + $c->cartCoupon->disamount);
            } elseif ($c->installment == 1) {
                foreach ($c->cartCoupons as $cartCoupon) {
                    if (in_array($cartCoupon->installment_id, $c->total_installments)) {
                        $coupon_discount = ($cartCoupon->disamount >= $c->offer_price) ? ($coupon_discount + $c->offer_price) : ($coupon_discount + $cartCoupon->disamount);
                    }
                }
            }
        }

        if ($coupon_discount != 0) {
            $pay_amount = ($total_amount - $coupon_discount) > 0 ? ($total_amount - $coupon_discount) : 0;
        } else {
            $pay_amount = $total_amount;
        }

        if ($pay_amount == 0) {
            try {
                PaymentController::couponCartOrder();

                $resp = [
                    'invoiceURL' => null,
                    'isDirectEnroll' => true,
                    'description' => 'This transaction is captured by system beacause it has zero amount to pay because cart item price is zero by using coupon.
                                      Therefore, no need to redirect on payment gateway that\'s why invoiceURL is null.',
                ];

                return response()->json(['IsSuccess' => true, 'Message' => 'Enrolled successfully.', 'Data' => $resp]);
            } catch (\Throwable $th) {
                return response()->json(['errors' => ['message' => [$th->getMessage()]]], 422);
            }
        } elseif ($pay_amount < 0.1) {
            return response()->json(['errors' => ['message' => ['Atleast 0.1 KWD is required to pay via Card OR KNET']]], 422);
        }

        $currency = Currency::where('default', '1')->first();

        // Get payment charges against payment method
        $payment_method = $request->payment_method == 'KNET' ? 'knet' : 'cc';
        $payment_charges = 0.000;
        $payment = PaymentGateway::where('payment_method', $request->payment_method)->first();

        if ($payment) {
            if ($payment->type == 'fixed') {
                $payment_charges = $payment->charges;
            } elseif ($payment->type == 'percentage') {
                $payment_charges = ($payment->charges / 100) * $pay_amount;
            }
        }

        // This is being used as order trackID in orders table as order_id
        $today = date('Ymd');
        $orderIds = Order::where('order_id', 'like', $today . '%')->pluck('order_id');
        do {
            $trackId = $today . rand(1000000, 9999999);
        } while ($orderIds->contains($trackId));


        foreach ($carts as $cart) {
            $cart_item = $cart->course_id ? Course::find($cart->course_id) : ($cart->bundle_id ? BundleCourse::find($cart->bundle_id) : ($cart->meeting_id ? BBL::find($cart->meeting_id) : ($cart->chapter_id ? CourseChapter::find($cart->chapter_id) : OfflineSession::find($cart->offline_session_id))));
            $created_orders[] = Order::create([
                'title' => $cart_item->_title(),
                'price' => $cart_item->price,
                'discount_price' => $cart_item->discount_price,
                'discount_type' => $cart_item->discount_type,
                'user_id' => $auth->id,
                'instructor_id' => $cart_item->_instructor(),
                'course_id' => $cart->course_id ?? null,
                'chapter_id' => $cart->chapter_id ?? null,
                'bundle_id' => $cart->bundle_id ?? null,
                'meeting_id' => $cart->meeting_id ?? null,
                'bundle_course_id' => $cart->bundle_id ? $cart_item->course_id : null,
                'offline_session_id' => $cart->offline_session_id ?? null,
                'order_id' => $trackId,
                'transaction_id' => 0,
                'payment_method' => ($cart->installment == 0 && $cart->cartCoupon && ($cart->cartCoupon->disamount >= $cart->offer_price)) ? 'Coupon' : $request->payment_method,
                // 'total_amount' => $cart->installment == 1 ? $cart->price : $cart->offer_price,
                'total_amount' => $total_amount,  //$cart->installment == 1 ? (isset($cart->price) ? $cart->price : 0) : (isset($cart->offer_price) ? $cart->offer_price : 0),
                'paid_amount' => 0,
                'installments' => $cart->installment,
                'total_installments' => $cart->installment == 1 ? $cart_item->total_installments : null,
                'coupon_discount' => 0,
                'coupon_id' => null,
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                // 'duration' => $duration,
                'enroll_start' => $cart_item->_enrollstart(),
                'enroll_expire' => $cart_item->_enrollexpire(),
                // 'instructor_revenue' => $instructor_payout,
                'status' => 0,
            ]);

            $productName[] = $cart_item->_title();
            $productQty[] = '1';
            $productPrice[] = ($cart->offer_price - $cart->disamount) > 0 ? $cart->offer_price - $cart->disamount : 0;
        }

        $udf = [
            'amt' => $pay_amount + $payment_charges,
            'payment_charges' => $payment_charges,
        ];

        // UPayment Payload Starts HERE
        try {
            $fields = [
                'merchant_id' => config('app.upayment_merchant_id'), // Defined in config/app.php,
                'username' => config('app.upayment_username'), // Defined in config/app.php,
                'password' => stripslashes(config('app.upayment_password')), // Defined in config/app.php,
                'api_key' => config('app.upayment_api_key'), //In production mode, please pass API_KEY with BCRYPT function, Defined in config/app.php,

                'order_id' => $trackId, // MIN 30 characters with strong unique function (like hashing function with time)
                'total_price' => $pay_amount + $payment_charges,
                'CurrencyCode' => $currency->code, //only works in production mode
                'CstFName' => $auth->fname . ' ' . $auth->lname,
                'CstEmail' => $auth->email,
                'CstMobile' => $auth->mobile,
                'trnUdf' => json_encode($udf),
                'success_url' => env('API_URL') . '/user/upayment/success',
                'error_url' => env('API_URL') . '/user/upayment/error',
                'test_mode' => config('app.upayment_test_mode'), // Defined in config/app.php,
                'customer_unq_token' => $auth->id, //pass unique customer identifier (eg: mobile number)
                'whitelabled' => true, // only accept in live credentials (it will not work in test)
                'payment_gateway' => $payment_method, // only works in production mode
                'notifyURL' => 'http://panel.lms.elite-class.com/api/upayment/cart/webhookurl',
                'ProductName' => json_encode($productName),
                'ProductQty' => json_encode($productQty),
                'ProductPrice' => json_encode($productPrice),
                'reference' => $auth->id, // Reference that you want to show in invoice in ref column
            ];

            $headers = [
                'X-Authorization' => 'hWFfEkzkYE1X691J4qmcuZHAoet7Ds7ADhL',
            ];

            $client = new GuzzleClient([
                'headers' => $headers
            ]);

            $response = $client->request('POST', config('app.upayment_url'), [
                'form_params' => $fields
            ]);

            $responseData = $response->getBody()->getContents();

            $upaymentResp = json_decode($responseData);

            $invoice = [
                'invoiceId' => $trackId,
                'invoiceURL' => $upaymentResp->paymentURL,
            ];

            return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['message' => [$e->getMessage()]]], 422);
        }
    }


    public function cartWebhookUrl(Request $response)
    {
        // if (env('TESTING_UPAYMENT_WEBHOOK_ENABLED') == '1') {
        //     $order = 'Cart items';
        //     $data = $response->all();
        //     Mail::to('zakawat.s@exodevs.com')->send(new TestMail($order, $data));
        //     Mail::to('nouman.s@exodevs.com')->send(new TestMail($order, $data));
        //     Mail::to('zakawat@excelorithm.com')->send(new TestMail($order, $data));
        // }

        if ($response->Result == 'CAPTURED') {
            $transactionSuccess = WalletTransactions::query()
                ->where('user_id', $response->cust_ref)
                ->where('transaction_id', $response->PaymentID)
                ->first();

            if (!$transactionSuccess) {
                $orders = Order::where(['order_id' => $response->OrderID, 'status' => 0])->get();

                if ($orders->isNotEmpty()) {
                    return $this->successResponse($response, $orders);
                }
            }
        } elseif ($response->Result != 'CAPTURED') {
            $transactionFailed = DB::table('order_payment_failed')
                ->where([
                    'order_id' => $response->OrderID,
                    'user_id' => $response->cust_ref,
                    'status' => $response->Result
                ])
                ->first();

            if (!$transactionFailed) {
                return $this->errorResponse($response);
            }
        }
    }


    public function success(Request $response)
    {
        $orders = Order::where(['order_id' => $response->OrderID, 'status' => 0])->get();

        if ($orders->isNotEmpty()) {

            $this->successResponse($response, $orders);
            $url = config('app.front-end-url') . '/user/success?success=1&message=Purchased successfully';
        } else {

            $orders = Order::where(['order_id' => $response->OrderID, 'status' => 1])->get();

            if ($orders->isNotEmpty()) {

                $url = config('app.front-end-url') . '/user/success?success=1&message=Purchased successfully';
            } else {

                DB::table('order_payment_failed')->insert([
                    'order_id' => $response->OrderID,
                    'user_id' => $response->cust_ref,
                    'status' => $response->Result,
                    'type' => 'cart item invoice | Order does not exist',
                    'payload' => json_encode($response->all()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $url = config('app.front-end-url') . '/user/cart?success=0&message=Payment Successfull, but you are unable to access purchased item. For support, contact Elite-class';
            }
        }

        return redirect($url);
    }

    public function successResponse($response, $orders)
    {
        $successResp = json_encode($response->all());
        $udf = json_decode($response->trnUdf);

        $carts = Cart::where('user_id', $response->cust_ref)->get();

        /** Create wallet transcation history */
        $wallet_transaction = WalletTransactions::firstOrCreate(
            [
                'user_id' => $response->cust_ref,
                'transaction_id' => $response->PaymentID,
            ],
            [
                'wallet_id' => $orders->first()->user->wallet->id,
                'payment_method' => $response->payment_type == 'card' ? 'VISA/MASTER' : 'KNET',
                'total_amount' => $udf->amt,
                'payment_charges' => $udf->payment_charges,
                'currency' => $orders->first()->currency,
                'currency_icon' => $orders->first()->currency_icon,
                'type' => 'Debit',
                'detail' => 'Cart items purchased',
                'invoice_data' => $successResp,
            ]
        );

        foreach ($orders as $key => $created_order) {
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
                    'offline_session_id' => null,
                    'user_id' => $created_order->user_id,
                    'status' => '1',
                ]);
            } elseif ($created_order->offline_session_id) {
                Wishlist::where(['offline_session_id' => $created_order->offline_session_id, 'user_id' => $orders->first()->user_id])->delete();
                OfflineSession::find($created_order->offline_session_id)->increment('order_count'); // Decrement numbers of participants can enrolled after one successfull order

                // Session Enrollment
                SessionEnrollment::create([
                    'meeting_id' => null,
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

            if ($created_order->installments && ($created_order->bundle_id || $created_order->course_id)) {
                $installments = $created_order->bundle_id ? $created_order->bundle->installments : $created_order->courses->installments;
                $totalInstallments = count($carts[$key]->total_installments);

                foreach ($carts[$key]->total_installments as $payInstallment) {
                    $amount = $installments->where('id', $payInstallment)->first()->amount;
                    $cpnDiscount = $carts[$key]->cartCoupons->isNotEmpty() ? $carts[$key]->cartCoupons->where('installment_id', $payInstallment)->first()->disamount : 0;
                    $couponId = $carts[$key]->cartCoupons->isNotEmpty() ? $carts[$key]->cartCoupons->where('installment_id', $payInstallment)->first()->coupon_id : null;

                    OrderInstallment::create([
                        'order_id' => $created_order->id,
                        'user_id' => $created_order->user_id,
                        'transaction_id' => $wallet_transaction->id,
                        'payment_method' => $response->payment_type == 'card' ? 'VISA/MASTER' : 'KNET',
                        'total_amount' => $amount - $cpnDiscount,
                        'coupon_discount' => $cpnDiscount,
                        'coupon_id' => $couponId,
                        'currency' => $created_order->currency,
                        'currency_icon' => $created_order->currency_icon,
                    ]);

                    $amount = 0;
                    $cpnDiscount = 0;
                    $couponId = null;
                }

                foreach ($installments as $i => $inst) {
                    OrderPaymentPlan::create([
                        'order_id' => $created_order->id,
                        'order_installment_id' => $i < $totalInstallments ? $created_order->installments_list[$i]->id : null,
                        'wallet_trans_id' => $i < $totalInstallments ? $wallet_transaction->id : null,
                        'created_by' => $created_order->user_id,
                        'amount' => $inst->amount,
                        'due_date' => $inst->due_date,
                        'installment_no' => $inst->sort,
                        'payment_date' => $i < $totalInstallments ? now() : null,
                        'status' => $i < $totalInstallments ? 'Paid' : null,
                    ]);
                }
            }

            $amountToPay = 0;
            if($carts[$key]->installment === 1){
                $amountToPay = $carts[$key]->offer_price;
            }else{
                if ($carts[$key]->offer_type === 'fixed'){
                    $amountToPay = $carts[$key]->price - $carts[$key]->offer_price;
                }elseif ($carts[$key]->offer_type === 'percentage'){
                    $amountToPay = $carts[$key]->price - (($carts[$key]->offer_price / 100) * $carts[$key]->price);
                }
            }
            if ($carts[$key]->cartCoupon){
                $amountToPay = $carts[$key]->cartCoupon->disamount >= $amountToPay ? 0 : $amountToPay - $carts[$key]->cartCoupon->disamount;
            }

            $created_order->paid_amount = $amountToPay;

//            $created_order->paid_amount = $carts[$key]->installment == 0 ?
//                (
//                    $carts[$key]->cartCoupon ?
//                    (($carts[$key]->cartCoupon->disamount >= $carts[$key]->offer_price) ? 0 : $carts[$key]->offer_price - $carts[$key]->cartCoupon->disamount)
//                    : $amountToPay
//                )
//                : $created_order->installments_list->sum('total_amount')
//            ;

            $created_order->coupon_discount = $carts[$key]->installment == 0 ? ($carts[$key]->cartCoupon ? (($carts[$key]->cartCoupon->disamount >= $carts[$key]->offer_price) ? $carts[$key]->offer_price : $carts[$key]->cartCoupon->disamount) : 0) : $created_order->installments_list->sum('coupon_discount');
            $created_order->coupon_id = $carts[$key]->installment == 0 ? optional($carts[$key]->cartCoupon)->coupon_id : null;
            $created_order->transaction_id = $wallet_transaction->id;
            $created_order->status = 1;
            $created_order->save();

            // Decrement number of maxuage coupon
            if ($created_order->coupon_id) {
                $coupon = Coupon::find($created_order->coupon_id);

                if ($coupon->maxusage > 0) {
                    $coupon->decrement('maxusage');
                }
            } elseif ($created_order->installments_list) {
                foreach ($created_order->installments_list as $orderInstallment) {
                    if ($orderInstallment->coupon_id) {
                        $coupon = Coupon::find($orderInstallment->coupon_id);

                        if ($coupon->maxusage > 0) {
                            $coupon->decrement('maxusage');
                        }
                    }
                }
            }

            if (env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1') {
                if (count($created_order->user->device_tokens) > 0 && $created_order->user->notifications) {
                    Notification::send($created_order->user, new UserEnroll($created_order));
                }
            }
        }

        // delete all carts item 
        $carts->each->delete();

        if (env('EMAIL_PAYMENT_INVOICE_ENABLED') == '1') {
            try {
                /* sending email */
                $x = 'Purchased Successfully.';
                $order = $orders->first();
                $data = $response->all();

                if ($order->user->exceptTestUser()) {
                    Mail::to($order->user->email)->send(new SendOrderMail($x, $order, $data));
                }
            } catch (\Swift_TransportException $e) {
            }
        }

        return response()->json(['message' => 'success', 200]);
    }


    public function error(Request $response)
    {
        $this->errorResponse($response);

        return redirect(config('app.front-end-url') . '/user/cart?success=0&message=Payment Failed');
    }

    public function errorResponse($response)
    {
        DB::table('order_payment_failed')->insert([
            'order_id' => $response->OrderID,
            'user_id' => $response->cust_ref,
            'status' => $response->Result,
            'payment_gateway' => 'UPayment',
            'type' => 'cart item invoice',
            'payload' => json_encode($response->all()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (env('EMAIL_PAYMENT_INVOICE_ENABLED') == '1') {
            $orders = Order::where(['order_id' => $response->OrderID, 'status' => 0])->get();

            try {
                /* sending email */
                $x = 'Payment Failed.';
                $order = $orders->first();
                $data = $response->all();

                if ($order->user->exceptTestUser()) {
                    Mail::to($order->user->email)->send(new SendOrderMail($x, $order, $data));
                }
            } catch (\Swift_TransportException $e) {
            }
        }

        return response()->json(['message' => 'failed', 200]);
    }


    /**
     * Pay Pending Installment
     * API Implemenation
     */
    public function payInstallment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:VISA/MASTER,KNET',
        ], [
            'payment_method.required' => __('Payment method not selected'),
            'payment_method.in' => __('Payment method is invalid'),
        ]);

        $totalAmount = 0;
        $totalDiscount = 0;
        $installmentIds = [];
        $auth = Auth::guard('api')->user();
        $installmentIds = Cache::get($auth->id) ?? [];

        if (empty($installmentIds)) {
            return response()->json(array("errors" => ["message" => [__("Atleast one installment should be selected")]]), 422);
        }

        $currency = Currency::where('default', '1')->first();

        foreach ($installmentIds as $installmentId) {
            $inst = OrderPaymentPlan::whereNull('status')->find($installmentId);
            if (!$inst) {
                return response()->json(['errors' => ['message' => [__('Installment does not exist OR may have been already paid')]]], 422);
            } elseif (($inst->pendingInstallments && $inst->pendingInstallments->count())) {
                foreach ($inst->pendingInstallments as $pending) {
                    if (!in_array($pending->id, $installmentIds)) {
                        return response()->json(['errors' => ['message' => [__('Pay the previous pending installment first, please')]]], 422);
                    }
                }
            }

            $couponDiscount = $inst->installmentCoupon ? $inst->installmentCoupon->disamount : 0;
            $totalDiscount += $couponDiscount;
            $totalAmount += $inst->amount;

            $productName[] = $inst->order->item()->_title();
            $productQty[] = '1';
            $productPrice[] = $inst->amount - $couponDiscount;
        }

        if ($totalDiscount >= $totalAmount) {
            $today = date('Ymd');
            // This is being used as order trackID in orders table as order_id
            $orderIds = Order::where('order_id', 'like', $today . '%')->pluck('order_id');
            do {
                $trackId = $today . rand(1000000, 9999999);
            } while ($orderIds->contains($trackId));

            //Generate unique transaction id
            $transactions = WalletTransactions::where('transaction_id', 'like', $today . '%')->pluck('transaction_id');
            do {
                $transaction_id = $today . rand(1000000, 9999999);
            } while ($transactions->contains($transaction_id));

            /** Create wallet transcation history */
            $wallet_transaction = WalletTransactions::create([
                'wallet_id' => $auth->wallet->id,
                'user_id' => $auth->id,
                'transaction_id' => $transaction_id,
                'payment_method' => 'Coupon',
                'total_amount' => 0,
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                'type' => 'Debit',
                'detail' => 'Installment paid',
            ]);

            foreach ($installmentIds as $key => $installmentId) {
                $inst = OrderPaymentPlan::find($installmentId);
                $couponDiscount = $inst->installmentCoupon ? $inst->installmentCoupon->disamount : 0;

                $orderInstallment = OrderInstallment::create([
                    'order_id' => $inst->order_id,
                    'user_id' => $auth->id,
                    'transaction_id' => $wallet_transaction->id,
                    'payment_method' => 'Coupon',
                    'total_amount' => 0,
                    'coupon_discount' => $inst->amount,
                    'coupon_id' => $inst->installmentCoupon->coupon_id,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                ]);

                $inst->order_installment_id = $orderInstallment->id;
                $inst->payment_date = now();
                $inst->wallet_trans_id = $wallet_transaction->id;
                $inst->status = 'Paid';
                $inst->save();

                // delete cart coupon
                $inst->installmentCoupon->delete();

                $paid = OrderInstallment::where('order_id', $inst->order_id)->get();
                Order::where('id', $inst->order_id)->update([
                    'paid_amount' => $paid->sum('total_amount'),
                    'coupon_discount' => $paid->sum('coupon_discount'),
                    'status' => 1,
                ]);
            }

            Cache::forget($auth->id);

            $resp = [
                'invoiceURL' => null,
                'isDirectEnroll' => true,
                'description' => 'This transaction is captured by system beacause it has zero amount to pay because installment amount is zero by using coupon.
                                Therefore, no need to redirect on payment gateway that\'s why invoiceURL is null.',
            ];

            return response()->json(['IsSuccess' => true, 'Message' => 'Paid successfully.', 'Data' => $resp]);
        }

        // Get payment charges against payment method
        $payment_method = $request->payment_method == 'KNET' ? 'knet' : 'cc';
        $payment_charges = 0.000;
        $payment = PaymentGateway::where('payment_method', $request->payment_method)->first();

        if ($payment) {
            if ($payment->type == 'fixed') {
                $payment_charges = $payment->charges;
            } elseif ($payment->type == 'percentage') {
                $payment_charges = ($payment->charges / 100) * $inst->amount;
            }
        }

        $udf = [
            'amt' => ($totalAmount - $totalDiscount) + $payment_charges,
            'payment_charges' => $payment_charges,
            'installment_ids' => $installmentIds,
        ];

        // UPayment Payload Starts HERE
        try {
            $fields = [
                'merchant_id' => config('app.upayment_merchant_id'), // Defined in config/app.php,
                'username' => config('app.upayment_username'), // Defined in config/app.php,
                'password' => stripslashes(config('app.upayment_password')), // Defined in config/app.php,
                'api_key' => config('app.upayment_api_key'), //In production mode, please pass API_KEY with BCRYPT function, Defined in config/app.php,

                'order_id' => implode('-', $installmentIds), // MIN 30 characters with strong unique function (like hashing function with time)
                'total_price' => ($totalAmount - $totalDiscount) + $payment_charges,
                'CurrencyCode' => $currency->code, //only works in production mode
                'CstFName' => $auth->fname . ' ' . $auth->lname,
                'CstEmail' => $auth->email,
                'CstMobile' => $auth->mobile,
                'trnUdf' => json_encode($udf),
                'success_url' => env('API_URL') . '/user/upayment/payinstallment/success',
                'error_url' => env('API_URL') . '/user/upayment/payinstallment/error',
                'test_mode' => config('app.upayment_test_mode'), // Defined in config/app.php,
                'customer_unq_token' => $auth->id, //pass unique customer identifier (eg: mobile number)
                'whitelabled' => true, // only accept in live credentials (it will not work in test)
                'payment_gateway' => $payment_method, // only works in production mode
                'notifyURL' => 'http://panel.lms.elite-class.com/api/upayment/pay-installment/webhookurl',
                'ProductName' => json_encode($productName),
                'ProductQty' => json_encode($productQty),
                'ProductPrice' => json_encode($productPrice),
                'reference' => $auth->id, // Reference that you want to show in invoice in ref column
            ];

            $headers = [
                'X-Authorization' => 'hWFfEkzkYE1X691J4qmcuZHAoet7Ds7ADhL',
            ];

            $client = new GuzzleClient([
                'headers' => $headers
            ]);

            $response = $client->request('POST', config('app.upayment_url'), [
                'form_params' => $fields
            ]);

            $responseData = $response->getBody()->getContents();

            $upaymentResp = json_decode($responseData);

            $invoice = [
                'invoiceId' => $inst->id,
                'invoiceURL' => $upaymentResp->paymentURL,
            ];

            return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['message' => [$e->getMessage()]]], 422);
        }
    }


    public function payInstallmentWebhookUrl(Request $response)
    {
        // if (env('TESTING_UPAYMENT_WEBHOOK_ENABLED') == '1') {
        //     $order = 'Pay installment';
        //     $data = $response->all();
        //     Mail::to('nouman.s@exodevs.com')->send(new TestMail($order, $data));
        //     Mail::to('zakawat@excelorithm.com')->send(new TestMail($order, $data));
        // }

        if ($response->Result == 'CAPTURED') {
            $transactionSuccess = WalletTransactions::query()
                ->where('user_id', $response->cust_ref)
                ->where('transaction_id', $response->PaymentID)
                ->first();

            if (!$transactionSuccess) {
                return $this->payInstallmentSuccessResponse($response);
            }
        } elseif ($response->Result != 'CAPTURED') {
            $transactionFailed = DB::table('order_payment_failed')
                ->where([
                    'order_id' => $response->OrderID,
                    'user_id' => $response->cust_ref,
                    'status' => $response->Result
                ])
                ->first();

            if (!$transactionFailed) {
                return $this->payInstallmentErrorResponse($response);
            }
        }
    }


    public function payInstallmentSuccess(Request $response)
    {
        $this->payInstallmentSuccessResponse($response);

        $queryParams = "?success=1&message=Installment Paid successfully";

        return redirect(config('app.front-end-url') . '/user/invoices' . $queryParams);
    }

    public function payInstallmentSuccessResponse($response)
    {
        $successResp = json_encode($response->all());
        $udf = json_decode($response->trnUdf);

        $user = User::find($response->cust_ref);
        $currency = Currency::where('default', '1')->first();

        $transactionSuccess = WalletTransactions::query()
            ->where('user_id', $response->cust_ref)
            ->where('transaction_id', $response->PaymentID)
            ->first();

        if (!$transactionSuccess) {

            $wallet_transaction = WalletTransactions::create(
                [
                    'user_id' => $response->cust_ref,
                    'transaction_id' => $response->PaymentID,
                    'wallet_id' => $user->wallet->id,
                    'payment_method' => $response->payment_type == 'card' ? 'VISA/MASTER' : 'KNET',
                    'total_amount' => $udf->amt,
                    'payment_charges' => $udf->payment_charges,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                    'type' => 'Debit',
                    'detail' => 'Installment paid',
                    'invoice_data' => $successResp,
                ]
            );

            foreach ($udf->installment_ids as $installmentId) {
                $inst = OrderPaymentPlan::find($installmentId);

                $orderInstallment = OrderInstallment::create([
                    'order_id' => $inst->order_id,
                    'user_id' => $user->id,
                    'transaction_id' => $wallet_transaction->id,
                    'payment_method' => $response->payment_type == 'card' ? 'VISA/MASTER' : 'KNET',
                    'total_amount' => $inst->amount - ($inst->installmentCoupon ? $inst->installmentCoupon->disamount : 0),
                    'coupon_discount' => $inst->installmentCoupon ? $inst->installmentCoupon->disamount : 0,
                    'coupon_id' => $inst->installmentCoupon ? $inst->installmentCoupon->coupon_id : null,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                ]);

                $inst->order_installment_id = $orderInstallment->id;
                $inst->payment_date = now();
                $inst->wallet_trans_id = $wallet_transaction->id;
                $inst->status = 'Paid';
                $inst->save();

                // Decrement number of maxuage coupon
                if ($orderInstallment->coupon_id) {
                    $coupon = Coupon::find($orderInstallment->coupon_id);

                    if ($coupon->maxusage > 0) {
                        $coupon->decrement('maxusage');
                    }
                }

                $paid = OrderInstallment::where('order_id', $inst->order_id)->get();
                Order::where('id', $inst->order_id)->update([
                    'paid_amount' => $paid->sum('total_amount'),
                    'coupon_discount' => $paid->sum('coupon_discount'),
                    'status' => 1,
                ]);

                // delete cart coupon
                optional($inst->installmentCoupon)->delete();
            }

            Cache::forget($user->id);
        }

        return response()->json(['message' => 'success', 200]);
    }


    public function payInstallmentError(Request $response)
    {
        $this->payInstallmentErrorResponse($response);

        $queryParams = "?success=0&message=Payment Failed";

        return redirect(config('app.front-end-url') . '/user/invoices' . $queryParams);
    }

    public function payInstallmentErrorResponse($response)
    {
        DB::table('order_payment_failed')->insert([
            'user_id' => $response->cust_ref,
            'order_id' => $response->OrderID,
            'status' => $response->Result,
            'payment_gateway' => 'UPayment',
            'type' => 'installment invoice',
            'payload' => json_encode($response->all()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $array = explode('-', $response->OrderID);
        $installmentId = $array[0];

        $inst = OrderPaymentPlan::find($installmentId);

        if (env('EMAIL_PAYMENT_INVOICE_ENABLED') == '1') {
            try {
                /* sending email */
                $x = 'Payment Failed.';
                $order = $inst->order;
                $data = $response->all();

                if ($order->user->exceptTestUser()) {
                    Mail::to($order->user->email)->send(new SendOrderMail($x, $order, $data));
                }
            } catch (\Swift_TransportException $e) {
            }
        }

        return response()->json(['message' => 'failed', 200]);
    }


    /**
     * Wallet Topup
     * API Implemenation
     */
    public function createWalletTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:5000',
        ], [
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount is invalid'),
            'amount.min' => __('Minimum amount shoud be 1KWD'),
            'amount.max' => __('Amount max range is 5000'),
        ]);

        $auth = Auth::guard('api')->user();
        $currency = \App\Currency::where('default', '=', '1')->first();

        $udf = [
            'amt' => $request->amount,
        ];

        $productName[] = 'Wallet Topup';
        $productPrice[] = $request->amount;

        // UPayment Payload Starts HERE
        try {
            $fields = [
                'merchant_id' => config('app.upayment_merchant_id'), // Defined in config/app.php,
                'username' => config('app.upayment_username'), // Defined in config/app.php,
                'password' => stripslashes(config('app.upayment_password')), // Defined in config/app.php,
                'api_key' => config('app.upayment_api_key'), //In production mode, please pass API_KEY with BCRYPT function, Defined in config/app.php,

                'order_id' => $auth->wallet->id, // MIN 30 characters with strong unique function (like hashing function with time)
                'total_price' => $request->amount,
                'CurrencyCode' => $currency->code, //only works in production mode
                'CstFName' => $auth->fname . ' ' . $auth->lname,
                'CstEmail' => $auth->email,
                'CstMobile' => $auth->mobile,
                'trnUdf' => json_encode($udf),
                'success_url' => env('API_URL') . '/user/upayment/wallet/topup/success',
                'error_url' => env('API_URL') . '/user/upayment/wallet/topup/error',
                'test_mode' => config('app.upayment_test_mode'), // Defined in config/app.php,
                'customer_unq_token' => $auth->id, //pass unique customer identifier (eg: mobile number)
                'whitelabled' => true, // only accept in live credentials (it will not work in test)
                'payment_gateway' => 'cc', // only works in production mode
                'notifyURL' => env('API_URL') . '/upayment/topup/webhookurl',
                'ProductName' => json_encode($productName),
                'ProductPrice' => json_encode($productPrice),
                'reference' => $auth->id, // Reference that you want to show in invoice in ref column
            ];

            $headers = [
                'X-Authorization' => 'hWFfEkzkYE1X691J4qmcuZHAoet7Ds7ADhL',
            ];

            $client = new GuzzleClient([
                'headers' => $headers
            ]);

            $response = $client->request('POST', config('app.upayment_url'), [
                'form_params' => $fields
            ]);

            $responseData = $response->getBody()->getContents();

            $upaymentResp = json_decode($responseData);

            $invoice = [
                'invoiceId' => $auth->wallet->id,
                'invoiceURL' => $upaymentResp->paymentURL,
            ];

            return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['message' => [$e->getMessage()]]], 422);
        }
    }


    public function topUpWebhookUrl(Request $response)
    {
        if ($response->Result == 'CAPTURED') {
            $transactionSuccess = WalletTransactions::query()
                ->where('user_id', $response->cust_ref)
                ->where('transaction_id', $response->PaymentID)
                ->first();
            if (!$transactionSuccess) {
                $this->walletTopUpSuccess($response);
            }
        } elseif ($response->Result != 'CAPTURED') {
            $transactionFailed = DB::table('order_payment_failed')->where([
                'order_id' => $response->OrderID,
                'user_id' => $response->cust_ref,
                'status' => $response->Result
            ])
                ->first();

            if (!$transactionFailed) {
                $this->walletTopUpError($response);
            }
        }

        return response()->json(['message' => 'Transaction status captured successfully'], 200);
    }


    public function walletTopUpSuccess(Request $response)
    {
        $successResp = json_encode($response->all());
        $udf = json_decode($response->trnUdf);

        $wallet = Wallet::find($response->OrderID);
        $currency = Currency::where('default', '=', '1')->first();

        $wallet->update([
            'balance' => $wallet->balance + $udf->amt,
        ]);

        /** Create wallet transaction history */
        $trans = WalletTransactions::firstOrCreate(
            [
                'user_id' => $response->cust_ref,
                'transaction_id' => $response->PaymentID,
            ],
            [
                'wallet_id' => $wallet->id,
                'payment_method' => $response->payment_type == 'card' ? 'VISA/MASTER' : 'KNET',
                'total_amount' => $udf->amt,
                'payment_charges' => '0',
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                'type' => 'Credit',
                'detail' => 'TopUp to wallet',
                'invoice_data' => $successResp,
            ]
        );

        if (env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1') {
            if (count($wallet->user->device_tokens) > 0 && $wallet->user->notifications) {
                Notification::send($wallet->user, new WalletTopUp($trans));
            }
        }

        return redirect(config('app.front-end-url') . '/user/refill?success=1&message=wallet topup success');
    }


    public function walletTopUpError(Request $response)
    {
        DB::table('order_payment_failed')->insert([
            'user_id' => $response->cust_ref,
            'order_id' => $response->OrderID,
            'status' => $response->Result,
            'payment_gateway' => 'UPayment',
            'type' => 'wallet topup invoice',
            'payload' => json_encode($response->all()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect(config('app.front-end-url') . '/user/refill?success=0&message=wallet topup failed');
    }
}
