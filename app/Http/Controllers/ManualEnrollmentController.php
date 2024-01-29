<?php

namespace App\Http\Controllers;

use App\BBL;
use App\Cart;
use App\User;
use App\Order;
use App\Course;
use App\Setting;
use App\Currency;
use App\Wishlist;
use App\CourseClass;
use App\Installment;
use App\BundleCourse;
use App\CourseChapter;
use App\CourseProgress;
use App\OfflineSession;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\SessionEnrollment;
use App\WalletTransactions;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Notifications\UserEnroll;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class ManualEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->select('orders.id', 'title', 'orders.user_id', 'orders.instructor_id', 'orders.course_id', 'orders.chapter_id', 'orders.bundle_id', 'orders.meeting_id', 'orders.offline_session_id', 'orders.installments', 'orders.transaction_id', 'orders.total_amount', 'orders.paid_amount', 'orders.enroll_start', 'orders.enroll_expire', 'orders.created_at', 'orders.currency_icon', 'orders.coupon_id', 'orders.coupon_discount', 'orders.status')
            ->allActiveInactiveOrder()
            ->whereHas('user', function ($q) {
                $q->exceptTestUser();
            })
            ->with('user:id,fname,lname,email,mobile')
            ->with('instructor:id,fname,lname')
            ->with('transaction:id,payment_method,transaction_id,created_at')
            ->with('payment_plan:id,order_id,due_date,installment_no,payment_date,amount,status');

        if ($request->ajax()) {
            return Datatables::eloquent($orders)
                ->addIndexColumn()
                ->editColumn('student_detail', 'admin.invoice.datatables.student_detail')
                ->editColumn('order_detail', 'admin.invoice.datatables.order_detail')
                ->editColumn('payment_detail', 'admin.invoice.datatables.payment_detail')
                ->editColumn('status', 'admin.manual_enrollment.datatables.status')
                ->editColumn('action', 'admin.manual_enrollment.datatables.action')

                ->rawColumns(['student_detail', 'order_detail', 'payment_detail', 'status', 'action'])
                ->toJson();
        }

        return view('admin.manual_enrollment.index', compact(['orders']));
    }


    public function create()
    {
        $testUser = null;
        if (request()->test_user) {
            $testUser = User::where('test_user', 1)->findOrFail(request()->test_user);
        }

        $currency = Currency::where('default', 1)->first();

        $users = User::query()
            ->select('id', 'fname', 'lname', 'mobile')
            ->where('role', 'user')
            ->active()
            ->exceptTestUser()
            ->latest('id')
            ->get();

        return view('admin.manual_enrollment.add', compact('currency', 'users', 'testUser'));
    }


    public function orderItems(Request $request)
    {
        if ($request->type === 'course') {
            $items = Course::query()
                ->select('id', 'title', 'installment', 'total_installments', 'price', 'discount_price', 'discount_type')
                ->whereDoesntHave('order', function ($query) {
                    $query->where('user_id', request()->user_id);
                })
                ->active()
                ->latest('id')
                ->get();
        } elseif ($request->type === 'package') {
            $items = BundleCourse::query()
                ->select('id', 'title', 'installment', 'total_installments', 'price', 'discount_price', 'discount_type')
                ->whereDoesntHave('order', function ($query) {
                    $query->where('user_id', request()->user_id);
                })
                ->active()
                ->latest('id')
                ->get();
        } elseif ($request->type === 'live-streaming') {
            $items = BBL::query()
                ->select('id', 'meetingname', 'price', 'discount_price', 'discount_type')
                ->whereDoesntHave('orders', function ($query) {
                    $query->where('user_id', request()->user_id);
                })
                ->active()
                ->latest('id')
                ->get();
        } elseif ($request->type === 'in-person-session') {
            $items = OfflineSession::query()
                ->select('id', 'title', 'price', 'discount_price')
                ->whereDoesntHave('orders', function ($query) {
                    $query->where('user_id', request()->user_id);
                })
                ->active()
                ->latest('id')
                ->get();
        }

        return response()->json($items, 200);
    }

    public function courseChapters(Request $request)
    {
        $chapters = CourseChapter::where(['course_id' => $request->course_id, 'status' => 1])->whereNull(['type', 'type_id'])->get();

        return response()->json($chapters, 200);
    }


    public function installments(Request $request)
    {
        if ($request->type == 'course') {
            $course = Course::find($request->type_id);
            $installments = $course->installments()->get();
        } elseif ($request->type == 'package') {
            $bundle = BundleCourse::find($request->type_id);
            $installments = $bundle->installments()->get();
        }
        if (isset($installments)) {
            $resp = [];

            foreach ($installments as $installment) {
                if ($installment->due_date < date('Y-m-d')) {
                    $expire = true;
                } else {
                    $expire = false;
                }

                $resp[] = [
                    'id' => $installment->id,
                    'amount' => $installment->amount,
                    'due_date' => $installment->due_date,
                    'expire' => $expire,
                ];
            }
        }

        return response()->json($resp, 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('role', 'user')
                        ->where('status', 1)
                        ->whereNull('deleted_at');
                })
            ],
            'type' => 'required',
            'course_id' => [
                'required_if:type,course',
                Rule::exists('courses', 'id')->where('status', 1),
                Rule::unique('orders')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id)
                        ->where('status', '<>', 0)
                        ->whereNull('deleted_at');
                })
            ],
            'chapter_id' => [
                'nullable',
                Rule::exists('course_chapters', 'id')->where('status', 1),
                Rule::unique('orders')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id)
                        ->where('status', '<>', 0)
                        ->whereNull('deleted_at');
                })
            ],
            'bundle_id' => [
                'required_if:type,package',
                Rule::exists('bundle_courses', 'id')->where('status', 1),
                Rule::unique('orders')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id)
                        ->where('status', '<>', 0)
                        ->whereNull('deleted_at');
                })
            ],
            'meeting_id' => [
                'required_if:type,live-streaming',
                Rule::exists('bigbluemeetings', 'id'),
                Rule::unique('orders')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id)
                        ->where('status', '<>', 0)
                        ->whereNull('deleted_at');
                })
            ],
            'offline_session_id' => [
                'required_if:type,in-person-session',
                Rule::exists('offline_sessions', 'id'),
                Rule::unique('orders')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id)
                        ->where('status', '<>', 0)
                        ->whereNull('deleted_at');
                })
            ],
            'payment_type' => 'nullable|in:installments,full',
            'installment1' => 'required_if:payment_type,installments',
        ], [
            "user_id.required" => __("Student name is required"),
            "user_id.exists" => __("Student does not exist OR may have been deleted"),
            "type.required" => __("Order type is required"),
            "course_id.required_if" => __("Course must be selected"),
            "course_id.exists" => __("Course does not exist OR may be disabled"),
            "course_id.unique" => __("User already enrolled in this course"),
            "chapter_id.exists" => __("Chapter does not exist OR may be disabled"),
            "chapter_id.unique" => __("User already enrolled in this chapter"),
            "bundle_id.required_if" => __("Package must be selected"),
            "bundle_id.exists" => __("Package does not exist OR may be disabled"),
            "bundle_id.unique" => __("User already enrolled in this package"),
            "meeting_id.required_if" => __("Live Streaming must be selected"),
            "meeting_id.exists" => __("Live streaming does not exist OR may be disabled"),
            "meeting_id.unique" => __("User already enrolled in this live streaming"),
            "offline_session_id.required_if" => __("In-Person Session must be selected"),
            "offline_session_id.exists" => __("In-person session does not exist OR may be disabled"),
            "offline_session_id.unique" => __("User already enrolled in this In-person session"),
            "payment_type.in" => __("Payment type should be Full OR Installment"),
            "installment.required_if" => __("Atleast one installment should be selected"),
        ]);

        $auth = User::find($request->user_id);

        if ($request->course_id) {
            if (Order::where('user_id', $request->user_id)->whereJsonContains('bundle_course_id', strval($request->course_id))->allActiveInactiveOrder()->exists()) {
                throw ValidationException::withMessages(['course_id' => 'User already enrolled in this course package']);
            } elseif ($request->chapter_id) {
                $courseChapter = CourseChapter::find($request->chapter_id);

                if (Order::where('user_id', $request->user_id)->where('course_id', $courseChapter->course_id)->allActiveInactiveOrder()->exists()) {
                    throw ValidationException::withMessages(['course_id' => 'User already enrolled in this course']);
                }

                if (Order::where('user_id', $request->user_id)->whereJsonContains('bundle_course_id', strval($courseChapter->course_id))->allActiveInactiveOrder()->exists()) {
                    throw ValidationException::withMessages(['course_id' => 'User already enrolled this course package']);
                }
            }
        } elseif ($request->meeting_id) {
            $meetings = BBL::where('id', $request->meeting_id)->whereColumn('order_count', '<', 'setMaxParticipants')->get();

            if ($meetings->isEmpty()) {
                throw ValidationException::withMessages(['meeting_id' => 'Live streaming seats not available anymore']);

                return response()->json(['errors' => ['message' => ['Live streaming seats not available anymore']]], 422);
            }
        } elseif ($request->offline_session_id) {
            $sessions = OfflineSession::where('id', $request->offline_session_id)->whereColumn('order_count', '<', 'setMaxParticipants')->get();

            if ($sessions->isEmpty()) {
                throw ValidationException::withMessages(['offline_session_id' => 'In-person session seats not available anymore']);
            }
        }

        //Get Order item i.e. course, bundle, meeting OR session
        $order_item = $request->chapter_id ? CourseChapter::find($request->chapter_id) : ($request->course_id ? Course::find($request->course_id) : ($request->bundle_id ? BundleCourse::find($request->bundle_id) : ($request->meeting_id ? BBL::find($request->meeting_id) : ($request->offline_session_id ? OfflineSession::find($request->offline_session_id) : null))));

        if ($request->payment_type === 'installments' && $order_item->installment == 0) {
            throw ValidationException::withMessages(['payment_type' => 'Installments have been removed']);
        }

        if ($request->installment3 && !$request->installment2) {
            throw ValidationException::withMessages(['installment2' => 'Pay pending installment first, please']);
        }

        $created_order = null;
        $payment_method = 'Manual Enrollment';
        $pay_amount = 0;

        $currency = Currency::where('default', 1)->first();

        //Get user cart for that item purchased
        $carts = Cart::where('user_id', $auth->id)->get();
        $user_cart = $request->chapter_id ? $carts->where('chapter_id', $request->chapter_id) : ($request->type === 'course' ? $carts->where('course_id', $request->course_id) : ($request->type === 'package' ? $carts->where('bundle_id', $request->bundle_id) : ($request->type === 'live-streaming' ? $carts->where('meeting_id', $request->meeting_id) : ($request->type === 'in-person-session' ? $carts->where('offline_session_id', $request->offline_session_id) : collect()))));

        //Calculate paid amount in terms of full payment OR installments
        if ($request->payment_type === 'installments' && $request->installment1 && isset($order_item->installments)) {
            $pay_amount = $order_item->installments[0]->amount;
            if ($request->installment2) {
                $pay_amount += $order_item->installments[1]->amount;
            }
            if ($request->installment3) {
                $pay_amount += $order_item->installments[2]->amount;
            }
        } elseif ($request->payment_type === 'full') {
            $pay_amount = $order_item->discount_price;
        }

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
            'detail' => Str::headline($request->type) . ' purchased',
        ]);

        $total = 0;
        if ($order_item->discount_type && $order_item->discount_type == 'fixed') {
            $total = $order_item->price - $order_item->discount_price;
        } elseif ($order_item->discount_type && $order_item->discount_type == 'percentage') {
            $total = $order_item->price - (($order_item->discount_price / 100) * $order_item->price);
        } else {
            $total = $order_item->discount_price;
        }

        $created_order = Order::create([
            'title' => $order_item->_title(),
            'price' => $order_item->price,
            'discount_price' => $order_item->discount_price,
            'discount_type' => $order_item->discount_type ?? null,
            'user_id' => $auth->id,
            'instructor_id' => $order_item->_instructor(),
            'course_id' => $request->chapter_id ? null : $request->course_id ?? null,
            'chapter_id' => $request->chapter_id ?? null,
            'bundle_id' => $request->bundle_id ?? null,
            'meeting_id' => $request->meeting_id ?? null,
            'bundle_course_id' => $request->bundle_id ? $order_item->course_id : null,
            'offline_session_id' => $request->offline_session_id ?? null,
            'order_id' => '#' . sprintf("%08d", intval($number) + 1),
            'transaction_id' => $wallet_transaction->id,
            'payment_method' => $payment_method,
            'total_amount' => $request->payment_type == 'installments' ? $order_item->installments->sum('amount') : $total,
            'paid_amount' => $pay_amount,
            'installments' => $request->payment_type === 'full' ? 0 : 1,
            'total_installments' => $request->payment_type === 'installments' ? $order_item->total_installments : null,
            'coupon_discount' => 0,
            'coupon_id' => null,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            // 'duration' => $duration,
            'enroll_start' => $order_item->_enrollstart(),
            'enroll_expire' => $order_item->_enrollexpire(),
            // 'instructor_revenue' => $instructor_payout,
            'status' => 1,
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
                'status' => 1,
            ]);
        } elseif ($created_order->offline_session_id) {
            Wishlist::where(['offline_session_id' => $created_order->offline_session_id, 'user_id' => $auth->id])->delete();
            OfflineSession::find($created_order->offline_session_id)->increment('order_count'); // Increment numbers of participants has been enrolled after successfull order

            // Session Enrollment
            SessionEnrollment::create([
                'meeting_id' => null,
                'offline_session_id' => $created_order->offline_session_id,
                'user_id' => $created_order->user_id,
                'status' => 1,
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
                        'status' => 1
                    ]
                );
            }
        }

        if ($order_item->installments && ($request->payment_type === 'installments')) {
            $count = 0;
            if ($request->installment1) {
                $count = $request->installment2 ? ($request->installment3 ? 3 : 2) : 1;
            }

            foreach ($order_item->installments as $i => $inst) {
                if ($i < $count) {
                    $orderInstallment = OrderInstallment::create([
                        'order_id' => $created_order->id,
                        'user_id' => $auth->id,
                        'transaction_id' => $wallet_transaction->id,
                        'payment_method' => $payment_method,
                        'total_amount' => $inst->amount,
                        'coupon_discount' => 0,
                        'coupon_id' => null,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
                    ]);
                } else {
                    $orderInstallment = null;
                }

                OrderPaymentPlan::create([
                    'order_id' => $created_order->id,
                    'order_installment_id' => $orderInstallment ? $orderInstallment->id : null,
                    'wallet_trans_id' => $orderInstallment ? $wallet_transaction->id : null,
                    'created_by' => $auth->id,
                    'amount' => $inst->amount,
                    'due_date' => $inst->due_date,
                    'installment_no' => $inst->sort,
                    'payment_date' => $orderInstallment ? now() : null,
                    'status' => $orderInstallment ? 'Paid' : null,
                ]);
            }
        }

        if (env('ONE_SIGNAL_NOTIFICATION_ENABLED') == 1) {
            if ($created_order) {
                // Notification when user enroll
                if (count($auth->device_tokens) > 0 && $auth->notifications) {
                    Notification::send($auth, new UserEnroll($created_order));
                }
            }
        }

        //Delete user carts
        if ($user_cart->isNotEmpty()) {
            $user_cart->each->delete();
        }

        return back()->with('success', __('User Enrolled successfully'));
    }


    public function view($id)
    {
        $setting = Setting::first();
        $show = Order::where('installments', 0)->allActiveInactiveOrder()->findOrFail($id);

        $bundle_order = BundleCourse::where('id', $show->bundle_id)->first();
        return view('admin.manual_enrollment.view', compact('show', 'setting', 'bundle_order'));
    }


    public function pendingInstallments($id)
    {
        $setting = Setting::first();
        $show = Order::where('installments', 1)->activeOrder()->findOrFail($id);
        $paidInstallments = OrderPaymentPlan::where('order_id', $id)->where('status', '<>', null)->get();
        $pendingInstallments = OrderPaymentPlan::where('order_id', $id)->where('status', null)->get();
        $bundle_order = BundleCourse::find($show->bundle_id);

        return view('admin.manual_enrollment.installments', compact('show', 'setting', 'paidInstallments', 'pendingInstallments', 'bundle_order'));
    }


    public function payInstallment($id)
    {
        $inst = OrderPaymentPlan::whereNull('status')->find($id);

        if (!$inst) {
            throw ValidationException::withMessages(['id' => 'Installment does not exist OR may have been already paid']);
        } elseif (($inst->pendingInstallments && $inst->pendingInstallments->count())) {
            throw ValidationException::withMessages(['id' => 'Pay the previous pending installment first, please']);
            // return back()->withErrors('Pay Pending installment first please');
        }

        $payment_method = 'Manual Enrollment';
        $currency = Currency::where('default', '=', 1)->first();

        $order = Order::find($inst->order_id);
        $user = User::find($order->user_id);

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
            'payment_method' => $payment_method,
            'total_amount' => $inst->amount,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'type' => 'Debit',
            'detail' => __('Installment Paid'),
        ]);

        $orderInstallment = OrderInstallment::create([
            'order_id' => $inst->order_id,
            'user_id' => $user->id,
            'transaction_id' => $wallet_transaction->id,
            'payment_method' => $payment_method,
            'total_amount' => $inst->amount,
            'coupon_discount' => 0,
            'coupon_id' => null,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
        ]);

        $inst->order_installment_id = $orderInstallment->id;
        $inst->payment_date = now();
        $inst->wallet_trans_id = $wallet_transaction->id;
        $inst->status = 'Paid';
        $inst->save();

        $paid = OrderInstallment::where('order_id', $inst->order_id)->get();
        Order::where('id', $inst->order_id)->update([
            'paid_amount' => $paid->sum('total_amount'),
            'coupon_discount' => $paid->sum('coupon_discount'),
            'status' => 1,
        ]);

        return back()->with('success', __("Installment Paid successfully"));
    }


    public function enrollmentStatus(Request $request)
    {
        $order = Order::findOrFail($request->id);

        if ($order->course_id) {
            CourseProgress::where(['user_id' => $order->user_id, 'course_id' => $order->course_id])->update([
                'status' => ($request->status === '1') ? '1' : '2' // Status = 2 means Inactive
            ]);
        } elseif ($order->chapter_id) {
            CourseProgress::where(['user_id' => $order->user_id, 'course_id' => $order->chapter->course_id])->update([
                'status' => ($request->status === '1') ? '1' : '2' // Status = 2 means Inactive
            ]);
        } elseif ($order->bundle_id) {
            CourseProgress::where('user_id', $order->user_id)->whereIn('course_id', $order->bundle_course_id)->update([
                'status' => ($request->status === '1') ? '1' : '2' // Status = 2 means Inactive
            ]);
        } elseif ($order->meeting_id) {
            SessionEnrollment::where('user_id', $order->user_id)->where('meeting_id', $order->meeting_id)->update([
                'status' => ($request->status === '1') ? '1' : '2' // Status = 2 means Inactive
            ]);
        } elseif ($order->offline_session_id) {
            SessionEnrollment::where('user_id', $order->user_id)->where('offline_session_id', $order->offline_session_id)->update([
                'status' => ($request->status === '1') ? '1' : '2' // Status = 2 means Inactive`
            ]);
        }

        $order->status = ($request->status === '1') ? '1' : '2';    // Status = 2 means Inactive
        $order->timestamps = false;
        $order->save();

        return response()->json(['success' => 'Status Updated Successfully']);
    }


    public function enrollmentDestroy($id)
    {
        $order = Order::findOrFail($id);

        if ($order->course_id) {
            foreach ($order->courses->chapter as $chapter) {
                if ($chapter->type == 'live-streaming') {
                    SessionEnrollment::where('user_id', $order->user_id)->where('meeting_id', $chapter->type_id)->delete();
                } elseif ($chapter->type == 'in-person-session') {
                    SessionEnrollment::where('user_id', $order->user_id)->where('offline_session_id', $chapter->type_id)->delete();
                }
            }
            CourseProgress::where(['user_id' => $order->user_id, 'course_id' => $order->course_id])->delete();
        } elseif ($order->chapter_id) {
            CourseProgress::where(['user_id' => $order->user_id, 'course_id' => $order->chapter->course_id])->delete();
        } elseif ($order->bundle_id) {
            CourseProgress::where('user_id', $order->user_id)->whereIn('course_id', $order->bundle_course_id)->delete();
        } elseif ($order->meeting_id) {
            SessionEnrollment::where('user_id', $order->user_id)->where('meeting_id', $order->meeting_id)->delete();
            BBL::where('id', $order->meeting_id)->decrement('order_count');
        } elseif ($order->offline_session_id) {
            SessionEnrollment::where('user_id', $order->user_id)->where('offline_session_id', $order->offline_session_id)->delete();
            OfflineSession::where('id', $order->offline_session_id)->decrement('order_count');
        }

        $order->delete();

        return back()->with('success', __('Enrollment Deleted Successfully'));
    }
}
