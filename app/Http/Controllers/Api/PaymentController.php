<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\BBL;
use App\Cart;
use App\User;
use App\Order;
use TwilioMsg;
use App\Coupon;
use App\Course;
use App\Wallet;
use App\Setting;
use App\Currency;
use App\Wishlist;
use App\CartOrder;
use Carbon\Carbon;
use App\CourseClass;
use App\BankTransfer;
use App\BundleCourse;
use App\CartCoupon;
use App\CourseChapter;
use App\ManualPayment;
use App\PendingPayout;
use App\CourseProgress;
use App\Mail\GiftOrder;
use App\OfflineSession;
use App\PaymentGateway;
use App\WalletSettings;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\InstructorSetting;
use App\SessionEnrollment;
use App\Mail\SendOrderMail;
use App\WalletTransactions;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\AdminMailOnOrder;
use Illuminate\Validation\Rule;
use App\Notifications\AdminOrder;
use App\Notifications\UserEnroll;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function paycartorder()
    {
        $auth = Auth::guard('api')->user();

        $created_order = null;
        $msg = null;

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
            // $total_amount = $total_amount + $c->offer_price;
            if (is_null($c->offer_type) && $c->offer_price) {
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
            $coupon_discount = ($c->disamount > $c->offer_price) ? ($coupon_discount + $c->offer_price) : ($coupon_discount + $c->disamount);
        }

        foreach ($carts as $c) {
            if ($coupon_discount != 0) {
                $pay_amount = ($total_amount - $coupon_discount) > 0 ? ($total_amount - $coupon_discount) : 0;
            } else {
                $pay_amount = $total_amount;
            }
        }

        $payment_method = 'wallet';

        $w_s = WalletSettings::first();
        if (($payment_method == 'wallet' && !$w_s->status)) {
            return response()->json(array("errors" => ["message" => [__("Payment via wallet is disabled")]]), 422);
        }
        if (($payment_method == 'wallet' && !$auth->wallet) || ($auth->wallet->balance < $pay_amount)) {
            return response()->json(array("errors" => ["message" => ['Low balance']]), 402);
        }

        // // This is being used as order trackID in orders table as order_id
        // $today = date('Ymd');
        // $orderIds = Order::where('order_id', 'like', $today . '%')->pluck('order_id');
        // do {
        //     $trackId = $today . rand(1000000, 9999999);
        // } while ($orderIds->contains($trackId));

        $lastOrder = Order::orderBy('created_at', 'desc')->first();
        if (!$lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = substr($lastOrder->order_id, 3);
        }

        //Generate unique transaction id
        $today = date('Ymd');
        $transactions = WalletTransactions::where('transaction_id', 'like', $today . '%')->pluck('transaction_id');
        do {
            $transaction_id = $today . rand(1000000, 9999999);
        } while ($transactions->contains($transaction_id));

        /** Create wallet transcation history */
        $wallet_transaction = WalletTransactions::create([
            'wallet_id' => $auth->wallet->id,
            'user_id' => $auth->id,
            'transaction_id' => $transaction_id,
            'payment_method' => $payment_method,
            'total_amount' => $pay_amount,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'type' => 'Debit',
            'detail' => 'Cart items purchased',
        ]);


        $user_wallet = Wallet::where('user_id', $auth->id)->first();

        Wallet::where('user_id', $auth->id)->update([
            'balance' => $user_wallet->balance - $pay_amount,
        ]);


        foreach ($carts as $cart) {
            $cart_item = $cart->course_id ? Course::find($cart->course_id) : ($cart->bundle_id ? BundleCourse::find($cart->bundle_id) : ($cart->meeting_id ? BBL::find($cart->meeting_id) : ($cart->chapter_id ? CourseChapter::find($cart->chapter_id) : OfflineSession::find($cart->offline_session_id))));


            $total = 0;
            if ($order_item->discount_type && $order_item->discount_type == 'fixed') {
                $total = $order_item->price - $order_item->discount_price;
            } elseif ($order_item->discount_type && $order_item->discount_type == 'percentage') {
                $total = $order_item->price - (($order_item->discount_price / 100) * $order_item->price);
            } else {
                $total = $order_item->discount_price;
            }

            $created_order = Order::create([
                'title' => $cart_item->_title(),
                'price' => $cart_item->price,
                'discount_price' => $cart_item->discount_price,
                'discount_type' => $cart_item->discount_type ?? null,
                'user_id' => $auth->id,
                'instructor_id' => $cart_item->_instructor(),
                'course_id' => $cart->course_id ?? null,
                'chapter_id' => $cart->chapter_id ?? null,
                'bundle_id' => $cart->bundle_id ?? null,
                'meeting_id' => $cart->meeting_id ?? null,
                'bundle_course_id' => $cart->bundle_id ? $cart_item->course_id : null,
                'offline_session_id' => $cart->offline_session_id ?? null,
                'order_id' => '#' . sprintf("%08d", intval($number) + 1),
                'transaction_id' => $wallet_transaction->id,
                'payment_method' => $payment_method,
                'total_amount' => $cart->installment == 1 ? $cart->price : $cart->offer_price,
                'paid_amount' => ($cart->offer_price - $cart->disamount) > 0 ? $cart->offer_price - $cart->disamount : 0,
                'installments' => $cart->installment,
                'coupon_discount' => ($cart->offer_price - $cart->disamount) > 0 ? $cart->disamount : $cart->offer_price,
                'coupon_id' => $cart->coupon_id ?? null,
                'currency' => $currency->code,
                'currency_icon' => $currency->symbol,
                // 'duration' => $duration,
                'enroll_start' => $cart_item->_enrollstart(),
                'enroll_expire' => $cart_item->_enrollexpire(),
                // 'instructor_revenue' => $instructor_payout,
                'status' => 1,
            ]);

            // Decrement number of maxuage coupon
            if ($created_order->coupon_id) {
                $coupon = Coupon::find($created_order->coupon_id);

                if ($coupon->maxusage > 0) {
                    $coupon->decrement('maxusage', 1);
                }
            }

            // Session Enrollment
            if ($created_order->meeting_id) {
                SessionEnrollment::create([
                    'meeting_id' => $created_order->meeting_id,
                    'offline_session_id' => null,
                    'user_id' => $created_order->user_id,
                    'status' => '1',
                ]);
            } elseif ($created_order->offline_session_id) {
                SessionEnrollment::create([
                    'meeting_id' => null,
                    'offline_session_id' => $created_order->offline_session_id,
                    'user_id' => $created_order->user_id,
                    'status' => '1',
                ]);
            }


            // Remove items from wishlists
            if ($created_order->course_id) {
                Wishlist::where(['course_id' => $created_order->course_id, 'user_id' => $auth->id])->delete();
            } elseif ($created_order->bundle_id) {
                Wishlist::where(['bundle_id' => $created_order->bundle_id, 'user_id' => $auth->id])->delete();
            } elseif ($created_order->meeting_id) {
                Wishlist::where(['meeting_id' => $created_order->meeting_id, 'user_id' => $auth->id])->delete();
                BBL::find($created_order->meeting_id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order
            } elseif ($created_order->offline_session_id) {
                Wishlist::where(['offline_session_id' => $created_order->offline_session_id, 'user_id' => $auth->id])->delete();
                OfflineSession::find($created_order->offline_session_id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order
            }

            if ($created_order->chapter_id || $created_order->course_id || $created_order->bundle_id) {
                $courses = $created_order->course_id ? [$created_order->course_id] : ($created_order->chapter_id ? [$created_order->chapter->course_id] : $created_order->bundle_course_id);
                foreach ($courses as $c) {
                    $p = \App\CourseProgress::where([
                        'course_id' => $c,
                        'user_id' => $auth->id
                    ])->first();
                    if (!isset($p)) {
                        $chapters = CourseClass::select('id')->where('course_id', $c)->pluck('id');
                        \App\CourseProgress::create([
                            'course_id' => $c,
                            'user_id' => $auth->id,
                            'progress' => 0,
                            'mark_chapter_id' => [],
                            'all_chapter_id' => $chapters,
                            'status' => '1'
                        ]);
                    }
                }
            }

            if ($cart_item->installments && ($cart->installment == 1)) {
                OrderInstallment::create([
                    'order_id' => $created_order->id,
                    'user_id' => $auth->id,
                    'transaction_id' => $wallet_transaction->id,
                    'payment_method' => $payment_method,
                    'total_amount' => $created_order->paid_amount,
                    'coupon_discount' => $created_order->coupon_discount,
                    'coupon_id' => $created_order->coupon_id ?? null,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                ]);

                $pay_amount = $created_order->paid_amount + $created_order->coupon_discount;

                foreach ($cart_item->installments as $key => $i) {
                    OrderPaymentPlan::create([
                        'order_id' => $created_order->id,
                        'wallet_trans_id' => ($pay_amount >= $i->amount) ? $wallet_transaction->id : null,
                        'created_by' => $auth->id,
                        'amount' => $i->amount,
                        'due_date' => $i->due_date,
                        'installment_no' => $key + 1,
                        'payment_date' => ($pay_amount >= $i->amount) ? now() : null,
                        'status' => ($pay_amount >= $i->amount) ? 'Paid' : null,
                    ]);
                    $pay_amount = $pay_amount >= $i->amount ? $pay_amount - $i->amount : 0;
                }
            }

            if (env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1') {
                if ($created_order) {
                    // Notification when user enroll
                    if (count($auth->device_tokens) > 0 && $auth->notifications) {
                        Notification::send($auth, new UserEnroll($created_order));
                    }
                }
            }
        }

        //Delete user carts
        foreach ($carts as $cart) {
            $cart->delete();
        }
    }

    public static function couponCartOrder()
    {
        $auth = Auth::guard('api')->user();

        $created_order = null;

        $currency = Currency::where('default', '1')->first();
        $carts = Cart::where('user_id', $auth->id)->get();

        $payment_method = 'Coupon';
        $total_amount = 0;
        $coupon_discount = 0;
        $pay_amount = 0;

        foreach ($carts as $c) {
            //cart item price i.e. offer_price
            // $total_amount = $total_amount + $c->offer_price;
            if (is_null($c->offer_type) && $c->offer_price) {
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
                        $coupon_discount = ($cartCoupon->disamount > $c->offer_price) ? ($coupon_discount + $c->offer_price) : ($coupon_discount + $cartCoupon->disamount);
                    }
                }
            }
        }

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
            'payment_method' => $payment_method,
            'total_amount' => $pay_amount,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'type' => 'Debit',
            'detail' => 'Cart Items purchased',
        ]);

        foreach ($carts as $cart) {
            $cart_item = $cart->course_id ? Course::find($cart->course_id) : ($cart->bundle_id ? BundleCourse::find($cart->bundle_id) : ($cart->meeting_id ? BBL::find($cart->meeting_id) : ($cart->chapter_id ? CourseChapter::find($cart->chapter_id) : OfflineSession::find($cart->offline_session_id))));

            $created_order = Order::create([
                'title' => $cart_item->_title(),
                'price' => $cart_item->price,
                'discount_price' => $cart_item->discount_price,
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
                'payment_method' => $payment_method,
                'total_amount' => $cart->installment == 1 ? $cart->price : $cart->offer_price,
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

            // Remove items from wishlists
            if ($created_order->course_id) {
                Wishlist::where(['course_id' => $created_order->course_id, 'user_id' => $auth->id])->delete();
            } elseif ($created_order->bundle_id) {
                Wishlist::where(['bundle_id' => $created_order->bundle_id, 'user_id' => $auth->id])->delete();
            } elseif ($created_order->meeting_id) {
                Wishlist::where(['meeting_id' => $created_order->meeting_id, 'user_id' => $auth->id])->delete();
                BBL::find($created_order->meeting_id)->increment('order_count'); // Increment numbers of participants has been enrolled after successfull order

                // Session Enrollment
                SessionEnrollment::create([
                    'meeting_id' => $created_order->meeting_id,
                    'offline_session_id' => null,
                    'user_id' => $created_order->user_id,
                    'status' => '1',
                ]);
            } elseif ($created_order->offline_session_id) {
                Wishlist::where(['offline_session_id' => $created_order->offline_session_id, 'user_id' => $auth->id])->delete();
                OfflineSession::find($created_order->offline_session_id)->increment('order_count'); // Increment numbers of participants has been enrolled after successfull order

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
                            'user_id' => $auth->id,
                            'course_id' => $c,
                        ],
                        [
                            'progress' => '0',
                            'mark_chapter_id' => [],
                            'all_chapter_id' => $chapters,
                            'status' => '1'
                        ]
                    );
                }
            }

            if ($cart_item->installments && ($cart->installment == 1)) {
                $totalInstallments = count($cart->total_installments);

                foreach ($cart->total_installments as $payInstallment) {
                    $amount = $cart_item->installments->where('id', $payInstallment)->first()->amount;
                    $cpnDiscount = $cart->cartCoupons->isNotEmpty() ? $cart->cartCoupons->where('installment_id', $payInstallment)->first()->disamount : 0;
                    $couponId = $cart->cartCoupons->isNotEmpty() ? $cart->cartCoupons->where('installment_id', $payInstallment)->first()->coupon_id : null;

                    OrderInstallment::create([
                        'order_id' => $created_order->id,
                        'user_id' => $auth->id,
                        'transaction_id' => $wallet_transaction->id,
                        'payment_method' => $payment_method,
                        'total_amount' => $amount - $cpnDiscount,
                        'coupon_discount' => $cpnDiscount,
                        'coupon_id' => $couponId,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                    ]);

                    $amount = 0;
                    $cpnDiscount = 0;
                    $couponId = null;
                }

                foreach ($cart_item->installments as $key => $inst) {
                    OrderPaymentPlan::create([
                        'order_id' => $created_order->id,
                        'order_installment_id' => $key < $totalInstallments ? $created_order->installments_list[$key]->id : null,
                        'wallet_trans_id' => $key < $totalInstallments ? $wallet_transaction->id : null,
                        'created_by' => $created_order->user_id,
                        'amount' => $inst->amount,
                        'due_date' => $inst->due_date,
                        'installment_no' => $inst->sort,
                        'payment_date' => $key < $totalInstallments ? now() : null,
                        'status' => $key < $totalInstallments ? 'Paid' : null,
                    ]);
                }
            }

            $created_order->coupon_discount = $cart->installment == 0 ? ($cart->cartCoupon ? (($cart->cartCoupon->disamount >= $cart->offer_price) ? $cart->offer_price : $cart->cartCoupon->disamount) : 0) : $created_order->installments_list->sum('coupon_discount');
            $created_order->coupon_id = $cart->installment == 0 ? optional($cart->cartCoupon)->coupon_id : null;
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
                if ($created_order) {
                    // Notification when user enroll
                    if (count($auth->device_tokens) > 0 && $auth->notifications) {
                        Notification::send($auth, new UserEnroll($created_order));
                    }
                }
            }
        }

        // delete cart coupons
        foreach ($carts as $cart) {
            CartCoupon::where('cart_id', $cart->id)->delete();
        }
        // delete carts as well
        $carts->each->delete();
    }

    public function enroll(Request $request)
    {
        $arr1 = ['course', 'chapter', 'package', 'live-streaming', 'in-person-session'];
        $arr2 = ['courses', 'course_chapters', 'bundle_courses', 'bigbluemeetings', 'offline_sessions'];
        $arr3 = ['course_id', 'chapter_id', 'bundle_id', 'meeting_id', 'offline_session_id'];

        $key1 = array_search($request->type, $arr1);

        $request->validate([
            'type' => 'required|in:course,chapter,package,live-streaming,in-person-session',
            'id' => [
                'required',
                Rule::exists($arr2[$key1], 'id')
                ->where(function ($query) {
                    $query->where('discount_price', null)
                        ->orWhere('discount_price', '0');
                })
                ->when($request->type == 'course' || $request->type == 'chapter' || $request->type == 'package', function ($query) {
                    $query->where('status', '1');
                }),
                // ->when($request->type == 'live-streaming', function ($q) {
                //     $q->where('expire_date', '>=', date('Y-m-d'));
                // })
                // ->when($request->type == 'in-person-session', function ($q) {
                //     $q->where('expire_date', '>=', date('Y-m-d'));
                // }),
                Rule::unique('orders', $arr3[$key1])->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                        ->where('status', '<>', '0')
                        ->whereNull('deleted_at');
                })
            ],

        ], [
            'type.required' => __("Type is required"),
            'type.in' => __("Type should be course, chapter, package, live-streaming, or in-person-session"),
            'id.required' => __("Type ID is required"),
            'id.exists' => __("This item does not exist OR may have been disabled OR may not be FREE"),
            'id.unique' => __("You already enrolled in this item"),
        ]);

        $auth = auth()->user();
        $currency = Currency::where('default', '1')->first();

        $orderItem = $request->type == 'course' ? Course::find($request->id) : ($request->type == 'package' ? BundleCourse::find($request->id) : ($request->type == 'live-streaming' ? BBL::find($request->id) : ($request->type == 'chapter' ? CourseChapter::find($request->id) : OfflineSession::find($request->id))));

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
            'payment_method' => 'Free',
            'total_amount' => '0',
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'type' => 'Debit',
            'detail' => Str::headline($request->type) . ' purchased',
        ]);

        $created_order = Order::create([
            'title' => $orderItem->_title(),
            'price' => $orderItem->price,
            'discount_price' => $orderItem->discount_price,
            'user_id' => $auth->id,
            'instructor_id' => $orderItem->_instructor(),
            'course_id' => $request->type == 'course' ? $request->id : null,
            'chapter_id' => $request->type == 'chapter' ? $request->id : null,
            'bundle_id' => $request->type == 'package' ? $request->id : null,
            'meeting_id' => $request->type == 'live-streaming' ? $request->id : null,
            'bundle_course_id' => $request->type == 'package' ? $orderItem->course_id : null,
            'offline_session_id' => $request->type == 'in-person-session' ? $request->id : null,
            'order_id' => $trackId,
            'transaction_id' => $wallet_transaction->id,
            'payment_method' => 'Free',
            'total_amount' => $orderItem->discount_price??0,
            'paid_amount' => $orderItem->discount_price??0,
            'installments' => 0,
            'coupon_discount' => 0,
            'coupon_id' => null,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            // 'duration' => $duration,
            'enroll_start' => $orderItem->_enrollstart(),
            'enroll_expire' => $orderItem->_enrollexpire(),
            // 'instructor_revenue' => $instructor_payout,
            'status' => '1',
        ]);

        // Remove items from wishlists
        if ($created_order->course_id) {
            Wishlist::where(['course_id' => $created_order->course_id, 'user_id' => $auth->id])->delete();
        } elseif ($created_order->bundle_id) {
            Wishlist::where(['bundle_id' => $created_order->bundle_id, 'user_id' => $auth->id])->delete();
        } elseif ($created_order->meeting_id) {
            Wishlist::where(['meeting_id' => $created_order->meeting_id, 'user_id' => $auth->id])->delete();
            BBL::find($created_order->meeting_id)->increment('order_count'); // Increment numbers of participants has been enrolled after successfull order

            // Session Enrollment
            SessionEnrollment::create([
                'meeting_id' => $created_order->meeting_id,
                'offline_session_id' => null,
                'user_id' => $created_order->user_id,
                'status' => '1',
            ]);
        } elseif ($created_order->offline_session_id) {
            Wishlist::where(['offline_session_id' => $created_order->offline_session_id, 'user_id' => $auth->id])->delete();
            OfflineSession::find($created_order->offline_session_id)->increment('order_count'); // Increment numbers of participants has been enrolled after successfull order

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
                        'user_id' => $auth->id,
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

        if (env('ONE_SIGNAL_NOTIFICATION_ENABLED') == '1') {
            if ($created_order) {
                // Notification when user enroll
                if (count($auth->device_tokens) > 0 && $auth->notifications) {
                    Notification::send($auth, new UserEnroll($created_order));
                }
            }
        }

        return response()->json(['message' => __("Enrolled Successfully"), 'status' => 'success'], 200);
    }

    public function payInstallment(Request $request)
    {
        $this->validate($request, [
            'payment_method' => 'required|In:wallet',
            'instalment_id' => [
                'required',
                Rule::exists('order_payment_plan', 'id') //->where(function($q){
                //$q->where([['due_date', '>=', date('Y-m-d')]]);})
                ,
                Rule::unique('order_payment_plan', 'id')->where('status', 'Paid')
            ],
        ], [
            "payment_method.required" => __("payment method not selected"),
            "payment_method.in" => __("payment method not valid"),
            "instalment_id.required" => __("Instalment not selected"),
            "instalment_id.exists" => __("selected Instalment has been removed or invalid"),
            "instalment_id.unique" => __("instalment already paid"),
        ]);
        $user = Auth::guard('api')->user();

        $inst = \App\OrderPaymentPlan::find($request->instalment_id);
        if (($inst->pendingInstallments && $inst->pendingInstallments->count())) {
            return response()->json(array("errors" => ["message" => [__('Pay Pending inatallment first please')]]), 422);
        }
        $w_s = \App\WalletSettings::first();
        if (($request->payment_method == 'wallet' && !$w_s->status)) {
            return response()->json(array("errors" => ["message" => [__('Payment via wallet is disabled')]]), 422);
        }

        if (($request->payment_method == 'wallet' && !$user->wallet) || ($user->wallet->balance < $inst->amount)) {
            return response()->json(array("errors" => ["message" => [__('low balance')]]), 402);
        }
        $currency = Currency::where('default', '=', '1')->first();

        //Generate unique transaction id
        $today = date('Ymd');
        $transactions = WalletTransactions::where('transaction_id', 'like', $today . '%')->pluck('transaction_id');
        do {
            $transaction_id = $today . rand(1000000, 9999999);
        } while ($transactions->contains($transaction_id));

        /** Create wallet transcation history */
        $wallet_transaction = \App\WalletTransactions::create([
            'wallet_id' => $user->wallet->id,
            'user_id' => $user->id,
            'transaction_id' => $transaction_id,
            'payment_method' => $request->payment_method,
            'total_amount' => $inst->amount,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'type' => 'Debit',
            'detail' => __('Installment Paid'),
        ]);
        if (($request->payment_method == 'wallet')) {
            $user_wallet = Wallet::where('user_id', $user->id)->first();
            Wallet::where('user_id', $user->id)->update([
                'balance' => $user_wallet->balance - $inst->amount,
            ]);
        }

        $orderInstallment = OrderInstallment::create([
            'order_id' => $inst->order_id,
            'user_id' => $user->id,
            'transaction_id' => $wallet_transaction->id,
            'payment_method' => $request->payment_method,
            'total_amount' => $inst->amount,
            'coupon_discount' => null,
            'coupon_id' => null,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
        ]);
        // $inst->payment_date = \Carbon\Carbon::now()->toDateTimeString();
        $inst->order_installment_id = $orderInstallment->id;
        $inst->payment_date = now();
        $inst->wallet_trans_id = $wallet_transaction->id;
        $inst->status = 'Paid';
        $inst->save();

        $paid_amount = OrderInstallment::where('order_id', $inst->order_id)->sum('total_amount');
        Order::where('id', $inst->order_id)->update([
            'status' => '1',
            'paid_amount' => $paid_amount,
        ]);
        return response()->json(__("Installment Paid successfully"), 200);
    }

    public function pendingInstalments(Request $request, $is_applied = null)
    {
        $request->validate([
            'payment_plan_id' => 'nullable|exists:order_payment_plan,id',
            'remove_payment_plan_id' => 'nullable|exists:order_payment_plan,id',
        ]);

        $user = Auth::user();

        $userPayInstallments = Cache::get($user->id) ?? [];

        if ($request->payment_plan_id) {
            if (!in_array($request->payment_plan_id, $userPayInstallments)) {
                $userPayInstallments = array_merge($userPayInstallments, [$request->payment_plan_id]);
                Cache::put($user->id, $userPayInstallments, 20160);
            }
        } elseif ($request->remove_payment_plan_id) {
            $userPayInstallments = array_diff($userPayInstallments, [$request->remove_payment_plan_id]);
            Cache::put($user->id, $userPayInstallments, 20160);
        }

        $userPayInstallments = Cache::get($user->id) ?? [];

        $items = [];
        $payableInstallments = [];
        $totalAmount = 0;
        $couponDiscount = 0;
        $afterDiscount = 0;

        $orders = Order::query()
            ->where('enroll_expire', '>=', date('Y-m-d'))
            ->where(function ($q) {
                $q->whereHas('courses', function ($q) {
                    $q->active();
                })
                    ->OrWhereHas('bundle', function ($q) {
                        $q->active();
                    });
            })
            ->where(['installments' => '1', 'user_id' => $user->id])
            ->whereHas('payment_plan', function ($query) {
                $query->whereNull(['status', 'payment_date']);
            })
            ->with('payment_plan', function ($query) {
                $query->whereNull(['status', 'payment_date'])
                    ->with(['order', 'installmentCoupon']);
            })
            // ->with('item')
            ->activeOrder()
            ->get();

        foreach ($orders as $key => $order) {
            $pending = [];

            $items[$key] = [
                'title' => $order->title,
                'order_id' => $order->id,
            ];

            foreach ($order->payment_plan as $index => $payable) {
                $installment = $payable->order->item()->installments->where('sort', $payable->installment_no)->first();
                $cpnDiscount = $payable->installmentCoupon ? $payable->installmentCoupon->disamount : 0;
                $payableInstallments[] = $payable->id;

                $pending[$index] = [
                    'type' => $order->course_id ? 'course' : 'package',
                    'type_id' => $payable->order->course_id ?? $payable->order->bundle_id,
                    'payment_plan_id' => $payable->id,
                    'instalment_id' => $installment ? $installment->id : null,
                    'is_selected' => in_array($payable->id, $userPayInstallments) ? true : false,
                    'instalment_number' => $payable->installment_no,
                    'amount' => $payable->amount,
                    'due_date' => $payable->due_date,
                    'cart_coupon_id' => $payable->installmentCoupon ? $payable->installmentCoupon->id : null,
                    'coupon' => $payable->installmentCoupon ? $payable->installmentCoupon->coupon->code : null,
                    'cpn_discount' => $cpnDiscount,
                ];

                if (in_array($payable->id, $userPayInstallments)) {
                    $totalAmount += $payable->amount;
                    $couponDiscount += $cpnDiscount;
                    $afterDiscount = $totalAmount - $couponDiscount;
                }
            }

            $items[$key] = $items[$key] + ['installments' => $pending];
        }

        $array1 = $userPayInstallments;
        $array2 = $payableInstallments;
        $result = array_diff($array1, $array2);

        if (!empty($result)) {
            // To remove the values from the first array, you can assign the result back to the original array
            $userPayInstallments = array_diff($array1, $result);
            Cache::put($user->id, $userPayInstallments, 20160);
        }

        // Get payment charges against VISA/MASTER and KNET
        $payments = PaymentGateway::all();
        $visaMaster = $payments->where('payment_method', 'VISA/MASTER')->pluck('charges')->first();
        $knet = $payments->where('payment_method', 'KNET')->pluck('charges')->first();

        $data = [
            'installments' => $items,
            'total_amount' => round($totalAmount, 3),
            'coupon_discount' => round($couponDiscount, 3),
            'after_discount' => round($afterDiscount, 3),
            'knet' => $knet,
            'knet_total' => $couponDiscount >= $totalAmount ? 0 : round($afterDiscount + $knet, 3),
            'visa_master' => $visaMaster,
            'visa_master_total' => $afterDiscount + round((($visaMaster / 100) * $afterDiscount), 3),
            'is_applied' => $is_applied,
            'msg' => $is_applied == true ? __('Coupon applied successfully') : ($is_applied == false ? __('Coupon removed successfully') : ''),

        ];

        return response()->json($data, 200);
    }

    public function Invoices()
    {
        $user = Auth::user();
        $data = [];
        $data1 = [];
        $data2 = [];

        $full_payments = Order::where([['user_id', $user->id], ['installments', '0']])->activeOrder()->get();
        $installment_payments = Order::where([['user_id', $user->id], ['installments', '1']])->activeOrder()->get();

        foreach ($full_payments as $b) {
            $data1[] = [
                'order_id' => $b->id,
                'user_id' => $b->user_id,
                'user_name' => $b->user->fname . ' ' . $b->user->lname,
                'title' => $b->title,
                'grand_total' => $b->total_amount,
                'amount' => $b->paid_amount + ($b->coupon_discount ?? 0),
                'CustomerServiceCharge' => $b->transaction->payment_charges ?? null,
                'transaction_id' => $b->transaction->transaction_id ?? null,
                'created_at' => $b->created_at,
                'payment_method' => $b->transaction->payment_method,
                'discount' => $b->coupon_discount ?? null,
                'coupon' => $b->coupon_id ? $b->coupon->code : null,
            ];
        }

        foreach ($installment_payments as $paid_installment) {
            foreach ($paid_installment->installments_list as $paid) {
                $data[] = $paid;
            }
        }

        foreach ($data as $b) {
            $data2[] = [
                'user_id' => $b->user_id,
                'user_name' => $b->user->fname . ' ' . $b->user->lname,
                'title' => $b->order->title,
                'grand_total' => ($b->total_amount + ($b->coupon_discount ?? 0)),
                'amount' => $b->total_amount,
                'order_id' => $b->order_id,
                'CustomerServiceCharge' => $b->transaction->payment_charges ?? null,
                'transaction_id' => $b->transaction->transaction_id ?? null,
                'created_at' => $b->created_at,
                'payment_method' => $b->payment_method,
                'discount' => $b->coupon_discount ?? null,
                'coupon' => $b->coupon_discount > 0 && $b->coupon ? $b->coupon->code : null,
            ];
        }

        $resp1 = collect($data1);
        $resp2 = collect($data2);
        $resp = $resp1->merge($resp2)->paginate(500); //TODO Need to fix paginate doesn't work

        return response()->json($resp, 200);
    }

    public function paystore(Request $request)
    {
        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $auth = Auth::user();

        $currency = Currency::where('default', '=', '1')->first();

        $carts = Cart::where('user_id', $auth->id)->get();

        if ($file = $request->file('proof')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('images/order', $name);
            $input['proof'] = $name;
        } else {
            $name = null;
        }


        if ($request->pay_status == 1) {
            foreach ($carts as $cart) {
                if ($cart->offer_price != 0) {
                    $pay_amount = $cart->offer_price;
                } else {
                    $pay_amount = $cart->price;
                }

                if ($cart->disamount != 0 || $cart->disamount != null) {
                    $cpn_discount = $cart->disamount;
                } else {
                    $cpn_discount = '';
                }


                $lastOrder = Order::orderBy('created_at', 'desc')->first();

                if (!$lastOrder) {
                    // We get here if there is no order at all
                    // If there is no number set it to 0, which will be 1 at the end.
                    $number = 0;
                } else {
                    $number = substr($lastOrder->order_id, 3);
                }

                if ($cart->type == 1) {
                    $bundle_id = $cart->bundle_id;
                    $course_id = null;
                    $duration = null;
                    $instructor_payout = 0;
                    $instructor_id = $cart->bundle->user_id;

                    if ($cart->bundle->duration_type == "m") {
                        if ($cart->bundle->duration != null && $cart->bundle->duration != '') {
                            $days = $cart->bundle->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    } else {
                        if ($cart->bundle->duration != null && $cart->bundle->duration != '') {
                            $days = $cart->bundle->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    }
                } else {
                    if ($cart->courses->duration_type == "m") {
                        if ($cart->courses->duration != null && $cart->courses->duration != '') {
                            $days = $cart->courses->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    } else {
                        if ($cart->courses->duration != null && $cart->courses->duration != '') {
                            $days = $cart->courses->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    }


                    $setting = InstructorSetting::first();

                    if ($cart->courses->instructor_revenue != null) {
                        $x_amount = $pay_amount * $cart->courses->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        if (isset($setting)) {
                            if ($cart->courses->user->role == "instructor") {
                                $x_amount = $pay_amount * $setting->instructor_revenue;
                                $instructor_payout = $x_amount / 100;
                            } else {
                                $instructor_payout = 0;
                            }
                        } else {
                            $instructor_payout = 0;
                        }
                    }



                    $bundle_id = null;
                    $course_id = $cart->course_id;
                    $duration = $cart->courses->duration;
                    $instructor_id = $cart->courses->user_id;
                }


                if ($request->payment_method == 'paypal') {
                    $saleId = $request->sale_id;
                } else {
                    $saleId = null;
                }

                if ($request->payment_method == 'bank_transfer') {
                    $transaction_id = str_random(32);
                    $status = '0';
                } else {
                    $manual_payment = ManualPayment::where('name', $request->payment_method)->first();
                    if (isset($manual_payment) && $manual_payment != null) {
                        $status = '0';
                    } else {
                        $status = '1';
                    }

                    $transaction_id = $request->transaction_id;
                }

                $created_order = Order::create([
                    'course_id' => $course_id,
                    'user_id' => $auth->id,
                    'instructor_id' => $instructor_id,
                    'order_id' => '#' . sprintf("%08d", intval($number) + 1),
                    'transaction_id' => $transaction_id,
                    'payment_method' => $request->payment_method,
                    'total_amount' => $pay_amount,
                    'coupon_discount' => $cpn_discount,
                    'currency' => $currency->currency,
                    'currency_icon' => $currency->icon,
                    'duration' => $duration,
                    'enroll_start' => $todayDate,
                    'enroll_expire' => $expireDate,
                    'bundle_id' => $bundle_id,
                    'sale_id' => $saleId,
                    'status' => $status,
                    'proof' => $name,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);

                if ($cart->type == 1) {
                    Cart::where('user_id', $auth->id)->where('bundle_id', $cart->bundle_id)->delete();
                } else {
                    Wishlist::where('user_id', $auth->id)->where('course_id', $cart->course_id)->delete();

                    Cart::where('user_id', $auth->id)->where('course_id', $cart->course_id)->delete();
                }


                if ($instructor_payout != 0) {
                    if ($created_order) {
                        if ($cart->type == 0) {
                            if ($cart->courses->user->role == "instructor") {
                                $created_payout = PendingPayout::create([
                                    'user_id' => $cart->courses->user_id,
                                    'course_id' => $cart->course_id,
                                    'order_id' => $created_order->id,
                                    'transaction_id' => $request->transaction_id,
                                    'total_amount' => $pay_amount,
                                    'currency' => $currency->currency,
                                    'currency_icon' => $currency->icon,
                                    'instructor_revenue' => $instructor_payout,
                                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                ]);
                            }
                        }
                    }
                }


                if ($created_order) {
                    try {
                        /* sending email */
                        $x = 'You are successfully enrolled in a course';
                        $order = $created_order;
                        Mail::to(Auth::User()->email)->send(new SendOrderMail($x, $order));
                    } catch (\Swift_TransportException $e) {
                    }
                }

                if ($cart->type == 0) {
                    if ($created_order) {
                        // Notification when user enroll
                        $cor = Course::where('id', $cart->course_id)->first();

                        $course = [
                            'title' => $cor->title,
                            'image' => $cor->preview_image,
                        ];

                        $enroll = Order::where('course_id', $cart->course_id)->get();

                        if (!$enroll->isEmpty()) {
                            foreach ($enroll as $enrol) {
                                $user = User::where('id', $enrol->user_id)->get();
                                //    Notification::send($user, new UserEnroll($course));
                            }
                        }
                    }
                }
            }

            return response()->json('Payment Successfull !', 200);
        } else {
            return response()->json('Payment Failed !', 401);
        }


        return response()->json('Payment Failed !', 401);
    }

    public function purchasehistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();

        $enroll = Order::where('user_id', $user->id)->where('status', 1)->with('courses')->get();

        return response()->json(array('orderhistory' => $enroll), 200);
    }

    public function apikeys(Request $request)
    {
        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }


        $stripekey = env('STRIPE_KEY');
        $stripesecret = env('STRIPE_SECRET');

        $paypal_client_id = env('PAYPAL_CLIENT_ID');
        $paypal_secret = env('PAYPAL_SECRET');
        $paypal_mode = env('PAYPAL_MODE');

        $instamojo_api_key = env('IM_API_KEY');
        $instamojo_auth_token = env('IM_AUTH_TOKEN');
        $instamojo_url = env('IM_URL');

        $razorpay_key = env('RAZORPAY_KEY');
        $razorpay_secret = env('RAZORPAY_SECRET');

        $paystack_public_key = env('PAYSTACK_PUBLIC_KEY');
        $paystack_secret = env('PAYSTACK_SECRET_KEY');
        $paystack_pay_url = env('PAYSTACK_PAYMENT_URL');
        $paystack_merchant_email = env('PAYSTACK_MERCHANT_EMAIL');

        $paytm_enviroment = env('PAYTM_ENVIRONMENT');
        $paytm_merchant_id = env('PAYTM_MERCHANT_ID');
        $paytm_merchant_key = env('PAYTM_MERCHANT_KEY');
        $paytm_merchant_website = env('PAYTM_MERCHANT_WEBSITE');
        $paytm_channel = env('PAYTM_CHANNEL');
        $paytm_industry_type = env('PAYTM_INDUSTRY_TYPE');

        $all_keys = [
            'MOLLIE_KEY' => env('MOLLIE_KEY'),
            'SKRILL_MERCHANT_EMAIL' => env('SKRILL_MERCHANT_EMAIL'),
            'SKRILL_API_PASSWORD' => env('SKRILL_API_PASSWORD'),
            'SKRILL_LOGO_URL' => env('SKRILL_LOGO_URL'),
            'RAVE_PUBLIC_KEY' => env('RAVE_PUBLIC_KEY'),
            'RAVE_SECRET_KEY' => env('RAVE_SECRET_KEY'),
            'RAVE_ENVIRONMENT' => env('RAVE_ENVIRONMENT'),
            'RAVE_LOGO' => env('RAVE_LOGO'),
            'RAVE_PREFIX' => env('RAVE_PREFIX'),
            'RAVE_COUNTRY' => env('RAVE_COUNTRY'),
            'RAVE_SECRET_HASH' => env('RAVE_SECRET_HASH'),
            'PAYU_MERCHANT_KEY' => env('PAYU_MERCHANT_KEY'),
            'PAYU_MERCHANT_SALT' => env('PAYU_MERCHANT_SALT'),
            'PAYU_AUTH_HEADER' => env('PAYU_AUTH_HEADER'),
            'PAYU_MONEY_TRUE' => env('PAYU_MONEY_TRUE'),
            'CASHFREE_APP_ID' => env('CASHFREE_APP_ID'),
            'CASHFREE_SECRET_KEY' => env('CASHFREE_SECRET_KEY'),
            'CASHFREE_END_POINT' => env('CASHFREE_END_POINT'),
            'OMISE_PUBLIC_KEY' => env('OMISE_PUBLIC_KEY'),
            'OMISE_SECRET_KEY' => env('OMISE_SECRET_KEY'),
            'OMISE_API_VERSION' => env('OMISE_API_VERSION'),
            'PAYHERE_MERCHANT_ID' => env('PAYHERE_MERCHANT_ID'),
            'PAYHERE_BUISNESS_APP_CODE' => env('PAYHERE_BUISNESS_APP_CODE'),
            'PAYHERE_APP_SECRET' => env('PAYHERE_APP_SECRET'),
            'PAYHERE_MODE' => env('PAYHERE_MODE'),
        ];

        $bank_details = BankTransfer::first();

        return response()->json(
            array(
                'stripekey' => $stripekey,
                'stripesecret' => $stripesecret,
                'paypal_client_id' => $paypal_client_id,
                'paypal_secret' => $paypal_secret,
                'paypal_mode' => $paypal_mode,
                'instamojo_api_key' => $instamojo_api_key,
                'instamojo_auth_token' => $instamojo_auth_token,
                'instamojo_url' => $instamojo_url,
                'razorpay_key' => $razorpay_key,
                'razorpay_secret' => $razorpay_secret,
                'paystack_public_key' => $paystack_public_key,
                'paystack_secret' => $paystack_secret,
                'paystack_pay_url' => $paystack_pay_url,
                'paystack_merchant_email' => $paystack_merchant_email,
                'paytm_enviroment' => $paytm_enviroment,
                'paytm_merchant_id' => $paytm_merchant_id,
                'paytm_merchant_key' => $paytm_merchant_key,
                'paytm_merchant_website' => $paytm_merchant_website,
                'paytm_channel' => $paytm_channel,
                'paytm_industry_type' => $paytm_industry_type,
                'bank_details' => $bank_details,
                'all_keys' => $all_keys
            ),
            200
        );
    }

    public function giftusercheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if ($errors->first('course_id')) {
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
        }

        $user_check = User::where('email', $request->email)->first();

        if ($user_check == null) {
            $password = '123456';

            $user = new User();
            $user->fname = $request->fname;
            $user->lname = $request->lname;
            $user->email = $request->email;
            $user->password = Hash::make($password);
            $user->email_verified_at = \Carbon\Carbon::now()->toDateTimeString();
            $user->save();
        }

        $user_check = User::where('email', $request->email)->first();

        $course = Course::where('id', $request->course_id)->first();

        $price_total = 0;
        $offer_total = 0;
        $cpn_discount = 0;

        if ($course->discount_price != 0) {
            $offer_total = $offer_total + $course->discount_price;
        } else {
            $offer_total = $offer_total + $course->price;
        }



        $price_total = $price_total + $course->price;

        //for offer percent
        $offer_amount = $price_total - ($offer_total);
        $value = $offer_amount / $price_total;
        $offer_percent = $value * 100;

        $offer_percent = $request->offer_percent;

        $cart_total = $offer_total;

        return response()->json(array('course' => $course, 'user' => $user_check), 200);
    }

    public function giftcheckout(Request $request)
    {
        $course = Course::where('id', $request->course_id)->first();

        $user = User::where('id', $request->gift_user_id)->first();

        $gsettings = Setting::first();

        $current_date = Carbon::now();

        $currency = Currency::where('default', '=', '1')->first();

        if ($request->pay_status == '0') {
            $pay_status = '0';
        } else {
            $pay_status = 1;
        }

        if (isset($request->sale_id)) {
            $saleId = $request->sale_id;
        } else {
            $saleId = null;
        }

        if ($file = $request->file('file')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('images/order', $name);
            $input['proof'] = $name;
        } else {
            $name = null;
        }



        if ($course->discount_price != 0) {
            $pay_amount = $course->discount_price;
        } else {
            $pay_amount = $course->price;
        }


        $cpn_discount = null;

        $lastOrder = Order::orderBy('created_at', 'desc')->first();

        if (!$lastOrder) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = substr($lastOrder->order_id, 3);
        }



        if ($course->duration_type == "m") {
            if ($course->duration != null && $course->duration != '') {
                $days = $course->duration * 30;
                $todayDate = date('Y-m-d');
                $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
            } else {
                $todayDate = null;
                $expireDate = null;
            }
        } else {
            if ($course->duration != null && $course->duration != '') {
                $days = $course->duration;
                $todayDate = date('Y-m-d');
                $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
            } else {
                $todayDate = null;
                $expireDate = null;
            }
        }


        $setting = InstructorSetting::first();

        if ($course->instructor_revenue != null) {
            $x_amount = $pay_amount * $course->instructor_revenue;
            $instructor_payout = $x_amount / 100;
        } else {
            if (isset($setting)) {
                if ($course->user->role == "instructor") {
                    $x_amount = $pay_amount * $setting->instructor_revenue;
                    $instructor_payout = $x_amount / 100;
                } else {
                    $instructor_payout = 0;
                }
            } else {
                $instructor_payout = 0;
            }
        }



        $bundle_id = null;
        $course_id = $course->id;
        $bundle_course_id = null;
        $duration = $course->duration;
        $instructor_id = $course->user_id;

        $created_order = Order::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'instructor_id' => $instructor_id,
            'order_id' => '#' . sprintf("%08d", intval($number) + 1),
            'transaction_id' => $request->txn_id,
            'payment_method' => $pay_status,
            'total_amount' => $pay_amount,
            'coupon_discount' => $cpn_discount,
            'currency' => $currency->currency,
            'currency_icon' => $currency->icon,
            'duration' => $duration,
            'enroll_start' => $todayDate,
            'enroll_expire' => $expireDate,
            'instructor_revenue' => $instructor_payout,
            'bundle_id' => $bundle_id,
            'bundle_course_id' => $bundle_course_id,
            'sale_id' => $saleId,
            'status' => $pay_status,
            'proof' => $name,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        if ($instructor_payout != 0) {
            if ($created_order) {
                if ($course->user->role == "instructor") {
                    $created_payout = PendingPayout::create([
                        'user_id' => $course->user_id,
                        'course_id' => $course->id,
                        'order_id' => $created_order->id,
                        'transaction_id' => uniqid(),
                        'total_amount' => $pay_amount,
                        'currency' => $currency->currency,
                        'currency_icon' => $currency->icon,
                        'instructor_revenue' => $instructor_payout,
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]);
                }
            }
        }

        if ($created_order) {
            if ($gsettings->twilio_enable == '1') {
                try {
                    $recipients = $user->mobile;

                    $msg = 'Hey' . ' ' . $user->fname . ' ' .
                        'You\'r successfully enrolled in ' . $course->title .
                        'Thanks' . ' ' . config('app.name');

                    TwilioMsg::sendMessage($msg, $recipients);
                } catch (\Exception $e) {
                }
            }
        }



        if ($created_order) {
            if (env('MAIL_USERNAME') != null) {
                try {
                    /* sending user email */
                    $x = 'You are successfully enrolled in a course';
                    $order = $created_order;
                    Mail::to($user->email)->send(new SendOrderMail($x, $order));

                    /* sending user email */
                    $x = 'A Gift for you !!';
                    $order = $created_order;
                    Mail::to($user->email)->send(new GiftOrder($x, $order, $order->id, $course));

                    /* sending admin email */
                    $x = 'User Enrolled in course ' . $course->title;
                    $order = $created_order;
                    Mail::to($course->user->email)->send(new AdminMailOnOrder($x, $order));
                } catch (\Exception $e) {
                }
            }
        }



        if ($created_order) {
            // Notification when user enroll
            $cor = Course::where('id', $course->id)->first();

            $course = [
                'title' => $cor->title,
                'image' => $cor->preview_image,
            ];

            if ($user->id != null) {
                $user = User::where('id', $user->id)->first();
                //    Notification::send($user, new UserEnroll($course));
            }

            $order_id = $created_order->order_id;
            $url = route('view.order', $created_order->id);

            if ($cor != null) {
                $user = User::where('id', $cor->user->id)->first();
                //    Notification::send($user, new AdminOrder($course, $order_id, $url));
            }
        }



        return response()->json('Payment Successfull !', 200);
    }

    public function stripepay(Request $request)
    {
        $token = $request->token;

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $user = Auth::user();

        $carts = Cart::where('user_id', $user->id)->get();

        $price_total = 0;
        $offer_total = 0;
        $cpn_discount = 0;

        //cart price after offer
        foreach ($carts as $key => $c) {
            if ($c->offer_price != 0) {
                $offer_total = $offer_total + $c->offer_price;
            } else {
                $offer_total = $offer_total + $c->price;
            }
        }

        //for price total
        foreach ($carts as $key => $c) {
            $price_total = $price_total + $c->price;
        }


        //for coupon discount total
        foreach ($carts as $key => $c) {
            $cpn_discount = $cpn_discount + $c->disamount;
        }


        $cart_total = 0;

        foreach ($carts as $key => $c) {
            if ($cpn_discount != 0) {
                $cart_total = $offer_total - $cpn_discount;
            } else {
                $cart_total = $offer_total;
            }
        }

        $charge = $stripe->charges->create([
            'amount' => $cart_total,
            'currency' => 'usd',
            'source' => $token,
            'description' => 'Enrolling in one time paid courses'
        ]);

        if ($charge['status'] == 'succeeded') {
            $txn_id = $charge['id'];

            $payment_method = 'Stripe';

            $gsettings = Setting::first();

            $currency = Currency::first();

            $carts = Cart::where('user_id', $user->id)->get();

            foreach ($carts as $cart) {
                if ($cart->offer_price != 0) {
                    $pay_amount = $cart->offer_price;
                } else {
                    $pay_amount = $cart->price;
                }

                if ($cart->disamount != 0 || $cart->disamount != null) {
                    $cpn_discount = $cart->disamount;
                } else {
                    $cpn_discount = '';
                }


                $lastOrder = Order::orderBy('created_at', 'desc')->first();

                if (!$lastOrder) {
                    $number = 0;
                } else {
                    $number = substr($lastOrder->order_id, 3);
                }

                if ($cart->type == 1) {
                    $bundle_id = $cart->bundle_id;
                    $bundle_course_id = $cart->bundle->course_id;
                    $course_id = null;
                    $duration = null;
                    $instructor_payout = 0;
                    $instructor_id = $cart->bundle->user_id;

                    if ($cart->bundle->duration_type == "m") {
                        if ($cart->bundle->duration != null && $cart->bundle->duration != '') {
                            $days = $cart->bundle->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    } else {
                        if ($cart->bundle->duration != null && $cart->bundle->duration != '') {
                            $days = $cart->bundle->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    }
                } else {
                    if ($cart->courses->duration_type == "m") {
                        if ($cart->courses->duration != null && $cart->courses->duration != '') {
                            $days = $cart->courses->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    } else {
                        if ($cart->courses->duration != null && $cart->courses->duration != '') {
                            $days = $cart->courses->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        } else {
                            $todayDate = null;
                            $expireDate = null;
                        }
                    }


                    $setting = InstructorSetting::first();

                    if ($cart->courses->instructor_revenue != null) {
                        $x_amount = $pay_amount * $cart->courses->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        if (isset($setting)) {
                            if ($cart->courses->user->role == "instructor") {
                                $x_amount = $pay_amount * $setting->instructor_revenue;
                                $instructor_payout = $x_amount / 100;
                            } else {
                                $instructor_payout = 0;
                            }
                        } else {
                            $instructor_payout = 0;
                        }
                    }



                    $bundle_id = null;
                    $course_id = $cart->course_id;
                    $bundle_course_id = null;
                    $duration = $cart->courses->duration;
                    $instructor_id = $cart->courses->user_id;
                }



                $created_order = Order::create([
                    'course_id' => $course_id,
                    'user_id' => $user->id,
                    'instructor_id' => $instructor_id,
                    'order_id' => '#' . sprintf("%08d", intval($number) + 1),
                    'transaction_id' => $txn_id,
                    'payment_method' => $payment_method,
                    'total_amount' => $pay_amount,
                    'coupon_discount' => $cpn_discount,
                    'currency' => $currency->currency,
                    'currency_icon' => $currency->icon,
                    'duration' => $duration,
                    'enroll_start' => $todayDate,
                    'enroll_expire' => $expireDate,
                    'instructor_revenue' => $instructor_payout,
                    'bundle_id' => $bundle_id,
                    'bundle_course_id' => $bundle_course_id,
                    'sale_id' => null,
                    'status' => 1,
                    'proof' => null,
                    'created_at' => now()
                ]);

                Wishlist::where('user_id', $user->id)->where('course_id', $cart->course_id)->delete();

                Cart::where('user_id', $user->id)->delete();

                if ($instructor_payout != 0) {
                    if ($created_order) {
                        if ($cart->type == 0) {
                            if ($cart->courses->user->role == "instructor") {
                                $created_payout = PendingPayout::create([
                                    'user_id' => $cart->courses->user_id,
                                    'course_id' => $cart->course_id,
                                    'order_id' => $created_order->id,
                                    'transaction_id' => $txn_id,
                                    'total_amount' => $pay_amount,
                                    'currency' => $currency->currency,
                                    'currency_icon' => $currency->icon,
                                    'instructor_revenue' => $instructor_payout,
                                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                ]);
                            }
                        }
                    }
                }

                if ($created_order) {
                    if ($gsettings->twilio_enable == '1') {
                        try {
                            $recipients = $user->mobile;

                            $msg = 'Hey' . ' ' . $user->fname . ' ' .
                                'You\'r successfully enrolled in ' . $cart->courses->title .
                                'Thanks' . ' ' . config('app.name');

                            TwilioMsg::sendMessage($msg, $recipients);
                        } catch (\Exception $e) {
                        }
                    }
                }



                if ($created_order) {
                    if (env('MAIL_USERNAME') != null) {
                        try {
                            /* sending user email */
                            $x = 'You are successfully enrolled in a course';
                            $order = $created_order;
                            Mail::to(Auth::User()->email)->send(new SendOrderMail($x, $order));

                            /* sending admin email */
                            $x = 'User Enrolled in course ' . $cart->courses->title;
                            $order = $created_order;
                            Mail::to($cart->courses->user->email)->send(new AdminMailOnOrder($x, $order));
                        } catch (\Exception $e) {
                        }
                    }
                }

                if ($cart->type == 0) {
                    if ($created_order) {
                        // Notification when user enroll
                        $cor = Course::where('id', $cart->course_id)->first();

                        $course = [
                            'title' => $cor->title,
                            'image' => $cor->preview_image,
                        ];

                        $enroll = Order::where('user_id', $user->id)->where('course_id', $cart->course_id)->first();

                        //    if ($enroll != NULL) {
                        //        $user = User::where('id', $enroll->user_id)->first();
                        //        Notification::send($user, new UserEnroll($course));
                        //    }

                        //    $order_id = $created_order->order_id;
                        //    $url = route('view.order', $created_order->id);

                        //    if ($cor != NULL) {
                        //        $user = User::where('id', $cor->user_id)->first();
                        //        Notification::send($user, new AdminOrder($course, $order_id, $url));
                        //    }
                    }
                }
            }


            return response()->json('Payment Successfull !', 200);
        } else {
            return response()->json('Payment Failed !', 401);
        }
    }
}
