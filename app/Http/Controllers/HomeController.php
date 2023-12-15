<?php

namespace App\Http\Controllers;

use DB;
use Config;
use Module;
use App\BBL;
use Session;
use App\Blog;
use App\User;
use Response;
use App\Batch;
use App\Facts;
use App\Order;
use App\Course;
use App\Slider;
use App\Meeting;
use App\Setting;
use App\Trusted;
use App\Categories;
use App\Googlemeet;
use App\SliderFacts;
use App\Testimonial;
use App\BundleCourse;
use App\JitsiMeeting;
use App\Videosetting;
use App\Advertisement;
use App\CategorySlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Modules\Googleclassroom\Models\Googleclassroom;

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth','verified']);
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->role == "admin") {
            return redirect()->route('admin.index');
        } elseif (Auth::check() && Auth::user()->role == "instructor") {
            return redirect()->route('instructor.index');
        } elseif (Auth::check() && Auth::user()->role != "user") {
            return view('admin.customrole-dashboard');
        } else {
            Auth::logout();
        }

        $category = Categories::where('status', '1')->orderBy('position', 'ASC')->with('subcategory')->get();
        $sliders = Slider::where('status', '1')->orderBy('position', 'ASC')->get();
        $facts = SliderFacts::limit(3)->get();
        $instructors = User::select('*')->where('role', 'instructor')->where('status', '1')->get();
        $discountcourse = Course::where('type', '1')->where('status', 1)->whereNotNUll('discount_price')->with('user')->inRandomOrder()->limit(5)->get();
        $categorie_ids = CategorySlider::first();
        $factsetting = Facts::limit(4)->where('status', '1')->get();
        $videosetting = Videosetting::first();
        $bestselling = Order::whereNotNUll('course_id')->inRandomOrder()->limit(5)->get();

        if (isset($categorie_ids)) {
            $categories = Categories::whereHas('courses')
                            ->whereIn('id', $categorie_ids->category_id)
                            ->where('status', '1')
                            ->get();
        } else {
            $categories = null;
        }

        $meetings = Meeting::where('link_by', null)->whereHas('user')->with('user')->get();
        $bigblue = BBL::where('is_ended', '!=', 1)->where('link_by', null)->with('user')->get();
        $testi = Testimonial::where('status', '1')->get();
        $trusted = Trusted::where('status', '1')->get();
        $blogs = Blog::where('status', '1')->orderBy('updated_at', 'DESC')->with('user')->get();
        if (Schema::hasTable('googlemeets')) {
            $allgooglemeet = Googlemeet::orderBy('id', 'DESC')->where('link_by', null)->with('user')->with('user')->get();
        } else {
            $allgooglemeet = null;
        }

        if (Schema::hasTable('jitsimeetings')) {
            $jitsimeeting = JitsiMeeting::orderBy('id', 'DESC')->where('link_by', null)->with('user')->with('user')->get();
        } else {
            $jitsimeeting = null;
        }

        if (Schema::hasColumn('bundle_courses', 'is_subscription_enabled')) {
            $bundles = BundleCourse::where('is_subscription_enabled', 0)->with('user')->get();
            $subscriptionBundles = BundleCourse::where('is_subscription_enabled', 1)->with('user')->get();
        } else {
            $bundles = null;
            $subscriptionBundles = null;
        }

        if (Schema::hasTable('batch')) {
            $batches = Batch::where('status', '1')->get();
        } else {
            $batches = null;
        }

        if (Schema::hasTable('advertisements')) {
            $advs = Advertisement::where('status', '=', 1)->get();
        } else {
            $advs = null;
        }

        $viewed = session()->get('courses.recently_viewed');

        if (isset($viewed)) {
            $recent_course_id = array_unique($viewed);
        } else {
            $recent_course_id = null;
        }

        if (Schema::hasTable('googleclassrooms') && Module::has('Googleclassroom') && Module::find('Googleclassroom')->isEnabled()) {
            $googleclassrooms = Googleclassroom::orderBy('id', 'DESC')->where('link_by', null)->where('status', '1')->get();
        } else {
            $googleclassrooms = null;
        }

        $counter = 0;
        $recent_course = null;

        if ($recent_course_id != null) {
            $recent_course_id = array_splice($recent_course_id, 0);
        } else {
            $recent_course_id = null;
        }

        if (Auth::check()) {
            if (isset($recent_course_id)) {
                $recent_course = Course::whereIn('id', $recent_course_id)->where('status', '1')->count();
            }
        }

        $total_count = $recent_course;

        // == to get visitor ip start
        // $ipaddress='157.37.174.226';
        $ipaddress = $request->getClientIp();

        $geoip = geoip()->getLocation($ipaddress);
        $usercountry = strtoupper($geoip->country);

        $cors = Course::where('status', '1')->where('featured', '1')->with('user')->inRandomOrder()->limit(20)->get()->map(function ($c) use ($usercountry) {

            if ($c->country != '') {
                if (!in_array($usercountry, $c->country)) {
                    return $c;
                }
            } else {
                return $c;
            }
        })->filter();

        // == to get visitor ip end
        return view('home', compact('category', 'sliders', 'facts', 'categories', 'cors', 'bundles', 'meetings', 'bigblue', 'testi', 'trusted', 'recent_course_id', 'blogs', 'subscriptionBundles', 'batches', 'recent_course', 'total_count', 'advs', 'allgooglemeet', 'jitsimeeting', 'googleclassrooms', 'usercountry', 'instructors', 'factsetting', 'videosetting', 'discountcourse', 'bestselling'));
    }
}
