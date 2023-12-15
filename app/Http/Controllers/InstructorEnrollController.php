<?php

namespace App\Http\Controllers;

use App\Order;
use App\Course;
use App\Setting;
use App\BundleCourse;
use App\OrderPaymentPlan;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class InstructorEnrollController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('permission:orders.manage', ['only' => ['index']]);
      
    }


    public function fullpayment()
    {
        $fullPayment = Order::query()
                            ->select('orders.id','title','orders.user_id','orders.instructor_id','orders.course_id','orders.chapter_id','orders.bundle_id','orders.meeting_id','orders.offline_session_id','orders.installments','orders.transaction_id','orders.total_amount','orders.paid_amount','orders.enroll_start','orders.enroll_expire','orders.created_at', 'orders.currency_icon', 'orders.coupon_id', 'orders.coupon_discount')
                            ->allActiveInactiveOrder()
                            ->where('instructor_id', Auth::id())
                            ->where('installments', '0')
                            ->whereHas('user', function($q){
                                $q->exceptTestuser();
                            })
                            ->with('user:id,fname,lname,email,mobile')
                            ->with('instructor:id,fname,lname')
                            ->with('transaction:id,payment_method,transaction_id,created_at')
                            ->with('payment_plan:id,order_id,due_date,installment_no,payment_date,amount,status')
                            ->latest('id')
                            ->get();

        return view('instructor.enroll.full_payment.index', compact('fullPayment'));
    }

    
    public function payInInstallment(){

        $payInInstallments = Order::query()
                                    ->select('id','title','user_id','instructor_id','course_id','chapter_id','bundle_id','meeting_id','offline_session_id','transaction_id','currency_icon')
                                    ->where('installments', '1')
                                    ->where('instructor_id', Auth::id())
                                    ->allActiveInactiveOrder()
                                    ->whereHas('user', function($q) {
                                        $q->where('test_user', '0');
                                    })
                                    ->whereHas('payment_plan')
                                    ->with('user:id,fname,lname,email,mobile')
                                    ->with('instructor:id,fname,lname')
                                    ->with('payment_plan:id,order_id,due_date,payment_date,amount,status')
                                    ->latest('id')
                                    ->get();

        return view('instructor.enroll.installment.index', compact('payInInstallments'));
    }


    public function viewFullPayment($id)
    {
        $show = Order::where('instructor_id', Auth::id())->where('installments','0')->allActiveInactiveOrder()->findOrFail($id);
        $bundleOrder = BundleCourse::where('id', $show->bundle_id)->first();
        $setting = Setting::first();

        return view('instructor.enroll.full_payment.view', compact('show', 'setting', 'bundleOrder'));
    }


    public function viewInstallment($id)
    {
        $payInInstallment = Order::query()
                                ->select('id','title','user_id','instructor_id','course_id','chapter_id','bundle_id','meeting_id','offline_session_id','transaction_id','currency_icon')
                                ->where('instructor_id', Auth::id())
                                ->where('installments', '1')
                                ->allActiveInactiveOrder()
                                ->whereHas('payment_plan')
                                ->whereHas('user', function($q) {
                                    $q->where('test_user', '0');
                                })
                                ->with('user:id,fname,lname,email,mobile')
                                ->with('instructor:id,fname,lname')
                                ->with('payment_plan:id,order_id,due_date,payment_date,amount,status')
                                ->findOrFail($id);

        $bundleOrder = BundleCourse::find($payInInstallment->bundle_id);
        $setting = Setting::first();

        return view('instructor.enroll.installment.view', compact('payInInstallment', 'bundleOrder', 'setting' ));
    }

}
