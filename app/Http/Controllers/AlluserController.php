<?php

namespace App\Http\Controllers;

use App\BBL;
use App\Cart;
use App\City;
use App\User;
use App\Order;
use App\State;
use App\Answer;
use App\Course;
use App\Allcity;
use App\Country;
use App\Meeting;
use App\Allstate;
use App\Question;
use App\Wishlist;
use App\Affiliate;
use App\Allcountry;
use App\Instructor;
use App\SubCategory;
use App\BundleCourse;
use App\ReviewRating;
use App\ChildCategory;
use App\CourseProgress;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AlluserController extends Controller
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

    public function viewAllUser(Request $request)
    {
        abort_if(!auth()->user()->can('Alluser.view'), 403, __('User does not have the right permissions'));

        $data = User::query()
                        ->select('id', 'fname', 'lname', 'dob', 'email', 'mobile', 'gender', 'role', 'institute', 'major', 'test_user', 'status')
                        ->where('role', 'user')
                        ->latest();

        if ($request->ajax()) {
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($row) {

                        $chk = "<div class='inline'>
                              <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                              <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                        return $chk;
                    })
                    ->editColumn('name', function ($row) {

                        return $row->fname ? ($row->lname ? $row->fname . ' ' . $row->lname : $row->fname) : '';
                    })
                    ->editColumn('email', function ($row) {

                        return $row->email ?? '';
                    })
                    ->editColumn('mobile', function ($row) {

                        return $row->mobile ?? '';
                    })
                    ->editColumn('test_user', 'admin.testuser.test_user')
                    ->editColumn('status', 'admin.alluser.status')
                    ->editColumn('action', 'admin.alluser.action')
                    ->rawColumns(['checkbox', 'name', 'email', 'mobile', 'test_user', 'status','action'])
                    ->toJson();
        }

        return view('admin.alluser.index');
    }


    public function create()
    {
        abort_if(!auth()->user()->can('Alluser.create'), 403, __('User does not have the right permissions'));

        // $cities = Allcity::all();
        // $states = Allstate::all();
        // $countries = Country::all();

        $instituteCategories = SubCategory::select('id','title','slug')
                                            ->active()
                                            ->get();

        $majorCategories = ChildCategory::select('id','title','slug')
                                            ->active()
                                            ->get();

        return view('admin.alluser.adduser', compact(['instituteCategories', 'majorCategories']));
    }


    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('Alluser.create'), 403, __('User does not have the right permissions'));

        $request->validate([
            'fname' => 'required|alpha|min:3|max:20',
            'lname' => 'required|alpha|min:3|max:20',
            'institute' => 'required',
            'major' => 'required',
            'full_phone' => 'required|unique:users,mobile',
            'email' => 'required|email:rfc,dns|max:40|unique:users,email',
            'user_img' => 'mimes:jpg,jpeg,png|max:10240',
            'timezone' => 'required',
            'role' => 'required|in:user',
            'password' => [ 'max:50',
                            Password::min(8)
                            ->mixedCase()
                            ->numbers()
                        ],
        ], [
            'fname.required' => __('First Name is required'),
            'fname.min' => __('First Name must contain at least 3 characters'),
            'fname.max' => __('First Name should not be more than 20 characters'),
            'fname.alpha' => __('First Name should only contains letters'),
            'lname.required' => __('Last Name is required'),
            'lname.min' => __('Last Name must contain at least 3 characters'),
            'lname.max' => __('Last Name should not be more than 20 characters'),
            'lname.alpha' => __('Last Name should only contains letters'),
            'email.required' => __("Email is required"),
            'institute.required' => __("Institute is required"),
            'major.required' => __("Major is required"),
            'email.email' => __("Email is invalid"),
            'email.max' => __('Email maximum length is 40'),
            'email.unique' => __('Email is already taken'),
            'user_img.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'user_img.max' => __('Image size should not be more than 10 MB'),
            'timezone.required' => __('Timezone is required'),
            'full_phone.required' => __('Mobile number is required'),
            'full_phone.unique' => __('Mobile number is already taken'),
            'role.required' => __('Role is required'),
            'password.required' => __('Password is required'),
            'password.min' => __('Password must be at least 8 characters'),
            'password.max' => __('Password should not be more than 50 characters'),
            'password.mixedCase' => __('Password must contain at least one uppercase and one lowercase letter'),
            'password.numbers' => __('Password must contain at least one number'),
            'user_img.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'user_img.max' => __('Image size should not be greater 10 MB'),
        ]);

        $input = $request->all();

        if ($file = $request->file('user_img')) {
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/user_img/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
            $input['user_img'] = $image;
        }

        $input['status'] = isset($request->status)  ? 1 : 0;
        $input['password'] = Hash::make($request->password);
        // $input['mobile'] = substr($request->full_phone, 1);
        $input['country_code'] = substr($request->full_phone, 0, strpos($request->full_phone, str_replace(' ', '', $request->mobile)));
        $input['mobile'] = $request->full_phone;
        $input['email_verified_at'] = \Carbon\Carbon::now()->toDateTimeString();

        if (Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')) {
            $affiliate = Affiliate::first();
            if (isset($affiliate) && $affiliate->status == 1) {
                $input['affiliate_id'] = User::createReferCode(); // Affiliate ID is actually a reffer code
            } else {
                $input['affiliate_id'] = null;
            }
        } else {
            $input['affiliate_id'] = null;
        }
        
        $data = User::create($input);
        $data->assignRole($request->role);

        $data->save();

        // Session::flash('success', trans('flash.AddedSuccessfully'));
        return redirect()->route('alluser.index')->with('success', trans('flash.AddedSuccessfully'));
    }


    public function show(User $user)
    {
        //
    }


    public function edit($id)
    {
        abort_if(!auth()->user()->can('Alluser.edit'), 403, __('User does not have the right permissions'));

        // $categories = \App\Categories::where('status', 1)->get(['id','title']);
        $roles = Role::all();

        if (Auth::user()->role == 'admin') {
            $user = User::findOrFail($id);
        } else {
            $user = User::findOrFail(Auth::user()->id);
        }

        $instituteCategories = SubCategory::select('id','title','slug')
                                            ->active()
                                            ->get();

        $majorCategories = ChildCategory::select('id','title','slug')
                                            ->active()
                                            ->get();

        return view('admin.alluser.edit', compact(['instituteCategories', 'majorCategories', 'user', 'roles']));
    }


    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('Alluser.edit'), 403, __('User does not have the right permissions'));

        if (Auth::user()->role == 'admin') {
            $user = User::findOrFail($id);
        } else {
            $user = User::findOrFail(Auth::user()->id);
        }

        $request->validate([
            'fname' => 'required|alpha|min:3|max:20',
            'lname' => 'required|alpha|min:3|max:20',
            'email' => 'required|email:rfc,dns|max:40|unique:users,email,' . $id,
            'institute' => 'required',
            'major' => 'required',
            'user_img' => 'mimes:jpg,jpeg,png|max:10240',
            'timezone' => 'required',
            'full_phone' => 'required|unique:users,mobile,' . $id,
            'role' => 'required|in:user',
            'password' => [ 'nullable',
                            'max:50',
                            Password::min(8)
                            ->mixedCase()
                            ->numbers()
                        ],
        ], [
            'fname.required' => __('First Name is required'),
            'fname.min' => __('First Name must contain at least 3 characters'),
            'fname.max' => __('First Name should not be more than 20 characters'),
            'fname.alpha' => __('First Name should only contains letters'),
            'lname.required' => __('Last Name is required'),
            'lname.min' => __('Last Name must contain at least 3 characters'),
            'lname.max' => __('Last Name should not be more than 20 characters'),
            'lname.alpha' => __('Last Name should only contains letters'),
            'email.required' => __("Email is required"),
            'institute.required' => __("Institute is required"),
            'major.required' => __("Major is required"),
            'email.email' => __("Email is invalid"),
            'email.max' => __('Email maximum length is 40'),
            'email.unique' => __('Email is already taken'),
            'user_img.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'user_img.max' => __('Image size should not be more than 10 MB'),
            'timezone.required' => __('Timezone is required'),
            'full_phone.required' => __('Mobile number is required'),
            'full_phone.unique' => __('Mobile number is already taken'),
            'role.required' => __('Role is required'),
            'role.in' => __('The selected role must be user'),
            'password.required' => __('Password is required'),
            'password.min' => __('Password must be at least 8 characters'),
            'password.max' => __('Password should not be more than 50 characters'),
            'password.mixedCase' => __('Password must contain at least one uppercase and one lowercase letter'),
            'password.numbers' => __('Password must contain at least one number'),
        ]);

        $input = $request->all();

        if ($file = $request->file('user_img')) {
            if ($user->user_img != null) {
                $content = @file_get_contents(public_path() . '/images/user_img/' . $user->user_img);
                if ($content) {
                    unlink(public_path() . '/images/user_img/' . $user->user_img);
                }
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/user_img/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
            $input['user_img'] = $image;
        }

        $verified = \Carbon\Carbon::now()->toDateTimeString();

        if (isset($request->verified)) {
            $input['email_verified_at'] = $verified;
        } else {
            $input['email_verified_at'] = null;
        }

        if (isset($request->update_pass)) {
            if ($request->password != null) {
                $input['password'] = Hash::make($request->password);
            } else {
                $input['password'] = $user->password;
            }
        } else {
            $input['password'] = $user->password;
        }

        if (isset($request->status)) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }

        // $input['mobile'] = substr($request->full_phone, 1);
        $input['country_code'] = substr($request->full_phone, 0, strpos($request->full_phone, str_replace(' ', '', $request->mobile)));
        $input['mobile'] = $request->full_phone;

        $user->update($input);

        if ($request->role) {
            $user->syncRoles($request->role);
        }

        Session::flash('success', trans('flash.UpdatedSuccessfully'));


        return redirect()->route('alluser.index');
    }


    public function destroy($id)
    {
        abort_if(!auth()->user()->can('Alluser.delete'), 403, __('User does not have the right permissions'));

        $request = new Request();
        $params = [
            'id' => $id,
            'status' => 0
        ];

        $request->setMethod('POST');
        $request->request->add($params);

        $resp = $this->status($request);

        if ($resp) {
            return back()->with('success', __('User Disabled Successfully'));
        }

        // if ($user->user_img != null) {

        //     $image_file = @file_get_contents(public_path().'/images/user_img/'.$user->user_img);

        //     if($image_file)
        //     {
        //         unlink(public_path().'/images/user_img/'.$user->user_img);
        //     }
        // }

        // $value = $user->delete();
        // Course::where('user_id', $id)->delete();
        // Wishlist::where('user_id', $id)->delete();
        // Cart::where('user_id', $id)->delete();
        // Order::where('user_id', $id)->delete();
        // ReviewRating::where('user_id', $id)->delete();
        // Question::where('user_id', $id)->delete();
        // Answer::where('ans_user_id', $id)->delete();
        // Meeting::where('user_id', $id)->delete();
        // BundleCourse::where('user_id', $id)->delete();
        // BBL::where('instructor_id', $id)->delete();
        // Instructor::where('user_id', $id)->delete();
        // CourseProgress::where('user_id', $id)->delete();

        // if ($value) {
        //     session()->flash('delete',trans('flash.DeletedSuccessfully'));
        //     return redirect('user');
        // }
    }


    public function bulk_delete(Request $request)
    {
        abort_if(!auth()->user()->can('Alluser.delete'), 403, __('User does not have the right permissions'));

        $validator = Validator::make($request->all(), [
            'checked' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('warning', __('Atleast one item is required to be checked'));
        } else {
            foreach ($request->checked as $id) {
                $this->destroy($id);
            }
            // User::whereIn('id',$request->checked)->delete();
        }
        // Session::flash('success',trans('Deleted Successfully'));
        return back();
    }


    public function testUser(Request $request)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, __('User does not have the right permissions'));

        $user = User::find($request->id);

        if ($user->hasRole('user')) {
            $user->test_user = $request->test_user;
            $user->save();
        } else {
            return response()->json(['error' => 'Only Student can be a test user', 403]);
        }

        return response()->json(['success' => 'Updated successfully']);
    }
}
