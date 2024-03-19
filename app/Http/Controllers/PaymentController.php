<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use App\Cart;
use App\User;
use App\Order;
use TwilioMsg;
use Validator;
use App\Coupon;
use App\Course;
use App\Setting;
use App\Currency;
use App\Wishlist;
use Notification;
use Carbon\Carbon;
use App\BankTransfer;
use App\BundleCourse;
use App\PendingPayout;
use PayPal\Api\Payout;
use App\Mail\GiftOrder;
use App\CompletedPayout;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\InstructorSetting;
use PayPal\Api\PayoutItem;
use App\Mail\SendOrderMail;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use App\Mail\AdminMailOnOrder;
use App\Exports\InvoicesExport;
use App\Notifications\AdminOrder;
use App\Notifications\UserEnroll;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PayoutSenderBatchHeader;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    private $_api_context;


    public function __construct()
    {
		$this->middleware('permission:orders.manage', ['only' => ['invoices','instructorOrderType','exportInvoice', 'fullPayment', 'allInstallments', 'viewInvoice', 'installmentInvoice']]);

      /** PayPal api context **/
      $paypal_conf = \Config::get('paypal');
      $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
      $this->_api_context->setConfig($paypal_conf['settings']);
    }
    
    
    public function invoices(Request $request){
        
        $startDate = $request->minDate ? Carbon::createFromFormat('Y-m-d', $request->minDate)->startOfDay() : now()->firstOfMonth()->format('Y-m-d H:i:s');
        $endDate = $request->maxDate ? Carbon::createFromFormat('Y-m-d', $request->maxDate)->endOfDay() : date('Y-m-d H:i:s'); 
        
        if ($request->ajax()) {
            
            $orders = Order::query()
                            ->select('orders.id','title','orders.user_id','orders.instructor_id','orders.course_id','orders.chapter_id','orders.bundle_id','orders.meeting_id','orders.offline_session_id','orders.installments','orders.transaction_id','orders.total_amount','orders.paid_amount','orders.enroll_start','orders.enroll_expire','orders.created_at', 'orders.currency_icon', 'orders.coupon_id', 'orders.coupon_discount')
                            ->allActiveInactiveOrder()
                            ->whereHas('user', function($q){
                                $q->where('users.test_user', '0');
                            })
                            ->with('user:id,fname,lname,email,mobile')
                            ->with('instructor:id,fname,lname')
                            ->with('transaction:id,payment_method,transaction_id,created_at')
                            ->with('payment_plan:id,order_id,due_date,installment_no,payment_date,amount,status')
                            ->where(DB::raw("case when `orders`.installments != 0 then exists (select * from order_payment_plan as pp where `orders`.id = pp.order_id and ((pp.payment_date between '$startDate' and '$endDate') or (pp.due_date between date('$startDate') and date('$endDate'))) )
                                    else `orders`.created_at between '$startDate' and '$endDate' end")
                                    , DB::raw('1'));

                                    // $addSlashes = str_replace('?', "'?'", $orders->toSql());
                                    // dd(vsprintf(str_replace('?', '%s', $addSlashes), $orders->getBindings()));
                
            return Datatables::eloquent($orders)
                ->addIndexColumn()
                ->filter( function($query) use ($request, $startDate, $endDate) {
                    if ($request->get('instructor_id') <> NULL) {
                        $query->where('instructor_id', $request->instructor_id);
                    }
                    if ($request->get('type') <> NULL) {
                        $query->whereNotNull($request->type);
                    }
                    if ($request->get('type') <> NULL && $request->get('type_ids') <> NULL) {
                        if($request->type == 'course_id'){
                            $query->whereIn('orders.course_id', $request->type_ids);
                        }
                        elseif($request->type == 'chapter_id'){
                            $query->whereIn('orders.chapter_id', $request->type_ids);
                        }
                        elseif($request->type == 'bundle_id'){
                            $query->whereIn('orders.bundle_id', $request->type_ids);
                        }
                        elseif($request->type == 'meeting_id'){
                            $query->whereIn('orders.meeting_id', $request->type_ids);
                        }
                        elseif($request->type == 'offline_session_id'){
                            $query->whereIn('orders.offline_session_id', $request->type_ids);
                        }
                    }


                    if ($request->get('installment_no') == NULL && $request->get('status') == 'paid') {

                        $query->whereRaw('orders.total_amount = (orders.paid_amount+orders.coupon_discount)')
                                ->where(DB::raw("case when `orders`.installments != 0 then exists (select * from order_payment_plan as pp where `orders`.id = pp.order_id and ((pp.payment_date between '$startDate' and '$endDate')) )
                                            else `orders`.created_at between '$startDate' and '$endDate' end")
                                            , DB::raw('1'));
                    }
                    elseif ($request->get('installment_no') == NULL && $request->get('status') == 'unpaid') {
                        $query->whereRaw('orders.total_amount <> (orders.paid_amount+orders.coupon_discount)')
                                ->whereHas('payment_plan', function($q) use($startDate, $endDate) {
                                    $q->whereNull('payment_date')->whereBetween('due_date', [$startDate, $endDate]);
                                });
                    }


                    if ($request->get('installments') == '1' && $request->get('installment_no') <> NULL && $request->get('status') == NULL) {
                        $query->whereHas('payment_plan', function($q) use($request, $startDate, $endDate) {
                            $q->where('installment_no', $request->installment_no)
                                ->where( function($query) use($startDate, $endDate) {
                                    $query->whereBetween('due_date', [$startDate, $endDate])
                                        ->orWhereBetween('payment_date', [$startDate, $endDate]);
                                });
                        });
                    }


                    if ($request->get('installments') == '1' && $request->get('installment_no') == NULL && $request->get('status') == 'unpaid') {
                        $query->whereHas('payment_plan', function($q) {
                            $q->where('installment_no', '1')->whereNull('payment_date');
                        });
                    }
                    elseif ($request->get('installments') == '1' && $request->get('installment_no') <> NULL && $request->get('status') == 'unpaid') {
                        $query->whereHas('payment_plan', function($q) use($request, $startDate, $endDate) {
                            $q->where('installment_no', $request->installment_no)->whereNull('payment_date')->whereBetween('due_date', [$startDate, $endDate]);
                        });
                    }


                    if ($request->get('installments') == '1' && $request->get('installment_no') == NULL && $request->get('status') == 'paid') {
                        $query->whereHas('payment_plan', function($q) use($startDate, $endDate) {
                            $q->whereBetween('payment_date', [$startDate, $endDate]);
                        });
                    }
                    elseif ($request->get('installments') == '1' && $request->get('installment_no') <> NULL && $request->get('status') == 'paid') {
                        $query->whereHas('payment_plan', function($q) use($request, $startDate, $endDate) {
                            $q->where('installment_no', $request->installment_no)->whereBetween('payment_date', [$startDate, $endDate]);
                        });
                    }

                })

                ->editColumn('student_detail', 'admin.invoice.datatables.student_detail')
                ->editColumn('order_detail', 'admin.invoice.datatables.order_detail')
                ->editColumn('payment_detail', 'admin.invoice.datatables.payment_detail')
                ->editColumn('action', 'admin.invoice.datatables.action')

                ->rawColumns(['student_detail', 'order_detail', 'payment_detail', 'action'])
                ->toJson();
        }

        $instructors = Order::query()
                    ->allActiveInactiveOrder()
                    ->whereHas('user', function($q){
                        $q->where('test_user', '0');
                    })
                    ->with('instructor:id,fname,lname')
                    ->distinct()
                    ->get(['instructor_id']);
                    
        $data = [
            'from_date' => $startDate,
            'to_date' => $endDate,
            'instructors' => $instructors,
        ];

        return view('admin.invoice.index', compact('data')); 
    }


    public function instructorOrderType(Request $request){

        $orders = Order::select("id","title","course_id","chapter_id", "bundle_id", "meeting_id","offline_session_id","$request->type as type_id")
                    ->where('instructor_id', $request->instructor_id)
                    ->allActiveInactiveOrder()
                    ->whereHas('user', function($q){
                        $q->where('test_user', '0');
                    })
                    ->whereNotNull($request->type)
                    ->with('instructor')
                    ->get()
                    ->unique($request->type);

        return response()->json($orders, 200);
    }


    public function exportInvoice(Request $request){

        $request = $request->except('_token');

        $request['from_date'] = Carbon::createFromFormat('Y-m-d', $request['from_date'])->startOfDay();
        $request['to_date'] = Carbon::createFromFormat('Y-m-d', $request['to_date'])->endOfDay();

        if (ob_get_contents()) ob_end_clean();
        ob_start();

        return (new InvoicesExport($request))->download();
    }


    public function fullPayment(Request $request){
        
        $fullPayments = Order::query()
                            ->select('orders.id','orders.title','orders.user_id','orders.instructor_id','orders.course_id','orders.chapter_id','orders.bundle_id','orders.meeting_id','orders.offline_session_id','orders.transaction_id','orders.currency_icon','orders.total_amount','orders.paid_amount','orders.coupon_id','orders.coupon_discount')
                            // ->select('orders.*')
                            ->where('orders.installments', 0)
                            ->allActiveInactiveOrder()
                            ->whereHas('user', function ($q) {
                                $q->exceptTestUser();
                            })
                            ->with('user:id,fname,lname,email,mobile')
                            ->with('instructor:id,fname,lname')
                            ->with('transaction:id,payment_method,transaction_id')
                            ->latest('orders.id');

            if ($request->ajax()) {
                return DataTables::eloquent($fullPayments)

                    ->addIndexColumn()
                    ->editColumn('student_detail', 'admin.order.full-payment.datatables.student_detail')
                    ->editColumn('payment_detail', 'admin.order.full-payment.datatables.payment_detail')
                    ->editColumn('action', 'admin.order.full-payment.datatables.action')
                    
                    ->rawColumns(['student_detail', 'payment_detail', 'action'])
                    ->toJson();
            }

        return view('admin.order.full-payment.index');
    }


    public function allInstallments(Request $request)
    {
        $paidInstallments = Order::query()
                                    ->select('orders.id','orders.title','orders.user_id','orders.instructor_id','orders.course_id','orders.chapter_id','orders.bundle_id','orders.meeting_id','orders.offline_session_id','orders.transaction_id','orders.currency_icon')
                                    // ->select('orders.*')
                                    ->where('orders.installments', 1)
                                    ->allActiveInactiveOrder()
                                    ->whereHas('payment_plan')
                                    ->whereHas('user', function ($q) {
                                        $q->exceptTestUser();
                                    })
                                    ->with('user:id,fname,lname,email,mobile')
                                    ->with('instructor:id,fname,lname')
                                    ->with('payment_plan:id,order_id,due_date,payment_date,amount,status')
                                    ->latest('orders.id');

        if ($request->ajax()) {
            return DataTables::eloquent($paidInstallments)

                ->addIndexColumn()
                ->editColumn('student_detail', 'admin.order.paid-installment.datatables.student_detail')
                ->editColumn('payment_detail', 'admin.order.paid-installment.datatables.payment_detail')
                ->editColumn('action', 'admin.order.paid-installment.datatables.action')
                
                ->rawColumns(['student_detail', 'payment_detail', 'action'])
                ->toJson();
        }

        return view('admin.order.paid-installment.index');
    }
    

    public function pendingInstallments(Request $request){
        
        $pending_installments = \App\OrderPaymentPlan::whereNull('status')->get();

        return view('admin.order.pending-installment', compact('pending_installments'));
    }


    public function viewInvoice($id)
    {
        $setting = Setting::first();
        $show = Order::where('installments','0')->allActiveInactiveOrder()->findOrFail($id);

        $bundle_order = BundleCourse::where('id', $show->bundle_id)->first();
        return view('admin.order.full-payment.view', compact('show', 'setting', 'bundle_order'));
    }


    public function installmentInvoice($id)
    {
        $setting = Setting::first();
        $show = Order::where('installments','1')->allActiveInactiveOrder()->findOrFail($id);
        $paidInstallments = OrderPaymentPlan::where('order_id', $id)->where('status', '<>', NULL)->get();
        $pendingInstallments = OrderPaymentPlan::where('order_id', $id)->where('status', NULL)->get();
        $bundle_order = BundleCourse::find($show->bundle_id);

        return view('admin.order.paid-installment.view', compact('show', 'setting', 'bundle_order', 'paidInstallments', 'pendingInstallments'));
    }


    public function paystore(Request $request){

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

        $auth = Auth::user();

        $currency = Currency::where('default', '=', '1')->first();
            
        $carts = Cart::where('user_id', $auth->id)->get();

        if($file = $request->file('proof'))
        {
            $name = time().$file->getClientOriginalName();
            $file->move('images/order', $name);
            $input['proof'] = $name;
        }
        else{
            $name = null;
        }


        if($request->pay_status == 1) {
           
            foreach($carts as $cart)
            {
                if ($cart->offer_price != 0)
                {
                    $pay_amount =  $cart->offer_price;
                }
                else
                {
                    $pay_amount =  $cart->price;
                }

                if ($cart->disamount != 0 || $cart->disamount != NULL)
                {
                    $cpn_discount =  $cart->disamount;
                }
                else
                {
                    $cpn_discount =  '';
                }


                $lastOrder = Order::orderBy('created_at', 'desc')->first();

                if ( ! $lastOrder )
                {
                    // We get here if there is no order at all
                    // If there is no number set it to 0, which will be 1 at the end.
                    $number = 0;
                }
                else
                { 
                    $number = substr($lastOrder->order_id, 3);
                }

                if($cart->type == 1)
                {
                    $bundle_id = $cart->bundle_id;
                    $course_id = NULL;
                    $duration = NULL;
                    $instructor_payout = 0;
                    $instructor_id = $cart->bundle->user_id;

                    if($cart->bundle->duration_type == "m")
                    {
                        
                        if($cart->bundle->duration != NULL && $cart->bundle->duration !='')
                        {
                            $days = $cart->bundle->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    }
                    else
                    {

                        if($cart->bundle->duration != NULL && $cart->bundle->duration !='')
                        {
                            $days = $cart->bundle->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }

                    }
                }
                else{

                    if($cart->courses->duration_type == "m")
                    {
                        
                        if($cart->courses->duration != NULL && $cart->courses->duration !='')
                        {
                            $days = $cart->courses->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    }
                    else
                    {

                        if($cart->courses->duration != NULL && $cart->courses->duration !='')
                        {
                            $days = $cart->courses->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }

                    }


                    $setting = InstructorSetting::first();


                    if($cart->courses->instructor_revenue != NULL)
                    {
                        $x_amount = $pay_amount * $cart->courses->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    }
                    else
                    {

                        if(isset($setting))
                        {
                            if($cart->courses->user->role == "instructor")
                            {
                                $x_amount = $pay_amount * $setting->instructor_revenue;
                                $instructor_payout = $x_amount / 100;
                            }
                            else
                            {
                                $instructor_payout = 0;
                            }
                            
                        }
                        else
                        {
                            $instructor_payout = 0;
                        }  
                    }

                    

                    $bundle_id = NULL;
                    $course_id = $cart->course_id;
                    $duration = $cart->courses->duration;
                    $instructor_id = $cart->courses->user_id;
                }


                if($request->payment_method == 'paypal')
                {
                    $saleId = $request->sale_id;
                }
                else{

                    $saleId = NULL;
                }

                if($request->payment_method == 'bank_transfer')
                {

                    $transaction_id = str_random(32);
                    $status =  '0';
                    
                }
                else{

                    $transaction_id = $request->transaction_id;
                    $status =  '1';
                    
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
                    'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );



                if($cart->type == 1)
                {

                    Cart::where('user_id',$auth->id)->where('bundle_id', $cart->bundle_id)->delete();
                }
                else{

                    Wishlist::where('user_id',$auth->id)->where('course_id', $cart->course_id)->delete();



                    Cart::where('user_id',$auth->id)->where('course_id', $cart->course_id)->delete();

                }
                

                if($instructor_payout != 0)
                {
                    if($created_order)
                    {
                        if($cart->type == 0)
                        {
                            if($cart->courses->user->role == "instructor")
                            {

                                $created_payout = PendingPayout::create([
                                    'user_id' => $cart->courses->user_id,
                                    'course_id' => $cart->course_id,
                                    'order_id' => $created_order->id,
                                    'transaction_id' => $request->transaction_id,
                                    'total_amount' => $pay_amount,
                                    'currency' => $currency->currency,
                                    'currency_icon' => $currency->icon,
                                    'instructor_revenue' => $instructor_payout,
                                    'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                                    ]
                                );
                            }
                        }
                    }
                }
                

                if($created_order){
                    try{
                        
                        /*sending email*/
                        $x = 'You are successfully enrolled in a course';
                        $order = $created_order;
                        Mail::to(Auth::User()->email)->send(new SendOrderMail($x, $order));


                    }catch(\Swift_TransportException $e){


                    }
                }

                if($cart->type == 0)
                {

                    if($created_order){
                        // Notification when user enroll
                        $cor = Course::where('id', $cart->course_id)->first();

                        $course = [
                          'title' => $cor->title,
                          'image' => $cor->preview_image,
                        ];

                        $enroll = Order::where('course_id', $cart->course_id)->get();

                    //    if(!$enroll->isEmpty())
                    //    {
                    //        foreach($enroll as $enrol)
                    //        {
                    //            $user = User::where('id', $enrol->user_id)->get();
                    //            Notification::send($user,new UserEnroll($course));
                    //        }
                    //    }
                    }
                }

                

            } 

            return response()->json('Payment Successfull !', 200);

        }
        else{

            return response()->json('Payment Failed !', 401);

        }
        
        
        return response()->json('Payment Failed !', 401);
                    
                
              
    }


    public function banktransfer(Request $request, $id)
    {

      $user = User::where('id', $id)->first();

      if($user->prefer_pay_method == "banktransfer") 
      {

        $currency = Currency::first();
        


        $orders = array();

        foreach ($request->checked as $checked) {

            $payout = PendingPayout::findOrFail($checked);

            $orders[] = $payout->order->id;

        }

        $created_order = CompletedPayout::create([
              'user_id' => $id,
              'payer_id' => Auth::User()->id,
              'pay_total' => $request->total,
              'order_id' =>  $orders,
              'payment_method' => 'banktransfer',
              'currency' => $currency->currency,
              'currency_icon' => $currency->icon,
              'pay_status' => 1,
              'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
              'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
              ]
          );

          if($created_order){

            foreach($request->checked as $checked) {

                $payout = PendingPayout::findOrFail($checked);

                
                // PendingPayout::where('id', $checked)->delete();
                PendingPayout::where('id', $checked)
                        ->update(['status' => '1']);

            }

          }

         session()->flash('success','Payment Success');

          return redirect('admin/instructor');
     

      }

    }

    public function paytm(Request $request, $id)
    {

      $user = User::where('id', $id)->first();

      if($user->prefer_pay_method == "paytm") 
      {

        $currency = Currency::first();

        $orders = array();

        foreach ($request->checked as $checked) {

            $payout = PendingPayout::findOrFail($checked);

            $orders[] = $payout->order->id;

        }
        

        $created_order = CompletedPayout::create([
              'user_id' => $id,
              'payer_id' => Auth::User()->id,
              'pay_total' => $request->total,
              'order_id' =>  $orders,
              'payment_method' => 'paytm',
              'currency' => $currency->currency,
              'currency_icon' => $currency->icon,
              'pay_status' => 1,
              'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
              'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
              ]
          );

          if($created_order){

            foreach($request->checked as $checked) {

                $payout = PendingPayout::findOrFail($checked);

                
                // PendingPayout::where('id', $checked)->delete();
                PendingPayout::where('id', $checked)
                        ->update(['status' => '1']);

            }

          }

         session()->flash('success','Payment Success');

          return redirect('admin/instructor');
     

      }

    }

    public function paypal(Request $request, $id)
    {

     $all_checked =  $request->checked;
      $pay_total =  $request->total;

      $user = User::where('id', $id)->first();


        if($user->prefer_pay_method == "paypal") 
        {
          	$amount = $request->total;

          	$user = User::where('id', $id)->first();

          	$sendemail = $user->paypal_email;
              $Currency = Currency::first();
              $defCurrency = $Currency->currency;
              $uniqid = str_random(10);
              $payouts = new Payout();
              // $inv_cus = Invoice::first();
              $senderBatchHeader = new PayoutSenderBatchHeader();
              $senderBatchHeader->setSenderBatchId(uniqid())
                  ->setEmailSubject("You have a Payout!");
              $senderItem = new PayoutItem();
              $senderItem->setRecipientType('Email')
                  ->setNote('Thanks for using our portal for selling your product!')
                  ->setReceiver($sendemail)
                  ->setSenderItemId($uniqid)
                  ->setAmount(new \PayPal\Api\Currency('{
                                      "value":'.$amount.',
                                      "currency":"'.$defCurrency.'"
                                  }'));
              $payouts->setSenderBatchHeader($senderBatchHeader)
                  ->addItem($senderItem);
              $request = clone $payouts;

              // return $request;

              try
              {
                  $output = $payouts->create(array('sync_mode' => 'false'), $this->_api_context);
                  $bid = $output->batch_header->payout_batch_id;
                  $response =  Payout::get($bid,$this->_api_context);

                  
                  $currency = Currency::first();
          
          
                  $orders = array();

                  foreach ($all_checked as $checked) {

                      $payout = PendingPayout::findOrFail($checked);

                      $orders[] = $payout->order->id;

                  }

                  $created_order = CompletedPayout::create([
                        'user_id' => $id,
                        'payer_id' => Auth::User()->id,
                        'pay_total' => $pay_total,
                        'order_id' =>  $orders,
                        'payment_method' => 'banktransfer',
                        'currency' => $currency->currency,
                        'currency_icon' => $currency->icon,
                        'pay_status' => 1,
                        'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                        ]
                    );

                    if($created_order){

                      foreach($all_checked as $checked) {

                          $payout = PendingPayout::findOrFail($checked);

                          PendingPayout::where('id', $checked)
                          ->update(['status' => '1']);

                      }

                    }

                   session()->flash('success',trans('flash.PaymentSuccess'));

                    return redirect('admin/instructor');
       

        			}
              catch(\Exception $e){

                $errorcode = $e->getCode();

                \Session::flash('delete', $e->getMessage());
                return redirect('admin/instructor');

                
          		}




        }


    }

    public function purchasehistory(Request $request){

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

        return response()->json(array('orderhistory' =>$enroll), 200);      
    }


    public function apikeys(Request $request)
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

        
        $stripekey =  env('STRIPE_KEY');
        $stripesecret = env('STRIPE_SECRET');

        $paypal_client_id =  env('PAYPAL_CLIENT_ID');
        $paypal_secret = env('PAYPAL_SECRET');
        $paypal_mode =  env('PAYPAL_MODE');

        $instamojo_api_key =  env('IM_API_KEY');
        $instamojo_auth_token = env('IM_AUTH_TOKEN');
        $instamojo_url =  env('IM_URL');

        $razorpay_key =  env('RAZORPAY_KEY');
        $razorpay_secret = env('RAZORPAY_SECRET');

        $paystack_public_key =  env('PAYSTACK_PUBLIC_KEY');
        $paystack_secret = env('PAYSTACK_SECRET_KEY');
        $paystack_pay_url =  env('PAYSTACK_PAYMENT_URL');
        $paystack_merchant_email =  env('PAYSTACK_MERCHANT_EMAIL');

        $paytm_enviroment = env('PAYTM_ENVIRONMENT');
        $paytm_merchant_id =  env('PAYTM_MERCHANT_ID');
        $paytm_merchant_key =  env('PAYTM_MERCHANT_KEY');
        $paytm_merchant_website = env('PAYTM_MERCHANT_WEBSITE');
        $paytm_channel =  env('PAYTM_CHANNEL');
        $paytm_industry_type =  env('PAYTM_INDUSTRY_TYPE');

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

        
        return response()->json(array(
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
              ), 200);
        
        
        
    }


    public function enroll(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required'
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if($errors->first('course_id')){
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $auth = Auth::user();

        $course = Course::findOrFail($request->course_id);


        $order = Order::create([
            'user_id' => $auth->id,
            'instructor_id' => $course->user_id,
            'course_id' => $course->id,
            'total_amount' => 'Free',
            'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        return response()->json(array('message' => 'User Enrolled', 'status' => 'success'), 200);

    }


    public function giftusercheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'course_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if($errors->first('course_id')){
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }
        }

        $user_check = User::where('email', $request->email)->first();

        if($user_check == NULL)
        {
            $password = '123456';

            $user = new User;
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


        if ($course->discount_price != 0)
        {
            $offer_total = $offer_total + $course->discount_price;
        }
        else
        {
            $offer_total = $offer_total + $course->price;
        }



        $price_total = $price_total + $course->price;


        

        //for offer percent
        $offer_amount  = $price_total - ($offer_total);
        $value         =  $offer_amount / $price_total;
        $offer_percent = $value * 100;

        $offer_percent = $request->offer_percent;


        $cart_total = $offer_total;

        return response()->json(array('course'=>$course, 'user'=>$user_check ), 200);
    }


    public function giftcheckout(Request $request)
    {

        $course = Course::where('id', $request->course_id)->first();

        $user = User::where('id', $request->gift_user_id)->first();


        $gsettings = Setting::first();

        $current_date = Carbon::now();

        $currency = Currency::where('default', '=', '1')->first();


        if($request->pay_status == '0')
        {
            $pay_status =  '0';
        }
        else
        {
            $pay_status =  1;
        }

        if(isset($request->sale_id))
        {
            $saleId = $request->sale_id;
        }
        else{

            $saleId = NULL;
        }

        if($file = $request->file('file'))
        {
            $name = time().$file->getClientOriginalName();
            $file->move('images/order', $name);
            $input['proof'] = $name;
        }
        else{
            $name = null;
        }



            if ($course->discount_price != 0)
            {
                $pay_amount =  $course->discount_price;
            }
            else
            {
                $pay_amount =  $course->price;
            }

            
            $cpn_discount =  NULL;
            


            $lastOrder = Order::orderBy('created_at', 'desc')->first();

            if ( ! $lastOrder )
            {
                // We get here if there is no order at all
                // If there is no number set it to 0, which will be 1 at the end.
                $number = 0;
            }
            else
            { 
                $number = substr($lastOrder->order_id, 3);
            }

            

                if($course->duration_type == "m")
                {
                    
                    if($course->duration != NULL && $course->duration !='')
                    {
                        $days = $course->duration * 30;
                        $todayDate = date('Y-m-d');
                        $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                    }
                    else{
                        $todayDate = NULL;
                        $expireDate = NULL;
                    }
                }
                else
                {

                    if($course->duration != NULL && $course->duration !='')
                    {
                        $days = $course->duration;
                        $todayDate = date('Y-m-d');
                        $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                    }
                    else{
                        $todayDate = NULL;
                        $expireDate = NULL;
                    }

                }


                $setting = InstructorSetting::first();


                if($course->instructor_revenue != NULL)
                {
                    $x_amount = $pay_amount * $course->instructor_revenue;
                    $instructor_payout = $x_amount / 100;
                }
                else
                {

                    if(isset($setting))
                    {
                        if($course->user->role == "instructor")
                        {
                            $x_amount = $pay_amount * $setting->instructor_revenue;
                            $instructor_payout = $x_amount / 100;
                        }
                        else
                        {
                            $instructor_payout = 0;
                        }
                        
                    }
                    else
                    {
                        $instructor_payout = 0;
                    }  
                }

                

                $bundle_id = NULL;
                $course_id = $course->id;
                $bundle_course_id = NULL;
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
                'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                ]
            );
            
            

            if($instructor_payout != 0)
            {
                if($created_order)
                {
                    
                    if($course->user->role == "instructor")
                    {
                        $created_payout = PendingPayout::create([
                            'user_id' => $course->user_id,
                            'course_id' => $course->id,
                            'order_id' => $created_order->id,
                            'transaction_id' => $txn_id,
                            'total_amount' => $pay_amount,
                            'currency' => $currency->currency,
                            'currency_icon' => $currency->icon,
                            'instructor_revenue' => $instructor_payout,
                            'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                            'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                            ]
                        );
                    }
                    
                }
            }

            if($created_order){
                if ($gsettings->twilio_enable == '1') {

                    try{
                        $recipients = $user->mobile;
                        
        
                        $msg = 'Hey'. ' ' .$user->fname . ' '.
                                'You\'r successfully enrolled in '. $course->title .
                                'Thanks'. ' ' . config('app.name');
                    
                        TwilioMsg::sendMessage($msg, $recipients);

                    }catch(\Exception $e){
                        
                    }

                }
            }
            


            if($created_order){
                if (env('MAIL_USERNAME')!=null) {
                    try{
                        
                        /*sending user email*/
                        $x = 'You are successfully enrolled in a course';
                        $order = $created_order;
                        Mail::to($user->email)->send(new SendOrderMail($x, $order));


                        /*sending user email*/
                        $x = 'A Gift for you !!';
                        $order = $created_order;
                        Mail::to($user->email)->send(new GiftOrder($x, $order, $course));


                        /*sending admin email*/
                        $x = 'User Enrolled in course '. $course->title;
                        $order = $created_order;
                        Mail::to($course->user->email)->send(new AdminMailOnOrder($x, $order));


                    }catch(\Swift_TransportException $e){
                        
                    }

                }
            }

            

            if($created_order){
                // Notification when user enroll
                $cor = Course::where('id', $course->id)->first();

                $course = [
                  'title' => $cor->title,
                  'image' => $cor->preview_image,
                ];

                

                if($user->id != NULL)
            //    {
            //        $user = User::where('id', $user->id)->first();
            //        Notification::send($user,new UserEnroll($course));
                   
            //    }

                $order_id = $created_order->order_id;
                $url = route('view.order', $created_order->id);

            //    if($cor != NULL)
            //    {
            //        $user = User::where('id', $cor->user->id)->first();
            //        Notification::send($user,new AdminOrder($course, $order_id, $url));
                   
            //    }
            }


        return response()->json('Payment Successfull !', 200);
    }


    public function stripepay(Request $request)
    {
        $token = $request->token;

        $stripe = new \Stripe\StripeClient(
          env('STRIPE_SECRET')
        );

        $user = Auth::user();

        $carts = Cart::where('user_id', $user->id)->get();

        $price_total = 0;
        $offer_total = 0;
        $cpn_discount = 0;


        //cart price after offer
        foreach ($carts as $key => $c)
        {
            if ($c->offer_price != 0)
            {
                $offer_total = $offer_total + $c->offer_price;
            }
            else
            {
                $offer_total = $offer_total + $c->price;
            }
        }

        //for price total
        foreach ($carts as $key => $c)
        {
            
            $price_total = $price_total + $c->price;
            
        }


        //for coupon discount total
        foreach ($carts as $key => $c)
        {
            
            $cpn_discount = $cpn_discount + $c->disamount;
        }


        $cart_total = 0;
        
        foreach ($carts as $key => $c)
        {

            if ($cpn_discount != 0)
            {
                $cart_total = $offer_total - $cpn_discount;
            }
            else{

                $cart_total = $offer_total;
            }
        }

        $charge = $stripe->charges->create([
          'amount' => $cart_total,
          'currency' => 'usd',
          'source' => $token,
          'description' => 'Enrolling in one time paid courses',
        ]);


        if($charge['status'] == 'succeeded') {

            $txn_id = $charge['id'];

            $payment_method = 'Stripe';

            $gsettings = Setting::first();

            $current_date = Carbon::now();

            $currency = Currency::first();


            if($payment_status == '0')
            {
                $pay_status =  '0';
            }
            else
            {
                $pay_status =  1;
            }

            $carts = Cart::where('user_id', $user->id)->get();

            foreach($carts as $cart)
            {

                if ($cart->offer_price != 0)
                {
                    $pay_amount =  $cart->offer_price;
                }
                else
                {
                    $pay_amount =  $cart->price;
                }

                if ($cart->disamount != 0 || $cart->disamount != NULL)
                {
                    $cpn_discount =  $cart->disamount;
                }
                else
                {
                    $cpn_discount =  '';
                }


                $lastOrder = Order::orderBy('created_at', 'desc')->first();

                if ( ! $lastOrder )
                {
                    // We get here if there is no order at all
                    // If there is no number set it to 0, which will be 1 at the end.
                    $number = 0;
                }
                else
                { 
                    $number = substr($lastOrder->order_id, 3);
                }

                if($cart->type == 1)
                {
                    $bundle_id = $cart->bundle_id;
                    $bundle_course_id = $cart->bundle->course_id;
                    $course_id = NULL;
                    $duration = NULL;
                    $instructor_payout = 0;
                    $instructor_id = $cart->bundle->user_id;

                    if($cart->bundle->duration_type == "m")
                    {
                        
                        if($cart->bundle->duration != NULL && $cart->bundle->duration !='')
                        {
                            $days = $cart->bundle->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    }
                    else
                    {

                        if($cart->bundle->duration != NULL && $cart->bundle->duration !='')
                        {
                            $days = $cart->bundle->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }

                    }
                }
                else{

                    if($cart->courses->duration_type == "m")
                    {
                        
                        if($cart->courses->duration != NULL && $cart->courses->duration !='')
                        {
                            $days = $cart->courses->duration * 30;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }
                    }
                    else
                    {

                        if($cart->courses->duration != NULL && $cart->courses->duration !='')
                        {
                            $days = $cart->courses->duration;
                            $todayDate = date('Y-m-d');
                            $expireDate = date("Y-m-d", strtotime("$todayDate +$days days"));
                        }
                        else{
                            $todayDate = NULL;
                            $expireDate = NULL;
                        }

                    }


                    $setting = InstructorSetting::first();


                    if($cart->courses->instructor_revenue != NULL)
                    {
                        $x_amount = $pay_amount * $cart->courses->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    }
                    else
                    {

                        if(isset($setting))
                        {
                            if($cart->courses->user->role == "instructor")
                            {
                                $x_amount = $pay_amount * $setting->instructor_revenue;
                                $instructor_payout = $x_amount / 100;
                            }
                            else
                            {
                                $instructor_payout = 0;
                            }
                            
                        }
                        else
                        {
                            $instructor_payout = 0;
                        }  
                    }

                    

                    $bundle_id = NULL;
                    $course_id = $cart->course_id;
                    $bundle_course_id = NULL;
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
                    'sale_id' => $sale_id,
                    'status' => $pay_status,
                    'proof' => $file,
                    'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );
                
                Wishlist::where('user_id',$user->id)->where('course_id', $cart->course_id)->delete();

                Cart::where('user_id',$user->id)->delete();


                if($instructor_payout != 0)
                {
                    if($created_order)
                    {
                        if($cart->type == 0)
                        {
                            if($cart->courses->user->role == "instructor")
                            {
                                $created_payout = PendingPayout::create([
                                    'user_id' => $cart->courses->user_id,
                                    'course_id' => $cart->course_id,
                                    'order_id' => $created_order->id,
                                    'transaction_id' => $txn_id,
                                    'total_amount' => $pay_amount,
                                    'currency' => $currency->currency,
                                    'currency_icon' => $currency->icon,
                                    'instructor_revenue' => $instructor_payout,
                                    'created_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                                    'updated_at'  => \Carbon\Carbon::now()->toDateTimeString(),
                                    ]
                                );
                            }
                        }
                    }
                }

                if($created_order){
                    if ($gsettings->twilio_enable == '1') {

                        try{
                            $recipients = $user->mobile;
                            
            
                            $msg = 'Hey'. ' ' .$user->fname . ' '.
                                    'You\'r successfully enrolled in '. $cart->courses->title .
                                    'Thanks'. ' ' . config('app.name');
                        
                            TwilioMsg::sendMessage($msg, $recipients);

                        }catch(\Exception $e){
                            
                        }

                    }
                }
                


                if($created_order){
                    if (env('MAIL_USERNAME')!=null) {
                        try{
                            
                            /*sending user email*/
                            $x = 'You are successfully enrolled in a course';
                            $order = $created_order;
                            Mail::to(Auth::User()->email)->send(new SendOrderMail($x, $order));


                            /*sending admin email*/
                            $x = 'User Enrolled in course '. $cart->courses->title;
                            $order = $created_order;
                            Mail::to($cart->courses->user->email)->send(new AdminMailOnOrder($x, $order));


                        }catch(\Swift_TransportException $e){
                            
                        }

                    }
                }

                // if($cart->type == 0)
                // {

                //     if($created_order){
                //         Notification when user enroll
                //        $cor = Course::where('id', $cart->course_id)->first();

                //        $course = [
                //          'title' => $cor->title,
                //          'image' => $cor->preview_image,
                //        ];

                //        $enroll = Order::where('user_id', $user->id)->where('course_id', $cart->course_id)->first();

                //        if($enroll != NULL)
                //        {
                //            $user = User::where('id', $enroll->user_id)->first();
                //            Notification::send($user,new UserEnroll($course));
                           
                //        }

                //        $order_id = $created_order->order_id;
                //        $url = route('view.order', $created_order->id);

                //        if($cor != NULL)
                //        {
                //            $user = User::where('id', $cor->user_id)->first();
                //            Notification::send($user,new AdminOrder($course, $order_id, $url));
                           
                //        }
                //     }

                // }
                
            }

            return response()->json('Payment Successfull !', 200);
          }
        else {
            return response()->json('Payment Failed !', 401);
        }
    }

}