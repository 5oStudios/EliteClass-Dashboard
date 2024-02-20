<?php

namespace App\Http\Controllers;

use DB;
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
use Carbon\Carbon;
use App\Instructor;
use App\QuizAnswer;
use App\BundleCourse;
use App\ReviewRating;
use App\CourseProgress;
use Laravel\Passport\Token;
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

class UserController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }


    public function viewAllUser(Request $request)
    {
        abort_if(!auth()->user()->can('users.view'), 403, __('User does not have the right permissions'));

        $data = User::query()
            ->select('id', 'fname', 'lname', 'dob', 'email', 'mobile', 'gender', 'role', 'status')->with(['roles'])->where('id', '!=', Auth::id())->latest();

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

                // ->editColumn('image', 'admin.user.image')
                ->editColumn('name', function ($row) {

                    return $row->fname ? ($row->lname ? $row->fname . ' ' . $row->lname : $row->fname) : '';
                })
                ->editColumn('email', function ($row) {

                    return $row->email ?? '';
                })
                ->editColumn('mobile', function ($row) {

                    return $row->mobile ?? '';
                })
                // ->editColumn('role', function ($row) {
                //     $btn = '<a href="javascript:void(0)" class="badge badge-pill badge-primary">' . $row->getRoleNames()->count() ? $row->getRoleNames()->toArray()[0] : '-'. '</a>';
                //     return $btn;
                // })
                ->editColumn('role', function ($row) {

                    return $row->roles[0]->name ?? 'No role set';
                })
                // ->editColumn('loginasuser', 'admin.user.login')
                ->editColumn('status', 'admin.user.status')
                ->editColumn('action', 'admin.user.action')
                ->rawColumns(['checkbox', 'name', 'email', 'mobile', 'role', 'status', 'action'])
                ->toJson();
        }

        return view('admin.user.index');
    }


    public function viewAllZakiUser(Request $request)
    {
        abort_if(!auth()->user()->can('users.view'), 403, __('User does not have the right permissions'));

        $data = User::select('id', 'fname', 'lname', 'email', 'mobile', 'role', 'status')->with(['roles'])->where('id', '!=', Auth::id());

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
                // ->editColumn('image', 'admin.user.image')
                ->editColumn('name', function ($row) {

                    return $row->fname ? ($row->lname ? $row->fname . ' ' . $row->lname : $row->fname) : '';
                })
                ->editColumn('email', function ($row) {

                    return $row->email ?? '';
                })
                ->editColumn('mobile', function ($row) {

                    return $row->mobile ?? '';
                })
                // ->editColumn('role', function ($row) {
                //     $btn = '<a href="javascript:void(0)" class="badge badge-pill badge-primary">' . $row->getRoleNames()->count() ? $row->getRoleNames()->toArray()[0] : '-'. '</a>';
                //     return $btn;
                // })
                ->editColumn('role', function ($row) {

                    return $row->roles[0]->name ?? 'No role set';
                })
                // ->editColumn('loginasuser', 'admin.user.login')
                ->editColumn('status', 'admin.user.status')
                ->editColumn('action', 'admin.user.action')
                ->rawColumns(['checkbox', 'name', 'email', 'mobile', 'role', 'status', 'action'])
                ->make(true);
        }
        return view('admin.user.zakiindex');
    }


    public function create()
    {
        abort_if(!auth()->user()->can('users.create'), 403, __('User does not have the right permissions'));

        $cities = Allcity::all();
        $states = Allstate::all();
        $countries = Country::all();
        $roles = Role::all();

        return view('admin.user.adduser')
            ->with(['cities' => $cities, 'states' => $states, 'countries' => $countries, 'roles' => $roles]);
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
            'role' => 'required|exists:roles,name',
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
        $input['status'] = isset($request->status) ? 1 : 0;
        $input['password'] = Hash::make($request->password);
        // $input['mobile'] = substr($request->full_phone, 1);
        $input['country_code'] = substr($request->full_phone, 0, strpos($request->full_phone, str_replace(' ', '', $request->mobile)));
        $input['mobile'] = $request->full_phone;
        $input['detail'] = $request->detail;
        $input['email_verified_at'] = \Carbon\Carbon::now()->toDateTimeString();

        if ($request->user_id) {
            $input['user_id'] = $request->user_id;
        }

        if ($request->role == 'user') {
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

        } elseif ($request->role == 'instructor') {
            $input['role'] = 'instructor';
        } elseif ($request->role == 'admin') {
            $input['role'] = 'admin';
        } else {
            $input['role'] = $request->role;
        }

        $data = User::create($input);
        $data->assignRole($request->role);
        $data->save();

        // Session::flash('success', trans('flash.AddedSuccessfully'));
        return redirect('user')->with('success', trans('flash.AddedSuccessfully'));
    }

    public function bulkAdd()
    {
        return view('admin.user.bulkAdd');
    }

    public function storeBulk(Request $request)
    {
        if (!$request->hasFile('csvFile')) {
            return back()->with('error', 'Please select a CSV file');
        }

        $errors = [];

        $path = $request->file('csvFile')->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $exist = User::where('email', $row[2])->orWhere('mobile', $row[4])->first();
            if ($exist) {
                $errors[] = [
                    "msg" => "This data is already exists",
                    "row" => $row
                ];
            } else {
                User::create([
                    'fname' => $row[0],
                    'lname' => $row[1],
                    'email' => $row[2],
                    'mobile' => $row[3],
                    'password' => bcrypt($row[4]),
                    'role' => 'user',
                ]);
            }
        }

        if (count($errors)) {
            return back()->with('warning', 'Bulk user created users successfully, But some data was invalid');
        }

        return back()->with('success', 'Bulk user created users successfully');
    }

    public function downloadFileSample()
    {
        $filePath = public_path('/excel/user_bulk.csv'); // Path to your file
        return response()->download($filePath, 'user_bulk.csv');
    }


    public function show(User $user)
    {
        //
    }


    public function edit($id)
    {
        abort_if(!auth()->user()->can('users.edit'), 403, __('User does not have the right permissions'));

        if ($id != Auth::id()) {
            $categories = \App\Categories::where('status', true)->get(['id', 'title']);
            $roles = Role::all();
            $user = User::findOrFail($id);
            if (Auth::user()->role == 'admin') {
                $user = User::findOrFail($id);
            } else {
                $user = User::where('id', Auth::user()->id)->first();
            }

            return view('admin.user.edit', compact('categories', 'user', 'roles'));
        } else {
            abort(403, __('User does not have the right permissions'));
        }
    }


    public function editProfile($id)
    {
        abort_unless(auth()->id() == $id, 403, __('User does not have the right permissions'));

        $roles = Role::all();
        $user = User::findOrFail($id);

        return view('admin.user.profile', compact('user', 'roles'));
    }


    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->can('users.edit') || auth()->id() == $id, 403, __('User does not have the right permissions'));

        $user = User::findOrFail($id);

        $request->validate([
            'fname' => 'required|alpha|min:3|max:20',
            'lname' => 'required|alpha|min:3|max:20',
            'email' => 'required|email:rfc,dns|max:40|unique:users,email,' . $id,
            'user_img' => 'mimes:jpg,jpeg,png|max:10240',
            'timezone' => 'required',
            'full_phone' => 'required|unique:users,mobile,' . $id,
            'role' => 'required|exists:roles,name',
            'password' => [
                'nullable',
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

        if ($request->role == 'user') {
            $input['role'] = 'user';
        } elseif ($request->role == 'instructor') {
            $input['role'] = 'instructor';
        } elseif ($request->role == 'admin') {
            $input['role'] = 'admin';
        } else {
            $input['role'] = $request->role;
        }

        // $input['mobile'] = substr($request->full_phone, 1);
        $input['country_code'] = substr($request->full_phone, 0, strpos($request->full_phone, str_replace(' ', '', $request->mobile)));
        $input['mobile'] = $request->full_phone;

        $user->update($input);

        if ($request->role) {
            $user->syncRoles($request->role);
        }

        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function destroy($id)
    {
        abort_unless(auth()->user()->can('users.delete'), 403, __('User does not have the right permissions'));

        $user = User::findOrFail($id);

        // $request = new Request();
        // $params = [
        //     'id' => $id,
        //     'status' => 0
        // ];

        // $request->setMethod('POST');
        // $request->request->add($params);

        // $resp = $this->status($request);

        // if($resp) return back()->with('success', __('User Disabled Successfully'));


        // if ($user->user_img != null) {

        //     $image_file = @file_get_contents(public_path() . '/images/user_img/' . $user->user_img);

        //     if ($image_file) {
        //         unlink(public_path() . '/images/user_img/' . $user->user_img);
        //     }
        // }

        $user->update([
            'email' => '[deleted]' . $user->email,
            'mobile' => '[deleted]' . $user->mobile,
            'deleted_by' => auth()->id(),
        ]);

        $value = $user->delete();
        Wishlist::where('user_id', $id)->delete();
        Cart::where('user_id', $id)->delete();
        CourseProgress::where('user_id', $id)->delete();

        ReviewRating::where('user_id', $id)->delete();
        Question::where('user_id', $id)->orWhere('instructor_id', $id)->delete();
        Answer::where('ans_user_id', $id)->orWhere('instructor_id', $id)->delete();
        QuizAnswer::where('user_id', $id)->delete();

        // Course::where('user_id', $id)->delete();
        // BundleCourse::where('user_id', $id)->delete();
        // BBL::where('instructor_id', $id)->delete();
        // Order::where('user_id', $id)->allActiveInactiveOrder()->delete();

        if ($value) {
            return back()->with('delete', trans('flash.DeletedSuccessfully'));
        }
    }


    public function bulk_delete(Request $request)
    {
        abort_if(!auth()->user()->can('users.delete'), 403, __('User does not have the right permissions'));

        $validator = Validator::make($request->all(), [
            'checked' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('warning', __('Atleast one item is required to be checked'));
        } else {
            foreach ($request->checked as $id) {
                $this->destroy($id);
            }
            // User::whereIn('id', $request->checked)->delete();
        }
        // Session::flash('success', trans('Deleted Successfully'));
        return back();
    }


    public function blockedUsers(Request $request)
    {
        abort_unless(auth()->user()->can('blocked-users.manage'), 403, __('User does not have the right permissions'));

        $blockedUsers = User::query()
            ->select('id', 'user_img', 'fname', 'lname', 'email', 'mobile', 'blocked_count', 'is_allow_multiple_device', 'is_locked', 'status', 'updated_at')
            ->where(function ($query) {
                $query->where('is_locked', 1)
                    ->orWhere('blocked_count', '>', 0)
                    ->orWhere('is_allow_multiple_device', 1);
            })
            ->latest('updated_at')
            ->with('fingerprint');

        if ($request->ajax()) {
            return DataTables::of($blockedUsers)
                ->addIndexColumn()
                ->editColumn('image', 'admin.user.blocked-users.datatables.image')
                ->editColumn('name', function ($row) {

                    return $row->fname ? ($row->lname ? $row->fname . ' ' . $row->lname : $row->fname) : '';
                })
                ->editColumn('email', function ($row) {

                    return $row->email ?? '';
                })
                ->editColumn('mobile', function ($row) {

                    return $row->mobile ?? '';
                })
                ->editColumn('blocked_attempt', function ($row) {

                    return $row->blocked_count ?? '';
                })
                ->editColumn('allow_multiple_device', 'admin.user.blocked-users.datatables.allow_multiple_device')
                ->editColumn('locked', 'admin.user.blocked-users.datatables.locked')
                ->editColumn('action', 'admin.user.blocked-users.datatables.action')
                ->rawColumns(['checkbox', 'image', 'name', 'email', 'mobile', 'blocked_attempt', 'allow_multiple_device', 'locked', 'action'])
                ->make(true);
        }

        return view('admin.user.blocked-users.index');
    }


    public function userFingerprint($email)
    {
        try {
            $user = User::where('email', $email)->firstOrFail();
        } catch (\Throwable $th) {
            return redirect('/admins')->with('warning', __('This Email does not exist'));
        }

        $userFingerprints = Token::where('user_id', $user->id)->get();

        return view('admin.user.fingerprint', compact('userFingerprints'));
    }


    public function status(Request $request)
    {
        abort_unless(auth()->user()->can('users.edit'), 403, __('User does not have the right permissions'));

        $user = User::find($request->id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['success' => 'Status Updated successfully']);
    }


    public function locked(Request $request)
    {
        abort_unless(auth()->user()->can('blocked-users.manage'), 403, __('User does not have the right permissions'));

        $user = User::find($request->id);
        $user->is_locked = $request->is_locked;
        $user->save();

        return response()->json(['success' => 'User Updated successfully']);
    }


    public function allowMultipleDevice(Request $request)
    {
        abort_unless(auth()->user()->can('blocked-users.manage'), 403, __('User does not have the right permissions'));

        $user = User::find($request->id);
        $user->is_allow_multiple_device = $request->is_allow;
        $user->save();

        return response()->json(['success' => 'Updated successfully']);
    }


    public function login($id)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, __('User does not have the right permissions'));

        $user = User::active()->find($id);

        if ($user) {
            if ($user->hasRole('instructor')) {
                // Auth::login($user);
                Auth::user()->impersonate($user);
            } else {
                return back()->with('warning', trans('This user is not an instructor'));
            }
        } else {
            return back()->with('info', trans('This instructor is not active'));
        }
        // Session::flash('success', trans('LoginSuccessfully'));

        return redirect()->route('instructor.index');
    }
}
