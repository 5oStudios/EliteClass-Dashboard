<?php

namespace App\Http\Controllers;

use App\Cart;
use App\User;
use App\Order;
use App\Course;
use App\Setting;
use App\Currency;
use App\Wishlist;
use Carbon\Carbon;
use App\Installment;
use App\BundleCourse;
use App\CoursesInBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BundleCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:bundle-courses.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:bundle-courses.create', ['only' => ['create', 'store', 'storeInstallments']]);
        $this->middleware('permission:bundle-courses.edit', ['only' => ['update', 'status', 'storeInstallments']]);
        $this->middleware('permission:bundle-courses.delete', ['only' => ['destroy', 'bulk_delete']]);
    }


    public function index(Request $request)
    {
        $bundles = BundleCourse::query()
            ->select('bundle_courses.*')
            // ->select('id','course_id','user_id','title','preview_image','installment','total_installments','price','discount_price','installment_price','status','type')
            ->with(['user:id,fname,lname', 'installments:id,bundle_id,amount,due_date'])
            ->latest();

        if ($request->ajax()) {
            return DataTables::of($bundles)
                ->addColumn('checkbox', function ($row) {
                    $chk = "<div class='inline'>
                            <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                            <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })
                ->addIndexColumn()
                ->editColumn('image', 'admin.bundle.datatables.image')
                ->editColumn('title', function ($row) {

                    return $row->title ?? '';
                })
                ->editColumn('instructor', function ($row) {

                    return $row->user->fname ? ($row->user->lname ? $row->user->fname . ' ' . $row->user->lname : $row->user->fname) : '';
                })
                ->editColumn('status', 'admin.bundle.datatables.status')
                ->editColumn('action', 'admin.bundle.datatables.action')
                ->rawColumns(['checkbox', 'image', 'title', 'instructor', 'status', 'action'])
                ->make(true);
        }

        return view('admin.bundle.index');
    }


    public function create()
    {
        $courses = Course::where('status', 1)->get();
        if (Auth::user()->role == 'admin') {
            $users = User::where('id', '!=', Auth::user()->id)->where('role', '!=', 'user')->where('status', 1)->get();
        } else {
            $users = User::where('id', Auth::user()->id)->where('status', 1)->first();
        }
        return view('admin.bundle.create', compact('courses', 'users'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            // 'user_id' => 'required',
            'title' => 'required|max:100',
            'preview_image' => 'required|mimes:jpg,jpeg,png|max:10240',
            'detail' => 'required',
            'start_date' => 'required|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
            'discount_type' => 'sometimes|string|in:fixed,percentage',
            'total_installments' => 'required_if:installment,1|in:2,3,4',
        ], [
            "course_id.required" => __("At least one course is required"),
            "course_id.exists" => __("The selected course name is not exist"),
            "title.required" => __("Package name is required"),
            "title.max" => __("Package name should not be more than 100 characters"),
            'preview_image.required' => __('Image is required'),
            'preview_image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'preview_image.max' => __('Image size should not be more than 10 MB'),
            "detail.required" => __("Package detail is required"),
            "start_date.required" => __("Start Date is required"),
            "start_date.date_format" => __("Start Date format must be YYYY-MM-DD"),
            "start_date.after_or_equal" => __("Start Date must be greater than or equal to today's date"),
            "end_date.required" => __("End Date is required"),
            "end_date.date_format" => __("End Date format must be YYYY-MM-DD"),
            "end_date.after" => __("End Date must be greater than selected Start Date"),
            "price.required" => __("Price is required"),
            "price.numeric" => __("Price must be in numeric"),
            "price.min" => __("Price should not be a negitive number"),
            "discount_price.required" => __("Discount Price is required"),
            "discount_price.numeric" => __("Discount Price must be in numeric"),
            "discount_price.min" => __("Discount Price should not be a negitive number"),
        ]);


        $input = $request->all();

        $input['user_id'] = Auth::id();

        $input['installment'] = isset($request->installment) ? 1 : 0;
        $input['featured'] = isset($request->featured) ? 1 : 0;
        $input['status'] = isset($request->status) ? 1 : 0;

        $slug = str_slug($request->title, '-');
        $input['slug'] = $slug;

        // if (isset($request->type)) {
        $input['type'] = 1;
        // } else {
        //     $input['type'] = 0;
        // }

        // if (isset($request->duration_type)) {
        //     $input['duration_type'] = "m";
        // } else {
        //     $input['duration_type']= "d";
        // }

        // $is_subscription_enabled = isset($request->is_subscription_enabled) ? 1 : 0;
        // if ($is_subscription_enabled == 1) {
        //     $plan = $this->createPlanInStripe($input['title'], $input['billing_interval'], $input['discount_price']);

        //     Log::debug('message', $plan);

        //     $input['price_id'] = $plan['price_id'];
        //     $input['product_id'] = $plan['product_id'];
        // } else {
        //     $input['price_id'] = null;
        //     $input['product_id'] = null;
        //     $input['billing_interval'] = null;
        // }

        // $input['is_subscription_enabled'] = isset($request->is_subscription_enabled) ? 1 : 0;

        if ($file = $request->file('preview_image')) {
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/bundle/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['preview_image'] = $image;
        }

        $bundle = BundleCourse::create($input);

        foreach ($bundle->courses() as $c) {
            $course = [
                'bundle_id' => $bundle->id,
                'course_id' => $c->id,
                'created_by' => Auth::id(),
            ];
            CoursesInBundle::create($course);
        }

        return redirect('bundle')->with('success', trans('flash.AddedSuccessfully'));
    }


    public function storeInstallments(Request $r)
    {
        $bundle = BundleCourse::findOrFail($r->bundle_id);

        $this->validate($r, [
            'bundle_id' => 'required|exists:bundle_courses,id',
            'amount' => 'required|array|min:2|max:4',
            'amount.*' => 'required|numeric|min:1',
            'due_date' => [
                'required',
                'array',
                'min:2',
                'max:4',
                function ($attribute, $value, $fail) {
                    for ($i = 1; $i < count($value); $i++) {
                        if ($value[$i] < $value[$i - 1] && !empty($value[$i])) {
                            $fail(__('Installment date should not be less than previous installment date'));
                        }
                    }
                }
            ],
            'due_date.*' => 'required|date_format:Y-m-d|after_or_equal:' . $bundle->start_date . '|before_or_equal:' . $bundle->end_date,
        ], [
            "bundle_id.required" => __("Package name is required"),
            "bundle_id.exists" => __("The selected package name is not exist"),
            "amount.*.required" => __("All installments amount is required"),
            "amount.*.numeric" => __("Amount must be a numeric value"),
            "amount.*.min" => __("Amount should not be a zero OR a negative integer"),
            "due_date.*.required" => __("All installments due date is required"),
            "due_date.*.date_format" => __("Due date format must be YYYY-MM-DD"),
            "due_date.*.after_or_equal" => __("Due date must be greater than or equal to package start date"),
            "due_date.*.before_or_equal" => __("Due date must be less than or equal to package end date"),
        ]);

        foreach ($r->amount as $k => $m) {
            Installment::updateOrCreate([
                'bundle_id' => $r->bundle_id,
                'sort' => $k + 1,
            ], [
                'due_date' => $r->due_date[$k],
                'amount' => $m,
                'created_by' => auth()->id()
            ]);
        }

        $total = $bundle->installments->sum('amount');

        $bundle->update([
            'installment_price' => $total,
            // 'total_installments' => 3,
        ]);

        Cart::where(['bundle_id' => $r->bundle_id, 'installment' => 1])->update([
            'price' => $total,
            'offer_price' => $r->amount[0],
            'total_installments' => json_encode([$bundle->installments[0]->id]),
        ]);

        return back()->with('success', __('Installment Updated'));
    }


    #region STRIPE

    /**
     * Note: Currently subscription plan is created only via stripe.
     * We should move this function in our custom provider `StripeProvider`
     */
    private function createPlanInStripe(
        $product_name,
        $billing_interval,
        $offer_price,
        $product_id = null
    ) {
        try {
            Log::debug('<==createPlanInStripe');

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $config = Setting::findOrFail(1);
            $projectTitle = $config->project_title;
            $currency = Currency::first();
            $price_id = null;

            if ($product_id === null) {
                $product = $stripe->products->create([
                    'name' => $product_name,
                ]);

                $product_id = $product['id'];

                Log::debug('Stripe product created successfully: ' . $product_id);
            }

            $priceArgs = [
                'product' => $product_id,
                'recurring' => ['interval' => $billing_interval],
                'unit_amount' => $offer_price * 100,
                'currency' => $currency->currency,
                "metadata" => ["course_title" => $projectTitle]
            ];

            Log::debug('calling stripe price with args: ' . print_r($priceArgs, true));

            $price = $stripe->prices->create([
                $priceArgs
            ]);

            $price_id = $price['id'];

            Log::debug('Stripe price created successfully: ' . $price_id);

            Log::debug('==>createPlanInStripe');

            $obj = ['price_id' => $price_id, 'product_id' => $product_id];

            Log::debug('Plan created in stripe: ' . print_r($obj, true));

            return $obj;
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());

            Session::flash('delete', trans('flash.PlanCreationFailed'));
            return back();
        }
    }
    #endregion


    public function show($id)
    {
        $cor = BundleCourse::findOrFail($id);
        $installments = $cor->installments()->get();
        $courses = Course::all();

        if (Auth::user()->role == 'admin') {
            $users = User::where('id', '!=', Auth::user()->id)->where('role', '!=', 'user')->where('status', 1)->get();
        } else {
            $users = User::where('id', Auth::user()->id)->where('status', 1)->first();
        }

        $orderExists = Order::where(['installments' => 1, 'bundle_id' => $cor->id])->activeOrder()->exists();

        return view('admin.bundle.edit', compact('cor', 'courses', 'users', 'installments', 'orderExists'));
    }


    public function edit(BundleCourse $bundleCourse)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            // 'user_id' => 'required',
            'title' => 'required|max:100',
            'preview_image' => 'mimes:jpg,jpeg,png|max:10240',
            'detail' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
            'discount_type' => 'sometimes|string|in:fixed,percentage',
            'total_installments' => 'sometimes|in:2,3,4',
        ], [
            "course_id.required" => __("At least one course is required"),
            "course_id.exists" => __("The selected course name is not exist"),
            "title.required" => __("Package name is required"),
            "title.max" => __("Package name should not be more than 100 characters"),
            'preview_image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'preview_image.max' => __('Image size should not be more than 10 MB'),
            "detail.required" => __("Package detail is required"),
            "start_date.required" => __("Start Date is required"),
            "start_date.date_format" => __("Start Date format must be YYYY-MM-DD"),
            "start_date.after_or_equal" => __("Start Date must be greater than or equal to today's date"),
            "end_date.required" => __("End Date is required"),
            "end_date.date_format" => __("End Date format must be YYYY-MM-DD"),
            "end_date.after" => __("End Date must be greater than selected Start Date"),
            "price.required" => __("Price is required"),
            "price.numeric" => __("Price must be in numeric"),
            "price.min" => __("Price should not be a negitive number"),
            "discount_price.required" => __("Discount Price is required"),
            "discount_price.numeric" => __("Discount Price must be in numeric"),
            "discount_price.min" => __("Discount Price should not be a negitive number"),
        ]);

        $bundle = BundleCourse::findOrFail($id);
        $oldInstallments = $bundle->total_installments;
        $input = $request->all();

        $input['user_id'] = Auth::id();

        // $input['featured'] = isset($request->featured) ? 1 : 0;
        $input['installment'] = isset($request->installment) ? 1 : 0;
        $input['type'] = isset($request->type) ? 1 : 0;

        $slug = str_slug($input['title'], '-');
        $input['slug'] = $slug;

        // if (isset($request->duration_type)) {
        //     $input['duration_type'] = "m";
        // } else {
        //     $input['duration_type'] = "d";
        // }

        // FSMS check  if price changed then only create new plan
        // if ($request->is_subscription_enabled == 1 && $this->isPriceChanged($bundle, $input)) {
        //     $input['price_id'] = $this->createNewPriceInStripe($bundle, $input);
        // }

        // $input['is_subscription_enabled'] = isset($request->is_subscription_enabled) ? 1 : 0;

        if ($file = $request->file('preview_image')) {
            if ($bundle->preview_image != null) {
                $content = @file_get_contents(public_path() . '/images/bundle/' . $bundle->preview_image);
                if ($content) {
                    unlink(public_path() . '/images/bundle/' . $bundle->preview_image);
                }
            }

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/bundle/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);

            $input['preview_image'] = $image;
        }

        if (isset($request->status)) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
            Cart::where('bundle_id', $id)->delete();
        }

        $bundle->update($input);

        CoursesInBundle::where('bundle_id', $id)->whereNotIn('course_id', $bundle->course_id)->delete();

        foreach ($bundle->courses() as $c) {
            $courseinbundle = CoursesInBundle::where([
                'bundle_id' => $id,
                'course_id' => $c->id
            ])->first();

            if (!$courseinbundle) {
                $course = [
                    'bundle_id' => $id,
                    'course_id' => $c->id,
                    'created_by' => Auth::id(),
                ];
                CoursesInBundle::create($course);
            }
        }

        if ($bundle->installment == 1 && $bundle->installments->isNotEmpty()) {
            Cart::where(['bundle_id' => $id, 'installment' => 1])->update([
                'price' => $bundle->installments->sum('amount'),
                'offer_price' => $bundle->installments[0]->amount,
                'total_installments' => json_encode([$bundle->installments[0]->id]),
            ]);
        } else {
            Cart::where(['bundle_id' => $id, 'installment' => 0])->update([
                'price' => $bundle->price,
                'offer_price' => $bundle->discount_price,
            ]);
        }

        Order::where('bundle_id', $id)->update([
            'enroll_start' => $bundle->start_date,
            'enroll_expire' => $bundle->end_date,
            'updated_at' => DB::raw('updated_at')
        ]);

        if (isset($request->total_installments) && $request->total_installments != $oldInstallments) {
            return redirect('bundle')->with('success', trans('flash.UpdateTotalInstallments'));
        }

        // Session::flash('success', trans('Update Successfully'));
        return redirect('bundle')->with('success', trans('flash.UpdatedSuccessfully'));
    }


    // FSMS
    private function isPriceChanged($bundle, $input)
    {
        return $bundle->price != $input['price'] || $bundle->discount_price != $input['discount_price'] || $bundle->billing_interval != $input['billing_interval'];
    }


    // FSMS
    private function createNewPriceInStripe(BundleCourse $bundle, $input)
    {
        Log::debug('<==createNewPriceInStripe');

        Log::debug('New: price: ' . $input['price'] . ', discounted: ' . $input['discount_price'] . ', interval: ' . $input['billing_interval']);

        Log::debug('Existing: price: ' . $bundle->price . ', discounted: ' . $bundle->discount_price . ', interval: ' . $bundle->billing_interval);

        if (
            $this->isPriceChanged($bundle, $input) // FSMS reusing the function to check price change
        ) {
            Log::debug('Plan changed therefore creating new plan in stripe. Stripe doesnot allow modifying current plan by design');

            return $this->createPlanInStripe(
                $input['title'],
                $input['billing_interval'],
                $input['discount_price'],
                $bundle->product_id
            )['price_id'];
        }

        Log::debug('Plan did not change therefore not creating new plan in stripe.');

        Log::debug('==>createNewPriceInStripe');

        return null;
    }


    public function destroy($id)
    {
        $course = BundleCourse::find($id);
        $order = Order::where('bundle_id', $id)->allActiveInactiveOrder()->get();

        if (!$order->isEmpty()) {
            return back()->with('delete', trans('flash.CannotDelete'));
        } else {
            Cart::where('bundle_id', $id)->delete();
            Wishlist::where('bundle_id', $id)->delete();
            CoursesInBundle::where('bundle_id', $id)->delete();

            if ($course->preview_image != null) {
                $image_file = @file_get_contents(public_path() . '/images/bundle/' . $course->preview_image);

                if ($image_file) {
                    unlink(public_path() . '/images/bundle/' . $course->preview_image);
                }
            }

            $course->delete();
        }

        Session::flash('success', trans('Delete Successfully'));
        return redirect('bundle');
    }


    public function addtocart(Request $request, $id)
    {
        $bundle_course = BundleCourse::findOrFail($id);

        DB::table('carts')->insert(
            array(
                'user_id' => Auth::user()->id,
                'course_id' => null,
                'price' => $bundle_course->price,
                'offer_price' => $bundle_course->discount_price,
                'bundle_id' => $id,
                'type' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            )
        );

        return back()->with('success', trans('flash.CartAdded'));
    }


    public function detailpage(Request $request, $id)
    {
        $bundle = BundleCourse::findOrFail($id);

        return view('front.bundle_detail', compact('bundle'));
    }


    public function enroll(Request $request, $id)
    {
        $course = BundleCourse::findOrFail($id);

        $bundle_course_id = $course->course_id;

        $created_order = Order::create([
            'user_id' => Auth::user()->id,
            'instructor_id' => $course->user_id,
            'course_id' => null,
            'total_amount' => 'Free',
            'status' => 1,
            'bundle_id' => $course->id,
            'bundle_course_id' => $bundle_course_id,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ]);

        return back()->with('success', trans('flash.EnrolledSuccessfully'));
    }


    public function status(Request $request)
    {
        $bundlecourse = Bundlecourse::find($request->id);
        $bundlecourse->status = $request->status;
        $bundlecourse->save();

        if (!$bundlecourse->status) {
            Wishlist::where('bundle_id', $request->id)->delete();
            Cart::where('bundle_id', $request->id)->delete();
        }

        return response()->json('success', 200);
    }


    public function subscriptionstatus(Request $request)
    {
        $bundlecourse = Bundlecourse::find($request->id);
        $bundlecourse->is_subscription_enabled = $request->is_subscription_enabled;
        $bundlecourse->save();

        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function featuredstatus(Request $request)
    {
        $bundlecourse = Bundlecourse::find($request->id);
        $bundlecourse->featured = $request->featured;
        $bundlecourse->save();

        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }


    public function bulk_delete(Request $request)
    {
        $validator = Validator::make($request->all(), ['checked' => 'required']);
        if ($validator->fails()) {
            return back()->with('error', trans('Please select field to be deleted.'));
        }
        foreach ($request->checked as $id) {
            $this->destroy($id);
        }
        // Bundlecourse::whereIn('id', $request->checked)->delete();

        // return back()->with('error', trans('Selected Packagecourse has been deleted.'));
        return back();
    }
}
