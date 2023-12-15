<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\Affiliate;
use Carbon\Carbon;
use App\Categories;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rules\Password;

class TestUserController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }


    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, __('User does not have the right permissions'));

        $data = User::query()
                    ->select('id', 'fname', 'lname', 'dob', 'email', 'mobile', 'gender', 'role', 'test_user', 'status')->where('test_user', 1)->latest();

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
                    ->editColumn('status', 'admin.alluser.status')
                    ->editColumn('action', 'admin.testuser.action')
                    ->rawColumns(['checkbox', 'name', 'email', 'mobile', 'status','action'])
                    ->toJson();
        }

        return view('admin.testuser.index');
    }


    public function create()
    {
        abort_if(!auth()->user()->can('users.create'), 403, __('User does not have the right permissions'));
        $roles = Role::all();

        return view('admin.testuser.create')->with(['roles' => $roles]);
    }


    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('users.create'), 403, __('User does not have the right permissions'));

        $request->validate([
            'fname' => 'required|alpha|min:3|max:20',
            'lname' => 'required|alpha|min:3|max:20',
            'email' => 'required|email:rfc,dns|max:40|unique:users,email',
            'user_img' => 'mimes:jpg,jpeg,png|max:10240',
            'timezone' => 'required',
            'full_phone' => 'required|unique:users,mobile',
            'password' => [
                            'required',
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

        $input['test_user'] = 1;
        $input['status'] = isset($request->status) ? 1 : 0;
        $input['password'] = Hash::make($request->password);
        $input['country_code'] = substr($request->full_phone, 0, strpos($request->full_phone, str_replace(' ', '', $request->mobile)));
        $input['mobile'] = $request->full_phone;
        $input['detail'] = $request->detail;
        $input['email_verified_at'] = Carbon::now()->toDateTimeString();

        $input['role'] = 'user';

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

        $data->assignRole('user');
        $data->save();

        return redirect()->route('testuser.index')->with('success', trans('flash.AddedSuccessfully'));
    }


    public function edit($id)
    {
        abort_if(!auth()->user()->can('users.edit'), 403, __('User does not have the right permissions'));

        if ($id != Auth::id()) {
            $roles = Role::all();
            $user = User::findOrFail($id);

            if (Auth::user()->role == 'admin') {
                $user = User::findOrFail($id);
            } else {
                $user = User::where('id', auth()->id())->first();
            }

            return view('admin.testuser.edit', compact('user', 'roles'));
        } else {
            abort(403, __('User does not have the right permissions'));
        }
    }


    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('users.edit'), 403, __('User does not have the right permissions'));

        if (Auth::user()->role == 'admin') {
            $user = User::findOrFail($id);
        } else {
            $user = User::where('id', Auth::user()->id)->first();
        }

        $request->validate([
            'fname' => 'required|alpha|min:3|max:20',
            'lname' => 'required|alpha|min:3|max:20',
            'email' => 'required|email:rfc,dns|max:40|unique:users,email,' . $id,
            'user_img' => 'mimes:jpg,jpeg,png|max:10240',
            'timezone' => 'required',
            'full_phone' => 'required|unique:users,mobile,' . $id,
            'password' => [ 'nullable',
                            'max:50',
                            Password::min(8)
                            ->mixedCase()
                            ->numbers()
                        ]
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

        $verified = Carbon::now()->toDateTimeString();

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

        $input['test_user'] = 1;
        $input['status'] = isset($request->status) ? 1 : 0;

        $input['country_code'] = substr($request->full_phone, 0, strpos($request->full_phone, str_replace(' ', '', $request->mobile)));
        $input['mobile'] = $request->full_phone;

        $user->update($input);

        return redirect()->route('testuser.index')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function enrollment(Request $request, $id)
    {
        $orders = Order::query()
                        ->select('orders.id', 'title', 'orders.user_id', 'orders.instructor_id', 'orders.course_id', 'orders.chapter_id', 'orders.bundle_id', 'orders.meeting_id', 'orders.offline_session_id', 'orders.installments', 'orders.transaction_id', 'orders.total_amount', 'orders.paid_amount', 'orders.enroll_start', 'orders.enroll_expire', 'orders.created_at', 'orders.currency_icon', 'orders.coupon_id', 'orders.coupon_discount', 'orders.status')
                        ->allActiveInactiveOrder()
                        ->where('user_id', $id)
                        ->with('user:id,fname,lname,email,mobile')
                        ->with('instructor:id,fname,lname')
                        ->with('transaction:id,payment_method,transaction_id,created_at')
                        ->with('payment_plan:id,order_id,due_date,installment_no,payment_date,amount,status');

        if ($request->ajax()) {
            return Datatables::eloquent($orders)
                ->addIndexColumn()

                ->editColumn('student_detail', 'admin.invoice.datatables.student_detail')
                ->editColumn('order_detail', 'admin.invoice.datatables.order_detail')
                ->editColumn('payment_detail', 'admin.invoice.datatables.payment_detail')
                ->editColumn('status', 'admin.manual_enrollment.datatables.status')
                ->editColumn('action', 'admin.manual_enrollment.datatables.action')

                ->rawColumns(['student_detail', 'order_detail', 'payment_detail', 'status', 'action'])
                ->toJson();
        }

        return view('admin.testuser.enrollment.index', compact($orders));
    }
}
