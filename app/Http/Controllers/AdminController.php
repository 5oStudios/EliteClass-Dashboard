<?php

namespace App\Http\Controllers;

use App\BBL;
use App\Blog;
use App\Page;
use App\User;
use App\admin;
use App\Order;
use App\Coupon;
use App\Course;
use App\Meeting;
use App\Followers;
use Carbon\Carbon;
use App\Categories;
use App\FaqStudent;
use App\Googlemeet;
use App\Testimonial;
use App\JitsiMeeting;
use App\RefundPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        if(Auth::User()->role == "admin")
        {
            $userss = User::query()
                            ->where('status', '1')
                            ->exceptTestUser()
                            ->count();
            $usergraph = array(
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(1))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(2))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(3))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(4))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(5))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(6))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
                User::query()
                        ->whereDate('created_at', '=', Carbon::now()->subdays(7))
                        ->where('status', '1')
                        ->exceptTestUser()
                        ->count(),
            );
            $courses = Course::where('status', '1')->count();
            // $coursegraph = array(
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Course::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            $categories = Categories::count();
            // $categorygraph = array(
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Categories::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $orders = Order::count();
            $orders = Order::query()
                            ->whereHas('user', function($q){
                                $q->exceptTestuser();
                            })
                            ->activeOrder()
                            ->count();
            $ordergraph = array(
                Order::whereDate('created_at', '=', Carbon::now()->subdays(1))
                ->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),
                Order::whereDate('created_at', '=', Carbon::now()->subdays(2))->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),             
                Order::whereDate('created_at', '=', Carbon::now()->subdays(3))->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),              
                Order::whereDate('created_at', '=', Carbon::now()->subdays(4))->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),              
                Order::whereDate('created_at', '=', Carbon::now()->subdays(5))->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),                
                Order::whereDate('created_at', '=', Carbon::now()->subdays(6))->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),
                Order::whereDate('created_at', '=', Carbon::now()->subdays(7))->whereHas('user', function($q){
                    $q->exceptTestuser();
                })->activeOrder()->count(),                
            );
            // $refund = RefundPolicy::count();
            // $refundgraph = array(
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     RefundPolicy::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            $coupon = Coupon::count();
            // $coupongraph = array(
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Coupon::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $zoom = Meeting::count();
            // $zoomgraph = array(
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Meeting::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            $bbl = BBL::count();
            // $bblgraph = array(
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     BBL::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $jitsi = JitsiMeeting::count();
            // $jitsigraph = array(
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     JitsiMeeting::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $googlemeet = Googlemeet ::count();
            // $googlemeetgraph = array(
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Googlemeet ::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $faq = FaqStudent ::count();
            // $faqgraph = array(
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     FaqStudent ::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $pages = Page ::count();
            // $pagegraph = array(
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Page ::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $blogs = Blog ::count();
            // $bloggraph = array(
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Blog ::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $testimonial = Testimonial ::count();
            // $testimonialgraph = array(
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Testimonial ::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            // $follower = Followers ::count();
            // $followergraph = array(
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     Followers ::whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            $instructors = User::query()
                                ->where('role', '=', 'instructor')
                                ->where('status', '1')
                                ->exceptTestUser()
                                ->count();
            // $instructorgraph = array(
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(1))->count(),
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(2))->count(),             
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(3))->count(),              
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(4))->count(),              
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(5))->count(),                
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(6))->count(),
            //     User::where('role', '=', 'instructor')->whereDate('created_at', '=', Carbon::now()->subdays(7))->count(),                
            // );
            $topuser = User::select('id','fname','email','user_img','verified','created_at')
                            ->where('role', '=', 'user')
                            ->where('status', '1')
                            ->exceptTestUser()
                            ->latest()
                            ->take(5)
                            ->get();
            $topinstructor = User::select('id','fname','email','user_img','verified','created_at')
                                ->where('role', '=', 'instructor')
                                ->where('status', '1')
                                ->exceptTestUser()
                                ->latest()
                                ->take(5)
                                ->get();
            $topcourses = Course::query()
                                ->where('status', '1')
                                ->with(['category','user'])
                                ->latest()
                                ->take(5)
                                ->get();

            $admins = User::query()
                            ->where('role', '=', 'admin')
                            ->where('status', '1')
                            ->exceptTestUser()
                            ->count();
            $users = User::query()
                            ->where('role', '=', 'user')
                            ->where('status', '1')
                            ->exceptTestUser()
                            ->count();

            $toporder = Order::query()
                                ->whereHas('user', function($q){
                                    $q->exceptTestuser();
                                })
                                ->with('user')
                                ->activeOrder()
                                ->latest()
                                ->take(5)
                                ->get();
            
            $admincharts = ([$admins,$instructors,$users]);

            $users =  User::select(DB::raw("COUNT(*) as count"))
                            ->whereYear('created_at',date('Y'))
                            ->where('status', '1')
                            ->groupBy(DB::raw("Month(created_at)"))
                            ->pluck('count');
            
            $months = User::select(DB::raw("Month(created_at) as month"))
                            ->whereYear('created_at',date('Y'))
                            ->where('status', '1')
                            ->groupBy(DB::raw("Month(created_at)"))
                            ->pluck('month');

            $datas = [0,0,0,0,0,0,0,0,0,0,0,0];
            foreach($months as $index => $month)
            {
                $datas[$month-1] = $users[$index];
            }  
            
            $users =    Order::select(DB::raw("COUNT(*) as count"))
                                ->whereYear('created_at',date('Y'))
                                ->whereHas('user', function($q){
                                    $q->exceptTestuser();
                                })
                                ->activeOrder()
                                ->groupBy(DB::raw("Month(created_at)"))
                                ->pluck('count');
            
            $months =   Order::select(DB::raw("Month(created_at) as month"))
                                ->whereYear('created_at',date('Y'))
                                ->whereHas('user', function($q){
                                    $q->exceptTestuser();
                                })
                                ->activeOrder()
                                ->groupBy(DB::raw("Month(created_at)"))
                                ->pluck('month');

            $datas1 = [0,0,0,0,0,0,0,0,0,0,0,0];
            foreach($months as $index => $month){
                $datas1[$month-1] = $users[$index];
            } 

            // return view('admin.dashboard', compact('userss','usergraph','categories','categorygraph','courses','coursegraph','orders','ordergraph','refund','refundgraph'
            //     ,'coupon','coupongraph','zoom','zoomgraph','bbl','bblgraph','jitsi','jitsigraph','googlemeet','googlemeetgraph','faq','faqgraph'
            //     ,'pages','pagegraph','blogs','bloggraph','testimonial','testimonialgraph','instructor','instructorgraph','topuser','topinstructor','topcourses','toporder'
            //     ,'admincharts','datas','datas1','followergraph','follower'));
            return view('admin.dashboard', compact('userss','usergraph','categories','courses','orders','ordergraph',
                'coupon','bbl','instructors','topuser','topinstructor','topcourses','toporder'
                ,'admincharts','datas','datas1'));

        } elseif (Auth::User()->role == "instructor"){
            return redirect()->route('instructor.index');

        } else {
            abort(403, 'User does not have right permissions.');

        }
    }    
    

    public function changedomain(Request $request)
    {
        $request->validate([
            'domain' => 'required'
        ]);

        $code = file_exists(storage_path() . '/app/keys/license.json') && file_get_contents(storage_path() . '/app/keys/license.json') != null ? file_get_contents(storage_path() . '/app/keys/license.json') : '';
       
        $code = json_decode($code);

        if($code == ''){
            return back()->withInput()->withErrors(['domain' => 'Purchase code not found please contact support !']);
        }

        $d = $request->domain;
        $domain = str_replace("www.", "", $d);  
        $domain = str_replace("http://", "", $domain);  
        $domain = str_replace("https://", "", $domain);  
        $alldata = ['app_id' => "25613271", 'ip' => $request->ip(), 'domain' => $domain , 'code' => $code->code];
        $data = $this->make_request($alldata);

        if ($data['status'] == 1)
        {
            $put = 1;
            file_put_contents(public_path().'/config.txt', $put);

            Session::flash('success', 'Domain permission changed successfully !');

            return redirect('/');
        }
        elseif ($data['msg'] == 'Already Register')
        {   
            return back()->withInput()->withErrors(['domain' => 'User is already registered !']);
        }
        else
        {
            return back()->withInput()->withErrors(['domain' => $data['msg']]);
        }
    }

    public function make_request($alldata)
    {
        $response = Http::post('https://mediacity.co.in/purchase/public/api/verifycode', [
            'app_id' => $alldata['app_id'],
            'ip' => $alldata['ip'],
            'code' => $alldata['code'],
            'domain' => $alldata['domain']
        ]);

        $result = $response->json();
        
        if($response->successful()){
            if ($result['status'] == '1')
            {
                $lic_json = array(
                
                    'name'     => request()->user_id,
                    'code'     => $alldata['code'],
                    'type'     => __('envato'),
                    'domain'   => $alldata['domain'],
                    'lic_type' => __('regular'),
                    'token'    => $result['token']
                );
    
                $file = json_encode($lic_json);
                
                $filename =  'license.json';
    
                Storage::disk('local')->put('/keys/'.$filename,$file);

                return array(
                    'msg' => $result['message'],
                    'status' => '1'
                );
            }
            else
            {
                $message = $result['message'];

                return array(
                    'msg' => $message,
                    'status' => '0'
                );
            }
        }else
        {
            $message = "Failed to validate";

            return array(
                'msg' => $message,
                'status' => '0'
            );
        }
    }
}
