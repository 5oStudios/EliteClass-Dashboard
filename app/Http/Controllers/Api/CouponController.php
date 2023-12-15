<?php

namespace App\Http\Controllers\Api;

use DB;
use Carbon;
use App\BBL;
use Session;
use App\Cart;
use App\Order;
use Validator;
use App\Coupon;
use App\Course;
use App\CartCoupon;
use App\Installment;
use App\BundleCourse;
use App\CourseChapter;
use App\OfflineSession;
use App\OrderPaymentPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\MainController;

class CouponController extends Controller
{
    public function applyOrdercoupon(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|numeric|min:1',
            'order' => 'required|in:course,bundle,meeting',
            'coupon' => 'required|exists:coupons,code'
        ], [
            'order_id.required' => __("Order not selected"),
            'order_id.numeric' => _("Order not valid"),
            'order_id.min' => _("Order not valid"),
            'order.required' => _("Order not selected"),
            'order.in' => _("Order not valid"),
            'coupon.required' => _("coupon not selected"),
            'coupon.exists' => _("Coupon is invalid"),
        ]);

        $c = Coupon::where('code', $request->coupon)->first();
        $order = $request->order == "bundle" ? BundleCourse::find($request->order_id) : ($request->order == "meeting" ? BBL::find($request->order_id) : Course::find($request->order_id));
        $r = $c->applycoupon($order, $request->order);

        if ($r[1]) {
            return response()->json(
                $r[0],
                200
            );
        } else {
            return response()->json([
                        "message" => __("Coupon is invalid"),
                        "errors" => [
                            "coupon" => [$r[0]]]
                            ], 422);
        }
    }

    public function coupons(Request $request)
    {
        $this->validate($request, [
           'course_id' => 'nullable|exists:courses,id',
           'bundle_id' => 'nullable|exists:bundle_courses,id',
           'meeting_id' => 'nullable|exists:bigbluemeetings,id',
        ], [
            'course_id.exists' => __("course not found"),
            'bundle_id.exists' => __("bundle not found"),
            'meeting_id.exists' => __("meeting not found"),
        ]);
        $c = Coupon::
                when($request->course_id, function ($q) use ($request) {
                    return $q->where('course_id', $request->course_id);
                })
                ->when($request->bundle_id, function ($q) use ($request) {
                    return $q->where('bundle_id', $request->bundle_id);
                })
                ->when($request->meeting_id, function ($q) use ($request) {
                    return $q->where('meeting_id', $request->meeting_id);
                })
                ->where([['maxusage','>',0],['expirydate','>=',date('Y-m-d')]])
                ->get();

        // $c->getCollection()->transform(function ($b){

        $data = [];
        foreach ($c as $b) {
            $data[] = [
                'id' => $b->id,
                'code' => $b->code,
                'type' => $b->distype,
                'amount' => $b->amount,
                // 'expirydate' => date('Y-m-d',strtotime($b->expirydate)),
                'expirydate' => $b->expirydate,
            ];
        }

        return $data;
    }

    public function applyCartCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'nullable|exists:coupons,code',
            'cart_coupon_id' => 'nullable|exists:cart_coupons,id',
            'course_id' => ['nullable',
                            Rule::exists('courses', 'id')->where(function ($query) {
                                return $query->where('status', '1')
                                            ->where('end_date', '>=', date('Y-m-d'))
                                            ->whereNull('deleted_at');
                            })],
            'bundle_id' => ['nullable',
                            Rule::exists('bundle_courses', 'id')->where(function ($query) {
                                return $query->where('status', '1')
                                            ->where('end_date', '>=', date('Y-m-d'));
                            })],
            'meeting_id' => ['nullable',
                            Rule::exists('bigbluemeetings', 'id')->where(function ($query) {
                                return $query->where('expire_date', '>=', date('Y-m-d'))
                                            ->whereNull('deleted_at');
                            })],
            'offline_session_id' => ['nullable',
                            Rule::exists('offline_sessions', 'id')->where(function ($query) {
                                return $query->where('expire_date', '>=', date('Y-m-d'))
                                            ->where('is_ended', '<>', '1')
                                            ->whereNull('deleted_at');
                            })],
            'chapter_id' => ['nullable',
                            Rule::exists('course_chapters', 'id')->where(function ($query) {
                                return $query->where('status', '1')
                                            ->whereNull('deleted_at');
                            })],
        ], [
            "course_id.exists" => __("Course not found"),
            "bundle_id.exists" => __("Package not found"),
            "meeting_id.exists" => __("Live Streaming not found"),
            "offline_session_id.exists" => __("In-Person Session not found"),
            "coupon.exists" => __("Coupon is invalid"),
            "coupon_id.exists" => __("Coupon is invalid"),
        ]);

        $auth = Auth::user();

        if ($request->coupon) {
            $coupon = $request->coupon ? Coupon::where('code', $request->coupon)->first() : null;
            $cartCoupon = CartCoupon::where('user_id', auth()->id())->where('coupon_id', $coupon->id)->first();

            $userOrders =  Order::query()
                            ->where('user_id', $auth->id)
                            ->where(function ($query) use ($coupon) {
                                $query->whereHas('installments_list', function ($query) use ($coupon) {
                                    $query->where('coupon_id', $coupon->id);
                                })
                                ->orWhere('coupon_id', $coupon->id);
                            })
                            ->activeOrder()
                            ->get();

            if ($userOrders->isNotEmpty()) {
                return response()->json(array("errors" => ["message" => ["You already have used this coupon"]]), 422);
            }
            
            if ($cartCoupon) {
                $cartCoupon->delete();
            }

            $cart_item = $request->course_id ? Course::find($request->course_id) : ($request->bundle_id ? BundleCourse::find($request->bundle_id) : ($request->meeting_id ? BBL::find($request->meeting_id) : ($request->offline_session_id ? OfflineSession::find($request->offline_session_id) : CourseChapter::find($request->chapter_id))));
            
            $installment = Installment::query()
            ->when($request->installment_id, function ($q) use ($request) {
                return $q->find($request->installment_id);
            });
            
            $cpn = $coupon ? $coupon->applycoupon($request->installment_id ? $installment : $cart_item, ($request->course_id ? 'course' : ($request->bundle_id ? 'bundle' : ($request->meeting_id ? 'meeting' : ($request->offline_session_id ? 'session' : null)))), $request->installment_id ?? null) : [0, false];
            
            if ($coupon && $cpn[1]) {
                $cart = $request->course_id ? Cart::where('course_id', $cart_item->id)->where('user_id', $auth->id)->first() : ($request->bundle_id ? Cart::where('bundle_id', $cart_item->id)->where('user_id', $auth->id)->first() : ($request->meeting_id ? Cart::where('meeting_id', $cart_item->id)->where('user_id', $auth->id)->first() : ($request->offline_session_id ? Cart::where('offline_session_id', $cart_item->id)->where('user_id', $auth->id)->first() : Cart::where('chapter_id', $cart_item->id)->where('user_id', $auth->id)->first())));
                
                $cpn_discount = $cpn[0]['discount_amount'];
                $distype = $cpn[0]['distype'];

                CartCoupon::create([
                    'user_id' => $auth->id,
                    'cart_id' => $cart->id,
                    'order_payment_plan_id' => null,
                    'coupon_id' => $coupon ? $coupon->id : null,
                    'installment_id' => $request->installment_id ?? null,
                    'disamount' => $cpn_discount,
                    'distype' => $distype,
                ]);

                // return all carts
                $mainController = new MainController();
                return $mainController->showcart(true);

                // return $this->showcart();
            } elseif ($coupon && !$cpn[1]) {
                return response()->json(array("errors" => ["message" => [$cpn[0]]]), 422);
            }
        } elseif ($request->cart_coupon_id) {
            $cartCoupon = CartCoupon::find($request->cart_coupon_id);
            $cartCoupon->delete();

            // return all carts
            $mainController = new MainController();
            return $mainController->showcart(false);

            // return $this->showcart();
        }
    }

    public function applyPendingInstallmentCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'nullable|exists:coupons,code',
            'payment_plan_id' => ['required_without:cart_coupon_id', Rule::exists('order_payment_plan', 'id')->whereNull('status')],
            'installment_id' => 'required_without:cart_coupon_id|exists:course_payment_plan,id',
            'course_id' => ['nullable',
                            Rule::exists('courses', 'id')->where(function ($query) {
                                return $query->where('status', '1')
                                            ->where('end_date', '>=', date('Y-m-d'))
                                            ->whereNull('deleted_at');
                            })],
            'bundle_id' => ['nullable',
                            Rule::exists('bundle_courses', 'id')->where(function ($query) {
                                return $query->where('status', '1')
                                            ->where('end_date', '>=', date('Y-m-d'));
                            })],
            'cart_coupon_id' => 'nullable|exists:cart_coupons,id',
        ], [
            "coupon.exists" => __("Coupon is invalid"),
            "course_id.exists" => __("Course not found"),
            "bundle_id.exists" => __("Package not found"),
            "coupon_id.exists" => __("Coupon is invalid"),
            "payment_plan_id.exists" => __("Installment does not exist OR may have been already paid"),
        ]);

        $auth = Auth::user();

        if ($request->coupon) {
            $coupon = Coupon::where('code', $request->coupon)->first();

            $userOrders =  Order::query()
                            ->where('user_id', $auth->id)
                            ->where(function ($query) use ($coupon) {
                                $query->whereHas('installments_list', function ($query) use ($coupon) {
                                    $query->where('coupon_id', $coupon->id);
                                })
                                ->orWhere('coupon_id', $coupon->id);
                            })
                            ->activeOrder()
                            ->get();

            if ($userOrders->isNotEmpty()) {
                return response()->json(array("errors" => ["message" => ["You already have used this coupon"]]), 422);
            }

            $pendingInstallment = OrderPaymentPlan::find($request->payment_plan_id);

            if ($pendingInstallment->installmentCoupon) {
                $pendingInstallment->installmentCoupon->delete();
            }

            $cpn = $coupon ? $coupon->applycoupon($pendingInstallment, ($request->course_id ? 'course' : ($request->bundle_id ? 'bundle' : null)), $request->installment_id, $request->payment_plan_id) : [0, false];

            if ($coupon && $cpn[1]) {
                $cpn_discount = $cpn[0]['discount_amount'];
                $distype = $cpn[0]['distype'];

                CartCoupon::create([
                    'user_id' => $auth->id,
                    'cart_id' => null,
                    'order_payment_plan_id' => $request->payment_plan_id,
                    'coupon_id' => $coupon ? $coupon->id : null,
                    'installment_id' => $request->installment_id,
                    'disamount' => $cpn_discount,
                    'distype' => $distype,
                ]);

                // return all pending installments
                $paymentController = new PaymentController();
                return $paymentController->pendingInstalments(new Request(), true);
            } elseif ($coupon && !$cpn[1]) {
                return response()->json(array("errors" => ["message" => [$cpn[0]]]), 422);
            }
        } elseif ($request->cart_coupon_id) {
            $cartCoupon = CartCoupon::find($request->cart_coupon_id);
            $cartCoupon->delete();

            // return all pending installments
            $paymentController = new PaymentController();
            return $paymentController->pendingInstalments(new Request(), false);
        }
    }
}
