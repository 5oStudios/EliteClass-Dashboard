<?php

namespace App\Http\Controllers;

use App\BBL;
use App\Cart;
use App\Order;
use App\Coupon;
use App\Course;
use App\Wallet;
use App\Currency;
use App\Wishlist;
use Carbon\Carbon;
use App\CourseClass;
use App\BundleCourse;
use App\CourseChapter;
use App\CourseProgress;
use App\OfflineSession;
use App\PaymentGateway;
use App\WalletSettings;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\SessionEnrollment;
use App\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Notifications\UserEnroll;
use App\Notifications\WalletTopUp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class MyFatoorahController extends Controller {

    public $mfObj;
    public $order;
    private $payment_charges = 0.000;

    /**
     * create MyFatoorah object
     */
    public function __construct() {
        $this->mfObj = new PaymentMyfatoorahApiV2(config('myfatoorah.api_key'), config('myfatoorah.country_iso'), config('myfatoorah.test_mode'));
    }


    /**
     * Create MyFatoorah invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        info('MyFatoorah Index Fucntion Call');
        info($this->myObj);

        try {
            $paymentMethodId = 0; // 0 for MyFatoorah invoice or 1 for Knet in test mode
            $data = $this->mfObj->getInvoiceURL($this->getPayLoadData(), $paymentMethodId);

            return response()->json(['IsSuccess' => 'true', 'Message' => 'Invoice created successfully.', 'Data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['IsSuccess' => 'false', 'Message' => $e->getMessage()]);
        }
    }


    /**
     * 
     * @param int|string $orderId
     * @return array
     */
    private function getPayLoadData($order) {
        $callbackURL = route('myfatoorah.callback');

        return [
            'CustomerName' => $order->user->fname . ' ' . $order->user->lname,
            'InvoiceValue' => ($order->total_amount - $order->coupon_discount),
            'DisplayCurrencyIso' => $order->currency,
            'CustomerEmail' => $order->user->email,
            'CallBackUrl' => $callbackURL,
            'ErrorUrl' => $callbackURL,
            'MobileCountryCode' => '+965',
            'CustomerMobile' => '12345678',
            'Language' => 'en',
            'CustomerReference' => $order->id,
            'SourceInfo' => $order->item()->_title()
        ];
    }


    /**
     * Get MyFatoorah payment information
     */
    public function old_callback() {
        // try {
        $data = $this->mfObj->getPaymentStatus(request('paymentId'), 'PaymentId');
        $order = \App\Order::find($data->CustomerReference);

        if ($data->InvoiceStatus == 'Paid') {
            $msg = 'Invoice is paid.';

            if ($order) {
                if ($order->bundle_id) {
                    $pay_detail = 'Package Purchased';
                    $instructor_id = $order->bundle->teacher->id;
                } elseif ($order->meeting_id) {
                    $instructor_id = $order->meeting->teacher->id;
                    $pay_detail = 'Live Streaming Purchased';
                } else {
                    $pay_detail = 'Course Purchased';
                    $instructor_id = $order->courses->teacher->id;
                }
                $invoice_data = $data;

                $wallettransaction = \App\WalletTransactions::where([
                    'transaction_id' => $data->focusTransaction->TransactionId,
                    'user_id' => $order->user_id])->first();

                if(!$wallettransaction){
                    $wallet_transaction = \App\WalletTransactions::create([
                            'wallet_id' => $order->user->wallet->id,
                            'user_id' => $order->user_id,
                            'transaction_id' => $data->focusTransaction->TransactionId,
                            'payment_method' => $data->focusTransaction->PaymentGateway,
                            'total_amount' => $data->InvoiceValue,
                            'currency' => $order->currency,
                            'currency_icon' => $order->currency_icon,
                            'type' => 'Debit',
                            'detail' => $pay_detail,
                            'invoice_data' => json_encode((array) $invoice_data),
                            'invoice_id' => $data->InvoiceId,
                    ]);
                }

                if ($order->coupon_discount && $order->coupon_id) {
                    DB::table('coupons')->where('id', '=', $order->coupon_id)->decrement('maxusage', 1);
                }
                if ($order->course_id) {
                    \App\Wishlist::where(['course_id' => $order->course_id, 'user_id' => $order->user_id])->delete();
                    $fe_route = "/courses/{$order->course_id}/success";
                } elseif ($order->bundle_id) {
                    \App\Wishlist::where(['bundle_id' => $order->bundle_id, 'user_id' => $order->user_id])->delete();
                    $fe_route = "/packages/{$order->bundle_id}/success";
                } elseif ($order->meeting_id) {
                    \App\Wishlist::where(['meeting_id' => $order->meeting_id, 'user_id' => $order->user_id])->delete();
                    $fe_route = "/live-sessions/{$order->meeting_id}/success";
                }
                if ($order->course_id || $order->bundle_id) {
                    $courses = $order->course_id ? [$order->course_id] : $order->bundle_course_id;
                    foreach ($courses as $c) {
                        $p = \App\CourseProgress::where([
                                    'course_id' => $c,
                                    'user_id' => $order->user_id])->first();
                        if (!isset($p)) {
                            $chapters = \App\CourseClass::where('status', 1)->where('course_id', $c)->get(['id'])->pluck('id');
                            \App\CourseProgress::create([
                                'course_id' => $c,
                                'user_id' => $order->user_id,
                                'progress' => 0,
                                'mark_chapter_id' => [],
                                'all_chapter_id' => $chapters,
                            ]);
                        }
                    }

                    $orderinstallment = \App\OrderInstallment::where([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id])->first();

                    if(!$orderinstallment){
                        OrderInstallment::create([
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'transaction_id' => $wallet_transaction->id,
                            'payment_method' => $data->focusTransaction->PaymentGateway,
                            'total_amount' => $data->InvoiceValue,
                            'coupon_discount' => $order->coupon_discount,
                            'coupon_id' => $order->coupon_discount > 0 ? ($order->coupon_id ?? null) : null,
                            'currency' => $order->currency,
                            'currency_icon' => $order->currency_icon,
                        ]);
                    }
                }

                
                $order->paid_amount = $order->installments_list->sum('total_amount');
                $order->status = 1;
                $order->save();
                $pay_amount = $data->InvoiceValue;
                if ($order->installments && ($order->bundle_id || $order->course_id)) {
                    $inst = $order->bundle_id ? $order->bundle->installments : $order->courses->installments;
                    foreach ($inst as $i) {
                        \App\OrderPaymentPlan::create([
                            'order_id' => $order->id,
                            'wallet_trans_id' => $pay_amount >= $i->amount ? $wallet_transaction->id : null,
                            'created_by' => $order->user_id,
                            'amount' => $i->amount,
                            'due_date' => $i->due_date,
                            // 'payment_date' => $pay_amount >= $i->amount ? \Carbon\Carbon::now()->toDateTimeString() : null,
                            'payment_date' => $pay_amount >= $i->amount ? now() : null,
                            'status' => $pay_amount >= $i->amount ? 'Paid' : null,
                        ]);
                        $pay_amount = $pay_amount >= $i->amount ? $pay_amount - $i->amount : 0;
                    }
                }

                if (($order->instructor_revenue > 0) && ($order->course_id || $order->meeting_id || $order->bundle_id)) {
                   
                        \App\PendingPayout::create([
                            'user_id' => $instructor_id,
                            'course_id' => $order->course_id ?? $order->meeting_id ?? $order->bundle_id,
                            'order_id' => $order->id,
                            'transaction_id' => $wallet_transaction->id,
                            'total_amount' => $order->total_amount,
                            'currency' => $order->currency,
                            'currency_icon' => $order->currency_icon,
                            'instructor_revenue' => $order->instructor_revenue,
                        ]);
                }

                if(count($order->user->device_tokens) > 0){
                    Notification::send($order->user, new UserEnroll($order));
                }
                $url = config('app.front-end-url').$fe_route.'?success=1&message=Purchased successfully';   
            }else{
                $url = config('app.front-end-url').'/page-not-found?success=0&message=Payment failed';
            }
        } elseif($order){
                if ($order->course_id) {
                    $fe_route = "/courses/{$order->course_id}/booking";
                } elseif ($order->bundle_id) {
                    $fe_route = "/packages/{$order->bundle_id}/booking";
                } elseif ($order->meeting_id) {
                    $fe_route = "/live-sessions/{$order->meeting_id}/booking?slug={$order->meeting_id}";
                }
                $url = config('app.front-end-url').$fe_route.'?success=0&message=Payment failed';
        }else{
                $url = config('app.front-end-url').'/page-not-found?success=0&message=Payment failed';
            
        }
        
        return Redirect::to($url);
    }


    public function callback(){
        
        $data = $this->mfObj->getPaymentStatus(request('paymentId'), 'PaymentId');
        $orders = Order::where([['order_id', $data->CustomerReference], ['status', 0]])->get();
        
        $carts = Cart::where('user_id', $orders->first()->user_id)->get();

        if ($data->InvoiceStatus == 'Paid') {

            if ($orders->isNotEmpty()) {
                /** Create wallet transcation history */
                $invoice_data = $data;

                $wallettransaction = WalletTransactions::where([
                    'transaction_id' => $data->focusTransaction->TransactionId,
                    'user_id' => $orders->first()->user_id])->first();

                if(!$wallettransaction){
                    $wallet_transaction = WalletTransactions::create([
                        'wallet_id' => $orders->first()->user->wallet->id,
                        'user_id' => $orders->first()->user_id,
                        'transaction_id' => $data->focusTransaction->TransactionId,
                        'payment_method' => $data->focusTransaction->PaymentGateway,
                        'total_amount' => $data->InvoiceValue,
                        'payment_charges' => $this->payment_charges,
                        'currency' => $orders->first()->currency,
                        'currency_icon' => $orders->first()->currency_icon,
                        'type' => 'Debit',
                        'detail' => 'Cart items purchased',
                        'invoice_data' => json_encode((array) $invoice_data),
                        'invoice_id' => $data->InvoiceId,
                    ]);
                }

                foreach($orders as $key => $created_order){

                    // Decrement number of maxuage coupon
                    if ($created_order->coupon_id) {
                        $coupon = Coupon::find($created_order->coupon_id);
                        
                        if($coupon->maxusage > 0){
                            $coupon->decrement('maxusage', 1);
                        }
                    }


                    // Session Enrollment
                    if ($created_order->meeting_id) {

                        SessionEnrollment::create([
                            'meeting_id' => $created_order->meeting_id,
                            'offline_session_id' => NULL,
                            'user_id' => $created_order->user_id,
                            'status' => '1',
                        ]);

                    }elseif($created_order->offline_session_id){
                        SessionEnrollment::create([
                            'meeting_id' => NULL,
                            'offline_session_id' => $created_order->offline_session_id,
                            'user_id' => $created_order->user_id,
                            'status' => '1',
                        ]);
                    }

                    // Remove items from wishlists
                    if ($created_order->course_id) {
                        Wishlist::where(['course_id'=> $created_order->course_id,'user_id' => $orders->first()->user_id])->delete();
                    } elseif ($created_order->bundle_id) {
                        Wishlist::where(['bundle_id'=> $created_order->bundle_id,'user_id' => $orders->first()->user_id])->delete();
                    } elseif ($created_order->meeting_id) {
                        Wishlist::where(['meeting_id'=> $created_order->meeting_id,'user_id' => $orders->first()->user_id])->delete();
                        BBL::find($created_order->meeting_id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order
                    } elseif ($created_order->offline_session_id) {
                        Wishlist::where(['offline_session_id'=> $created_order->offline_session_id,'user_id' => $orders->first()->user_id])->delete();
                        OfflineSession::find($created_order->offline_session_id)->increment('order_count', 1); // Decrement numbers of participants can enrolled after one successfull order
                    }

                    if ($created_order->chapter_id || $created_order->course_id || $created_order->bundle_id) {
                        $courses = $created_order->course_id ? [$created_order->course_id] : ($created_order->chapter_id ? [$created_order->chapter->course_id] : $created_order->bundle_course_id);
                        foreach ($courses as $c) {
                            $p = CourseProgress::where([
                                        'course_id' => $c,
                                        'user_id' => $orders->first()->user_id])->first();
                            if (!isset($p)) {
                                $chapters = CourseClass::select('id')->where('course_id', $c)->pluck('id');
                                CourseProgress::create([
                                    'course_id' => $c,
                                    'user_id' => $orders->first()->user_id,
                                    'progress' => 0,
                                    'mark_chapter_id' => [],
                                    'all_chapter_id' => $chapters,
                                    'status' => '1'
                                ]);
                            }
                        }
                    }

                    $created_order->paid_amount = ($carts[$key]->offer_price - $carts[$key]->disamount) > 0 ? $carts[$key]->offer_price - $carts[$key]->disamount : 0;
                    $created_order->coupon_discount =  ($carts[$key]->offer_price - $carts[$key]->disamount) > 0 ? $carts[$key]->disamount : $carts[$key]->offer_price;
                    $created_order->coupon_id = $carts[$key]->coupon_id?? NULL;
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
                            'payment_method' => $data->focusTransaction->PaymentGateway,
                            'total_amount' => $created_order->paid_amount,
                            'coupon_discount' => $created_order->coupon_discount,
                            'coupon_id' => $created_order->coupon_id?? NULL,
                            'currency' => $created_order->currency,
                            'currency_icon' => $created_order->currency_icon,
                        ]);

                        foreach ($inst as $key => $i) {
                            OrderPaymentPlan::create([
                                'order_id' => $created_order->id,
                                'wallet_trans_id' => ($pay_amount >= $i->amount) ? $wallet_transaction->id : null,
                                'created_by' => $created_order->user_id,
                                'amount' => $i->amount,
                                'due_date' => $i->due_date,
                                'installment_no' => $key + 1,
                                'payment_date' => $pay_amount >= $i->amount ?  now() : null,
                                'status' => $pay_amount >= $i->amount ? 'Paid' : null,
                            ]);
                            $pay_amount = $pay_amount >= $i->amount ? $pay_amount - $i->amount : 0;
                        }
                    }

                    if(env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1'){

                        if(count($created_order->user->device_tokens) > 0 && $created_order->user->notifications){
                            Notification::send($created_order->user, new UserEnroll($created_order));
                        }
                    }
                }

                //Delete user carts
                foreach($carts as $cart){
                    $cart->delete();
                }
                
                $url = config('app.front-end-url').'/user/success?success=1&message=Purchased successfully';   
            } else{
                $url = config('app.front-end-url').'/user/cart?success=0&message=Payment failed';
            }

        } else{
            $url = config('app.front-end-url').'/user/cart?success=0&message=Payment failed';
        }

        return Redirect::to($url);
    }


    public function createorder(Request $request) {
        $user = Auth::guard('api')->user();
        $this->validate($request, [
            'bundle_id' => ['nullable', 'exists:bundle_courses,id', Rule::unique('orders')->where('user_id', $user->id)->where('status', 1)],
            'course_id' => ['nullable', 'exists:courses,id', Rule::unique('orders')->where('user_id', $user->id)->where('status', 1)],
            'meeting_id' => ['nullable', 'exists:bigbluemeetings,id', Rule::unique('orders')->where('user_id', $user->id)->where('status', 1)],
            'payment_method' => 'required|in:card',
            'payment_type' => 'required|in:instalment,full',
            'instalments' => 'required_if:payment_type,instalment|array',
            'coupon' => 'nullable|exists:coupons,code',
                ], [
            "bundle_id.exists" => __("bundle not found"),
            "bundle_id.unique" => __("you already enrolled in this bundle"),
            "course_id.exists" => __("course not found"),
            "course_id.unique" => __("you already enrolled in this course"),
            "meeting_id.exists" => __("meeting not found"),
            "meeting_id.unique" => __("you already enrolled in this meeting"),
            "payment_method.required" => __("payment method not selected"),
            "payment_method.in" => __("payment method not valid"),
            "payment_type.required" => __("payment type not selected"),
            "payment_type.in" => __("payment type not valid"),
            "instalments.required_if" => __("instalment not selected"),
            "instalments.array" => __("instalment not valid"),
            "coupon.exists" => __("coupon is invalid")
        ]);

        $order = $request->bundle_id ? \App\BundleCourse::find($request->bundle_id) : ($request->meeting_id ? \App\BBL::find($request->meeting_id) : \App\Course::find($request->course_id));
        $coupon = $request->coupon ? \App\Coupon::where('code', $request->coupon)->first() : null;
        if (isset($request->instalments) && $order->installment && $order->installments) {
           $due_inst = $order->installments->where('due_date','<=',date('Y-m-d'))->pluck('id')->toArray();
           $inst = $order->installments->pluck('id')->toArray();

           if(count($request->instalments) < count($due_inst)){
                return response()->json(array("errors"=>["message"=>[__("Pay all due instalments")]]), 422);
            }
             foreach ($request->instalments as $in) {
                if (!in_array($in, $inst)) {
                    return response()->json(array("errors" => ["message" => [__("selected Instalment has been removed or invalid")]]), 422);
                }
            }
            $price_total = $order->installments->sum('amount');
            $pay_total = $order->installments->whereIn('id', $request->instalments)->sum('amount');
        } else {
            $price_total = $order->discount_price ?? $order->price ?? 0;
            $pay_total = $price_total;
        }
        $cpn = ($coupon && ($price_total == $pay_total) ? $coupon->applycoupon($order, ($request->bundle_id ? 'bundle' : ($request->meeting_id ? 'meeting' : ($request->course_id ? 'course' : '')))) : [0, false]);
        $cpn_discount = $cpn[1] ? $cpn[0]['discount_amount'] : 0;
        $pay_amount = $pay_total - $cpn_discount;

        $txn_id = $request->txn_id;

        $payment_method = $request->payment_method;

        $gsettings = \App\Setting::first();

        $currency = \App\Currency::where('default', '=', '1')->first();

        $lastOrder = \App\Order::orderBy('created_at', 'desc')->where('status', '1')->first();

        if (!$lastOrder) {
            $number = 0;
        } else {
            $number = substr($lastOrder->order_id, 3);
        }

        $resp = [];

        if ($request->bundle_id) {
            $pay_detail = __('Bundle Purchased');
            $bundle_id = $request->bundle_id;
            $bundle_course_id = $order->course_id;
            $course_id = NULL;
            $meeting_id = NULL;
            $duration = NULL;
            $instructor_payout = 0;
            $instructor_id = $order->user_id;
            $resp = [
                'bundle_id' => $request->bundle_id,
                'type' => 'bundle'
            ];

            $todayDate = $order->start_date;
            $expireDate = $order->end_date;
        } elseif ($request->meeting_id) {


            // $todayDate = date('Y-m-d', strtotime($order->start_time));
            // $expireDate = date('Y-m-d', strtotime($order->start_time));
            $todayDate = $order->start_time;
            $expireDate = $order->start_time;

            $setting = \App\InstructorSetting::first();

            if ($order->instructor_revenue != NULL) {
                $x_amount = $price_total * $order->instructor_revenue;
                $instructor_payout = $x_amount / 100;
            } else {

                if (isset($setting)) {
                    if ($order->teacher->role == "instructor") {
                        $x_amount = $price_total * $setting->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        $instructor_payout = 0;
                    }
                } else {
                    $instructor_payout = 0;
                }
            }
            $resp = [
                'meeting_id' => $request->meeting_id,
                'type' => 'meeting'
            ];
            $bundle_id = NULL;
            $course_id = NULL;
            $bundle_course_id = NULL;
            $meeting_id = $order->id;
            $duration = $order->duration;
            $instructor_id = $order->user_id;
            $pay_detail = __('Live Session Purchased');
        } else {

            $todayDate = $order->start_date;
            $expireDate = $order->end_date;
            $setting = \App\InstructorSetting::first();

            if ($order->instructor_revenue != NULL) {
                $x_amount = $price_total * $order->instructor_revenue;
                $instructor_payout = $x_amount / 100;
            } else {

                if (isset($setting)) {
                    if ($order->teacher->role == "instructor") {
                        $x_amount = $price_total * $setting->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        $instructor_payout = 0;
                    }
                } else {
                    $instructor_payout = 0;
                }
            }
            $resp = [
                'course_id' => $request->course_id,
                'type' => 'course'
            ];

            $bundle_id = NULL;
            $meeting_id = NULL;
            $course_id = $request->course_id;
            $bundle_course_id = NULL;
            $duration = $order->duration;
            $instructor_id = $order->user_id;
            $pay_detail = __('Course Purchased');
        }
        $wallet_transaction = null;

        $or = [
            'title' => $order->_title(),
            'price' => $order->price,
            'discount_price' => $order->discount_price,
            'course_id' => $course_id,
            'user_id' => $user->id,
            'instructor_id' => $instructor_id,
            'order_id' => '#' . sprintf("%08d", intval($number) + 1),
            'transaction_id' => 0,
            'payment_method' => $payment_method,
            'total_amount' => $price_total,
            'paid_amount' =>  0,
            'installments' => $request->instalments ? 1 : 0,
            'coupon_discount' => $cpn_discount ?? null,
            'coupon_id' => $cpn_discount > 0 ? ($coupon->id ?? null) : null,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'duration' => $duration,
            'enroll_start' => $todayDate,
            'enroll_expire' => $expireDate,
            'instructor_revenue' => $instructor_payout,
            'bundle_id' => $bundle_id,
            'meeting_id' => $meeting_id,
            'bundle_course_id' => $bundle_course_id,
            'sale_id' => NULL,
            'status' => 0,
            'proof' => NULL,
        ];
        $created_order = \App\Order::create($or);
        // if (($request->payment_method == 'card')) {
            try {
                $callbackURL = route('myfatoorah.callback');

                $detail = [
                    'CustomerName' => $user->fname . ' ' . $user->lname,
                    'InvoiceValue' => $pay_amount,
                    'DisplayCurrencyIso' => $currency->code,
                    'CustomerEmail' => $user->email,
                    'CallBackUrl' => $callbackURL,
                    'ErrorUrl' => $callbackURL,
                    'MobileCountryCode' => $user->country_code,
                    'CustomerMobile' => str_replace($user->country_code, '', $user->mobile),
                    'Language' => 'en',
                    'CustomerReference' => $created_order->id,
                    'SourceInfo' => $created_order->item()->_title()
                ];
                $paymentMethodId = 'myfatoorah'; // 0 for MyFatoorah invoice or 1 for Knet in test mode
                $invoice = $this->mfObj->getInvoiceURL($detail, $paymentMethodId);
                $created_order->transaction_id = ($invoice ? $invoice['invoiceId'] : null );
                $created_order->save();
                return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
            } catch (\Exception $e) {
                return response()->json(array("errors"=>["message"=>[$e->getMessage()]]),422);
            }
        // }
    }


    public function paycartorder(Request $request) {

        $request->validate([
            'payment_method' => 'required|in:VISA/MASTER,KNET',
        ],[
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Payment method is invalid',
        ]);


        $auth = Auth::guard('api')->user();

        $created_order =  NULL;
        $msg =  NULL;

        Cart::validatecartitem($auth->id);  // validate cart items

        if($msg){
            return response()->json(array("errors"=>["message"=>[$msg]]), 422);
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

        if($pay_amount < 1){
            return response()->json(array("errors"=>["message"=>['Atleast 1 KWD is required to pay via card']]), 422);
        }

        // Get payment charges against payment method
        $payment_method = $request->payment_method;
        $payment = PaymentGateway::where('payment_method', $request->payment_method)->first();

        if($payment->type == 'fixed'){
            $this->payment_charges = $payment->charges;
        }elseif($payment->type == 'percentage'){
            $this->payment_charges = ($payment->charges/100) * $pay_amount; 
        }
        
        $lastOrder = Order::orderBy('created_at', 'desc')->first();
        if (!$lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = substr($lastOrder->order_id, 3);
            $number++;
        }

        foreach ($carts as $cart) {

            $cart_item  = $cart->course_id ? Course::find($cart->course_id) : ($cart->bundle_id ? BundleCourse::find($cart->bundle_id) : ($cart->meeting_id ? BBL::find($cart->meeting_id) : ($cart->chapter_id ? CourseChapter::find($cart->chapter_id) : OfflineSession::find($cart->offline_session_id))));
            
            $created_orders[] = Order::create([
                'title' => $cart_item->_title(),
                'price' => $cart_item->price,
                'discount_price' => $cart_item->discount_price,
                'user_id' => $auth->id,
                'instructor_id' => $cart_item->_instructor(),
                'course_id' => $cart->course_id?? NULL,
                'chapter_id' => $cart->chapter_id?? NULL,
                'bundle_id' => $cart->bundle_id?? NULL,
                'meeting_id' => $cart->meeting_id?? NULL,
                'bundle_course_id' => $cart->bundle_id ? $cart_item->course_id : NULL,
                'offline_session_id' => $cart->offline_session_id?? NULL,
                'order_id' => '#' . sprintf("%08d", intval($number)),
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
                'status' => 0,
            ]);

        }

        try {
            $callbackURL = route('myfatoorah.callback');

            $source_info = '';
            foreach($created_orders as $order){
                $source_info = $source_info .', '. $order->item()->_title();
            }

            $detail = [
                'CustomerName' => $auth->fname . ' ' . $auth->lname,
                'InvoiceValue' => $pay_amount + $this->payment_charges,
                'DisplayCurrencyIso' => $currency->code,
                'CustomerEmail' => $auth->email,
                'CallBackUrl' => $callbackURL,
                'ErrorUrl' => $callbackURL,
                'MobileCountryCode' => $auth->country_code,
                'CustomerMobile' => str_replace($auth->country_code, '', $auth->mobile),
                'Language' => 'en',
                'CustomerReference' => current($created_orders)->order_id,
                'SourceInfo' => $source_info,
            ];

            $paymentMethodId = 'myfatoorah'; // 0 for MyFatoorah invoice or 1 for Knet in test mode
            $invoice = $this->mfObj->getInvoiceURL($detail, $paymentMethodId);
            // $created_order->transaction_id = ($invoice ? $invoice['invoiceId'] : null );
            // $created_order->save();
            return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(array("errors"=>["message"=>[$e->getMessage()]]),422);
        }
    }


    public function payInstallment(Request $request) {

        $this->validate($request, [
            'payment_method' => 'required|in:VISA/MASTER,KNET',
            'instalment_id' => ['required', Rule::exists('order_payment_plan', 'id')//->where(function ($q) { $q->where([['due_date', '>=', date('Y-m-d')]]);})
                , Rule::unique('order_payment_plan', 'id')->where('status', 'Paid')],
        ], [
            "payment_method.required" => __("Payment method not selected"),
            "payment_method.in" => __("Payment method is invalid"),
            "instalment_id.required" => __("Installment not selected"),
            "instalment_id.exists" => __("Selected Installment has been removed or invalid"),
            "instalment_id.unique" => __("Instalment already paid"),
        ]);
        $user = Auth::guard('api')->user();
        $inst = \App\OrderPaymentPlan::find($request->instalment_id);
        
        if (($inst->pendingInstallments && $inst->pendingInstallments->count())) {
            return response()->json(array("errors"=>["message"=>[__('Pay Previous Pending inatallment first please')]]), 422);
        }
        
        $currency = \App\Currency::where('default', '=', '1')->first();

        // Get payment charges against payment method
        $payment = PaymentGateway::where('payment_method', $request->payment_method)->first();

        if($payment->type == 'fixed'){
            $this->payment_charges = $payment->charges;
        }elseif($payment->type == 'percentage'){
            $this->payment_charges = ($payment->charges/100) * $inst->amount; 
        }


        $paymentMethodId = 'myfatoorah';
        $callbackURL = route('payinstalment-inv');
        $detail = [
            'CustomerName' => $user->fname . ' ' . $user->lname,
            'InvoiceValue' => $inst->amount + $this->payment_charges,
            'DisplayCurrencyIso' => $currency->code,
            'CustomerEmail' => $user->email,
            'CallBackUrl' => $callbackURL,
            'ErrorUrl' => $callbackURL,
                'MobileCountryCode' => $user->country_code,
                'CustomerMobile' => str_replace($user->country_code, '', $user->mobile),
            'Language' => 'en',
            'CustomerReference' => $inst->id,
            'SourceInfo' => ''
        ];
        $invoice = $this->mfObj->getInvoiceURL($detail, $paymentMethodId);
        
        return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
    }


    public function InstalmentInvoicePaid(Request $request) {

        $data = $this->mfObj->getPaymentStatus(request('paymentId'), 'PaymentId');
        $inst = \App\OrderPaymentPlan::find($data->CustomerReference);

        if ($data->InvoiceStatus == 'Paid' && $inst) {

            $user = $inst->user;
            $currency = \App\Currency::where('default', '=', '1')->first();
            $wallet_transaction = \App\WalletTransactions::create([
                        'wallet_id' => $user->wallet->id,
                        'user_id' => $user->id,
                        'transaction_id' => $data->focusTransaction->TransactionId,
                        'payment_method' => $data->focusTransaction->PaymentGateway,
                        'total_amount' => $data->InvoiceValue,
                        'payment_charges' => $this->payment_charges,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                        'type' => 'Debit',
                        'detail' => __('Installment Paid'),
                        'invoice_data' => json_encode((array) $data),
                        'invoice_id' => $data->InvoiceId,
            ]);

            $Installment = \App\OrderInstallment::create([
                        'order_id' => $inst->order_id,
                        'user_id' => $user->id,
                        'transaction_id' => $wallet_transaction->id,
                        'payment_method' => $data->focusTransaction->PaymentGateway,
                        'total_amount' => $data->InvoiceValue,
                        'coupon_discount' => null,
                        'coupon_id' => null,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
            ]);
            // $inst->payment_date = \Carbon\Carbon::now()->toDateTimeString();
            $inst->payment_date = now();
            $inst->wallet_trans_id = $wallet_transaction->id;
            $inst->status = 'Paid';
            $inst->save();
            
            $paid_amount = OrderPaymentPlan::where(['order_id' => $inst->order_id, 'status' => 'Paid'])->whereNotNull('payment_date')->sum('amount'); //[0]->total_amount;
            \App\Order::where('id', $inst->order_id)->update([
                    'status' => '1',
                    'paid_amount' => $paid_amount,
                ]);
            $msg = '?success=1&message=Installment Paid successfully';
        } else{

            // Get payment charges against VISA/MASTER and KNET
            $payments = PaymentGateway::all();
            $visa_masterr = $payments->where('payment_method', 'VISA/MASTER')->pluck('charges')->first();
            $knet = $payments->where('payment_method', 'KNET')->pluck('charges')->first();

            $knet_total = $inst->amount + $knet;
            $visa_master = round((($visa_masterr/100) * $inst->amount), 3);
            $visa_master_total = round($inst->amount + (($visa_masterr/100) * $inst->amount), 3);

            $msg = "?id={$inst->id}&amount={$inst->amount}&title={$inst->order->item()->_title()}&success=0&message=Payment failed&knet={$knet}&knet_total={$knet_total}&visa_master={$visa_master}&visa_master_total={$visa_master_total}";// . $data->InvoiceError;
        }
        
        $url = config('app.front-end-url').'/user/invoices'.$msg;
        return Redirect::to($url);

    }


    public function generateInvoiceForWallet(Request $request) {
        $this->validate($request, [
            'amount' => 'required|numeric|min:1|max:5000',
                ], [
            "amount.required" => __("amount is required"),
            "amount.numeric" => __("amount is invalid"),
            "amount.min" => __("amount is nivalid"),
            "amount.max" => __("amount max range is 5000"),
            "transaction_id" => __('transaction ID is missing'),
            "payment_method" => __('payment method not selected')
        ]);
        $paymentMethodId = 'myfatoorah';
        $callbackURL = route('myfatoorah.wallettopup');
        $user = Auth::guard('api')->user();

        $currency = \App\Currency::where('default', '=', '1')->first();
        $detail = [
            'CustomerName' => $user->fname . ' ' . $user->lname,
            'InvoiceValue' => $request->amount,
            'DisplayCurrencyIso' => $currency->code,
            'CustomerEmail' => $user->email,
            'CallBackUrl' => $callbackURL,
            'ErrorUrl' => $callbackURL,
                    'MobileCountryCode' => $user->country_code,
                    'CustomerMobile' => str_replace($user->country_code, '', $user->mobile),
            'Language' => 'en',
            'CustomerReference' => $user->wallet->id,
            'SourceInfo' => ''
        ];
        $invoice = $this->mfObj->getInvoiceURL($detail, $paymentMethodId);
        return response()->json(['IsSuccess' => true, 'Message' => 'Invoice created successfully.', 'Data' => $invoice]);
    }


    public function walletTopUp(Request $request) {
 
        $data = $this->mfObj->getPaymentStatus(request('paymentId'), 'PaymentId');
        
        if ($data->InvoiceStatus == 'Paid') {
            $wallet = \App\Wallet::where('id', $data->CustomerReference)->first();
            $currency = \App\Currency::where('default', '=', '1')->first();

            $wallet->update([
                'balance' => $wallet->balance + $data->InvoiceValue,
            ]);

            /** Create wallet transaction history */
           $trans = \App\WalletTransactions::create([
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'transaction_id' => $data->focusTransaction->TransactionId,
                'payment_method' => $data->focusTransaction->PaymentGateway,
                'total_amount' => $data->InvoiceValue,
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                'type' => 'Credit',
                'detail' => __('TopUp to wallet'),
                'invoice_data' => json_encode((array) $data),
                'invoice_id' => $data->InvoiceId,
            ]);

            if(env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1'){

                if(count($wallet->user->device_tokens) > 0 && $wallet->user->notifications){
                    Notification::send($wallet->user,new WalletTopUp($trans));
                }
            }
            
            $msg = '?success=1&message=wallet topup success';
        } else {
            $msg = '?success=0&message=wallet topup failed';
        }
            $url = config('app.front-end-url').'/user/refill'.$msg;
            return Redirect::to($url);

        return response()->json(array("errors"=>["message"=>[ $msg]]),422);
    }

}
