<?php

namespace App\Http\Controllers\Api;

use PDF;
use Module;
use App\BBL;
use Session;
use App\Order;
use App\Course;
use App\Wallet;
use App\Meeting;
use App\Setting;
use App\Currency;
use App\Language;
use App\Wishlist;
use App\Affiliate;
use App\Institute;
use Carbon\Carbon;
use App\Attandance;
use App\ChildCategory;
use App\Googlemeet;
use App\SubCategory;
use App\WatchCourse;
use App\JitsiMeeting;
use App\ManualPayment;
use App\WidgetSetting;
use App\CourseProgress;
use App\OfflineSession;
use App\UserBankDetail;
use App\SessionEnrollment;
use App\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use BigBlueButton\BigBlueButton;
use Illuminate\Support\Facades\DB;
use Modules\Resume\Models\Postjob;
use Modules\Resume\Models\Project;
use Modules\Resume\Models\Workexp;
use Modules\Resume\Models\Acedemic;
use Modules\Resume\Models\Applyjob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Homework\Models\Homework;
use Modules\Resume\Models\Personalinfo;
use Illuminate\Support\Facades\Validator;
use Modules\Homework\Models\SubmitHomework;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;

class OtherApiController extends Controller
{
    public function siteLanguage(Request $request)
    {

    	$validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

    	$language = Language::get();

        return response()->json(array('language'=>$language), 200);
    }

    public function getInstituteCategories()
    {
        $instituteCategories = SubCategory::select('id','title as label', 'slug as value')
                                ->active()
                                ->groupBy('slug')
                                ->get();

        return response()->json($instituteCategories, 200);
    }

    public function getMajorCategories()
    {
        $majorCategories = ChildCategory::select('id','title as label', 'slug as value')
                                ->active()
                                ->groupBy('slug')
                                ->get();

        return response()->json($majorCategories, 200);
    }

    public function search(Request $request)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
            'searchTerm' => 'required'
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if($errors->first('searchTerm')){
                return response()->json(['message' => $errors->first('searchTerm'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $searchTerm = $request->searchTerm;

        $coursequery = Course::query()->with('user');

        if(isset($searchTerm))
        {
            $search_data = collect();

            $lang =app()->getLocale();

            if($lang == 'ar' || $lang == 'ur')
            {

                $course_title = $coursequery->where('title->'.app()->getLocale(), 'LIKE', '%'.$searchTerm.'%')->paginate(10);
                 
            }
            else{
                
                 $course_title = $coursequery->where('title', 'LIKE', "%$searchTerm%")->where('status','=',1)->paginate(10);

            }
        

            if (isset($course_title) && count($course_title) > 0)
            {
                
                $search_data->push($course_title);
                                

            }

            $course_tags = $coursequery->where('level_tags', 'LIKE', "%$searchTerm%")->where('status','=',1)->paginate(10);

            if (isset($course_tags) && count($course_tags) > 0)
            {
                
                $search_data->push($course_tags);
                                

            }

            $search_data = $search_data->flatten();

            $courses = Course::search($searchTerm)->with('user')->paginate(10);

            return response()->json(array('courses'=>$courses), 200);
        }
        else
        {
            return response()->json(array('message' => 'No searchTerm found', 'status' => 'fail'), 200);
        }
    }

    public function meetings(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|max:100'
        ],[
            'perPage.max'=>__("Pagination should not be more than 100"),
        ]);

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : NULL;
        $lang = $request->header('Accept-Language') ?? 'en';

        $category_id = $request->category_id;   
        $seach_text = $request->search_text?? NULL;
        $date = $request->date?? NULL;
        $scnd_category_id = $request->scnd_category_id;
        $sub_category_id = $request->sub_category;
        $child_sub_category = $request->ch_sub_category;
        $perPage = $request->perPage?? 10;

        if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category) && $user) {
             $category_id = $user->main_category;
            $scnd_category_id = $user->scnd_category_id;
            $sub_category_id = $user->sub_category;
            $child_sub_category = $user->ch_sub_category;
        }

        // if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category)) {
        //     return response()->json(array("errors"=>["message"=>['Category Not selected']]),403);
        // }
        
        $bbl_meetings = BBL::query()
                // ->when($user,function($q)use($user){
                //     $q->whereDoesntHave('orders',function($sq)use($user){
                //         $sq->where('user_id',$user->id); 
                //     });
                // })
                ->when($date, function ($q)use ($date) {
                    $q->where(DB::raw('date(start_time)'),$date);
                })
                ->when($seach_text, function ($q)use ($seach_text, $lang) {
                    $q->where(DB::raw("LOWER(meetingname->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                        ->orWhere(DB::raw("LOWER(meetingname->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
                })
                ->when($category_id, function ($q)use ($category_id) {
                    $q->where('main_category', $category_id);
                })
                ->when($scnd_category_id, function ($q)use ($scnd_category_id) {
                    $q->where('scnd_category_id', $scnd_category_id);
                })
                ->when($sub_category_id, function ($q)use ($sub_category_id) {
                    $q->where('sub_category', $sub_category_id);
                })
                ->when($child_sub_category, function ($q)use ($child_sub_category) {
                    $q->whereJsonContains('ch_sub_category', strval($child_sub_category));
                })
                ->when($request->max_price, function ($q)use ($request) {
                    $q->whereRaw("(price between $request->min_price and $request->max_price or discount_price between $request->min_price and $request->max_price)");
                    // $q->whereBetween('price', [$request->min_price, $request->max_price]);
                    // $q->orWhereBetween('discount_price', [$request->min_price, $request->max_price]);
                })
                ->when($request->from && $request->to, function ($q)use ($request) {
                    $q->whereBetween(DB::raw("date(start_time)"), [$request->from, $request->to]);
                })
                ->active()
                ->latest('id')
                ->paginate($perPage);

        $bbl_meetings->getCollection()->transform(function ($m)use($user) {
            return [
                'id' => $m->id,
                'owner_id' => $m->owner_id,
                'instructor_id' => $m->instructor_id,
                'in_wishlist'=>$user ? ($m->inwishlist($user->id)?true:false) :false,
                'meeting_title' => $m->meetingname,
                'bigblue_meetingid' => $m->meetingid,
                'instructor' => $m->user->fname. ' ' .$m->user->lname,
                // 'date' => date('d M',strtotime($m->start_time)),
                // 'time' => date('h:i A',strtotime($m->start_time)),
                'date_time' => $m->start_time,
                'image' => url('images/bg/'.$m->image),
                'discount_price' => $m->discount_price,
                'type' => 'live-streaming'
            ];
        });

        return response()->json($bbl_meetings, 200);
    }


    public function inpersonsession(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|max:100'
        ],[
            'perPage.max'=>__("Pagination should not be more than 100"),
        ]);

       $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : NULL;
       $lang = $request->header('Accept-Language') ?? 'en';

        $category_id = $request->category_id;   
        $seach_text = $request->search_text?? NULL;
        $date = $request->date?? NULL;
        $scnd_category_id = $request->scnd_category_id;
        $sub_category_id = $request->sub_category;
        $child_sub_category = $request->ch_sub_category;
        $perPage = $request->perPage?? 10;

        if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category) && $user) {
             $category_id = $user->main_category;
            $scnd_category_id = $user->scnd_category_id;
            $sub_category_id = $user->sub_category;
            $child_sub_category = $user->ch_sub_category;
        }

        // if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category)) {
        //     return response()->json(array("errors"=>["message"=>['Category Not selected']]),403);
        // }

        $offline_sessions = OfflineSession::query()
                ->when($seach_text, function ($q)use ($seach_text, $lang) {
                    $q->where(DB::raw("LOWER(title->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                        ->orWhere(DB::raw("LOWER(title->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
                })
                ->when($category_id, function ($q)use ($category_id) {
                    $q->where('main_category', $category_id);
                })
                ->when($scnd_category_id, function ($q)use ($scnd_category_id) {
                    $q->where('scnd_category_id', $scnd_category_id);
                })
                ->when($sub_category_id, function ($q)use ($sub_category_id) {
                    $q->where('sub_category', $sub_category_id);
                })
                ->when($child_sub_category, function ($q)use ($child_sub_category) {
                    $q->whereJsonContains('ch_sub_category', strval($child_sub_category));
                })
                ->when($request->max_price, function ($q)use ($request) {
                    $q->whereRaw("(price between $request->min_price and $request->max_price or discount_price between $request->min_price and $request->max_price)");
                })
                ->active()
                ->latest('id')
                ->paginate($perPage);

        $offline_sessions->getCollection()->transform(function ($m)use($user) {
            return [
                'id' => $m->id,
                'owner_id' => $m->owner_id,
                'instructor_id' => $m->instructor_id,
                'in_wishlist' => $user ? ($m->inwishlist($user->id) ? true : false) : false,
                'meeting_title' => $m->title,
                'bigblue_meetingid' => null,
                'instructor' => $m->user->fname . ' ' . $m->user->lname,
                'date_time' => $m->start_time,
                'image' => url('images/offlinesession/' . $m->image),
                'discount_price' => $m->discount_price,
            ];
        });

        return response()->json($offline_sessions, 200);
    }


    public function sessionenrollment(Request $request){
        $request->validate([
            'meeting_id' => 'nullable|exists:bigbluemeetings,id',
            'offline_session_id' => 'nullable|exists:offline_sessions,id',
        ],[
            'meeting_id.exists' => 'Live streaming ID is invalid',
            'offline_session_id.exists' => 'In-Person Session ID is invalid',
        ]);

        $newRequest = new Request();

        if($request->meeting_id){
            $sessions = BBL::where('id',$request->meeting_id)->whereColumn('order_count', '<', 'setMaxParticipants')->get();
            
        }elseif($request->offline_session_id){
            $sessions = OfflineSession::where('id',$request->offline_session_id)->whereColumn('order_count', '<', 'setMaxParticipants')->get();
        }
        
        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : NULL;

        if($user){
            $enrolled = SessionEnrollment::where('user_id', $user->id)
                            ->when($request->meeting_id, function($query) use($request) {
                                $query->where('meeting_id', $request->meeting_id);
                            })
                            ->when($request->offline_session_id, function($query) use($request) {
                                $query->where('offline_session_id', $request->offline_session_id);
                            })
                            ->active()
                            ->first();
        }

        if(isset($enrolled)){
            return response()->json('You already enrolled this session', 200);
            
        }elseif($sessions->isNotEmpty()){
            
            SessionEnrollment::create([
                'meeting_id' => $request->meeting_id?? NULL,
                'offline_session_id' => $request->offline_session_id?? NULL,
                'user_id' => Auth::guard('api')->id(),
                'status' => '1',
            ]);

            if ($request->meeting_id) {
                Wishlist::where(['meeting_id'=> $request->meeting_id,'user_id' => $user->id])->delete();
                BBL::find($request->meeting_id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order

                return $this->meetingdetail($newRequest, $request->meeting_id);

            } elseif ($request->offline_session_id) {
                Wishlist::where(['offline_session_id'=> $request->offline_session_id,'user_id' => $user->id])->delete();
                OfflineSession::find($request->offline_session_id)->increment('order_count', 1); // Increment numbers of participants has been enrolled after successfull order

                return $this->sessiondetail($newRequest, $request->offline_session_id);
            }
              
        }else{
            return response()->json(['errors'=>['message'=>['Session seats not available anymore']]], 422);
        }
    }


    public function streamingbookinglist()
    {
    //    $meeting_orders = Order::has('meeting')->where('user_id', Auth::id())->where('status', '1')->latest('id')->paginate(10);
       $meeting_orders = SessionEnrollment::whereHas('meeting', function($query) {
                                                $query->where('expire_date', '>=', date('Y-m-d'));
                                            })
                                            ->where('user_id', Auth::id())
                                            ->active()
                                            ->latest('id')
                                            ->paginate(10);

        $meeting_orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->meeting->id,
                'owner_id' => $order->meeting->owner_id,
                'instructor_id' => $order->meeting->instructor_id,
                'meeting_title' => $order->meeting->meetingname,
                'bigblue_meetingid' => $order->meeting->meetingid,
                'instructor' => $order->meeting->user->fname. ' ' .$order->meeting->user->lname,
                // 'date' => date('d M',strtotime($order->meeting->start_time)),
                // 'time' => date('h:i A',strtotime($order->meeting->start_time)),
                'date_time' => $order->meeting->start_time,
                'image' => url('images/bg/'.$order->meeting->image),
                'price' =>$order->meeting->price?$order->meeting->price:0,
            ];
        });

        return response()->json($meeting_orders, 200);
    }


    public function sessionbookinglist()
    {
    //    $session_orders = Order::has('offlineSession')->where('user_id', Auth::id())->where('status', '1')->latest('id')->paginate(10);
       $session_orders = SessionEnrollment::whereHas('offlinesession', function($query) {
                                                    $query->where('expire_date', '>=', date('Y-m-d'));
                                                })
                                                ->where('user_id', Auth::id())
                                                ->active()
                                                ->latest('id')
                                                ->paginate(10);

        $session_orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->offline_session_id,
                'owner_id' => $order->offlinesession->owner_id,
                'instructor_id' => $order->offlinesession->instructor_id,
                'meeting_title' => $order->offlinesession->title,
                'bigblue_meetingid' => null,
                'instructor' => $order->offlinesession->user->fname. ' ' .$order->offlinesession->user->lname,
                'date_time' => $order->offlinesession->start_time,
                'image' => url('images/offlinesession/'.$order->offlinesession->image),
                'price' =>$order->offlinesession->price?$order->offlinesession->price:0,
            ];
        });

        return response()->json($session_orders, 200);
    }


    public function meetingdetail(Request $request, $id){
      
        if($request->type === "course") {
            $bbl_meeting = $m = BBL::find($id);

            if(!$bbl_meeting->chapter){
                return response()->json(array("errors"=>["message"=>[__("Live Streaming not exist OR may have been ended.")]]), 422);
            }

        } else {
            $bbl_meeting = $m = BBL::active()->find($id);
    
            if(!$bbl_meeting){
                return response()->json(array("errors"=>["message"=>[__("Live Streaming not exist OR may have been ended.")]]), 422);
            }
        }

        $order =  null;
        $course_order =  null;
        $progress = null;
        
        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : NULL;

        if($user){
            $order =  SessionEnrollment::where(['user_id' => $user->id, 'meeting_id' => $id])
                        ->active()
                        ->first();
        }

        if(!$order && ($user && $bbl_meeting->course_id)){

            $course_order = Order::where(['user_id' => $user->id, 'course_id' => $bbl_meeting->course_id, 'status' => '1'])->first();

            if(!$course_order){
                $course_order = Order::whereJsonContains('bundle_course_id', strval($bbl_meeting->course_id))->where(['user_id' => $user->id, 'status' => '1'])->first();
            }

            if($course_order){
                if ($course_order->total_amount == ($course_order->paid_amount + $course_order->coupon_discount)) {
                    $is_lock = 3;
                    
                } elseif ($course_order->installments && (($course_order->paid_amount + $course_order->coupon_discount) > 0)) {
                    $paid = $course_order->paid_amount + $course_order->coupon_discount;
                    $p_inst = $course_order->installments_list ? $course_order->installments_list->count() : 0;
                    $is_lock = 0;

                    foreach ($course_order->payment_plan as $i) {
                        if ($paid > 0) {
                            $is_lock++;
                        }
                        $paid -= $i->amount;
                    }
                    if ($p_inst > $is_lock) {
                        $is_lock = $p_inst;
                    }
                } else {
                    $is_lock = 0;
                }
            }else {
                $is_lock = 0;
            }
        } else {
            $is_lock = 0;
        }

        
        $bbb = new BigBlueButton();
        $url = null;
        if ($m && !$m->is_ended && $user && $order) {
            $joinMeetingParams = new JoinMeetingParameters($m->meetingid, $m->meetingname, $m->attendeepw);
            $joinMeetingParams->setUsername($user->fname.' '.$user->lname);
            $joinMeetingParams->setRedirect(true);
            $url = $bbb->getJoinMeetingURL($joinMeetingParams);

        }elseif ($m && $m->is_ended && $user && $order) {
            $recordingParams = new GetRecordingsParameters();
            $recordingParams->setMeetingId($m->meetingid);
            $recordingParams->setState('processing,processed,published');
            $response = $bbb->getRecordings($recordingParams);

            if ($response->getReturnCode() == 'SUCCESS') {
                $array = json_decode(json_encode((array)$response->getRawXml()->recordings), TRUE);

                if(is_array($array) && isset($array["recording"]) && isset($array["recording"]["playback"]) && isset($array["recording"]["playback"]["format"]) && isset($array["recording"]["playback"]["format"]["url"])){
                    $url[] = $array["recording"]["playback"]["format"]["url"];//??"";

                }elseif(is_array($array) && isset($array["recording"])){
                    foreach ($array["recording"] as $meeting) {
                        if(isset($meeting["playback"])){
                            if(isset($meeting["playback"]["format"])){
                                if(isset($meeting["playback"]["format"]["url"])){
                                    $url[] = $meeting["playback"]["format"]["url"];
                                }else{
                                    $url[] = "URL doesn't exist";
                                }
                            }else{
                                $url[] = "URL doesn't exist";
                            }
                        }else{
                            $url[] = "URL doesn't exist";
                        }
                    }
                }
                //  dd('no array');
            }
        }
        //dd(gettype($url));
        // $newURL = "";
          
        if (getType($url) == "array") {
            $url = $url == null ? null : array_filter($url);
        }
        // elseif (getType($url) == "string") {
        //   $newURL = $url;
        // }
        // else {
        //   throw new Exception("Issue in type of meeting returned to client | ERR:ZK:MT:101", 1);
        // }
    
        $resp = [
            'id' => $bbl_meeting->id,
            'link_by' => [
                'course_id' => $bbl_meeting->course_id?? NULL,
                'course_name' => $bbl_meeting->course_id ? $bbl_meeting->course->title : NULL,
            ],
            'isPartOfCourse' => $bbl_meeting->course_id ? true:false,
            'order_id'=>$order ? $order->id : null,
            'is_purchased' => $order ? true : ($is_lock > 0 && (isset($bbl_meeting->chapter->unlock_installment) && $bbl_meeting->chapter->unlock_installment <= $is_lock) ? true : false),
            'owner_id' => $bbl_meeting->owner_id,
            'is_started' => $bbl_meeting->is_started,
            'is_ended' => $bbl_meeting->is_ended?true:false,
            'is_cart' => $user ? ($user->cartType('meeting',$bbl_meeting->id)->exists()? true : false) : false,
            'rec_status' => $bbl_meeting->reco_status,
            'course_id' => $bbl_meeting->course_id,
            'class_id' => $bbl_meeting->courseclass ? $bbl_meeting->courseclass->id :null,
            "is_complete" => $progress && $bbl_meeting->courseclass && in_array($bbl_meeting->courseclass->id, $progress->mark_chapter_id) ? true : false,
            'instructor_id' => $bbl_meeting->instructor_id,
            'meeting_title' => $bbl_meeting->meetingname,
            'meeting_id' => $bbl_meeting->meetingid,
            'image' => url('/images/bg/'.$bbl_meeting->image),
            'in_wishlist' => $user ? ($bbl_meeting->inwishlist($user->id) ? true : false) : false,
            'location' => null,
            'google_map_link' => null,
            'agenda' => $bbl_meeting->detail,
            // 'date' => date('d M, Y',strtotime($bbl_meeting->start_time)),
            // 'time' => date('h:i A',strtotime($bbl_meeting->start_time)),
            'date_time' => $bbl_meeting->start_time,
            'url' => $url,
            // 'price' =>$bbl_meeting->price??0,
            'discount_price' =>$bbl_meeting->discount_price,
            'instructor' => [
            'id' => $bbl_meeting->user->id,
            'name' => $bbl_meeting->user->fname. ' ' .$bbl_meeting->user->lname,
            'image' => url('/images/user_img/'.$bbl_meeting->user->user_img),
            'short_info' => $bbl_meeting->user->short_info
            ]
            
        ];
      
      return response()->json($resp, 200);
    }


    public function sessiondetail(Request $request, $id){

        if($request->type === "course") {
            $session = OfflineSession::find($id);

            if(!$session->chapter){
                return response()->json(array("errors"=>["message"=>[__("In-person session not exist OR may have been ended.")]]), 422);
            }

        } else {
            $session = OfflineSession::active()->find($id);
    
            if(!$session){
                return response()->json(array("errors"=>["message"=>[__("In-person session not exist OR may have been ended.")]]), 422);
            }
        }

        $order =  NULL;
        $course_order =  NULL;
        
        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : NULL;

        if($user){
            $order =  SessionEnrollment::where(['user_id' => $user->id, 'offline_session_id' => $id])
                        ->active()
                        ->first();
        }

        if(!$order && ($user && $session->course_id)){
            
            $course_order = Order::where(['user_id' => $user->id, 'course_id' => $session->course_id, 'status' => '1'])->first();

            if(!$course_order){
                $course_order = Order::whereJsonContains('bundle_course_id', strval($session->course_id))->where(['user_id' => $user->id, 'status' => '1'])->first();
            }

            if($course_order){
                if ($course_order->total_amount == ($course_order->paid_amount + $course_order->coupon_discount)) {
                    $is_lock = 3;
                    
                } elseif ($course_order->installments && (($course_order->paid_amount + $course_order->coupon_discount) > 0)) {
                    $paid = $course_order->paid_amount + $course_order->coupon_discount;
                    $p_inst = $course_order->installments_list ? $course_order->installments_list->count() : 0;
                    $is_lock = 0;

                    foreach ($course_order->payment_plan as $i) {
                        if ($paid > 0) {
                            $is_lock++;
                        }
                        $paid -= $i->amount;
                    }
                    if ($p_inst > $is_lock) {
                        $is_lock = $p_inst;
                    }
                } else {
                    $is_lock = 0;
                }
            }else {
                $is_lock = 0;
            }
        } else {
            $is_lock = 0;
        }

    
        $resp = [
            'id' => $session->id,
            'link_by' => [
                'course_id' => $session->course_id?? NULL,
                'course_name' => $session->course_id ? $session->course->title : NULL,
            ],
            'isPartOfCourse' => $session->course_id ? true:false,
            'order_id'=>$order ? $order->id : NULL,
            'is_purchased' => $order ? true : ($is_lock > 0 && (isset($session->chapter->unlock_installment) && $session->chapter->unlock_installment <= $is_lock) ? true : false),
            'owner_id' => $session->owner_id,
            'is_started' => null,
            'is_ended' => null,
            'rec_status' => null,
            'course_id' => $session->course_id,
            'class_id' => $session->courseclass ? $session->courseclass->id :null,
            "is_complete" => null,
            'instructor_id' => $session->instructor_id,
            'meeting_title' => $session->title,
            'meeting_id' => null,
            'image' => url('/images/offlinesession/'.$session->image),
            'in_wishlist' => $user ? ($session->inwishlist($user->id) ? true : false) : false,
            'location' => $order ? $session->location : NULl,
            'google_map_link' => $order ? $session->google_map_link : NULL,
            'agenda' => $session->detail,
            'date_time' => $session->start_time,
            'url' => null,
            'price' =>$session->price,
            'discount_price' =>$session->discount_price,
            'instructor' => [
                'id' => $session->user->id,
                'name' => $session->user->fname. ' ' .$session->user->lname,
                'image' => url('/images/user_img/'.$session->user->user_img),
                'short_info' => $session->user->short_info
            ]
        ];
      
      return response()->json($resp, 200);
    }
    

    public function userbankdetail(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }


        $user = Auth::user();
        $banks = UserBankDetail::where('user_id', $user->id)->get();


        return response()->json(array('user_bankdetail' => $banks), 200);


    }

    public function addbankdetail(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
            'bank_name' => 'required',
            'ifcs_code' => 'required',
            'account_number' => 'required',
            'account_holder_name' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }

            if($errors->first('bank_name')){
                return response()->json(['message' => $errors->first('bank_name'), 'status' => 'fail']);
            }

            if($errors->first('ifcs_code')){
                return response()->json(['message' => $errors->first('ifcs_code'), 'status' => 'fail']);
            }

            if($errors->first('account_number')){
                return response()->json(['message' => $errors->first('account_number'), 'status' => 'fail']);
            }

            if($errors->first('account_holder_name')){
                return response()->json(['message' => $errors->first('account_holder_name'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();

        $bank = new UserBankDetail;
        $bank->user_id = $user->id;
        $bank->bank_name = $request->bank_name;
        $bank->ifcs_code = $request->ifcs_code;
        $bank->account_number = $request->account_number;
        $bank->account_holder_name = $request->account_holder_name;
        $bank->bank_enable = 1;

        $bank->save();


        return response()->json(array('message' => 'Your bank detail has been added successfully', 'status' => 'success'), 200);
    }

    public function updatebankdetail(Request $request, $id)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }

        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();

        if (UserBankDetail::where('id', $id)->exists()) {
            $data = UserBankDetail::find($id);

            $data->user_id = isset($request->user_id) ? $request->user_id : $data->user_id;
            $data->bank_name = isset($request->bank_name) ? $request->bank_name : $data->bank_name;
            $data->ifcs_code = isset($request->ifcs_code) ? $request->ifcs_code : $data->ifcs_code;
            $data->account_number = isset($request->account_number) ? $request->account_number : $data->account_number;
            $data->account_holder_name = isset($request->account_holder_name) ? $request->account_holder_name : $data->account_holder_name;

            $data->status = isset($request->bank_enable) ? $request->bank_enable : $data->bank_enable;
            $data->save();

            return response()->json([
              "message" => "updated successfully",
              'record'=>$data
            ]);
        } else {
            return response()->json([
              "message" => "data not found"
            ], 404);
        }


        return response()->json(array('message' => 'Your bank detail has been added successfully', 'status' => 'success'), 200);
    }


    public function updatelanguage(Request $request, $id) {

        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (UserBankDetail::where('id', $id)->exists()) {
            $userBank = UserBankDetail::find($id);

            $userBank->bank_name = isset($request->bank_name) ? $request->bank_name : $userBank->bank_name;
            $userBank->ifcs_code = isset($request->ifcs_code) ? $request->ifcs_code : $userBank->ifcs_code;
            $userBank->account_number = isset($request->account_number) ? $request->account_number : $userBank->account_number;
            $userBank->account_holder_name = isset($request->account_holder_name) ? $request->account_holder_name : $userBank->account_holder_name;

            $userBank->save();

            return response()->json([
              "message" => "records updated successfully",
              'userBank'=>$userBank
            ]);
        } 
        else {
            return response()->json([
              "message" => "record not found"
            ], 404);
        }
    }

    public function widget(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }


        $widget = WidgetSetting::first();

        return response()->json(array('widget' => $widget), 200);
    }

    public function addwatchlist(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
            'course_id' => 'required',
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

        $user = Auth::user();

        $watch = WatchCourse::create([
            'user_id'    => $user->id,
            'course_id'  => $request->course_id,
            'start_time' => \Carbon\Carbon::now()->toDateTimeString(),
            'active'     => '1',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );

        return response()->json(array('watchlist' => $watch), 200);
    }

    public function viewwatchlist(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();

        $watch = WatchCourse::where('user_id', $user->id)->get();

        return response()->json(array('watchlist' => $watch), 200);
    }

    public function deletewatchlist(Request $request)
    {
        $validator = $this->validate($request, [
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

        $user = Auth::user();

        if(WatchCourse::where('course_id', $request->course_id)->where('user_id', $user->id)->exists()) {
            WatchCourse::where('course_id', $request->course_id)->where('user_id', $user->id)->delete();

            return response()->json([
              "message" => "records deleted"
            ]);

        } else {
            return response()->json([
              "message" => "record not found"
            ], 404);
        }
    }

    public function manual(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        } 

        $payments = ManualPayment::get();

        $result = array();

        foreach ($payments as $data) {

            $result[] = array(
                'id' => $data->id,
                'name' => $data->name,
                'detail' => strip_tags($data->detail),
                'image' => $data->image,
                'image_path' => url('images/manualpayment/'.$data->image),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('manual_payment' => $result), 200);

    }


    public function attandance(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }
        $user = Auth::user();
        $date = Carbon::now();
            //Get date
        $date->toDateString();
        $zoom = Meeting::where('id', $request->meeting_id)->first();
        if($request->meeting_type == '1')
        {
            $courseAttandance = Attandance::where('user_id', $user->id)->where('zoom_id', $request->meeting_id)->first();
            if(!$courseAttandance)
            {
                $attanded = Attandance::create([
                    'user_id'    => Auth::user()->id,
                    'zoom_id'  => $zoom->id,
                    'instructor_id' => $zoom->user_id,
                    'date'     => $date->toDateString(),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );
                return response()->json(array('attanded'=>$attanded));
            }
        }
        $googlemeet = Googlemeet::where('id', $request->meeting_id)->first();
        if($request->meeting_type == '2')
        {
            $courseAttandance = Attandance::where('user_id', $user->id)->where('googlemeet_id', $request->meeting_id)->first();
            if(!$courseAttandance)
            {
                $attanded = Attandance::create([
                    'user_id'    => Auth::user()->id,
                    'zoom_id'  => $googlemeet->id,
                    'instructor_id' => $googlemeet->user_id,
                    'date'     => $date->toDateString(),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );
                return response()->json(array('attanded'=>$attanded));
            }
        }
        $jitsimeetings = JitsiMeeting::where('meeting_id', '=', $request->meeting_id)->first();
        if($request->meeting_type == '3')
        {
            $courseAttandance = Attandance::where('user_id', $user->id)->where('jitsi_id', $request->meeting_id)->first();
            if(!$courseAttandance)
            {
                $attanded = Attandance::create([
                    'user_id'    => Auth::user()->id,
                    'zoom_id'  => $jitsimeetings->id,
                    'instructor_id' => $jitsimeetings->user_id,
                    'date'     => $date->toDateString(),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );
                return response()->json(array('attanded'=>$attanded));
            }
        }
        $bigblue = BBL::where('meetingid',$request->meeting_id)->first();
        if($request->meeting_type == '4')
        {
            $courseAttandance = Attandance::where('user_id', $user->id)->where('bbl_id', $request->meeting_id)->first();
            if(!$courseAttandance)
            {
                $attanded = Attandance::create([
                    'user_id'    => Auth::user()->id,
                    'zoom_id'  => $bigblue->id,
                    'instructor_id' => $bigblue->user_id,
                    'date'     => $date->toDateString(),
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    ]
                );
                return response()->json(array('attanded'=>$attanded));
            }
        }
    }


    public function currencies(Request $request)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $currencies = Currency::get();

        return response()->json(array('currencies'=>$currencies), 200);
    }

    public function currency_rates(Request $request, $code)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
            'amount' => 'required',
            'currency_from' => 'required',
            'currency_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required']);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $currency = currency($request->price, $from = $currency_from, $to = $currency_to, $format = true);

        return response()->json(array('currency'=>$currency), 200);
    }

    public function getAffiliate(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $user = Auth::user();
        $Affiliate = Affiliate::get();
        return response()->json(array('Affiliate' => $Affiliate), 200);
    }

    public function getInstitute(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $user = Auth::user();
        $Institute = Institute::where('user_id', $user->id)->get();
        return response()->json(array('Institute' => $Institute), 200);
    }

    public function getHomework(Request $request)
    {
        $validator = $this->validate($request, [
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
        $user = Auth::user();
       
        $Homework = Homework::select('homework.id as id','homework.title as title','homework.description as description','homework.pdf as pdf','homework.compulsory as compulsory', 'submit_homework.homework as homework','submit_homework.remark as remark','submit_homework.marks as marks',\DB::raw('(CASE 
        WHEN submit_homework.id IS NULL THEN "0" ELSE "1" END) AS is_submit'),DB::raw("DATEDIFF(homework.endtime,CURDATE())AS Days"))->
        leftJoin('submit_homework','submit_homework.homework_id','=','homework.id')->where('homework.status','=', 1)->where('homework.course_id', $request->course_id)->get();

        return response()->json(array('Homework' => $Homework), 200);
    }


    public function submitHomework(Request $request)
    {
        $validator = $this->validate($request, [
            'homework_id' => 'required',
    		'course_id' => 'required',
    		'detail' => 'required',
    		'homework' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('homework_id')){
                return response()->json(['message' => $errors->first('homework_id'), 'status' => 'fail']);
            }

            if($errors->first('course_id')){
                return response()->json(['message' => $errors->first('course_id'), 'status' => 'fail']);
            }

            if($errors->first('detail')){
                return response()->json(['message' => $errors->first('detail'), 'status' => 'fail']);
            }

            if($errors->first('homework')){
                return response()->json(['message' => $errors->first('homework'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();
        $filename = '';
        if($file = $request->file('homework'))
        {
            
          $filename = time().$file->getClientOriginalName();
          $file->move('files/Homework/',$filename);
          $courseclass['homework'] = $filename;
        }
        $submitHomework = SubmitHomework::create([
            'user_id'    => $user->id,
            'homework_id' => $request->homework_id,
            'course_id'  => $request->course_id,
            'detail'  => $request->detail,
            'homework' => $filename,
            ]
        );
        return response()->json(array('submitHomework' => $submitHomework), 200);
    }


    public function getSpecificHomework(Request $request, $id)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        if (Homework::where('id', $id)->exists()) {
            $user = Auth::user();
            $Homework = Homework::select('homework.id as id','homework.pdf as pdf')->where('id', $id)->get();
            return response()->json(array('Homework' => $Homework), 200);
        }
        else {
            return response()->json([
              "message" => "data not found"
            ], 404);
        }
    }

    public function getAnswer(Request $request, $id)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        if (Homework::where('id', $id)->exists()) {
            $user = Auth::user();
            $Homework = SubmitHomework::select('submit_homework.id as id','submit_homework.homework as answer')->where('id', $id)->get();
            return response()->json(array('Answer' => $Homework), 200);
        }
        else {
            return response()->json([
              "message" => "data not found"
            ], 404);
        }
    }

    public function getCertificate(Request $request, $course_id)
    {
        $user = Auth::user();

        $random = $request.'CR-'.uniqid();

        $serial_no = $random;

        $whatIWant = strtok($random, 'CR-'); 
    
        $progress = CourseProgress::where('user_id', $user->id)->where('course_id', $course_id)->first();

        $course = Course::where('id', $progress->course_id)->first();

        if($progress == NULL)
        {
            return response()->json(['Please Complete your course to get certificate !'], 400); 
        }
        
        
        $pdf = PDF::loadView('front.certificate.download', compact('course', 'progress', 'serial_no'), [], 
        [ 
          'title' => 'Certificate', 
          'orientation' => 'L'
        ]);
        
        // $pdf->save(storage_path().'/app/pdf/certificate.pdf');
        
        return $pdf->download('certificate.pdf');

    }
    
    public function setting(){
        $setting = \App\Setting::first();
        $w_s = \App\WalletSettings::first();
        $currency = Currency::where('default', '=', '1')->first();
        
        $data = [
            'currency'=>$currency->code,
            'currency_symbol'=>$currency->symbol,
            'wallet_payment'=>$w_s->status?true:false,
            'login_with_email'=>$setting->login_email,
            'login_with_mobile'=>$setting->login_mobile
        ];
        return response()->json(array('setting'=>$data),200);
    }
    
    public function homeModules(){

        $setting = Setting::first();
        $is_homemodule = 0;
        $is_resumemodules = 0;
        $is_certicifate = 0;
        $is_forum = 0;
        // $home_modules = array();
        if(Module::has('Homework') &&  Module::find('Homework')->isEnabled()){
            $is_homemodule=1;
        }
        if(Module::has('Resume') &&  Module::find('Resume')->isEnabled()){
            $is_resumemodules=1;
        }
        if(Module::has('Certificate') &&  Module::find('Certificate')->isEnabled()){
            $is_certicifate=1;
        }
        if(Module::has('Forum') &&  Module::find('Forum')->isEnabled() && $setting->forum_enable==1){
            $is_forum=1;
        }
        // $home_modules = array('Homework'=> $is_homemodule, 'Resume'=> $is_resumemodules, 'Certificate'=> $is_certicifate, 'forum'=> $is_forum);
        return response()->json(array('Homework'=> $is_homemodule, 'Resume'=> $is_resumemodules, 'Certificate'=> $is_certicifate, 'Forum'=> $is_forum), 200);
    }

    public function addResumeDetails(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
            'fname' => 'required',
            'lname' => 'required',
            'profession' => 'required',
            'country' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'skill' => 'required',
            'strength' => 'required',
            'interest' => 'required',
            'objective' => 'required',
            'language' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }

            if($errors->first('fname')){
                return response()->json(['message' => $errors->first('fname'), 'status' => 'fail']);
            }

            if($errors->first('lname')){
                return response()->json(['message' => $errors->first('lname'), 'status' => 'fail']);
            }

            if($errors->first('profession')){
                return response()->json(['message' => $errors->first('profession'), 'status' => 'fail']);
            }

            if($errors->first('country')){
                return response()->json(['message' => $errors->first('country'), 'status' => 'fail']);
            }

            if($errors->first('address')){
                return response()->json(['message' => $errors->first('address'), 'status' => 'fail']);
            }
            
            if($errors->first('phone')){
                return response()->json(['message' => $errors->first('phone'), 'status' => 'fail']);
            }

            if($errors->first('email')){
                return response()->json(['message' => $errors->first('email'), 'status' => 'fail']);
            }

            if($errors->first('skill')){
                return response()->json(['message' => $errors->first('skill'), 'status' => 'fail']);
            }

            if($errors->first('strength')){
                return response()->json(['message' => $errors->first('strength'), 'status' => 'fail']);
            }

            if($errors->first('interest')){
                return response()->json(['message' => $errors->first('interest'), 'status' => 'fail']);
            }

            if($errors->first('objective')){
                return response()->json(['message' => $errors->first('objective'), 'status' => 'fail']);
            }

            if($errors->first('language')){
                return response()->json(['message' => $errors->first('language'), 'status' => 'fail']);
            }
        }

        $persoanl['fname']      = strip_tags($request->fname);
        $persoanl['lname']      = strip_tags($request->lname);
        $persoanl['profession'] = strip_tags($request->profession);
        $persoanl['country']    = strip_tags($request->country);
        $persoanl['address']    = strip_tags($request->address);
        $persoanl['phone']      = strip_tags($request->phone);
        $persoanl['email']      = strip_tags($request->email);
        $persoanl['skill']      = strip_tags($request->skill);
        $persoanl['strength']   = strip_tags($request->strength);
        $persoanl['interest']   = strip_tags($request->interest);
        $persoanl['objective']  = strip_tags($request->objective);
        $persoanl['language']   = strip_tags($request->language);
        if ($file = $request->file('photo'))
        {
            $validator = Validator::make(
                [
                    'file' => strip_tags($request->photo),
                    'extension' => strtolower($request->photo->getClientOriginalExtension()),
                ],
                [
                    'file' => 'required',
                    'extension' => 'required|in:jpg,png',
                ]
            );
            if ($validator->fails()) {
                return back()->withErrors(__('Invalid file !'));
            }
            if ($file = $request->file('photo')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('files/resume', $name);
                $persoanl['image'] = $name;
            }
        }
    
        /** foreach for acedmic **/
        if(!empty($request->course))
        {
            foreach ($request->course as $key => $course) {
                
            Acedemic::create([
                'user_id'       => Auth::user()->id,
                'course'        => strip_tags($request->course[$key]),
                'school'        => strip_tags($request->school[$key]),
                'marks'         => strip_tags($request->marks[$key]),
                'yearofpassing' => strip_tags($request->yearofpassing[$key]),
                ]);
            }
        }
        /** foreach for workexp **/
        if(!empty($request->startdate))
        {
        foreach ($request->startdate as $key => $course) {
            Workexp::create([
                'user_id'       => Auth::user()->id,
                'startdate'     => strip_tags($request->startdate[$key]),
                'enddate'       => strip_tags($request->enddate[$key]),
                'city'          => strip_tags($request->city[$key]),
                'state'         => strip_tags($request->state[$key]),
                'jobtitle'      => strip_tags($request->jobtitle[$key]),
                'employer'      => strip_tags($request->employer[$key]),
                ]);
            }
        }

        /** foreach for project **/
        if(!empty($request->projecttitle))
        {
        foreach ($request->projecttitle as $key => $course) {
            Project::create([
                'user_id' => Auth::user()->id,
                'projecttitle' => strip_tags($request->projecttitle[$key]),
                'role' => strip_tags($request->role[$key]),
                'description' => strip_tags($request->description[$key]),
               ]);
            }
        }
        $data=Personalinfo::create($persoanl);
        
        return response()->json(array('Create Resume Details' => $data), 200);
    }

    public function updateResumeDetails(Request $request, $id)
    {
        $data = Personalinfo::where('user_id', $id)->first();
        $persoanl['fname']      = strip_tags($request->fname);
        $persoanl['lname']      = strip_tags($request->lname);
        $persoanl['profession'] = strip_tags($request->profession);
        $persoanl['country']    = strip_tags($request->country);
        $persoanl['address']    = strip_tags($request->address);
        $persoanl['phone']      = strip_tags($request->phone);
        $persoanl['email']      = strip_tags($request->email);
        $persoanl['skill']      = strip_tags($request->skill);
        $persoanl['strength']   = strip_tags($request->strength);
        $persoanl['interest']   = strip_tags($request->interest);
        $persoanl['objective']  = strip_tags($request->objective);
        $persoanl['language']   = strip_tags($request->language);
        if ($file = $request->file('photo'))
        {
            $validator = Validator::make(
                [
                    'file' => strip_tags($request->photo),
                    'extension' => strtolower($request->photo->getClientOriginalExtension()),
                ],
                [
                    'file' => 'required',
                    'extension' => 'required|in:jpg,png',
                ]
            );
            if ($validator->fails()) {
                return back()->withErrors(__('Invalid file !'));
            }
            if ($file = $request->file('photo')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('files/resume', $name);
                $persoanl['image'] = $name;
            }
        }
    
        /** foreach for acedmic **/
        if(!empty($request->course))
        {
            Acedemic::where('user_id', Auth::user()->id)->delete();
            foreach ($request->course as $key => $course) {
                
            Acedemic::create([
                'user_id'       => Auth::user()->id,
                'course'        => strip_tags($request->course[$key]),
                'school'        => strip_tags($request->school[$key]),
                'marks'         => strip_tags($request->marks[$key]),
                'yearofpassing' => strip_tags($request->yearofpassing[$key]),
                ]);
            }
        }
        /** foreach for workexp **/
        if(!empty($request->startdate))
        {
        Workexp::where('user_id', Auth::user()->id)->delete();
        foreach ($request->startdate as $key => $course) {
            Workexp::create([
                'user_id'       => Auth::user()->id,
                'startdate'     => strip_tags($request->startdate[$key]),
                'enddate'       => strip_tags($request->enddate[$key]),
                'city'          => strip_tags($request->city[$key]),
                'state'         => strip_tags($request->state[$key]),
                'jobtitle'      => strip_tags($request->jobtitle[$key]),
                'employer'      => strip_tags($request->employer[$key]),
                ]);
            }
        }

        /** foreach for project **/
        if(!empty($request->projecttitle))
        {
        Project::where('user_id', Auth::user()->id)->delete();
        foreach ($request->projecttitle as $key => $course) {
            Project::create([
                'user_id' => Auth::user()->id,
                'projecttitle' => strip_tags($request->projecttitle[$key]),
                'role' => strip_tags($request->role[$key]),
                'description' => strip_tags($request->description[$key]),
               ]);
            }
        }
        $data->update($persoanl);
        
        Session::flash('success', __('Resume edit successfully'));
        return response()->json(array('Update Resume Details' => $data), 200);
    }

    public function viewResumeDetails(Request $request, $id)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $persoanl = Personalinfo::where('user_id', $id)->firstorfail();
        $works = Workexp::where('user_id', $id)->get();
        $education = Acedemic::where('user_id', $id)->get();
        $project = Project::where('user_id', $id)->get();
       
        return response()->json(array('persoanl' => $persoanl, "works" => $works, "education" => $education, "project" => $project), 200);
    }


    public function createPostJob(Request $request)
    {
        $request->validate([
            'companyname' => 'required',
            'title' => 'required',
            'description' => 'required',
            'experience' => 'required',
            'minexp' => 'required',
            'maxexp' => 'required',
            'location' => 'required',
            'requirement' => 'required',
            'role' => 'required',
            'industry_type' => 'required',
            'employment_type' => 'required',
            'skills' => 'required',
        ]);

        $job['user_id'] = Auth::user()->id;
        $job['companyname'] = strip_tags($request->companyname);
        $job['title'] = strip_tags($request->title);
        $job['description'] = clean($request->description);
        $job['experience'] = strip_tags($request->experience);
        $job['min_experience'] = strip_tags($request->minexp);
        $job['max_experience'] = strip_tags($request->maxexp);
        $job['location'] = strip_tags($request->location);
        $job['requirement'] = strip_tags($request->requirement);
        $job['role'] = strip_tags($request->role);
        $job['industry_type'] = strip_tags($request->industry_type);
        $job['employment_type'] = strip_tags($request->employment_type);
        $job['salary'] = strip_tags($request->salary);
        $job['min_salary'] = strip_tags($request->minsalary);
        $job['max_salary'] = strip_tags($request->maxsalary);
        $job['skills'] = strip_tags($request->skills);

        if ($file = $request->file('image')) {

            $validator = Validator::make(
                [
                    'file' => strip_tags($request->image),
                    'extension' => strtolower($request->image->getClientOriginalExtension()),
                ],
                [
                    'file' => 'required',
                    'extension' => 'required|in:jpg,png',
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors(__('Invalid file !'));
            }

            if ($file = $request->file('image')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('files/job', $name);
                $job['image'] = $name;
            }
        }

        Postjob::create($job);
        $data=Postjob::create($job);
        return response()->json(array('Create Resume Details' => $data), 200);
    }

    public function JobList(Request $request, $id)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $jobs = Postjob::where('user_id', $id)->get();
        return response()->json(array("Posted Jobs" => $jobs), 200);
    }

    public function updateJobList(Request $request, $id)
    {
        $request->validate([
            'companyname' => 'required',
            'title' => 'required',
            'description' => 'required',
            'experience' => 'required',
            'minexp' => 'required',
            'maxexp' => 'required',
            'location' => 'required',
            'requirement' => 'required',
            'role' => 'required',
            'industry_type' => 'required',
            'employment_type' => 'required',
            'skills' => 'required',
        ]);

        $data = Postjob::where('id', $id)->first();
        $job['user_id'] = Auth::user()->id;
        $job['companyname'] = strip_tags($request->companyname);
        $job['title'] = strip_tags($request->title);
        $job['description'] = clean($request->description);
        $job['experience'] = strip_tags($request->experience);
        $job['min_experience'] = strip_tags($request->minexp);
        $job['max_experience'] = strip_tags($request->maxexp);
        $job['years'] = strip_tags($request->years);
        $job['location'] = strip_tags($request->location);
        $job['requirement'] = strip_tags($request->requirement);
        $job['role'] = strip_tags($request->role);
        $job['industry_type'] = strip_tags($request->industry_type);
        $job['employment_type'] = strip_tags($request->employment_type);
        $job['salary'] = strip_tags($request->salary);
        $job['min_salary'] = strip_tags($request->minsalary);
        $job['max_salary'] = strip_tags($request->maxsalary);
        $job['skills'] = strip_tags($request->skills);

        if ($file = $request->file('image')) {

            $validator = Validator::make(
                [
                    'file' => strip_tags($request->image),
                    'extension' => strtolower($request->image->getClientOriginalExtension()),
                ],
                [
                    'file' => 'required',
                    'extension' => 'required|in:jpg,png',
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors(__('Invalid file !'));
            }

            if ($file = $request->file('image')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('files/job', $name);
                $job['image'] = $name;
            }
        }
        
        $data->update($job);
        // Session::flash('success', __('Job update successfully'));
        return response()->json(array("Update Posted Jobs" => $data), 200);

    }


    public function Jobview(Request $request, $jobid)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $jobs = Postjob::where('id', $jobid)->get();
       // $job = Postjob::findorfail($jobid);
        return response()->json(array("view Jobs" => $jobs), 200);
    }

    public function jobdestroy(Request $request,$id)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);


        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $job = Postjob::where('id', $id)->first();
        if(!empty($job)){
            $job->postjob()->delete();
            $job->delete();
            return response()->json(['message' => 'Delete Successfully', 'status' => 'success']);
        }else{
            return response()->json(['message' => 'data not found', 'status' => 'fail']);  
        }    
    }


    public function userstatus(Request $request)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);


        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        $user = Auth::user();

        $job = Postjob::where('id', strip_tags($request->id))->where('user_id', $user->id)->first();
        if(!empty($job)){
            $job->status = strip_tags($request->status);
            $job->save();
            return response()->json(['message' => 'update status successfully', 'status' => 'success']);
        }else{
            return response()->json(['message' => 'data not found', 'status' => 'fail']);   
        }    
    }


    public function searchfind(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

        
        /* Intitialize Query Builder */
        $postjob = Postjob::query();

        if ($request->search) {

            $result = $postjob->where("skills", "LIKE", '%' . strip_tags($request->search) . '%')
                ->orWhere("companyname", "LIKE", '%' . strip_tags($request->search) . '%')
                ->orWhere("title", "LIKE", '%' . strip_tags($request->search) . '%');

        } else {
            $result = $postjob->where('status', '1')
                ->where('approved', '1')
                ->orderBy('id', 'DESC');
        }
        
            $result = $postjob->paginate(10);
          return response()->json(array("List of Jobs" => $result), 200);
   
    }

    public function locationfilter(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        
        if ($request->location) {

            $result = Postjob::whereIN('location', $request->location)
                ->where('status', '1')
                ->where('approved', '1')
                ->paginate(10);

        } else {
            $result = Postjob::where('status', '1')
                ->where('approved', '1')
                ->paginate(10);

        }
      
        return response()->json(array("List of Jobs" => $result), 200);
    }

    public function allcompanylist(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        
        $result = Postjob::select('companyname')->distinct()->get();
       
      
        return response()->json(array("List of Company" => $result), 200);
    }


    public function allcountrystatelist(Request $request)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        
        $result = Postjob::select('location')->distinct()->where('status', '1')
        ->where('approved', '1')->get();
       
        return response()->json(array("List of Countrystates" => $result), 200);
    }

    public function viewjobcreatedbyuser(Request $request, $jobid)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }
        $user = Auth::user();

        $jobs = Postjob::where('id', $jobid)->where('user_id', $user->id)->get();
       // $job = Postjob::findorfail($jobid);
        return response()->json(array("view Jobs" => $jobs), 200);
    }

    public function applyJobs(Request $request,$jobid)
    {
        $validator = $this->validate($request, [
            'secret' => 'required',
            'skills' => 'required',
            'experiense' => 'required',
            'years' => 'required',

        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
            if($errors->first('skills')){
                return response()->json(['message' => $errors->first('skills'), 'status' => 'fail']);
            }
            if($errors->first('experiense')){
                return response()->json(['message' => $errors->first('experiense'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }


        $applyjob['skills'] = strip_tags($request->skills);
        $applyjob['experiense'] = strip_tags($request->experience);
        $applyjob['years'] = strip_tags($request->years);
        $applyjob['job_id'] = $jobid;
        $applyjob['user_id'] = Auth::user()->id;

        if ($file = $request->file('pdf')) {

            $validator = Validator::make(
                [
                    'file' => strip_tags($request->pdf),
                    'extension' => strtolower($request->pdf->getClientOriginalExtension()),
                ],
                [
                    'file' => 'required',
                    'extension' => 'required|in:pdf',
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors(__('Invalid file !'));
            }

            if ($file = $request->file('pdf')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('files/applyjob', $name);
                $applyjob['pdf'] = $name;
            }
        }


        $applyjobs= Applyjob::create($applyjob);
        return response()->json(array("List of Apply Jobs" => $applyjobs), 200);
    }


     /**
     *  This function holds the functionality to  apply for job delete.
     *  @return response true
     *  @param $id
     */
    public function applyjobdestroy(Request $request,$id)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);


        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

       // Applyjob::where('id', $id)->delete();
        $user = Auth::user();
        $desjob = Applyjob::where('id', $id)->where('user_id',$user->id)->first();
        if(!empty($desjob)){
            $desjob->postjob()->delete();
            $desjob->delete();
            return response()->json(['message' => 'Delete Successfully', 'status' => 'success']);
        }else{
            return response()->json(['message' => 'data not found', 'status' => 'fail']);  
        }  
        
    }


    public function applyjoblist(Request $request)
    {

        $validator = $this->validate($request, [
            'secret' => 'required',
        ]);


        if ($validator->fails()) {

            $errors = $validator->errors();

            if($errors->first('secret')){
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !']);
        }

       // Applyjob::where('id', $id)->delete();
        $user = Auth::user();
        $applyjob = Applyjob::where('user_id',$user->id)->get();
        if(!empty($applyjob)){
            return response()->json(array("List of apply jobs" => $applyjob), 200);
        }else{
            return response()->json(['message' => 'data not found', 'status' => 'fail']);  
        }  
        
    }

    

}