<?php

namespace App\Http\Controllers;

use App\BBL;
use App\Coupon;
use App\Course;
use App\Currency;
use App\CartCoupon;
use App\Installment;
use App\BundleCourse;
use App\OfflineSession;
use App\OrderPaymentPlan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:coupons.view', ['only' => ['index']]);
        $this->middleware('permission:coupons.create', ['only' => ['create', 'store', 'bulkCreate', 'bulkStore']]);
        $this->middleware('permission:coupons.edit', ['only' => ['edit', 'update', 'status']]);
        $this->middleware('permission:coupons.delete', ['only' => ['destroy', 'bulk_delete']]);
    }


    public function index(Request $request)
    {
        if (Auth::user()->is_abpp) {
            $data = Coupon::query()
                ->select('*')
                ->where('user_id', '=', Auth::user()->id)
                ->with(['course', 'bundle', 'meeting', 'session'])
                ->latest('id');
        } else {
            $data = Coupon::query()
                ->select('*')
                ->with(['course', 'bundle', 'meeting', 'session'])
                ->latest('id');
        }

        if ($request->ajax()) {
            return DataTables::eloquent($data)
                ->setRowClass(function ($row) {
                    return ($row->expirydate < date('Y-m-d') || $row->maxusage <= '0') ? 'text-danger' : 'active-coupon-color';
                })
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {

                    $chk = "<div class='inline'>
                              <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                              <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })
                ->editColumn('code', function ($row) {

                    return $row->code ?? '';
                })
                ->editColumn('amount', function ($row) {

                    return $row->amount ?? '';
                })
                ->editColumn('maxusage', function ($row) {

                    return $row->maxusage ?? '';
                })
                ->editColumn('detail', 'admin.coupan.datatables.detail')
                ->editColumn('action', 'admin.coupan.datatables.action')
                ->rawColumns(['checkbox', 'code', 'amount', 'maxusage', 'detail', 'action'])
                ->toJson();
        }

        return view('admin.coupan.index');
    }


    public function create()
    {
        $courses = Course::active()->where('discount_price', '<>', '0')->get();
        $bundles = BundleCourse::active()->where('discount_price', '<>', '0')->get();
        $meetings = BBL::active()->where('discount_price', '<>', '0')->get();
        $sessions = OfflineSession::active()->where('discount_price', '<>', '0')->get();

        $coupon_code = '';
        $arr = str_replace("-", "", strtoupper(Str::uuid())); // remove all "-" characters
        $coupon_code = substr($arr, 0, 20);

        // $coupon_code = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 7);
        return view("admin.coupan.add", compact('coupon_code', 'courses', 'bundles', 'meetings', 'sessions'));
    }


    public function bulkCreate()
    {
        $courses = Course::active()->where('discount_price', '<>', '0')->get();
        $bundles = BundleCourse::active()->where('discount_price', '<>', '0')->get();
        $meetings = BBL::active()->where('discount_price', '<>', '0')->get();
        $sessions = OfflineSession::active()->where('discount_price', '<>', '0')->get();

        $coupon_code = '';
        $arr = str_replace("-", "", strtoupper(Str::uuid())); // remove all "-" characters
        $coupon_code = substr($arr, 0, 20);

        // $coupon_code = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 7);
        return view("admin.coupan.bulk.create", compact('coupon_code', 'courses', 'bundles', 'meetings', 'sessions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'coupon_type' => 'required|in:general,item',
            'code' => 'required|unique:coupons,code|max:20',
            'minamount' => 'nullable|required_if:coupon_type,general|numeric|min:1',
            'link_by' => 'nullable|required_if:coupon_type,item|in:course,bundle,meeting,session',
            'course_id' => 'nullable|required_if:link_by,course',
            'bundle_id' => 'nullable|required_if:link_by,bundle',
            'meeting_id' => 'nullable|required_if:link_by,meeting',
            'offline_session_id' => 'nullable|required_if:link_by,session',
            'payment_type' => 'nullable|required_with:link_by|in:full,installment',
            'installment_number' => 'nullable|required_if:payment_type,installment',
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->distype == 'per') {
                        if (!is_int(intval($value))) {
                            $fail(__('Percentage number should be a positive integer'));
                        } elseif ($value > 100) {
                            $fail(__('Percentage number should not be greater than 100'));
                        }
                    }
                }
            ],
            'distype' => 'required|in:fix,per',
            'expirydate' => "required|date|date_format:Y-m-d|after_or_equal:" . date('Y-m-d'),
            'maxusage' => 'required|numeric|min:1',
        ], [
            'code.required' => __('Code is required'),
            'code.unique' => __('This code is already exist'),
            'code.max' => __('Code should not be more than 20 characters'),
            'distype.required' => __('Discount type is required'),
            'distype.in' => __('Discount type should be fixed amount OR in percentage'),
            'minamount.required_if' => 'Min amount is required',
            'minamount.numeric' => __('Min amount should be a numeric value'),
            'minamount.min' => __('Min amount should be greater than zero'),
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount must be in numeric'),
            'amount.min' => __('Amount should be greater than zero'),
            'link_by.required_if' => __('Coupon must be linked with Live Streaming, Package or Course'),
            'link_by.in' => __('Coupon must be linked with Live Streaming, Offline Session, Package or Course'),
            'expirydate.required' => __('Expiry Date is required'),
            'expirydate.date_format' => __('Expiry Date format must be in YYYY-MM-DD format'),
            'expirydate.after_or_equal' => __('Expiry Date must be greater than or equal to today\'s date'),
            'maxusage.required' => __('Maximum usage is required'),
            'maxusage.numeric' => __('Maximum usage should be a numeric value'),
            'maxusage.min' => __('Maximum usage should be greater than zero'),
        ]);

        try {
            $input = $request->all();
            $coupon = new Coupon();

            if ($request->coupon_type == 'general') {
                $input['link_by'] = null;
                $input['course_id'] = null;
                $input['bundle_id'] = null;
                $input['meeting_id'] = null;
                $input['offline_session_id'] = null;
                $input['payment_type'] = null;
                $input['installment_id'] = null;
            } else {
                $input['minamount'] = null;

                $installment = Installment::query()
                    ->when(request('payment_type') == 'installment' && request('course_id') && request('installment_number'), function ($q) {
                        $q->where(['course_id' => request('course_id'), 'sort' => request('installment_number')]);
                    })
                    ->when(request('payment_type') == 'installment' && request('bundle_id') && request('installment_number'), function ($q) {
                        $q->where(['bundle_id' => request('bundle_id'), 'sort' => request('installment_number')]);
                    })
                    ->first();

                $input['installment_id'] = $installment ? $installment->id : null;
            }

            $input['show_to_users'] = 0;
            $input['user_id'] = Auth::user()->id;

            // stripe coupon creation
            // $input = $this->processSubscriptionCoupon($input);
            $coupon->create($input);

            return redirect("coupon")->with('success', trans('flash.CouponCreatedSuccessfully'));
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());

            return redirect('coupon')->with('delete', trans('flash.CouponFailed'));
        }
    }


    public function bulkStore(Request $request)
    {
        $request->validate([
            'coupon_type' => 'required|in:general,item',
            'coupon_category' => 'required|in:random,increment',
            'coupon_count' => 'required|numeric|min:1',
            'minamount' => 'nullable|required_if:coupon_type,general|numeric|min:1',
            'code' => 'nullable|required_if:coupon_category,increment|unique:coupons,code|max:20',
            'link_by' => 'nullable|required_if:coupon_type,item|in:course,bundle,meeting,session',
            'course_id' => 'nullable|required_if:link_by,course',
            'bundle_id' => 'nullable|required_if:link_by,bundle',
            'meeting_id' => 'nullable|required_if:link_by,meeting',
            'offline_session_id' => 'nullable|required_if:link_by,session',
            'payment_type' => 'nullable|required_with:link_by|in:full,installment',
            'installment_number' => 'nullable|required_if:payment_type,installment',
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->distype == 'per') {
                        if (!is_int(intval($value))) {
                            $fail(__('Percentage number should be a positive integer'));
                        } elseif ($value > 100) {
                            $fail(__('Percentage number should not be greater than 100'));
                        }
                    }
                }
            ],
            'distype' => 'required|in:fix,per',
            'expirydate' => "required|date|date_format:Y-m-d|after_or_equal:" . date('Y-m-d'),
            'maxusage' => 'required|numeric|min:1',
        ], [
            'code.required' => __('Code is required'),
            'code.unique' => __('This code is already exist'),
            'code.max' => __('Code should not be more than 20 characters'),
            'distype.required' => __('Discount type is required'),
            'distype.in' => __('Discount type should be fixed amount OR in percentage'),
            'minamount.required_if' => 'Min amount is required',
            'minamount.numeric' => __('Min amount should be a numeric value'),
            'minamount.min' => __('Min amount should be greater than zero'),
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount must be in numeric'),
            'amount.min' => __('Amount should be greater than zero'),
            'link_by.required_if' => __('Coupon must be linked with Live Streaming, Package or Course'),
            'link_by.in' => __('Coupon must be linked with Live Streaming, Offline Session, Package or Course'),
            'expirydate.required' => __('Expiry Date is required'),
            'expirydate.date_format' => __('Expiry Date format must be in YYYY-MM-DD format'),
            'expirydate.after_or_equal' => __('Expiry Date must be greater than or equal to today\'s date'),
            'maxusage.required' => __('Maximum usage is required'),
            'maxusage.numeric' => __('Maximum usage should be a numeric value'),
            'maxusage.min' => __('Maximum usage should be greater than zero'),
        ]);

        $input = $request->all();

        if ($request->coupon_type == 'general') {
            $input['link_by'] = null;
            $input['course_id'] = null;
            $input['bundle_id'] = null;
            $input['meeting_id'] = null;
            $input['offline_session_id'] = null;
            $input['payment_type'] = null;
            $input['installment_id'] = null;
        } else {
            $input['minamount'] = null;

            $installment = Installment::query()
                ->when(request('payment_type') == 'installment' && request('course_id') && request('installment_number'), function ($q) {
                    $q->where(['course_id' => request('course_id'), 'sort' => request('installment_number')]);
                })
                ->when(request('payment_type') == 'installment' && request('bundle_id') && request('installment_number'), function ($q) {
                    $q->where(['bundle_id' => request('bundle_id'), 'sort' => request('installment_number')]);
                })
                ->first();

            $input['installment_id'] = $installment ? $installment->id : null;
        }

        DB::transaction(function () use ($request, $input) {
            for ($i = 1; $i <= $request->coupon_count; $i++) {
                $coupon = new Coupon();
                if ($request->code) {
                    $input['code'] = $request->code . $i;
                } else {
                    $arr = str_replace("-", "", strtoupper(Str::uuid())); // remove all "-" characters
                    $input['code'] = substr($arr, 0, 20);
                }

                $input['show_to_users'] = 0;
                $input['user_id'] = Auth::user()->id;
                $coupon->create($input);
            }
        });

        if (Auth::user()->is_abpp) {
            return redirect("coupon")->with('success', trans('flash.CreatedSuccessfully'));
        }
        return redirect("coupon")->with('success', trans('flash.CreatedSuccessfully'));
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $courses = Course::active()->where('discount_price', '<>', '0')->get();
        $bundles = BundleCourse::active()->where('discount_price', '<>', '0')->get();
        $meetings = BBL::active()->where('discount_price', '<>', '0')->get();
        $sessions = OfflineSession::active()->where('discount_price', '<>', '0')->get();
        $installment = Installment::find($coupon->installment_id);

        return view('admin.coupan.edit', compact(['coupon', 'courses', 'bundles', 'meetings', 'sessions', 'installment']));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'coupon_type' => 'required|in:general,item',
            'code' => 'required|unique:coupons,code,' . $id . '|max:25',
            'minamount' => 'nullable|required_if:coupon_type,general|numeric|min:1',
            'link_by' => 'nullable|required_if:coupon_type,item|in:course,bundle,meeting,session',
            'payment_type' => 'nullable|required_with:link_by|in:full,installment',
            'installment_number' => 'nullable|required_if:payment_type,installment',
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->distype == 'per') {
                        if (!is_int(intval($value))) {
                            $fail(__('Percentage number should be a positive integer'));
                        } elseif ($value > 100) {
                            $fail(__('Percentage number should not be greater than 100'));
                        }
                    }
                }
            ],
            'distype' => 'required|in:fix,per',
            'expirydate' => "required|date|date_format:Y-m-d|after_or_equal:" . date('Y-m-d'),
            'maxusage' => 'required|numeric|min:1',
        ], [
            'code.required' => __('Code is required'),
            'code.unique' => __('This code is already exist'),
            'code.max' => __('Code should not be more than 25 characters'),
            'distype.required' => __('Discount type is required'),
            'distype.in' => __('Discount type should be fixed amount OR in percentage'),
            "amount.required" => __("Amount is required"),
            "amount.numeric" => __("Amount must be in numeric"),
            "amount.min" => __("Amount should not be a zero"),
            "link_by.required" => __("Coupon must be linked with Live Streaming, Package or Course"),
            "link_by.in" => __("Coupon must be linked with Live Streaming, Package or Course"),
            "expirydate.required" => __("Expiry Date is required"),
            "expirydate.date_format" => __("Expiry Date format must be YYYY-MM-DD"),
            "expirydate.after_or_equal" => __("Expiry Date must be greater than or equal to today's date"),
            'maxusage.required' => __('Maximum usage is required'),
            'maxusage.numeric' => __('Maximum usage should be defined in numeric'),
            'maxusage.min' => __('Maximum usage should not be zero'),
        ]);

        $input = $request->all();
        $coupon = Coupon::find($id);

        if ($request->coupon_type == 'general') {
            $input['link_by'] = null;
            $input['course_id'] = null;
            $input['bundle_id'] = null;
            $input['meeting_id'] = null;
            $input['offline_session_id'] = null;
            $input['payment_type'] = null;
            $input['installment_id'] = null;
        } else {
            $input['minamount'] = null;

            $installment = Installment::query()
                ->when(request('payment_type') == 'installment' && request('course_id') && request('installment_number'), function ($q) {
                    $q->where(['course_id' => request('course_id'), 'sort' => request('installment_number')]);
                })
                ->when(request('payment_type') == 'installment' && request('bundle_id') && request('installment_number'), function ($q) {
                    $q->where(['bundle_id' => request('bundle_id'), 'sort' => request('installment_number')]);
                })
                ->first();

            $input['installment_id'] = $installment ? $installment->id : null;
        }

        $input['show_to_users'] = 0;

        $requestLinkId = $request->course_id ?? $request->bundle_id ?? $request->meeting_id ?? $request->offline_session_id ? $request->offline_session_id : null;
        $requestLinkBy = $request->course_id ? 'course' : ($request->bundle_id ? 'bundle' : ($request->meeting_id ? 'meeting' : ($request->offline_session_id ? 'session' : null)));

        $couponLinkId = $coupon->course_id ?? $coupon->bundle_id ?? $coupon->meeting_id ?? $coupon->offline_session_id ? $coupon->offline_session_id : null;
        $couponLinkBy = $coupon->link_by;

        if (
            $coupon->code == $input['code']
            && $coupon->coupon_type == $input['coupon_type']
            && $coupon->payment_type == $input['payment_type']
            && $couponLinkId == $requestLinkId
            && $couponLinkBy == $requestLinkBy
            && $coupon->installment_id == $input['installment_id']
        ) {
            $coupon->update($input);

            foreach ($coupon->cartCoupons as $cartCoupon) {
                // Update coupons that have been reserved in cart list
                if ($cartCoupon->cart_id) {
                    $item = $cartCoupon->cart->course_id ? Course::find($cartCoupon->cart->course_id) : ($cartCoupon->cart->bundle_id ? BundleCourse::find($cartCoupon->cart->bundle_id) : ($cartCoupon->cart->meeting_id ? BBL::find($cartCoupon->cart->meeting_id) : ($cartCoupon->cart->offline_session_id ? OfflineSession::find($cartCoupon->cart->offline_session_id) : null)));
                    $cpn = $coupon->applycoupon($item, ($coupon->course_id ? 'course' : ($coupon->bundle_id ? 'bundle' : ($coupon->meeting_id ? 'meeting' : ($coupon->offline_session_id ? 'session' : null)))), $coupon->installment_id);

                } elseif ($cartCoupon->order_payment_plan_id) {
                    $pendingInstallment = OrderPaymentPlan::find($cartCoupon->order_payment_plan_id);

                    $cpn = $coupon->applycoupon($pendingInstallment, ($coupon->course_id ? 'course' : ($coupon->bundle_id ? 'bundle' : null)), $coupon->installment_id, $pendingInstallment->id);
                }

                if ($cpn[1]) {
                    $cpn_discount = $cpn[0]['discount_amount'];
                    $distype = $cpn[0]['distype'];

                    $cartCoupon->update([
                        'disamount' => $cpn_discount,
                        'distype' => $distype,
                    ]);
                }
            }
        } else {
            CartCoupon::where('coupon_id', $coupon->id)->delete();  // Delete coupons that have been reserved in cart list
            $coupon->update($input);
        }


        return redirect('coupon')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        if ($coupon->orders->isNotEmpty()) {
            return back()->with('delete', trans('flash.CouponCannotDelete'));
        } else {
            CartCoupon::where('coupon_id', $coupon->id)->delete();  // Delete coupons that have been reserved in cart list
            $coupon->delete();
            return back()->with('success', trans('flash.DeletedSuccessfully'));
        }
    }


    public function bulk_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checked' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('warning', __('Atleast one item is required to be checked'));
        } else {
            foreach ($request->checked as $id) {
                $this->destroy($id);
            }
            return back()->with('success', trans('flash.DeletedSuccessfully'));
        }
    }


    public function course(Request $request)
    {
        if ($request->type == 'course') {
            $course = Course::select('id', 'installment')->find($request->id);
        } elseif ($request->type == 'bundle') {
            $course = BundleCourse::select('id', 'installment')->find($request->id);
        }

        return response()->json($course, 200);
    }


    /**
     * only create coupon in stripe for subscription bundle
     * dont allow edit of this coupon since stripe by design dont allow editing coupons
     */
    private function processSubscriptionCoupon($input)
    {
        if (isset($input['bundle_id'])) {
            $bundle = BundleCourse::where('id', $input['bundle_id'])->first();
            Log::debug('checking for coupon');

            if ($bundle->is_subscription_enabled && isset($bundle->product_id)) {
                Log::debug('going to create coupon in stripe');
                return $this->createCouponInStripe($input, $bundle);
            }
        }

        return $input;
    }



    private function createCouponInStripe($input, $bundle)
    {
        $stripe = $this->getStripe();

        $couponCreateArgs = [];

        $couponCreateArgs['name'] = $input['code'];

        if ($input['distype'] == 'per') {
            $couponCreateArgs['percent_off'] = $input['amount'];
        } else {
            $currency = Currency::where('default', '=', '1')->first();
            $couponCreateArgs['currency'] = $currency->code;
            $couponCreateArgs['amount_off'] = $input['amount'] * 100;
        }

        $couponCreateArgs['applies_to'] = ['products' => [$bundle->product_id]];

        $couponCreateArgs['duration'] = 'forever';

        $couponCreateArgs['max_redemptions'] = $input['maxusage'];

        $couponCreateArgs['redeem_by'] = \Carbon\Carbon::createFromDate($input['expirydate'])->timestamp;

        Log::debug('creating coupon in stripe for subscription course: ' . print_r($couponCreateArgs, true));

        $coupon = $stripe->coupons->create($couponCreateArgs);
        Log::debug('stripe coupon created: ' . $coupon['id']);

        $input['stripe_coupon_id'] = $coupon['id'];

        return $input;
    }


    private function getStripe()
    {
        return new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }


    /**
     * If bundle with subscription then delete coupon in stripe
     */
    private function deleteCouponInStripe($coupon)
    {
        if (!isset($coupon->stripe_coupon_id)) {
            return;
        }

        $this->getStripe()->coupons->delete($coupon->stripe_coupon_id);

        Log::debug('Stripe coupon deleted: ' . $coupon->stripe_coupon_id);
    }
}
