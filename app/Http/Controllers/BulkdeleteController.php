<?php

namespace App\Http\Controllers;

use App\BBL;
use Session;
use App\City;
use App\State;
use App\Slider;
use App\Country;
use App\Meeting;
use App\Trusted;
use App\Googlemeet;
use App\Instructor;
use App\SliderFacts;
use App\Testimonial;
use App\CourseReport;
use App\JitsiMeeting;
use App\SeoDirectory;
use App\Advertisement;
use App\OfflineSession;
use App\QuestionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BulkdeleteController extends Controller
{
    public function sliderdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('info',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Slider::whereIn('id',$request->checked)->delete();
                
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function factssliderdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
              return back()->with('warning', 'Atleast one item is required to be checked');
            }
            else{
                SliderFacts::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function trustsliderdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Trusted::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function testimonaldeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Testimonial::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
                
            }

        }
    public function advertismentdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Advertisement::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function seodirectorydeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                SeoDirectory::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
            
    public function reportedcoursedeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                CourseReport::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }

        public function reportedquestiondeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                QuestionReport::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
          

    public function countrydeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                $daa = new Country;
                State::where('country_id', $obj->country_id)->delete();
                City::where('country_id', $obj->country_id)->delete();
                Country::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();

                
            }

        }
    public function statedeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
              State::whereIn('id',$request->checked)->delete();
              Session::flash('success',trans('Deleted Successfully'));
              return redirect()->back();
                
            }

        }
    public function citydeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                City::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function instructorrequestdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Instructor::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function instructorpendingdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Instructor::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function ZoommeetingdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
              Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Meeting::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function googlemeetingdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                Googlemeet::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }
    public function bblmeetingdelete($meetingid) 
        {
            $meeting = BBL::find($meetingid);
            $order = \App\Order::where('meeting_id', $meetingid)->where('status', 1)->get();
            $orders = $meeting->courseclass;
            if ($order->count() || !empty($orders)) {
                return back()->with('delete', trans('flash.MeetingCannotDelete'));
            } elseif (isset($meeting)) {
                \App\Wishlist::where('meeting_id', $meetingid)->delete();
                $meeting->delete();
                return back()->with('delete', trans('flash.DeletedSuccessfully'));
            } else {
                return back()->with('delete', __('Live Streaming not found !'));
            }
        }
        
    public function bblmeetingdeleteAll(Request $request)
        
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
            
            if ($validator->fails()) {
                
                Session::flash('warning',trans('Please select atleast one to delete'));
                return redirect()->back();
            }
            else{
                foreach($request->checked as $id){
                    $this->bblmeetingdelete($id);
                }
                // BBL::whereIn('id',$request->checked)->delete();
            }
            // Session::flash('success',trans('Deleted Successfully'));
            return back();
            
        }
        
    public function offlineSessiondelete($sessionid) 
        {
            $session = OfflineSession::find($sessionid);
            $order = \App\Order::where('offline_session_id', $sessionid)->where('status', 1)->get();
            $orders = $session->courseclass;
            if ($order->count() || !empty($orders)) {
                return back()->with('delete', trans('flash.SessionCannotDelete'));
            } elseif (isset($session)) {
                \App\Wishlist::where('offline_session_id', $sessionid)->delete();
                $session->delete();
                return back()->with('delete', trans('flash.DeletedSuccessfully'));
            } else {
                return back()->with('delete', __('Offline Session not found !'));
            }
        }

    public function sessiondeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
                Session::flash('warning',trans('Please select atleast one to delete'));
               return back();
            }
            else{
                foreach($request->checked as $id){
                    $this->offlineSessiondelete($id);
                }
            }
           return back();
        }

    public function jitsimeetingdeleteAll(Request $request)
    
        {
            $validator = Validator::make($request->all(), [
                'checked' => 'required',
            ]);
    
            if ($validator->fails()) {
    
               Session::flash('warning',trans('Please select delete'));
               return redirect()->back();
            }
            else{
                JitsiMeeting::whereIn('id',$request->checked)->delete();
                Session::flash('success',trans('Deleted Successfully'));
                return redirect()->back();
                
            }

        }      
}
