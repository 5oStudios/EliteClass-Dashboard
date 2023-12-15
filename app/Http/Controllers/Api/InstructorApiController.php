<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CourseLanguage;
use App\Course;
use App\BundleCourse;
use App\JitsiMeeting;
use App\Currency;
use App\InstructorSetting;
use App\Order;
use App\User;
use App\PendingPayout;
use Mail;
use App\Mail\SendOrderMail;
use App\Mail\AdminMailOnOrder;
use App\Notifications\AdminOrder;
use App\Notifications\UserEnroll;
use App\OrderInstallment;
use Notification;
use Carbon\Carbon;
use TwilioMsg;
use App\Coupon;
use App\Question;
use App\Answer;
use App\Meeting;
use App\BBL;
use App\Blog;
use Auth;
use App\CompletedPayout;
use Validator;
use App\Categories;
use App\secondaryCategory;
use App\SubCategory;
use App\ChildCategory;
use Image;
use DB;
use File;
use App\Cart;
use App\RefundPolicy;
use App\CourseInclude;
use App\WhatLearn;
use App\CourseChapter;
use App\Subtitle;
use App\CourseClass;
use Illuminate\Support\Facades\Hash;
use App\RefundCourse;
use App\Assignment;
use App\Involvement;
use App\Setting;
use App\Wallet;

class InstructorApiController extends Controller {

    public function getAlllanguage(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $language = CourseLanguage::get();

        $result = array();

        foreach ($language as $data) {

            $result[] = array(
                'id' => $data->id,
                'name' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('name')),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('language' => $result));
    }

    public function getlanguage(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseLanguage::where('id', $id)->exists()) {

            $language = CourseLanguage::first();

            $result = array();

            $result[] = array(
                'id' => $language->id,
                'name' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $language->getTranslations('name')),
                'status' => $language->status,
                'created_at' => $language->created_at,
                'updated_at' => $language->updated_at,
            );

            return response()->json(array('language' => $result));
        } else {
            return response()->json([
                        "message" => "language not found"
                            ], 404);
        }
    }

    public function createlanguage(Request $request) {


        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $language = new CourseLanguage;
        $language->name = $request->name;
        $language->status = isset($request->status) ? 1 : 0;
        $language->save();

        return response()->json([
                    "message" => "language created",
                    'language' => $language
        ]);
    }

    public function updatelanguage(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseLanguage::where('id', $id)->exists()) {
            $language = CourseLanguage::find($id);

            $language->name = isset($request->name) ? $request->name : $language->name;
            $language->status = isset($request->status) ? $request->status : $language->status;
            $language->save();

            return response()->json([
                        "message" => "records updated successfully",
                        'language' => $language
            ]);
        } else {
            return response()->json([
                        "message" => "language not found"
                            ], 404);
        }
    }

    public function deletelanguage(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseLanguage::where('id', $id)->exists()) {
            $language = CourseLanguage::find($id);
            $language->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "language not found"
                            ], 404);
        }
    }

    public function dashboard(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $auth = Auth::user();

        $course_count = Course::where('user_id', $auth->id)->where('status', '1')->count();
        $featured_course_count = Course::where('user_id', $auth->id)->where('status', '1')->where('featured', '1')->count();
        $enrolled_user = Order::where('instructor_id', $auth->id)->where('status', '1')->count();
        $question = Question::where('instructor_id', $auth->id)->where('status', '1')->count();
        $answer = Answer::where('instructor_id', $auth->id)->where('status', '1')->count();
        $blog = Blog::where('user_id', $auth->id)->where('status', '1')->count();
        $zoom_meeting = Meeting::where('owner_id', $auth->id)->count();
        $bigblue_meeting = BBL::where('instructor_id', $auth->id)->count();
        $jitsi_meet = JitsiMeeting::where('owner_id', $auth->id)->count();

        $userenroll_chart = array(
                    Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '01')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //January
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '02')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //Feb
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '03')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //March
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '04')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //April
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '05')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //May
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '06')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //June
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '07')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //July
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '08')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //August
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '09')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //September
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '10')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //October
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '11')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //November
            Order::where('instructor_id', Auth::user()->id)->whereMonth('created_at', '12')->where('status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //December
        );

        $payout_chart = array(
                    CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '01')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //January
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '02')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //Feb
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '03')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //March
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '04')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //April
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '05')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //May
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '06')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //June
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '07')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //July
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '08')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //August
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '09')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //September
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '10')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //October
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '11')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //November
            CompletedPayout::where('user_id', Auth::user()->id)->whereMonth('created_at', '12')->where('pay_status', '1')
                    ->whereYear('created_at', date('Y'))
                    ->count(), //December
        );

        return response()->json(array(
                    'course_count' => $course_count,
                    'featured_course_count' => $featured_course_count,
                    'enrolled_user_count' => $enrolled_user,
                    'questions_count' => $question,
                    'answer_count' => $answer,
                    'blog_count' => $blog,
                    'zoomm_meeting_count' => $zoom_meeting,
                    'bigblue_meeting_count' => $bigblue_meeting,
                    'userenroll_chart' => $userenroll_chart,
                    'payout_chart' => $payout_chart,
                        ),
                        200);
    }

    public function getAllcategory(Request $request) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ],[
            'secret.required'=>__("secret key is missing"),
            'secret.exists'=>__("secret key is invalid"),
           ]);

        $categories = Categories::where('status', 1)->get();

        $result = array();

        foreach ($categories as $category) {

            $result[] = array(
                'id' => $category->id,
                'title' => $category->title,
                'icon' => asset("flags/128x128/".$category->icon),
                'image' => $category->cat_image? asset("images/category/".$category->cat_image) : null,
            );
        }

        return response()->json(array('category' => $result), 200);
    }

    public function getcategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        if (Categories::where('id', $id)->exists()) {
            $category = Categories::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $category->id,
                'title' => $category->title,
                'icon' => $category->icon,
                'slug' => $category->slug,
                'status' => $category->status,
                'featured' => $category->featured,
                'image' => $category->cat_image,
                'imagepath' => url('images/category/' . $category->cat_image),
                'position' => $category->position,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            );

            return response()->json(array('category' => $result), 200);
        } else {
            return response()->json([
                        "message" => "category not found"
                            ], 404);
        }
    }

    public function createcategory(Request $request) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
            "title" => "required|unique:categories,title",
            "title.required" => "Please enter category title !",
            "title.unique" => "This Category name is already exist !",
            "image" => "required",
            "slug" => "required",
            "icon" => "required",
            "status" => "required",
            "featured" => "required",
        ]);

        $category = new Categories;

        $category['position'] = (Categories::count() + 1);

        if ($file = $request->file('image')) {

            $path = 'images/category/';

            if (!file_exists(public_path() . '/' . $path)) {

                $path = 'images/category/';
                File::makeDirectory(public_path() . '/' . $path, 0777, true);
            }
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/category/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $category['cat_image'] = $image;
        }


        $category->title = $request->title;
        $category->slug = $request->slug;
        $category->icon = $request->icon;
        $category->status = isset($request->status) ? 1 : 0;
        $category->featured = isset($request->featured) ? 1 : 0;
        $category->save();

        return response()->json([
                    "message" => "category created"
                        ], 201);
    }

    public function updatecategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        if (Categories::where('id', $id)->exists()) {
            $category = Categories::find($id);

            if ($file = $request->file('image')) {

                $path = 'images/category/';

                if (!file_exists(public_path() . '/' . $path)) {

                    $path = 'images/category/';
                    File::makeDirectory(public_path() . '/' . $path, 0777, true);
                }

                if ($category->cat_image != null) {
                    $content = @file_get_contents(public_path() . '/images/category/' . $category->cat_image);
                    if ($content) {
                        unlink(public_path() . '/images/category/' . $category->cat_image);
                    }
                }

                $optimizeImage = Image::make($file);
                $optimizePath = public_path() . '/images/category/';
                $image = time() . $file->getClientOriginalName();
                $optimizeImage->save($optimizePath . $image, 72);

                $category['cat_image'] = $image;
            }

            $category->title = isset($request->title) ? $request->title : $category->title;
            $category->slug = isset($request->slug) ? $request->slug : $category->slug;
            $category->icon = isset($request->icon) ? $request->icon : $category->icon;
            $category->status = isset($request->status) ? $request->status : $category->status;
            $category->featured = isset($request->featured) ? $request->featured : $category->featured;
            $category->save();

            return response()->json([
                        "message" => "records updated successfully", 'category' => $category
                            ], 200);

            return response()->json(array('category' => $result), 200);
        } else {
            return response()->json([
                        "message" => "category not found"
                            ], 404);
        }
    }

    public function deletecategory(Request $request, $id) {
        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        if (Categories::where('id', $id)->exists()) {
            $category = Categories::find($id);

            if ($category->image != null) {

                $image_file = @file_get_contents(public_path() . '/images/category/' . $category->image);

                if ($image_file) {
                    unlink(public_path() . '/images/category/' . $category->image);
                }
            }

            $category->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "Category not found"
                            ], 404);
        }
    }

    public function getAllTypeCategory(Request $request, $id = null) {

        $categories = secondaryCategory::
                when($id, function ($q)use ($id) {
                    $q->where('category_id', $id);
                })
                ->where('status', 1)
                ->get();

        $result = array();

        foreach ($categories as $category) {

            $result[] = array(
                'id' => $category->id,
                'category_id' => $category->category_id,
                'title' => $category->title,
                'image' => $category->image? asset("images/typecategory/".$category->image) : null,

            );
        }

        return response()->json(array('category' => $result), 200);
    }

    public function getAllsubcategory(Request $request, $id = null) {


        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ],[
            'secret.required'=>__("secret key is missing"),
            'secret.exists'=>__("secret key is invalid"),
        ]);

        $categories = SubCategory::
                when($id, function ($q)use ($id) {
                    $q->where('scnd_category_id', $id);
                })
                ->where('status', 1)
                ->get();

        $result = array();

        foreach ($categories as $category) {

            $result[] = array(
                'id' => $category->id,
                'category_id' => $category->category_id,
                'title' => $category->title,
                'image' => $category->image? asset("images/institutecategory/".$category->image) : null,

            );
        }

        return response()->json(array('category' => $result), 200);
    }

    public function getsubcategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ],[
            'secret.required'=>__("secret key is missing"),
            'secret.exists'=>__("secret key is invalid"),
         ]);
        if (Categories::where('id', $id)->exists()) {
            $category = SubCategory::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $category->id,
                'category_id' => $category->category_id,
                'title' => $category->title,
                'icon' => $category->icon,
                'slug' => $category->slug,
                'status' => $category->status,
                'featured' => $category->featured,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            );

            return response()->json(array('category' => $result), 200);
        } else {
            return response()->json(array("errors"=>["message"=>["category not found"]]), 404);
        }
    }

    public function createsubcategory(Request $request) {


        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
            "title" => "required|unique:categories,title",
            "title.required" => "Please enter category title !",
            "title.unique" => "This Category name is already exist !",
            "slug" => "required",
            "icon" => "required",
            "status" => "required",
        ]);

        $category = new SubCategory;

        $category->category_id = $request->category_id;
        $category->title = $request->title;
        $category->slug = $request->slug;
        $category->icon = $request->icon;
        $category->status = isset($request->status) ? 1 : 0;
        $category->save();

        return response()->json([
                    "message" => "category created", 'subcategory' => $category
                        ], 200);
    }

    public function updatesubcategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        if (SubCategory::where('id', $id)->exists()) {
            $category = SubCategory::find($id);

            $category->category_id = isset($request->category_id) ? $request->category_id : $category->category_id;
            $category->title = isset($request->title) ? $request->title : $category->title;
            $category->slug = isset($request->slug) ? $request->slug : $category->slug;
            $category->icon = isset($request->icon) ? $request->icon : $category->icon;
            $category->status = isset($request->status) ? $request->status : $category->status;
            $category->save();

            return response()->json([
                        "message" => "records updated successfully", 'subcategory' => $category
                            ], 200);
        } else {
            return response()->json([
                        "message" => "category not found"
                            ], 404);
        }
    }

    public function deletesubcategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);
        if (SubCategory::where('id', $id)->exists()) {
            $category = SubCategory::find($id);

            $category->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "Category not found"
                            ], 404);
        }
    }

    public function getAllchildcategory(Request $request, $category_id, $sub_category_id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ],[
            'secret.required'=>__("secret key is missing"),
            'secret.exists'=>__("secret key is invalid"),
        ]);

        $categories = ChildCategory::
//                when($category_id, function ($q)use ($category_id) {
//                    $q->where('category_id', $category_id);
//                })
//                ->when($sub_category_id, function ($q)use ($sub_category_id) {
                where('subcategory_id', $sub_category_id)
//                })
                ->where('status', 1)
                ->get();

        $result = array();

        foreach ($categories as $category) {

            $result[] = array(
                'id' => $category->id,
                'category_id' => $category->category_id,
                'subcategory_id' => $category->subcategory_id,
                'title' => $category->title,
                'image' => $category->image? asset("images/majorcategory/".$category->image) : null,

            );
        }

        return response()->json(array('childcategory' => $result), 200);
    }

    public function getchildcategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);
        if (ChildCategory::where('id', $id)->exists()) {
            $category = ChildCategory::where('id', $id)
                            ->where('status', 1)->first();

            $result = array();

            $result[] = array(
                'id' => $category->id,
                'category_id' => $category->category_id,
                'subcategory_id' => $category->subcategory_id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $category->getTranslations('title')),
                'icon' => $category->icon,
                'slug' => $category->slug,
                'status' => $category->status,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            );

            return response()->json(array('childcategory' => $result), 200);
        } else {
            return response()->json(array("errors"=>["message"=>["category not found"]]) , 404);
        }
    }

    public function createchildcategory(Request $request) {
        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
            "title" => "required|unique:categories,title",
            "title.required" => "Please enter category title !",
            "title.unique" => "This Category name is already exist !",
            "slug" => "required",
            "icon" => "required",
            "status" => "required",
        ]);

        $category = new ChildCategory;

        $category->category_id = $request->category_id;
        $category->subcategory_id = $request->subcategory_id;
        $category->title = $request->title;
        $category->slug = $request->slug;
        $category->icon = $request->icon;
        $category->status = $request->status;
        $category->save();

        return response()->json([
                    "message" => "category created", 'childcategory' => $category
                        ], 200);

        return response()->json(array('category' => $result), 200);
    }

    public function updatechildcategory(Request $request, $id) {

        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        if (ChildCategory::where('id', $id)->exists()) {
            $category = ChildCategory::find($id);

            $category->category_id = isset($request->category_id) ? $request->category_id : $category->category_id;
            $category->subcategory_id = isset($request->subcategory_id) ? $request->subcategory_id : $category->subcategory_id;
            $category->title = isset($request->title) ? $request->title : $category->title;
            $category->slug = isset($request->slug) ? $request->slug : $category->slug;
            $category->icon = isset($request->icon) ? $request->icon : $category->icon;
            $category->status = isset($request->status) ? $request->status : $category->status;
            $category->save();

            return response()->json([
                        "message" => "records updated successfully", 'childcategory' => $category
                            ], 200);
        } else {
            return response()->json([
                        "message" => "category not found"
                            ], 404);
        }
    }

    public function deletechildcategory(Request $request, $id) {


        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        if (ChildCategory::where('id', $id)->exists()) {
            $category = ChildCategory::find($id);

            $category->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "Category not found"
                            ], 404);
        }
    }

    public function getAllcourse(Request $request) {


        $this->validate($request, [
            'secret' => 'required|exists:api_keys,secret_key',
        ]);
        $user = Auth::user();

        $courses = course::where('user_id', $user->id)->get();

        $result = array();

        foreach ($courses as $course) {

            $result[] = array(
                'id' => $course->id,
                'subcategory_id' => $course->subcategory_id,
                'category_id' => $course->category_id,
                'childcategory_id' => $course->childcategory_id,
                'language_id' => $course->language_id,
                'user_id' => $course->user_id,
                'user' => optional($course->user)['fname'] . ' ' . optional($course->user)['lname'],
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('title')),
                'short_detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('short_detail')),
                'requirement' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('requirement')),
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('detail')),
                'price' => $course->price,
                'discount_price' => $course->discount_price,
                'day' => $course->day,
                'video' => $course->video,
                'video_path' => url('video/preview/' . $course->video),
                'video_url' => $course->video_url,
                'url' => $course->url,
                'featured' => $course->featured,
                'status' => $course->status,
                'slug' => $course->slug,
                'duration' => $course->duration,
                'duration_type' => $course->duration_type,
                'instructor_revenue' => $course->instructor_revenue,
                'involvement_request' => $course->involvement_request,
                'refund_policy_id' => $course->refund_policy_id,
                'assignment_enable' => $course->assignment_enable,
                'appointment_enable' => $course->appointment_enable,
                'certificate_enable' => $course->certificate_enable,
                'course_tags' => $course->course_tags,
                'level_tags' => $course->level_tags,
                'preview_image' => $course->preview_image,
                'imagepath' => url('images/course/' . $course->preview_image),
                'course_tags' => $course->course_tags,
                'level_tags' => $course->level_tags,
                'reject_txt' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($course->reject_txt))),
                'drip_enable' => $course->drip_enable,
                'preview_type' => $course->preview_type,
                'updated_at' => $course->created_at,
            );
        }

        return response()->json(array('course' => $result), 200);
    }

    public function getcourse(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $user = Auth::user();

        if (Course::where('id', $id)->where('user_id', $user->id)->first()) {

            if (Course::where('id', $id)->exists()) {
                $course = Course::where('id', $id)->first();

                $result = array();

                $result[] = array(
                    'id' => $course->id,
                    'subcategory_id' => $course->subcategory_id,
                    'category_id' => $course->category_id,
                    'childcategory_id' => $course->childcategory_id,
                    'language_id' => $course->language_id,
                    'user_id' => $course->user_id,
                    'user' => optional($course->user)['fname'] . ' ' . optional($course->user)['lname'],
                    'title' => array_map(function ($lang) {
                        return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                    }, $course->getTranslations('title')),
                    'short_detail' => array_map(function ($lang) {
                        return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                    }, $course->getTranslations('short_detail')),
                    'requirement' => array_map(function ($lang) {
                        return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                    }, $course->getTranslations('requirement')),
                    'detail' => array_map(function ($lang) {
                        return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                    }, $course->getTranslations('detail')),
                    'price' => $course->price,
                    'discount_price' => $course->discount_price,
                    'day' => $course->day,
                    'video' => $course->video,
                    'video_path' => url('video/preview/' . $course->video),
                    'video_url' => $course->video_url,
                    'url' => $course->url,
                    'featured' => $course->featured,
                    'status' => $course->status,
                    'slug' => $course->slug,
                    'duration' => $course->duration,
                    'duration_type' => $course->duration_type,
                    'instructor_revenue' => $course->instructor_revenue,
                    'involvement_request' => $course->involvement_request,
                    'refund_policy_id' => $course->refund_policy_id,
                    'assignment_enable' => $course->assignment_enable,
                    'appointment_enable' => $course->appointment_enable,
                    'certificate_enable' => $course->certificate_enable,
                    'course_tags' => $course->course_tags,
                    'level_tags' => $course->level_tags,
                    'preview_image' => $course->preview_image,
                    'imagepath' => url('images/course/' . $course->preview_image),
                    'course_tags' => $course->course_tags,
                    'level_tags' => $course->level_tags,
                    'reject_txt' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($course->reject_txt))),
                    'drip_enable' => $course->drip_enable,
                    'preview_type' => $course->preview_type,
                    'updated_at' => $course->created_at,
                );

                return response()->json(array('course' => $result), 200);
            } else {
                return response()->json([
                            "message" => "course not found"
                                ], 404);
            }
        } else {
            return response()->json([
                        "message" => "Invalid Access"
                            ], 404);
        }
    }

    public function createcourse(Request $request) {


        // return $request;

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }



        $validator = Validator::make($request->all(), [
                    "title" => "required",
                    "title.required" => "Please enter course title !",
                    "category_id" => "required",
                    "subcategory_id" => "required",
                    "language_id" => "required",
                    "user_id" => "required",
                    'video' => 'mimes:mp4,avi,wmv',
                    'slug' => 'required|unique:courses,slug',
        ]);

        // return $request;

        $input = $request->all();

        $data = Course::create($input);

        if (isset($request->type)) {
            $data->type = "1";
        } else {
            $data->type = "0";
        }


        if ($file = $request->file('preview_image')) {
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/course/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $data->preview_image = $image;
        }

        $data->drip_enable = isset($request->drip_enable) ? 1 : 0;

        if (isset($request->preview_type)) {
            $data->preview_type = "video";
        } else {
            $data->preview_type = "url";
        }

        if (isset($request->duration_type)) {
            $data->duration_type = "m";
        } else {
            $data->duration_type = "d";
        }

        if (isset($request->involvement_request)) {
            $data->involvement_request = "1";
        } else {
            $data->involvement_request = "0";
        }

        if (isset($request->assignment_enable)) {
            $data->assignment_enable = "1";
        } else {
            $data->assignment_enable = "0";
        }

        if (isset($request->appointment_enable)) {
            $data->appointment_enable = "1";
        } else {
            $data->appointment_enable = "0";
        }

        if (isset($request->certificate_enable)) {
            $data->certificate_enable = "1";
        } else {
            $data->certificate_enable = "0";
        }


        if (!isset($request->preview_type)) {
            $data->url = $request->url;
        } else if ($request->preview_type) {
            if ($file = $request->file('video')) {

                $filename = time() . $file->getClientOriginalName();
                $file->move('video/preview', $filename);
                $data->video = $filename;
            }
        }


        $data->save();

        return response()->json([
                    "message" => "Course created",
                    'course' => $data
        ]);
    }

    public function updatecourse(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Course::where('id', $id)->exists()) {
            $course = Course::findOrFail($id);
            $input = $request->all();

            if (isset($request->type)) {
                $input['type'] = "1";
            } else {
                $input['type'] = "0";
            }


            if ($file = $request->file('image')) {

                if ($course->preview_image != null) {
                    $content = @file_get_contents(public_path() . '/images/course/' . $course->preview_image);
                    if ($content) {
                        unlink(public_path() . '/images/course/' . $course->preview_image);
                    }
                }

                $optimizeImage = Image::make($file);
                $optimizePath = public_path() . '/images/course/';
                $image = time() . $file->getClientOriginalName();
                $optimizeImage->save($optimizePath . $image, 72);

                $input['preview_image'] = $image;
            }

            $input['drip_enable'] = isset($request->drip_enable) ? 1 : 0;

            if (isset($request->preview_type)) {
                $input['preview_type'] = "video";
            } else {
                $input['preview_type'] = "url";
            }

            if (isset($request->duration_type)) {
                $input['duration_type'] = "m";
            } else {
                $input['duration_type'] = "d";
            }

            if (isset($request->involvement_request)) {
                $input['involvement_request'] = "1";
            } else {
                $input['involvement_request'] = "0";
            }

            if (isset($request->assignment_enable)) {
                $input['assignment_enable'] = "1";
            } else {
                $input['assignment_enable'] = "0";
            }

            if (isset($request->appointment_enable)) {
                $input['appointment_enable'] = "1";
            } else {
                $input['appointment_enable'] = "0";
            }

            if (isset($request->certificate_enable)) {
                $input['certificate_enable'] = "1";
            } else {
                $input['certificate_enable'] = "0";
            }


            if (!isset($request->preview_type)) {
                $course->url = $request->video_url;
                $course->video = null;
            } else if ($request->preview_type) {
                if ($file = $request->file('video')) {
                    if ($course->video != "") {
                        $content = @file_get_contents(public_path() . '/video/preview/' . $course->video);
                        if ($content) {
                            unlink(public_path() . '/video/preview/' . $course->video);
                        }
                    }

                    $filename = time() . $file->getClientOriginalName();
                    $file->move('video/preview', $filename);
                    $input['video'] = $filename;
                    $course->url = null;
                }
            }



            Cart::where('course_id', $id)
                    ->update([
                        'price' => $request->price,
                        'offer_price' => $request->discount_price,
            ]);

            $course->update($input);

            return response()->json([
                        "message" => "records updated successfully", 'course' => $course
                            ], 200);
        } else {
            return response()->json([
                        "message" => "course not found"
                            ], 404);
        }
    }

    public function deletecourse(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Categories::where('id', $id)->exists()) {
            $course = Categories::find($id);

            if ($course->image != null) {

                $image_file = @file_get_contents(public_path() . '/images/course/' . $course->image);

                if ($image_file) {
                    unlink(public_path() . '/images/course/' . $course->image);
                }
            }

            $course->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "course not found"
                            ], 404);
        }
    }

    public function getAllrefundpolicy(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $policies = RefundPolicy::get();

        $result = array();

        foreach ($policies as $policy) {

            $result[] = array(
                'id' => $policy->id,
                'name' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $policy->getTranslations('name')),
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $policy->getTranslations('detail')),
                'amount' => $policy->amount,
                'days' => $policy->days,
                'status' => $policy->status,
                'created_at' => $policy->created_at,
                'updated_at' => $policy->updated_at,
            );
        }

        return response()->json(array('refundpolicies' => $result), 200);
    }

    public function instructorprofileupdate(Request $request) {
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

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $auth->id,
        ]);

        if (config('app.demolock') == 0) {

            $input = $request->all();

            if ($file = $request->file('user_img')) {

                if ($auth->user_img != null) {
                    $content = @file_get_contents(public_path() . '/images/user_img/' . $auth->user_img);
                    if ($content) {
                        unlink(public_path() . '/images/user_img/' . $auth->user_img);
                    }
                }

                $optimizeImage = Image::make($file);
                $optimizePath = public_path() . '/images/user_img/';
                $image = time() . $file->getClientOriginalName();
                $optimizeImage->save($optimizePath . $image, 72);
                $input['user_img'] = $image;
            }


            $verified = \Carbon\Carbon::now()->toDateTimeString();

            if (isset($request->password)) {

                $input['password'] = Hash::make($request->password);
            } else {
                $input['password'] = $auth->password;
            }


            $input['email_verified_at'] = isset($request->email_verified_at) ? $request->email_verified_at : $auth->email_verified_at;
            $input['status'] = isset($request->status) ? $request->status : $auth->status;

            $auth->update($input);

            return response()->json(array('profile' => $auth), 200);
        } else {
            return response()->json('error: password doesnt match', 400);
        }
    }

    public function getAllorder(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $user = Auth::user();

        $enroll = Order::where('instructor_id', $user->id)->where('status', 1)->get();

        $enroll_details = array();

        if (isset($enroll)) {

            foreach ($enroll as $enrol) {


                $enroll_details[] = array(
                    'id' => $enrol->id,
                    'instructor_id' => $enrol->instructor_id,
                    'user_id' => $enrol->user_id,
                    'user' => optional($enrol->user)['fname'] . ' ' . optional($enrol->user)['lname'],
                    'course_id' => $enrol->courses->title,
                    'order_id' => $enrol->order_id,
                    'transaction_id' => $enrol->transaction_id,
                    'payment_method' => $enrol->payment_method,
                    'total_amount' => $enrol->total_amount,
                    'coupon_discount' => $enrol->coupon_discount,
                    'currency' => $enrol->currency,
                    'currency_icon' => $enrol->currency_icon,
                    'duration' => $enrol->duration,
                    'enroll_start' => $enrol->enroll_start,
                    'enroll_expire' => $enrol->enroll_expire,
                    'bundle_course_id' => $enrol->bundle_course_id,
                    'bundle_id' => $enrol->bundle_id,
                    'proof' => $enrol->proof,
                    'sale_id' => $enrol->sale_id,
                    'refunded' => $enrol->refunded,
                    'price_id' => $enrol->price_id,
                    'subscription_id' => $enrol->subscription_id,
                    'customer_id' => $enrol->customer_id,
                    'subscription_status' => $enrol->subscription_status,
                    'status' => $enrol->status,
                    'created_at' => $enrol->created_at,
                    'updated_at' => $enrol->updated_at,
                );
            }
            return response()->json(array('enroll_details' => $enroll_details), 200);
        }

        return response()->json(array('enroll_details' => $enroll_details), 200);
    }

    public function getorder(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Order::where('id', $id)->where('status', '1')->exists()) {
            $enrol = Order::find($id);

            $result = array();

            $result[] = array(
                'id' => $enrol->id,
                'instructor_id' => $enrol->instructor_id,
                'user_id' => $enrol->user_id,
                'user' => optional($enrol->user)['fname'] . ' ' . optional($enrol->user)['lname'],
                'course_id' => $enrol->courses->title,
                'order_id' => $enrol->order_id,
                'transaction_id' => $enrol->transaction_id,
                'payment_method' => $enrol->payment_method,
                'total_amount' => $enrol->total_amount,
                'coupon_discount' => $enrol->coupon_discount,
                'currency' => $enrol->currency,
                'currency_icon' => $enrol->currency_icon,
                'duration' => $enrol->duration,
                'enroll_start' => $enrol->enroll_start,
                'enroll_expire' => $enrol->enroll_expire,
                'bundle_course_id' => $enrol->bundle_course_id,
                'bundle_id' => $enrol->bundle_id,
                'proof' => $enrol->proof,
                'sale_id' => $enrol->sale_id,
                'refunded' => $enrol->refunded,
                'price_id' => $enrol->price_id,
                'subscription_id' => $enrol->subscription_id,
                'customer_id' => $enrol->customer_id,
                'subscription_status' => $enrol->subscription_status,
                'status' => $enrol->status,
                'created_at' => $enrol->created_at,
                'updated_at' => $enrol->updated_at,
            );

            return response()->json(array('data' => $result), 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function createorder(Request $request) {
        $user = Auth::guard('api')->user();
        $this->validate($request, [
            'bundle_id' => ['nullable', 'exists:bundle_courses,id', Rule::unique('orders')->where('user_id', $user->id)->where('status', 1)],
            'course_id' => ['nullable', 'exists:courses,id', Rule::unique('orders')->where('user_id', $user->id)->where('status', 1)],
            'meeting_id' => ['nullable', 'exists:bigbluemeetings,id', Rule::unique('orders')->where('user_id', $user->id)->where('status', 1)],
            'payment_method' => 'required|in:wallet',
            'payment_type' => 'required|in:instalment,full',
            'instalments'=>'required_if:payment_type,instalment|array',
            'coupon' => 'nullable|exists:coupons,code',
        ],[
            "bundle_id.exists"=>__("bundle not found"),
            "bundle_id.unique"=>__("you already enrolled in this bundle"),
            "course_id.exists"=>__("course not found"),
            "course_id.unique"=>__("you already enrolled in this course"),
            "meeting_id.exists"=>__("meeting not found"),
            "meeting_id.unique"=>__("you already enrolled in this meeting"),
            "payment_method.required"=>__("payment method not selected"),
            "payment_method.in"=>__("payment method not valid"),
            "payment_type.required"=>__("payment type not selected"),
            "payment_type.in"=>__("payment type not valid"),
            "instalments.required_if"=>__("instalment not selected"),
            "instalments.array"=>__("instalment not valid"),
            "coupon.exists"=>__("coupon is invalid")
        ]);

        $order = $request->bundle_id ? BundleCourse::find($request->bundle_id) : ($request->meeting_id ? BBL::find($request->meeting_id) : Course::find($request->course_id));
        $coupon = $request->coupon ? Coupon::where('code', $request->coupon)->first() : null;
        
        if (isset($request->instalments) && $order->installment && $order->installments) {
            $due_inst = $order->installments->where('due_date','<=',date('Y-m-d'))->pluck('id')->toArray();
            $inst = $order->installments->pluck('id')->toArray();
            if(count($request->instalments) < count($due_inst)){
                return response()->json(array("errors"=>["message"=>[__("Pay all due instalments")]]), 422);
            }
            foreach ($request->instalments as $in) {
                if (!in_array($in, $inst)) {
                    return response()->json(array("errors"=>["message"=>[__("selected Instalment has been removed or invalid")]]), 422);
                }
            }
            $price_total = $order->installments->sum('amount');
            $pay_total = $order->installments->whereIn('id', $request->instalments)->sum('amount');
        } else {
            $price_total = $order->discount_price ?? $order->price ?? 0;
            $pay_total = $price_total;
        }

        $cpn = ($coupon && ($price_total == $pay_total) ? $coupon->applycoupon($order, ($request->bundle_id ? 'bundle' : ($request->meeting_id ? 'meeting' : ($request->course_id ? 'course' : '')))) : [0, false]);
        $cpn_discount = $cpn[1] ? $cpn[0]['discount_amount'] : 0;
        $pay_amount = $pay_total - $cpn_discount;
        
        $w_s = \App\WalletSettings::first();
        if (($request->payment_method == 'wallet' && !$w_s->status)) {
            return response()->json(array("errors"=>["message"=>[__("Payment via wallet is disabled")]]), 422);
        }
        if (($request->payment_method == 'wallet' && !$user->wallet) || ($user->wallet->balance < $pay_amount)) {
            return response()->json(array("errors"=>["message"=>['low balance']]), 402);
        }

        $txn_id = $request->txn_id;

        $payment_method = $request->payment_method;

        $gsettings = Setting::first();

        $currency = Currency::where('default', '=', '1')->first();

        $lastOrder = Order::orderBy('created_at', 'desc')->where('status', '1')->first();

        if (!$lastOrder) {
            $number = 0;
        } else {
            $number = substr($lastOrder->order_id, 3);
        }

        $resp = [];

        if ($request->bundle_id) {
            $pay_detail = __('Package Purchased');
            $bundle_id = $request->bundle_id;
            $bundle_course_id = $order->course_id;
            $course_id = NULL;
            $meeting_id = NULL;
            $duration = NULL;
            $instructor_payout = 0;
            $instructor_id = $order->user_id;
            $resp = [
                'bundle_id' => $request->bundle_id,
                'type' => 'bundle'
            ];

            $todayDate = $order->start_date;
            $expireDate = $order->end_date;
        } elseif ($request->meeting_id) {

            // $todayDate = date('Y-m-d', strtotime($order->start_time));
            // $expireDate = date('Y-m-d', strtotime($order->start_time));
            $todayDate = $order->start_time;
            $expireDate = $order->start_time;

            $setting = InstructorSetting::first();

            if ($order->instructor_revenue != NULL) {
                $x_amount = $price_total * $order->instructor_revenue;
                $instructor_payout = $x_amount / 100;
            } else {

                if (isset($setting)) {
                    if ($order->teacher->role == "instructor") {
                        $x_amount = $price_total * $setting->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        $instructor_payout = 0;
                    }
                } else {
                    $instructor_payout = 0;
                }
            }
            $resp = [
                'meeting_id' => $request->meeting_id,
                'type' => 'meeting'
            ];
            $bundle_id = NULL;
            $course_id = NULL;
            $bundle_course_id = NULL;
            $meeting_id = $order->id;
            $duration = $order->duration;
            $instructor_id = $order->instructor_id;
            $pay_detail = __('Live Streaming Purchased');
        } elseif ($request->course_id) {

            $todayDate = $order->start_date;
            $expireDate = $order->end_date;

            $setting = InstructorSetting::first();

            if ($order->instructor_revenue != NULL) {
                $x_amount = $price_total * $order->instructor_revenue;
                $instructor_payout = $x_amount / 100;
            } else {

                if (isset($setting)) {
                    if ($order->teacher->role == "instructor") {
                        $x_amount = $price_total * $setting->instructor_revenue;
                        $instructor_payout = $x_amount / 100;
                    } else {
                        $instructor_payout = 0;
                    }
                } else {
                    $instructor_payout = 0;
                }
            }
            $resp = [
                'course_id' => $request->course_id,
                'type' => 'course'
            ];

            $bundle_id = NULL;
            $meeting_id = NULL;
            $course_id = $request->course_id;
            $bundle_course_id = NULL;
            $duration = $order->duration;
            $instructor_id = $order->user_id;
            $pay_detail = __('Course Purchased');
        }

        /** Create wallet transcation history */
        $wallet_transaction = \App\WalletTransactions::create([
                    'wallet_id' => $user->wallet->id,
                    'user_id' => $user->id,
                    'transaction_id' => $txn_id ?? '',
                    'payment_method' => $request->payment_method,
                    'total_amount' => $pay_amount,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                    'type' => 'Debit',
                    'detail' => $pay_detail,
        ]);

        if (($request->payment_method == 'wallet')) {

            $user_wallet = Wallet::where('user_id', $user->id)->first();

            Wallet::where('user_id', $user->id)->update([
                'balance' => $user_wallet->balance - $pay_amount,
            ]);
        }
        
        $or = [
            'title' => $order->_title(),
            'price' => $order->price,
            'discount_price' => $order->discount_price,
            'course_id' => $course_id,
            'user_id' => $user->id,
            'instructor_id' => $instructor_id,
            'order_id' => '#' . sprintf("%08d", intval($number) + 1),
            'transaction_id' => $txn_id ?? $wallet_transaction->id,
            'payment_method' => $payment_method,
            'total_amount' => $price_total,
            'paid_amount' => $pay_amount,
            'installments' => ($pay_amount+$cpn_discount) != $price_total ? 1 : 0,
            'coupon_discount' => $cpn_discount ?? null,
            'coupon_id' => $cpn_discount > 0 ? ($coupon->id ?? null) : null,
            'currency' => $currency->code,
            'currency_icon' => $currency->symbol,
            'duration' => $duration,
            'enroll_start' => $todayDate,
            'enroll_expire' => $expireDate,
            'instructor_revenue' => $instructor_payout,
            'bundle_id' => $bundle_id,
            'meeting_id' => $meeting_id,
            'bundle_course_id' => $bundle_course_id,
            'sale_id' => NULL,
            'status' => 1,
            'proof' => NULL,
        ];

        $created_order = Order::create($or);
        if ($coupon && ($price_total == ($pay_amount + $cpn_discount)) && $cpn[1]) {
            DB::table('coupons')->where('code', '=', $coupon->code)->decrement('maxusage', 1);
        }

        if ($course_id) {
            \App\Wishlist::where(['course_id'=> $course_id,'user_id' => $user->id])->delete();
        } elseif ($bundle_id) {
            \App\Wishlist::where(['bundle_id'=> $bundle_id,'user_id' => $user->id])->delete();
        } elseif ($meeting_id) {
            \App\Wishlist::where(['meeting_id'=> $meeting_id,'user_id' => $user->id])->delete();
        }

        if ($created_order) {
            if ($course_id || $bundle_course_id) {
                $courses = $course_id ? [$course_id] : $bundle_course_id;
                foreach ($courses as $c) {
                    $p = \App\CourseProgress::where([
                                'course_id' => $c,
                                'user_id' => $user->id])->first();
                    if (!isset($p)) {
                        $chapters = CourseClass::where('status', 1)->where('course_id', $c)->get(['id'])->pluck('id');
                        \App\CourseProgress::create([
                            'course_id' => $c,
                            'user_id' => $user->id,
                            'progress' => 0,
                            'mark_chapter_id' => [],
                            'all_chapter_id' => $chapters,
                        ]);
                    }
                }
            }

            $Installment = OrderInstallment::create([
                        'order_id' => $created_order->id,
                        'user_id' => $user->id,
                        'transaction_id' => $wallet_transaction->id,
                        'payment_method' => $payment_method,
                        'total_amount' => $pay_amount,
                        'coupon_discount' => $cpn_discount,
                        'coupon_id' => $cpn_discount > 0 ? ($coupon->id ?? null) : null,
                        'currency' => $currency->code,
                        'currency_icon' => $currency->symbol,
            ]);

            if ($order->installments && ($request->payment_type == 'instalment' || (($pay_amount+$cpn_discount) != $price_total))) {
                foreach ($order->installments as $i) {
                    \App\OrderPaymentPlan::create([
                        'order_id' => $created_order->id,
                        'wallet_trans_id' => $pay_amount >= $i->amount ? $wallet_transaction->id : null,
                        'created_by' => $user->id,
                        'amount' => $i->amount,
                        'due_date' => $i->due_date,
                        // 'payment_date' => $pay_amount >= $i->amount ? \Carbon\Carbon::now()->toDateTimeString() : null,
                        'payment_date' => $pay_amount >= $i->amount ? now() : null,
                        'status' => $pay_amount >= $i->amount ? 'Paid' : null,
                    ]);
                    $pay_amount = $pay_amount >= $i->amount ? $pay_amount - $i->amount : 0;
                }
            }
        }

        if ($instructor_payout != 0) {
            if ($order->teacher->role == "instructor" && ($request->course_id || $request->meeting_id || $request->bundle_id)) {
                PendingPayout::create([
                    'user_id' => $order->teacher->id,
                    'course_id' => $request->course_id ?? $request->meeting_id ?? $request->bundle_id,
                    'order_id' => $created_order->id,
                    'transaction_id' => $wallet_transaction->id,
                    'total_amount' => $price_total,
                    'currency' => $currency->code,
                    'currency_icon' => $currency->symbol,
                    'instructor_revenue' => $instructor_payout,
                ]);
            }
        }

//        if ($created_order) {
//            if ($gsettings->twilio_enable == '1') {
//
//                try {
//                    $recipients = $user->mobile;
//                    $msg = 'Hey' . ' ' . $user->fname . ' ' .
//                            'You\'r successfully enrolled in ' . $order->title .
//                            'Thanks' . ' ' . config('app.name');
//
//                    TwilioMsg::sendMessage($msg, $recipients);
//                } catch (\Exception $e) {
//                    
//                }
//            }
//        }
//        if ($created_order) {
//            try {
//
//                /* sending user email */
//                $x = 'You are successfully enrolled in a ' . $order->Entity;
//                Mail::to(Auth::User()->email)->send(new SendOrderMail($x, $created_order));
//
//                /* sending admin email */
//                $x = 'User Enrolled in ' . $order->Entity;
//                Mail::to($order->user->email)->send(new AdminMailOnOrder($x, $created_order));
//            } catch (\Exception $e) {
//                
//            }
//        }

        if ($created_order) {
            // Notification when user enroll
            if(count($user->device_tokens) > 0){
                Notification::send($user, new UserEnroll($created_order));
            }

//            $url = route('view.order', $created_order->id);
//            Notification::send($order->user, new AdminOrder($order, $created_order->id, $url));
        }

        return response()->json([
                    "message" => "Added successfully",
                    'response' => $resp
                        ], 201);
    }

    public function deleteorder(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Order::where('id', $id)->where('status', '1')->exists()) {
            $data = Order::find($id);

            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllinclude(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $includes = CourseInclude::get();

        $result = array();

        foreach ($includes as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('detail')),
                'icon' => $data->icon,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result), 200);
    }

    public function getinclude(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseInclude::where('id', $id)->exists()) {
            $data = CourseInclude::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('detail')),
                'icon' => $data->icon,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );

            return response()->json(array('data' => $result), 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function createinclude(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $this->validate($request, [
            "course_id" => "required",
            "detail" => "required",
            "icon" => "required",
            "status" => "required",
        ]);

        $data = new CourseInclude;

        $data->course_id = $request->course_id;
        $data->detail = $request->detail;
        $data->icon = $request->icon;
        $data->status = $request->status;
        $data->save();

        return response()->json([
                    "message" => "Added successfully"
                        ], 201);
    }

    public function updateinclude(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseInclude::where('id', $id)->exists()) {
            $data = CourseInclude::find($id);

            $data->course_id = isset($request->course_id) ? $request->course_id : $data->course_id;
            $data->detail = isset($request->detail) ? $request->detail : $data->detail;
            $data->icon = isset($request->icon) ? $request->icon : $data->icon;
            $data->status = isset($request->status) ? $request->status : $data->status;
            $data->save();

            return response()->json([
                        "message" => "records updated successfully", 'data' => $data
                            ], 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deleteinclude(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseInclude::where('id', $id)->exists()) {
            $data = CourseInclude::find($id);

            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllwhatlearn(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $whatlearns = WhatLearn::get();

        $result = array();

        foreach ($whatlearns as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('detail')),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result), 200);
    }

    public function getwhatlearn(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (WhatLearn::where('id', $id)->exists()) {
            $data = WhatLearn::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('detail')),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );

            return response()->json(array('data' => $result), 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function createwhatlearn(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $this->validate($request, [
            "course_id" => "required",
            "detail" => "required",
            "status" => "required",
        ]);

        $data = new WhatLearn;

        $data->course_id = $request->course_id;
        $data->detail = $request->detail;
        $data->status = $request->status;
        $data->save();

        return response()->json([
                    "message" => "Added successfully"
                        ], 201);
    }

    public function updatewhatlearn(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (WhatLearn::where('id', $id)->exists()) {
            $data = WhatLearn::find($id);

            $data->course_id = isset($request->course_id) ? $request->course_id : $data->course_id;
            $data->detail = isset($request->detail) ? $request->detail : $data->detail;
            $data->status = isset($request->status) ? $request->status : $data->status;
            $data->save();

            return response()->json([
                        "message" => "records updated successfully", 'data' => $data
                            ], 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deletewhatlearn(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (WhatLearn::where('id', $id)->exists()) {
            $data = WhatLearn::find($id);

            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllchapter(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $coursechapter = CourseChapter::get();

        $result = array();

        foreach ($coursechapter as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'chapter_name' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('chapter_name')),
                'status' => $data->status,
                'file' => $data->file,
                'position' => $data->position,
                'drip_type' => $data->file,
                'drip_date' => $data->drip_date,
                'drip_days' => $data->drip_days,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result), 200);
    }

    public function getchapter(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseChapter::where('id', $id)->exists()) {
            $data = CourseChapter::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'chapter_name' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('chapter_name')),
                'status' => $data->status,
                'file' => $data->file,
                'position' => $data->position,
                'drip_type' => $data->file,
                'drip_date' => $data->drip_date,
                'drip_days' => $data->drip_days,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );

            return response()->json(array('data' => $result), 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function createchapter(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $this->validate($request, [
            "course_id" => "required",
            "chapter_name" => "required",
            "status" => "required",
        ]);

        $input = $request->all();

        if ($file = $request->file('file')) {
            $filename = time() . $file->getClientOriginalName();
            $file->move('files/material', $filename);
            $input['file'] = $filename;
        }

        if ($request->drip_type == "date") {
            $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
            $input['drip_date'] = $start_time;
            $input['drip_days'] = null;
        } elseif ($request->drip_type == "days") {

            $input['drip_days'] = $request->drip_days;
            $input['drip_date'] = null;
        } else {

            $input['drip_days'] = null;
            $input['drip_date'] = null;
        }



        $input['position'] = (CourseChapter::count() + 1);

        $data = CourseChapter::create($input);

        $data->save();

        return response()->json([
                    "message" => "Added successfully"
                        ], 201);
    }

    public function updatechapter(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseChapter::where('id', $id)->exists()) {
            $data = CourseChapter::findorfail($id);

            $input = $request->all();

            if ($request->drip_type == "date") {
                $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
                $input['drip_date'] = $start_time;
                $input['drip_days'] = null;
            } elseif ($request->drip_type == "days") {

                $input['drip_days'] = $request->drip_days;
                $input['drip_date'] = null;
            } else {

                $input['drip_days'] = null;
                $input['drip_date'] = null;
            }

            if (isset($request->status)) {
                $input['status'] = '1';
            } else {
                $input['status'] = '0';
            }

            if ($file = $request->file('file')) {
                if ($data->file != "") {
                    $chapter_file = @file_get_contents(public_path() . '/files/material/' . $data->file);

                    if ($chapter_file) {
                        unlink('files/material/' . $data->file);
                    }
                }
                $name = time() . $file->getClientOriginalName();
                $file->move('files/material', $name);
                $input['file'] = $name;
            }

            $data->update($input);

            return response()->json([
                        "message" => "records updated successfully", 'data' => $data
                            ], 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deletechapter(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseChapter::where('id', $id)->exists()) {
            $data = CourseChapter::find($id);

            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllclass(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $class = CourseClass::get();

        $result = array();

        foreach ($class as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'coursechapter_id' => $data->coursechapter_id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('title')),
                'status' => $data->status,
                'file' => $data->file,
                'position' => $data->position,
                'duration' => $data->duration,
                'featured' => $data->featured,
                'url' => $data->url,
                'size' => $data->size,
                'image' => $data->image,
                'video' => $data->video,
                'pdf' => $data->pdf,
                'file' => $data->file,
                'zip' => $data->zip,
                'preview_video' => $data->preview_video,
                'preview_url' => $data->preview_url,
                'preview_type' => $data->preview_type,
                'date_time' => $data->date_time,
                'audio' => $data->audio,
                'detail' => $data->detail,
                'aws_upload' => $data->aws_upload,
                'type' => $data->type,
                'drip_type' => $data->file,
                'drip_date' => $data->drip_date,
                'drip_days' => $data->drip_days,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result), 200);
    }

    public function getclass(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseClass::where('id', $id)->exists()) {
            $data = CourseClass::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'coursechapter_id' => $data->coursechapter_id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('title')),
                'status' => $data->status,
                'file' => $data->file,
                'position' => $data->position,
                'duration' => $data->duration,
                'featured' => $data->featured,
                'url' => $data->url,
                'size' => $data->size,
                'image' => $data->image,
                'video' => $data->video,
                'pdf' => $data->pdf,
                'file' => $data->file,
                'zip' => $data->zip,
                'preview_video' => $data->preview_video,
                'preview_url' => $data->preview_url,
                'preview_type' => $data->preview_type,
                'date_time' => $data->date_time,
                'audio' => $data->audio,
                'detail' => $data->detail,
                'aws_upload' => $data->aws_upload,
                'type' => $data->type,
                'drip_type' => $data->file,
                'drip_date' => $data->drip_date,
                'drip_days' => $data->drip_days,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );

            return response()->json(array('data' => $result), 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function createclass(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $this->validate($request, [
            "course_id" => "required",
            "title" => "required",
            "status" => "required",
        ]);

        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $courseclass = new CourseClass;
        $courseclass->course_id = $request->course_id;
        $courseclass->coursechapter_id = $request->course_chapters;
        $courseclass->title = $request->title;
        $courseclass->duration = $request->duration;
        $courseclass->status = $request->status;
        $courseclass->featured = $request->featured;
        $courseclass->video = $request->video;
        $courseclass->image = $request->image;
        $courseclass->zip = $request->zip;
        $courseclass->pdf = $request->pdf;
        $courseclass->size = $request->size;
        $courseclass->url = $request->url;
        $courseclass->date_time = $request->date_time;
        $courseclass->detail = $request->detail;

        $courseclass->user_id = Auth::user()->id;

        $courseclass['position'] = (CourseClass::count() + 1);

        if ($request->drip_type == "date") {
            $courseclass->drip_type = $request->drip_type;
            $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
            $courseclass->drip_date = $start_time;
            $courseclass->drip_days = null;
        } elseif ($request->drip_type == "days") {

            $courseclass->drip_type = $request->drip_type;
            $courseclass->drip_days = $request->drip_days;
            $courseclass->drip_date = null;
        } else {

            $courseclass->drip_days = null;
            $courseclass->drip_date = null;
        }


        $courseclass->status = $request->status;
        $courseclass->featured = $request->featured;

        if ($request->type == "video") {
            $courseclass->type = "video";

            if ($request->checkVideo == "url") {
                $courseclass->url = $request->vidurl;
                $courseclass->video = null;
                $courseclass->iframe_url = null;
            } else if ($request->checkVideo == "uploadvideo") {
                if ($file = $request->file('video_upld')) {
                    $name = 'video_course_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move('video/class', $name);
                    $courseclass->video = $name;
                    $courseclass->url = null;
                    $courseclass->iframe_url = null;
                }
            } else if ($request->checkVideo == "iframeurl") {
                $courseclass->iframe_url = $request->iframe_url;
                $courseclass->url = null;
                $courseclass->video = null;
            } elseif ($request->checkVideo == "liveurl") {
                $courseclass->url = $request->vidurl;
                $courseclass->video = null;
                $courseclass->iframe_url = null;
            } elseif ($request->checkVideo == "aws_upload") {

                if ($request->hasFile('aws_upload')) {

                    $file = request()->file('aws_upload');
                    $videoname = time() . '_' . $file->getClientOriginalName();

                    $t = Storage::disk('s3')->put($videoname, file_get_contents($file), 'public');
                    $upload_video = $videoname;
                    $aws_url = env('AWS_URL') . $videoname;

                    $videoname = Storage::disk('s3')->url($videoname);

                    $courseclass->aws_upload = $aws_url;
                }
            } elseif ($request->checkVideo == "youtube") {
                $courseclass->url = $request->vidurl;
                $courseclass->video = null;
                $courseclass->iframe_url = null;
            } elseif ($request->checkVideo == "vimeo") {
                $courseclass->url = $request->vidurl;
                $courseclass->video = null;
                $courseclass->iframe_url = null;
            }
        }




        if (!isset($request->preview_type)) {
            $courseclass['preview_url'] = $request->url;
            $courseclass['preview_type'] = "url";
        } else {
            if ($file = $request->file('video')) {

                $filename = time() . $file->getClientOriginalName();
                $file->move('video/class/preview', $filename);
                $courseclass['preview_video'] = $filename;
            }
            $courseclass['preview_type'] = "video";
        }



        if ($request->type == "image") {
            $courseclass->type = "image";

            if ($request->checkImage == "url") {
                $courseclass->url = $request->imgurl;
                $courseclass->image = null;
            } else if ($request->checkImage == "uploadimage") {
                if ($file = $request->file('image')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move('images/class', $name);
                    $courseclass->image = $name;
                    $courseclass->url = null;
                }
            }
        }


        if ($request->type == "zip") {
            $courseclass->type = "zip";

            if ($request->checkZip == "zipURLEnable") {
                $courseclass->url = $request->zipurl;
                $courseclass->zip = null;
            } else if ($request->checkZip == "zipEnable") {
                if ($file = $request->file('uplzip')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move('files/zip', $name);
                    $courseclass->zip = $name;
                    $courseclass->url = null;
                }
            }
        }


        if ($request->type == "pdf") {
            $courseclass->type = "pdf";

            if ($request->checkPdf == "pdfURLEnable") {
                $courseclass->url = $request->pdfurl;
                $courseclass->pdf = null;
            } elseif ($request->checkPdf == "pdfEnable") {
                if ($file = $request->file('pdf')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move('files/pdf', $name);
                    $courseclass->pdf = $name;
                    $courseclass->url = null;
                }
            }
        }


        if ($request->type == "audio") {
            $courseclass->type = "audio";

            if ($request->checkAudio == "audiourl") {
                $courseclass->url = $request->audiourl;
                $courseclass->audio = null;
            } elseif ($request->checkAudio == "uploadaudio") {
                if ($file = $request->file('audioupload')) {
                    $name = time() . $file->getClientOriginalName();
                    $file->move('files/audio', $name);
                    $courseclass->audio = $name;
                    $courseclass->url = null;
                }
            }
        }

        if ($file = $request->file('file')) {

            $path = 'files/class/material/';

            if (!file_exists(public_path() . '/' . $path)) {

                $path = 'files/class/material/';
                File::makeDirectory(public_path() . '/' . $path, 0777, true);
            }

            $filename = time() . $file->getClientOriginalName();
            $file->move('files/class/material', $filename);
            $courseclass['file'] = $filename;
        }




        $courseclass->save();

        // Subtitle 
        if ($request->has('sub_t')) {
            foreach ($request->file('sub_t') as $key => $image) {

                $name = $image->getClientOriginalName();
                $image->move(public_path() . '/subtitles/', $name);

                $form = new Subtitle();
                $form->sub_lang = $request->sub_lang[$key];
                $form->sub_t = $name;
                $form->c_id = $courseclass->id;
                $form->save();
            }
        }




        return response()->json([
                    "message" => "Added successfully",
                    'courseclass' => $courseclass
                        ], 201);
    }

    public function updateclass(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseClass::where('id', $id)->exists()) {

            $courseclass = CourseClass::findOrFail($id);

            $courseclass->coursechapter_id = $request->coursechapter_id;
            $courseclass->title = $request->title;
            $courseclass->duration = $request->duration;
            $courseclass->status = $request->status;
            $courseclass->featured = $request->featured;
            $courseclass->size = $request->size;
            $courseclass->date_time = $request->date_time;
            $courseclass->detail = $request->detail;

            $coursefind = CourseChapter::findOrFail($request->coursechapter);
            $maincourse = Course::findorfail($coursefind->course_id);

            if ($request->drip_type == "date") {
                $courseclass->drip_type = $request->drip_type;
                $start_time = date('Y-m-d\TH:i:s', strtotime($request->drip_date));
                $courseclass->drip_date = $start_time;
                $courseclass->drip_days = null;
            } elseif ($request->drip_type == "days") {

                $courseclass->drip_type = $request->drip_type;
                $courseclass->drip_days = $request->drip_days;
                $courseclass->drip_date = null;
            } else {

                $courseclass->drip_days = null;
                $courseclass->drip_date = null;
            }


            if ($request->type == "video") {

                $courseclass->type = "video";

                if ($request->checkVideo == "url") {

                    $courseclass->url = $request->vidurl;
                    $courseclass->video = null;
                    $courseclass->iframe_url = null;
                    $courseclass->date_time = null;
                    $courseclass->aws_upload = null;
                } else if ($request->checkVideo == "uploadvideo") {

                    if ($file = $request->file('video_upld')) {
                        if ($courseclass->video != "") {
                            $content = @file_get_contents(public_path() . '/video/class/' . $courseclass->video);

                            if ($content) {
                                unlink(public_path() . '/video/class/' . $courseclass->video);
                            }
                        }

                        $name = 'video_course_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move('video/class', $name);
                        $courseclass->video = $name;
                        $courseclass->url = null;
                        $courseclass->iframe_url = null;
                        $courseclass->date_time = null;
                        $courseclass->aws_upload = null;
                    }
                } else if ($request->checkVideo == "iframeurl") {
                    $courseclass->iframe_url = $request->iframe_url;
                    $courseclass->url = null;
                    $courseclass->video = null;
                    $courseclass->date_time = null;
                    $courseclass->aws_upload = null;
                } elseif ($request->checkVideo == "liveurl") {
                    $courseclass->url = $request->vidurl;
                    $courseclass->video = null;
                    $courseclass->iframe_url = null;
                    $courseclass->aws_upload = null;
                } elseif ($request->checkVideo == "aws_upload") {

                    if ($request->hasFile('aws_upload')) {

                        $file = request()->file('aws_upload');
                        $videoname = time() . '_' . $file->getClientOriginalName();

                        $t = Storage::disk('s3')->put($videoname, file_get_contents($file), 'public');
                        $upload_video = $videoname;
                        $aws_url = env('AWS_URL') . $videoname;

                        $videoname = Storage::disk('s3')->url($videoname);

                        $courseclass->aws_upload = $aws_url;
                        $courseclass->video = null;
                        $courseclass->iframe_url = null;
                        $courseclass->date_time = null;
                    }
                } elseif ($request->checkVideo == "youtube") {
                    $courseclass->url = $request->vidurl;
                    $courseclass->video = null;
                    $courseclass->iframe_url = null;
                } elseif ($request->checkVideo == "vimeo") {
                    $courseclass->url = $request->vidurl;
                    $courseclass->video = null;
                    $courseclass->iframe_url = null;
                }
            }


            if ($request->type == "audio") {
                $courseclass->type = "audio";

                if ($request->checkAudio == "audiourl") {
                    $courseclass->url = $request->audiourl;
                    $courseclass->audio = null;
                } else if ($request->checkAudio == "uploadaudio") {
                    if ($file = $request->file('audio')) {
                        if ($courseclass->audio != "") {
                            $content = @file_get_contents(public_path() . '/files/audio/' . $courseclass->audio);

                            if ($content) {
                                unlink(public_path() . '/files/audio/' . $courseclass->audio);
                            }
                        }

                        $name = time() . $file->getClientOriginalName();
                        $file->move('files/audio', $name);
                        $courseclass->audio = $name;
                        $courseclass->url = null;
                    }
                }
            }


            if ($request->type == "image") {
                $courseclass->type = "image";

                if ($request->checkImage == "url") {
                    $courseclass->url = $request->imgurl;
                    $courseclass->image = null;
                } else if ($request->checkImage == "uploadimage") {
                    if ($file = $request->file('image')) {
                        if ($courseclass->image != "") {
                            $content = @file_get_contents(public_path() . '/images/class/' . $courseclass->image);

                            if ($content) {
                                unlink(public_path() . '/images/class/' . $courseclass->image);
                            }
                        }

                        $name = time() . $file->getClientOriginalName();
                        $file->move('images/class', $name);
                        $courseclass->image = $name;
                        $courseclass->url = null;
                    }
                }
            }

            if ($request->type == "zip") {

                $courseclass->type = "zip";

                if ($request->checkZip == "zipURLEnable") {
                    $courseclass->url = $request->zipurl;
                    $courseclass->zip = null;
                } else if ($request->checkZip == "zipEnable") {
                    if ($file = $request->file('uplzip')) {
                        $content = @file_get_contents(public_path() . '/files/zip/' . $courseclass->zip);

                        if ($content) {
                            unlink(public_path() . '/files/zip/' . $courseclass->zip);
                        }

                        $name = time() . $file->getClientOriginalName();
                        $file->move('files/zip', $name);
                        $courseclass->zip = $name;
                        $courseclass->url = null;
                    }
                }
            }


            if ($request->type == "pdf") {
                $courseclass->type = "pdf";

                if ($request->checkPdf == "url") {
                    $courseclass->url = $request->pdfurl;
                    $courseclass->pdf = null;
                } else if ($request->checkPdf == "uploadpdf") {
                    if ($file = $request->file('pdf')) {
                        $content = @file_get_contents(public_path() . '/files/pdf/' . $courseclass->pdf);

                        if ($content) {
                            unlink(public_path() . '/files/pdf/' . $courseclass->pdf);
                        }


                        $name = time() . $file->getClientOriginalName();
                        $file->move('files/pdf', $name);
                        $courseclass->pdf = $name;
                        $courseclass->url = null;
                    }
                }
            }




            if (isset($request->preview_type)) {
                $courseclass['preview_type'] = "video";
            } else {
                $courseclass['preview_type'] = "url";
            }


            if (!isset($request->preview_type)) {
                $courseclass->preview_url = $request->preview_url;
                $courseclass->preview_video = null;
                $courseclass['preview_type'] = "url";
            } else {

                if ($file = $request->file('video')) {
                    // return $request;
                    if ($courseclass->preview_video != "") {
                        $content = @file_get_contents(public_path() . '/video/class/preview/' . $courseclass->preview_video);
                        if ($content) {
                            unlink(public_path() . '/video/class/preview/' . $courseclass->preview_video);
                        }
                    }

                    $filename = time() . $file->getClientOriginalName();
                    $file->move('video/class/preview', $filename);
                    $courseclass['preview_video'] = $filename;
                    $courseclass->preview_url = null;

                    $courseclass['preview_type'] = "video";
                }
            }

            if ($file = $request->file('file')) {
                $path = 'files/class/material/';

                if (!file_exists(public_path() . '/' . $path)) {

                    $path = 'files/class/material/';
                    File::makeDirectory(public_path() . '/' . $path, 0777, true);
                }

                if ($courseclass->file != "") {
                    $class_file = @file_get_contents(public_path() . '/files/class/material/' . $courseclass->file);

                    if ($class_file) {
                        unlink('files/class/material/' . $courseclass->file);
                    }
                }
                $name = time() . $file->getClientOriginalName();
                $file->move('files/class/material', $name);
                $courseclass['file'] = $name;
            }


            if (isset($request->status)) {
                $courseclass['status'] = '1';
            } else {
                $courseclass['status'] = '0';
            }

            if (isset($request->featured)) {
                $courseclass['featured'] = '1';
            } else {
                $courseclass['featured'] = '0';
            }


            $courseclass->save();

            return response()->json([
                        "message" => "records updated successfully", 'data' => $data
                            ], 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deleteclass(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (CourseClass::where('id', $id)->exists()) {
            $data = CourseClass::find($id);

            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllrelated(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $data = RelatedCourse::get();

        $result = array();

        foreach ($language as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('related_course' => $result));
    }

    public function getrelated(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (RelatedCourse::where('id', $id)->exists()) {

            $data = RelatedCourse::first();

            $result = array();

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );

            return response()->json(array('related_course' => $result));
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function createrelated(Request $request) {


        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $data = new RelatedCourse;
        $data->course_id = $request->course_id;
        $data->status = $request->status;
        $data->save();

        return response()->json([
                    "message" => "created successfully",
                    'related_course' => $data
        ]);
    }

    public function updaterelated(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (RelatedCourse::where('id', $id)->exists()) {
            $data = RelatedCourse::find($id);

            $data->name = isset($request->name) ? $request->name : $data->name;
            $data->status = isset($request->status) ? $request->status : $data->status;
            $data->save();

            return response()->json([
                        "message" => "records updated successfully",
                        'related_course' => $data
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deleterelated(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (RelatedCourse::where('id', $id)->exists()) {
            $data = RelatedCourse::find($id);
            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllquestions(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $auth = Auth::user();

        $question = Question::where('instructor_id', $auth->id)->get();

        $result = array();

        foreach ($question as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course_id,
                'course' => $data->courses->title,
                'user_id' => $data->id,
                'user' => optional($data->user)['fname'] . ' ' . optional($data->user)['lname'],
                'instructor_id' => $data->user_id,
                'question' => $data->question,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('course_questions' => $result));
    }

    public function getquestions(Request $request, $id=null) {

        if (Question::where('id', $id)->exists()) {
            $ques = Question::find($id);
            $roles = $ques->user->getRoleNames(); 
            $result = [
                'question_id' => $ques->id,
                'user' => $ques->user->fname . ' ' . $ques->user->lname,
                'user_id' => $ques->user->id,
                'role' => count($roles)?ucfirst($roles[0]):"User",
                'imagepath' => $ques->user->user_img ? url('images/user_img/' . $ques->user->user_img) : null,
                'question' => strip_tags($ques->question),
                'created_at' => $ques->created_at->diffForHumans(),
                'timestamp' => $ques->created_at,
                'answer' => $ques->answers->count() ?? 0,
            ];

            return response()->json($result);
        } else {
            return response()->json(array("errors"=>["message"=>[__("data not found")]]), 404);
        }
    }

    public function createquestions(Request $request) {


        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $auth = Auth::user();

        $data = new Question;
        $data->course_id = $request->course_id;
        $data->user_id = $auth->id;
        $data->instructor_id = $auth->id;
        $data->question = $request->question;
        $data->status = $request->status;
        $data->save();

        return response()->json([
                    "message" => "created successfully",
                    'course_questions' => $data
        ]);
    }

    public function updatequestions(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $auth = Auth::user();

        if (Question::where('id', $id)->exists()) {
            $data = Question::find($id);

            $data->course_id = isset($request->course_id) ? $request->course_id : $data->course_id;
            $data->user_id = isset($auth->id) ? $auth->id : $data->user_id;
            $data->instructor_id = isset($auth->id) ? $auth->id : $data->instructor_id;
            $data->question = isset($request->question) ? $request->question : $data->question;
            $data->status = isset($request->status) ? $request->status : $data->status;
            $data->save();

            return response()->json([
                        "message" => "records updated successfully",
                        'questions' => $data
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deletequestions(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Question::where('id', $id)->exists()) {
            $data = Question::find($id);
            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllanswer(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $answers = Answer::where('instructor_id', Auth::user()->id)->get();

        $result = array();

        foreach ($answers as $data) {

            $result[] = array(
                'id' => $data->id,
                'course' => $data->courses->title,
                'user_fname' => $data->user->fname,
                'user_lname' => $data->user->lname,
                'question' => $data->question->question,
                'answer' => $data->answer,
                'status' => $data->status,
            );
        }

        return response()->json(array('course_answer' => $result));
    }

    public function getanswer(Request $request, $id) {


        if (Answer::where('id', $id)->exists()) {

            $ques = Answer::findOrFail($id);
            $roles = $ques->user->getRoleNames();
            $result = [
                'question_id' => $ques->question_id,
                'answer_id' => $ques->id,
                'user' => $ques->user->fname . ' ' . $ques->user->lname,
                'role' => count($roles)?ucfirst($roles[0]):"User",
                'user_id' => $ques->user->id,
                'imagepath' => $ques->user->user_img ? url('images/user_img/' . $ques->user->user_img) : null,
                'answer' => strip_tags($ques->answer),
                'created_at' => $ques->created_at->diffForHumans(),
            ];

            return response()->json($result);
        } else {
            return response()->json(array("errors" => ["message" => [__('Answer not found')]]), 404);
        }
    }

    public function updateanswer(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Answer::where('id', $id)->exists()) {
            $answer = Answer::find($id);
            $data->answer = isset($request->answer) ? $request->answer : $answer->answer;
            $answer->update($data);

            return response()->json([
                        "message" => "Updated successfully",
            ]);
        } else {
            return response()->json([
                        "message" => "Data not found"
                            ], 404);
        }
    }

    public function deleteanswer(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Answer::where('id', $id)->exists()) {
            $data = Answer::find($id);
            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllrefund(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }


        $user = Auth::user();

        $enroll = RefundCourse::where('instructor_id', $user->id)->where('status', 1)->get();

        $enroll_details = array();

        if (isset($enroll)) {

            foreach ($enroll as $enrol) {


                $enroll_details[] = array(
                    'id' => $enrol->id,
                    'instructor_id' => $enrol->instructor_id,
                    'user_id' => $enrol->user_id,
                    'user' => optional($enrol->user)['fname'] . ' ' . optional($enrol->user)['lname'],
                    'order_id' => $enrol->order_id,
                    'refund_transaction_id' => $enroll->refund_transaction_id,
                    'ref_id' => $enroll->ref_id,
                    'txn_fee' => $enroll->txn_fee,
                    'payment_method' => $enrol->payment_method,
                    'total_amount' => $enrol->total_amount,
                    'currency' => $enrol->currency,
                    'currency_icon' => $enrol->currency_icon,
                    'reason' => $enrol->reason,
                    'detail' => $enrol->detail,
                    'approved' => $enrol->approved,
                    'bank_id' => $enrol->bank_id,
                    'order_refund_id' => $enrol->order_refund_id,
                    'refunded_amt' => $enrol->refunded_amt,
                    'status' => $enrol->status,
                    'created_at' => $enrol->created_at,
                    'updated_at' => $enrol->updated_at,
                );
            }
            return response()->json(array('refund' => $enroll_details), 200);
        }

        return response()->json(array('refund' => $enroll_details), 200);
    }

    public function getrefund(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (RefundCourse::where('id', $id)->exists()) {
            $enrol = RefundCourse::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $enrol->id,
                'instructor_id' => $enrol->instructor_id,
                'user_id' => $enrol->user_id,
                'user' => optional($enrol->user)['fname'] . ' ' . optional($enrol->user)['lname'],
                'order_id' => $enrol->order_id,
                'refund_transaction_id' => $enroll->refund_transaction_id,
                'ref_id' => $enroll->ref_id,
                'txn_fee' => $enroll->txn_fee,
                'payment_method' => $enrol->payment_method,
                'total_amount' => $enrol->total_amount,
                'currency' => $enrol->currency,
                'currency_icon' => $enrol->currency_icon,
                'reason' => $enrol->reason,
                'detail' => $enrol->detail,
                'approved' => $enrol->approved,
                'bank_id' => $enrol->bank_id,
                'order_refund_id' => $enrol->order_refund_id,
                'refunded_amt' => $enrol->refunded_amt,
                'status' => $enrol->status,
                'created_at' => $enrol->created_at,
                'updated_at' => $enrol->updated_at,
            );

            return response()->json(array('data' => $result), 200);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function updaterefund(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (RefundCourse::where('id', $id)->exists()) {
            $data = RefundCourse::find($id);

            RefundCourse::where('id', $id)
                    ->update([
                        'status' => 1,
                        'order_refund_id' => $request->order_id,
                        'refund_transaction_id' => $request->txn_id,
                        'txn_fee' => null,
                        'refunded_amt' => $request->amount,
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]);

            Order::where('id', $request->order_id)->where('status', '1')
                    ->update([
                        'refunded' => 1,
            ]);

            return response()->json([
                        "message" => "records updated successfully",
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deleterefund(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (RefundCourse::where('id', $id)->exists()) {
            $data = RefundCourse::find($id);

            $data->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function getAllassignment(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        $user = Auth::user();

        $assignment = Assignment::where('instructor_id', $user->id)->get();

        $result = array();

        foreach ($assignment as $data) {

            $result[] = array(
                'id' => $data->id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('title')),
                'user_id' => $data->user->fname,
                'course_id' => $data->courses->title,
                'instructor_id' => $data->instructor->fname,
                'assignment' => $data->assignment,
                'type' => $data->type,
                'chapter_id' => $data->chapter->chapter_name,
                'detail' => $data->detail,
                'rating' => $data->rating,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result));
    }

    public function getassignment(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Assignment::where('id', $id)->exists()) {

            $data = Assignment::where('id', $id)->first();

            $result = array();

            $result[] = array(
                'id' => $data->id,
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $data->getTranslations('title')),
                'user_id' => $data->user->fname,
                'course_id' => $data->courses->title,
                'instructor_id' => $data->instructor->fname,
                'assignment' => $data->assignment,
                'type' => $data->type,
                'chapter_id' => $data->chapter->chapter_name,
                'detail' => $data->detail,
                'rating' => $data->rating,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );

            return response()->json(array('data' => $result));
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function updateassignment(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Assignment::where('id', $id)->exists()) {
            $language = Assignment::find($id);

            if (isset($request->type)) {
                Assignment::where('id', $id)
                        ->update(['rating' => $request->rating, 'type' => 1]);
            } else {
                Assignment::where('id', $id)
                        ->update(['rating' => NULL, 'type' => 0]);
            }
        } else {
            return response()->json([
                        "message" => "data not found"
                            ], 404);
        }
    }

    public function deleteassignment(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Assignment::where('id', $id)->exists()) {
            $assignment = Assignment::find($id);
            $assignment->delete();

            return response()->json([
                        "message" => "records deleted"
            ]);
        } else {
            return response()->json([
                        "message" => "Assignment not found"
                            ], 404);
        }
    }

    public function toinvolvecourses(Request $request) {
        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $user = Auth::user();

        $all_course = Course::where('involvement_request', '1')->where('user_id', '!=', $user->id)->get();

        foreach ($all_course as $course) {

            $result[] = array(
                'id' => $course->id,
                'subcategory_id' => $course->subcategory_id,
                'category_id' => $course->category->title,
                'childcategory_id' => $course->childcategory_id,
                'language_id' => $course->language->name,
                'user_id' => $course->user_id,
                'user' => optional($course->user)['fname'] . ' ' . optional($course->user)['lname'],
                'title' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('title')),
                'short_detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('short_detail')),
                'requirement' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('requirement')),
                'detail' => array_map(function ($lang) {
                    return trim(preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($lang))));
                }, $course->getTranslations('detail')),
                'price' => $course->price,
                'discount_price' => $course->discount_price,
                'day' => $course->day,
                'video' => $course->video,
                'video_path' => url('video/preview/' . $course->video),
                'video_url' => $course->video_url,
                'url' => $course->url,
                'featured' => $course->featured,
                'status' => $course->status,
                'slug' => $course->slug,
                'duration' => $course->duration,
                'duration_type' => $course->duration_type,
                'instructor_revenue' => $course->instructor_revenue,
                'involvement_request' => $course->involvement_request,
                'refund_policy_id' => $course->refund_policy_id,
                'assignment_enable' => $course->assignment_enable,
                'appointment_enable' => $course->appointment_enable,
                'certificate_enable' => $course->certificate_enable,
                'course_tags' => $course->course_tags,
                'level_tags' => $course->level_tags,
                'preview_image' => $course->preview_image,
                'imagepath' => url('images/course/' . $course->preview_image),
                'course_tags' => $course->course_tags,
                'level_tags' => $course->level_tags,
                'reject_txt' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($course->reject_txt))),
                'drip_enable' => $course->drip_enable,
                'preview_type' => $course->preview_type,
                'updated_at' => $course->created_at,
            );
        }


        return response()->json(array('courses' => $result), 200);
    }

    public function requesttoinvolve(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
                    'reason' => 'required',
                    'course_id' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $user = Auth::user();

        $data = new Involvement;
        $data->user_id = $user->id;
        $data->course_id = $request->course_id;
        $data->reason = $request->reason;
        $data->status = 0;
        $data->save();

        return response()->json([
                    "message" => "Involvement request successfully submited!",
                    'request' => $data
        ]);
    }

    public function Allinvolvementrequest(Request $request) {
        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $user = Auth::user();

        $involve_requests = Involvement::where('user_id', '!=', $user->id)->where('status', '0')->get();

        $result = array();

        foreach ($involve_requests as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course->title,
                'user_id' => $data->user->fname,
                'reason' => $data->reason,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result));
    }

    public function involvedcourses(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if ($errors->first('secret')) {
                return response()->json(['message' => $errors->first('secret'), 'status' => 'fail']);
            }
        }

        $user = Auth::user();

        $involve_requests = Involvement::where('user_id', '!=', $user->id)->where('status', '0')->get();

        $result = array();

        foreach ($involve_requests as $data) {

            $result[] = array(
                'id' => $data->id,
                'course_id' => $data->course->title,
                'user_id' => $data->user->fname,
                'reason' => $data->reason,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('data' => $result));
    }

    public function Allannouncement(Request $request) {
        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }

        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();

        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (Announcement::where('user_id', Auth::user()->id)->exists()) {

            $announcement = Announcement::where('user_id', Auth::user()->id)->get();

            $result = array();
            foreach ($announcement as $data) {
                $result[] = array(
                    'id' => $data->id,
                    'announsment' => $data->announsment,
                    'course_id' => $data->courses->title,
                    'status' => $data->status,
                );
            }

            return response()->json(array('data' => $result));
        } else {
            return response()->json([
                        "message" => "announcement not found",
                            ], 404);
        }
    }

    public function vacationmode(Request $request) {
        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }
        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }
        $vacation = User::where('id', Auth::user()->id)->select(['vacation_start', 'vacation_end'])->get();
        return response()->json(array('vacation' => $vacation));
    }

    public function vacationmodeupdate(Request $request) {

        $validator = Validator::make($request->all(), [
                    'secret' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['Secret Key is required'], 402);
        }
        $key = DB::table('api_keys')->where('secret_key', '=', $request->secret)->first();
        if (!$key) {
            return response()->json(['Invalid Secret Key !'], 400);
        }

        if (User::where('id', Auth::user()->id)->exists()) {
            $vacation = User::findOrFail(Auth::user()->id);
            $vacationmode = User::where('id', Auth::user()->id)->select(['vacation_start', 'vacation_end'])->get();
            $data['vacation_start'] = isset($request->vacation_start) ? $request->vacation_start : $vacation->vacation_start;
            $data['vacation_end'] = isset($request->vacation_end) ? $request->vacation_end : $vacation->vacation_end;
            $vacation->update($data);
            return response()->json([
                        "message" => "Vacation mode updated successfully",
                        'vacation' => $vacationmode
            ]);
        } else {
            return response()->json([
                        "message" => "language not found"
                            ], 404);
        }
    }

}
