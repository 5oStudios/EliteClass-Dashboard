<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\Course;
use Illuminate\Http\Request;
use Ladumor\OneSignal\OneSignal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OfferPushNotifications;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;


class OneSignalNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:push-notification.manage', ['only' => ['index', 'updateKeys', 'push']]);
    }


    public function index()
    {
        return view('admin.push_notification.index');
    }


    public function orderUsers(Request $request)
    {
        $orders = Order::query()
            ->whereHas('user')
            ->with('user')
            ->activeOrder()
            ->groupBy('user_id')
            ->get();

        return response()->json($orders);
    }


    public function orderTypes(Request $request)
    {
        if ($request->type == 'course_id') {
            $items = 'courses';
        } else {
            $items = str_replace(array("_", "id"), "", $request->type);
        }

        $orders = Order::select("id", "title", "user_id", "instructor_id", "course_id", "chapter_id", "bundle_id", "meeting_id", "offline_session_id", "$request->type as type_id", "status")
            ->activeOrder()
            ->whereHas('user')
            ->whereHas($items, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereNotNull($request->type)
            ->with('instructor:id,fname,lname,mobile')
            ->groupBy($request->type)
            ->get();

        return response()->json($orders, 200);
    }


    public function orderTypeUsers(Request $request)
    {
        $orders = Order::select("id", "title", "user_id", "course_id", "chapter_id", "bundle_id", "meeting_id", "offline_session_id", "$request->type as type_id", "status")
            ->whereIn($request->type, $request->type_id)
            ->whereHas('user')
            ->with('user:id,fname,lname,mobile')
            ->activeOrder()
            ->get();

        return response()->json($orders);
    }


    public function push(Request $request)
    {
        ini_set('max_excecution_time', -1);
        ini_set('memory_limit', -1);

        $request->validate([
            'user_group' => 'required',
            'type' => 'required_if:user_group,users',
            'type_ids' => 'required_if:user_group,users|array|min:1',
            'user_ids' => 'nullable|array|min:1',
            'subject' => 'required|string',
            'message' => 'required'
        ], [
            'course_ids.required_if' => 'Course is required',
            'user_ids.required_if' => 'User is required'
        ]);

        if (env('ONE_SIGNAL_APP_ID') == '' && env('ONESIGNAL_REST_API_KEY') == '') {
            Session::flash('success', 'Please update onesignal keys in settings !');
            return back()->withInput();
        }

        $data = [
            'subject' => $request->subject,
            'body' => $request->message,
            'target_url' => $request->target_url ?? null,
            'icon' => $request->icon ?? null,
            'image' => $request->image ?? null,
            'buttonChecked' => $request->show_button ? "yes" : "no",
            'button_text' => $request->btn_text ?? null,
            'button_url' => $request->btn_url ?? null,
        ];

        if ($request->user_group == 'users' && $request->user_ids) {

            $users = User::has('device_tokens')->select('id')->where('role', '=', 'user')->whereIn('id', $request->user_ids)->get();
        } else if ($request->user_group == 'users' && $request->type_ids && !isset($request->user_ids)) {

            $users = User::select('id')
                ->where('role', '=', 'user')
                ->whereHas('device_tokens')
                ->whereHas('orders', function ($query) use ($request) {
                    $query->whereIn($request->type, $request->type_ids);
                })->get();
        } else if ($request->user_group == 'all_users') {

            $users = User::has('device_tokens')->select('id')->where('role', '=', 'user')->get();
        } elseif ($request->user_group == 'all_instructors') {

            $users = User::has('device_tokens')->select('id')->where('role', '=', 'instructors')->get();
        } elseif ($request->user_group == 'all_admins') {

            $users = User::has('device_tokens')->select('id')->where('role', '=', 'admin')->get();
        } else {
            $users = User::has('device_tokens')->select('id')->get();
        }

        if ($users->isNotEmpty()) {
            try {
                //code...
                Notification::send($users, new OfferPushNotifications($data));

                Session::flash('success', 'Notification pushed successfully');
                return redirect()->route('onesignal.settings');
            } catch (\Exception $e) {
                info(['OneSignal push notifications has been disabled', $e]);

                Session::flash('push-notifications-disabled', 'It\'s seems like your OneSignal push notifications has been disabled');
                return redirect()->route('onesignal.settings');
            }
        } else {
            Session::flash('warning', 'These users have not enabled push notifications');
            return redirect()->route('onesignal.settings');
        }
    }


    public function updateKeys(Request $request)
    {
        abort('403', 'ACCESS DENIED');
        
        $request->validate([
            'ONE_SIGNAL_APP_ID' => 'required|string',
            'ONE_SIGNAL_REST_API_KEY' => 'required|string'
        ], [
            'ONE_SIGNAL_APP_ID.required' => 'OneSignal app id is required',
            'ONE_SIGNAL_REST_API_KEY.required' => 'Onesignal rest api key is required'
        ]);


        $env_update = DotenvEditor::setKeys([
            'ONE_SIGNAL_APP_ID' => $request->ONE_SIGNAL_APP_ID,
            'ONE_SIGNAL_REST_API_KEY' => $request->ONE_SIGNAL_REST_API_KEY
        ]);

        $env_update->save();

        Session::flash('success', 'Keys updated successfully');
        return back();
    }
}
