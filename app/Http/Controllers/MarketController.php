<?php

namespace App\Http\Controllers;
use App\BBL;
use App\User;
use App\Order;
use App\Coupon;
use App\Course;
use App\Wallet;
use App\Meeting;
use App\Currency;
use Carbon\Carbon;
use App\Googlemeet;
use App\BundleCourse;
use App\JitsiMeeting;
use App\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:marketing-dashboard.manage', ['only' => ['index']]);

    }


    public function index(){

        $currencies = Currency::where('default','1')->value('symbol');
        $user_order_count =  User::select('users.fname', DB::raw('COUNT(orders.user_id) AS order_count'), DB::raw('SUM(orders.paid_amount) as total_amount'), DB::raw('SUM(orders.coupon_discount) as coupon_discount'))
                                    ->join('orders', 'users.id', '=', 'orders.user_id')
                                    ->where('users.role', 'user')
                                    ->where('users.status', '1')
                                    ->exceptTestUser()
                                    ->groupBy('users.id')
                                    ->orderBy('order_count', 'DESC')
                                    ->take(5)
                                    ->get();
        
        $order_sum = array(
            Order::whereMonth('created_at', '01')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '02')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '03')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()    
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '04')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()    
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '05')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '06')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()    
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '07')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '08')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '09')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '10')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()    
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '11')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()    
                    ->sum('paid_amount'),
            Order::whereMonth('created_at', '12')
                    ->whereYear('created_at', date('Y'))
                    ->whereHas('user', function($q){
                        $q->where('users.test_user', '0');
                    })
                    ->activeOrder()
                    ->sum('paid_amount')
        );

        $order_total = [];
        foreach($order_sum as $order){
            $order_total[] = round($order, 2);
        }
            
        // $featured       =       Course::where('featured' ,1)->count();
        $coupan         =       Coupon::where('expirydate' ,'>=' ,Carbon::now())->Where('maxusage', '>', '0')->count();
        // $total          =       DB::table('wallet_transactions')->sum('total_amount');
        $total          =       Wallet::query()
                                    ->whereHas('user', function($q){
                                        $q->exceptTestuser();
                                    })
                                    ->sum('balance');
        $total = $this->nice_number($total); // Wallet Amount Card

        $total_order    =       Order::query()
                                    ->whereHas('user', function($q){
                                        $q->exceptTestuser();
                                    })
                                    ->activeOrder()
                                    ->sum('paid_amount');
        $total_order = $this->nice_number($total_order); // This total_order amount display in Total Revenue Card
        
        $coupon_discount    =       Order::query()
                                            ->whereHas('user', function($q){
                                                $q->exceptTestuser();
                                            })
                                            ->activeOrder()
                                            ->sum('coupon_discount');
        $coupon_discount = $this->nice_number($coupon_discount); // This total_order amount display in Total Revenue Card

        $order          =       Order::query()
                                    ->whereHas('user', function($q){
                                        $q->where('users.test_user', '0');
                                    })
                                    ->activeOrder()
                                    ->pluck('user_id');
        $users          =       User::whereIn('id',$order)->count();
        // $ins_payment    =       DB::table('pending_payouts')->sum('instructor_revenue');
        // $total_amount   =       DB::table('pending_payouts')->sum('total_amount');
        // $admin_payment  =       $total_amount - $ins_payment;
        // $admin_amount   =       DB::table('orders')->where('user_id', Auth::user()->id)->sum('total_amount');
        // $admin_total    =       $admin_amount + $admin_payment;
        $course         =       Course::where('status', '1')->count();
        $bundle_course  =       BundleCourse::where('status', '1')->count();
        // $meeting        =       Meeting::count();
        // $jitsi          =       JitsiMeeting::count();
        $bbl            =       BBL::count();
        // $google         =       Googlemeet::count();
        
        // $total_meeting  =       $meeting + $jitsi + $bbl + $google;
        $total_meeting  =       $bbl;
        $graph          =       [$course,$bundle_course,$total_meeting];            
        
        // return view('admin.marketing.dashboard',compact('order_total','featured','coupan','total',
        // 'total_order','users','ins_payment','admin_total',
        // 'graph','user_order_count','currencies'));
        return view('admin.marketing.dashboard',compact('order_total','coupan','total', 'coupon_discount',
        'total_order','users','graph','user_order_count','currencies'));
    }


    public function nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return false;

        // now filter it;
        if ($n > 1000000000000) return round(($n/1000000000000), 2).' trillion';
        elseif ($n > 1000000000) return round(($n/1000000000), 2).' billion';
        elseif ($n > 1000000) return round(($n/1000000), 2).'M';
        elseif ($n > 1000) return round(($n/1000), 2).'K';

        return number_format($n);
    }

    // echo nice_number('14120000');
    //14.12 million
    
}
