<?php

namespace App\Http\Controllers\Api;

use Hash;
use Avatar;
use App\BBL;
use App\Blog;
use App\Cart;
use App\Page;
use App\Quiz;
use App\User;
use App\About;
use App\Order;
use App\Terms;
use Validator;
use App\Answer;
use App\Career;
use App\Coupon;
use App\Course;
use App\Remark;
use App\Slider;
use App\Adsense;
use App\Contact;
use App\Meeting;
use App\Setting;
use App\Trusted;
use App\Currency;
use App\Question;
use App\Wishlist;
use App\QuizTopic;
use App\Assignment;
use App\Attandance;
use App\CartCoupon;
use App\Categories;
use App\FaqStudent;
use App\GetStarted;
use App\Googlemeet;
use App\Instructor;
use App\QuizAnswer;
use App\Appointment;
use App\CourseClass;
use App\Installment;
use App\SliderFacts;
use App\SubCategory;
use App\Testimonial;
use App\WatchCourse;
use App\Announcement;
use App\BundleCourse;
use App\CourseReport;
use App\JitsiMeeting;
use App\ReviewRating;
use App\ChildCategory;
use App\CourseChapter;
use App\FaqInstructor;
use App\PreviousPaper;
use App\PrivateCourse;
use App\RelatedCourse;
use App\ReviewHelpful;
use App\CategorySlider;
use App\CourseProgress;
use App\OfflineSession;
use App\PaymentGateway;
use App\CoursesInBundle;
use App\OrderInstallment;
use App\OrderPaymentPlan;
use App\SessionEnrollment;
use Illuminate\Support\Str;
use App\Helpers\Is_wishlist;
use Illuminate\Http\Request;
use App\Mail\UserAppointment;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\CourseController;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller
{

    public function home(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        $lang = $request->header('Accept-Language') ?? 'en';

        $category_id = $request->category_id;
        $scnd_category_id = $request->scnd_category_id;
        $sub_category_id = $request->sub_category;
        $child_sub_category = $request->ch_sub_category;
        $seach_text = $request->search_text ?? null;

        if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category) && Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $category_id = $user->main_category;
            $scnd_category_id = $user->scnd_category_id;
            $sub_category_id = $user->sub_category;
            $child_sub_category = $user->ch_sub_category;
        }

        // if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category)) {
        //     return response()->json(array("errors"=>["message"=>['Category Not selected']]),403);
        // }

        $instructor = User::where(['role' => 'instructor', ['user_img', '<>', null]])
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('main_category', $category_id);
            })
            ->active()
            ->paginate(10);
        $instructor->getCollection()->transform(function ($i) {
            return [
                'id' => $i->id,
                'name' => $i->fname . ' ' . $i->lname,
                'image' => url('/images/user_img/' . $i->user_img),
            ];
        });
        $Course = Course::
            select("courses.*")
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })
            ->when($seach_text, function ($q) use ($seach_text, $lang) {
                $q->where(DB::raw("LOWER(courses.title->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                    ->orWhere(DB::raw("LOWER(courses.title->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
            })
            ->when($scnd_category_id, function ($q) use ($scnd_category_id) {
                $q->where('scnd_category_id', $scnd_category_id);
            })
            ->when($sub_category_id, function ($q) use ($sub_category_id) {
                $q->where('subcategory_id', $sub_category_id);
            })
            ->when($child_sub_category, function ($q) use ($child_sub_category) {
                $q->whereJsonContains('childcategory_id', strval($child_sub_category));

            })
            ->when($request->max_price, function ($q) use ($request) {
                $q->whereRaw("(courses.price between $request->min_price and $request->max_price or courses.discount_price between $request->min_price and $request->max_price)");
            })
            ->when($request->max_time, function ($q) use ($request) {
                $q->whereBetween('courses.credit_hours', [$request->min_time, $request->max_time]);
            })
            ->when($request->rating, function ($q) use ($request) {
                $q->join('review_ratings', 'review_ratings.course_id', '=', 'courses.id')
                    ->groupBy('review_ratings.course_id')
                    ->havingRaw('avg(avg_rating) >= ?', [$request->rating]);
            })
            ->active()
            ->paginate(5);
        // ->inRandomOrder()
        // ->get();

        $Course->getCollection()->transform(function ($b) use ($user) {
            return [
                'id' => $b->id,
                'title' => $b->title,
                'image' => url('/images/course/' . $b->preview_image),
                'instructor' => $b->user->fname . ' ' . $b->user->lname,
                'lessons' => $b->courseclass->count(),
                'in_wishlist' => $user ? ($b->inwishlist($user->id) ? true : false) : false,
                'rating' => round($b->review->avg('avg_rating'), 2),
                'reviews_by' => $b->review->count() ?? 0,
                'price' => $b->price,
                'discount_price' => $b->discount_price,
                'discount_type' => $b->discount_type,
            ];
        });

        $packages = BundleCourse::select('bundle_courses.*')
            ->join('courses_in_bundle', 'bundle_courses.id', '=', 'courses_in_bundle.bundle_id')
            ->join('courses', 'courses.id', '=', 'courses_in_bundle.course_id')
            ->when($request->max_price, function ($q) use ($request) {
                $q->whereRaw("(bundle_courses.price between $request->min_price and $request->max_price or bundle_courses.discount_price between $request->min_price and $request->max_price)");
                // $q->whereBetween('bundle_courses.price', [$request->min_price, $request->max_price]);
                // $q->orWhereBetween('bundle_courses.discount_price', [$request->min_price, $request->max_price]);
            })
            ->when($seach_text, function ($q) use ($seach_text, $lang) {
                $q->where(DB::raw("LOWER(bundle_courses.title->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                    ->orWhere(DB::raw("LOWER(bundle_courses.title->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
            })
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('courses.category_id', $category_id);
            })
            ->when($scnd_category_id, function ($q) use ($scnd_category_id) {
                $q->where('courses.scnd_category_id', $scnd_category_id);
            })
            ->when($sub_category_id, function ($q) use ($sub_category_id) {
                $q->where('courses.subcategory_id', $sub_category_id);

            })
            ->when($child_sub_category, function ($q) use ($child_sub_category) {
                $q->whereJsonContains('courses.childcategory_id', strval($child_sub_category));
            })
            ->when($request->max_time, function ($q) use ($request) {
                $q->whereBetween('courses.credit_hours', [$request->min_time, $request->max_time]);
            })
            ->active()
            ->groupBy('bundle_courses.id')
            ->paginate(5);
        // ->inRandomOrder()
        // ->get();

        $packages->getCollection()->transform(function ($b) use ($user) {
            return [
                'id' => $b->id,
                'title' => $b->title,
                'image' => url('/images/bundle/' . $b->preview_image),
                'total_courses' => is_array($b->course_id) ? count($b->course_id) : 0,
                'in_wishlist' => $user ? ($b->inwishlist($user->id) ? true : false) : false,
                'price' => $b->price,
                'discount_price' => $b->discount_price,
                'discount_type' => $b->discount_type,
            ];
        });

        $bbl_meetings = BBL::
            query()
            ->where('is_ended', 0)
            ->when($seach_text, function ($q) use ($seach_text, $lang) {
                $q->where(DB::raw("LOWER(meetingname->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                    ->orWhere(DB::raw("LOWER(meetingname->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
            })
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('main_category', $category_id);
            })
            ->when($scnd_category_id, function ($q) use ($scnd_category_id) {
                $q->where('scnd_category_id', $scnd_category_id);
            })
            ->when($sub_category_id, function ($q) use ($sub_category_id) {
                $q->where('sub_category', $sub_category_id);
            })
            ->when($child_sub_category, function ($q) use ($child_sub_category) {
                $q->whereJsonContains('ch_sub_category', strval($child_sub_category));
            })
            ->when($request->max_price, function ($q) use ($request) {
                $q->whereRaw("(price between $request->min_price and $request->max_price or discount_price between $request->min_price and $request->max_price)");
                // $q->whereBetween('price', [$request->min_price, $request->max_price]);
                // $q->orWhereBetween('discount_price', [$request->min_price, $request->max_price]);
            })
            // ->when($request->max_time, function ($q)use ($request) {
            //    $q->whereBetween('duration', [$request->min_time, $request->max_time]);
            // })
            ->active()
            ->latest('id')
            ->paginate(5);
        // ->inRandomOrder()
        // ->get();

        $bbl_meetings->getCollection()->transform(function ($m) use ($user) {
            return [
                'id' => $m->id,
                'owner_id' => $m->owner_id,
                'instructor_id' => $m->instructor_id,
                'in_wishlist' => $user ? ($m->inwishlist($user->id) ? true : false) : false,
                'meeting_title' => $m->meetingname,
                'bigblue_meetingid' => $m->meetingid,
                'instructor' => $m->user->fname . ' ' . $m->user->lname,
                // 'date' => date('d M', strtotime($m->start_time)),
                // 'time' => date('h:i A', strtotime($m->start_time)),
                'date_time' => $m->start_time,
                'image' => url('images/bg/' . $m->image),
                'discount_price' => $m->discount_price,
                'price' => $m->price,
                'discount_type' => $m->discount_type
            ];
        });

        $offline_sessions = OfflineSession::query()
            ->when($seach_text, function ($q) use ($seach_text, $lang) {
                $q->where(DB::raw("LOWER(title->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                    ->orWhere(DB::raw("LOWER(title->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
            })
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('main_category', $category_id);
            })
            ->when($scnd_category_id, function ($q) use ($scnd_category_id) {
                $q->where('scnd_category_id', $scnd_category_id);
            })
            ->when($sub_category_id, function ($q) use ($sub_category_id) {
                $q->where('sub_category', $sub_category_id);
            })
            ->when($child_sub_category, function ($q) use ($child_sub_category) {
                $q->whereJsonContains('ch_sub_category', strval($child_sub_category));
            })
            ->when($request->max_price, function ($q) use ($request) {
                $q->whereRaw("(price between $request->min_price and $request->max_price or discount_price between $request->min_price and $request->max_price)");
            })
            ->active()
            ->latest('id')
            ->paginate(5);

        $offline_sessions->getCollection()->transform(function ($m) use ($user) {
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

        $slid = Slider::where('status', '1')->orderBy('position', 'ASC')->get();
        $slider = [];
        foreach ($slid as $b) {
            $slider[] = [
                'image' => url('/images/slider/' . $b->image),
                'link' => $b->link
            ];
        }

        return response()->json(
            array(
                'slider' => $slider,
                'instructors' => $instructor,
                'packages' => $packages,
                'courses' => $Course,
                'meetings' => $bbl_meetings,
                'sessions' => $offline_sessions,
                'cart_count' => $user ? $user->carts->count() : NULL,
            ),
            200
        );
    }


    public function main()
    {
        return response()->json(array('ok'), 200);
    }

    /**
     * @OA\Post(
     *      path="/course",
     *      tags={"Courses"},
     *      summary="Get list of courses",
     *     @OA\Response(response=200, description="Successfull"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */

    public function course(Request $request)
    {

        $request->validate([
            'perPage' => 'nullable|max:100'
        ], [
            'perPage.max' => __("Pagination should not be more than 100"),
        ]);

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        $lang = $request->header('Accept-Language') ?? 'en';

        $category_id = $request->category_id;
        $seach_text = $request->search_text ?? null;
        $scnd_category_id = $request->scnd_category_id;
        $sub_category_id = $request->sub_category;
        $child_sub_category = $request->ch_sub_category;
        $perPage = $request->perPage ?? 10;

        if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category) && Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $category_id = $user->main_category;
            $scnd_category_id = $user->scnd_category_id;
            $sub_category_id = $user->sub_category;
            $child_sub_category = $user->ch_sub_category;
        }

        // if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category)) {
        //     return response()->json(array("errors"=>["message"=>[__('Category Not selected')]]),422);
        // }

        $courses = Course::
            select("courses.*")
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })
            ->when($seach_text, function ($q) use ($seach_text, $lang) {
                $q->where(DB::raw("LOWER(courses.title->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                    ->orWhere(DB::raw("LOWER(courses.title->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
            })
            ->when($scnd_category_id, function ($q) use ($scnd_category_id) {
                $q->where('scnd_category_id', $scnd_category_id);
            })
            ->when($sub_category_id, function ($q) use ($sub_category_id) {
                $q->where('subcategory_id', $sub_category_id);
            })
            ->when($child_sub_category, function ($q) use ($child_sub_category) {
                $q->whereJsonContains('childcategory_id', strval($child_sub_category));
            })
            ->when($request->max_price, function ($q) use ($request) {
                $q->whereRaw("(courses.price between $request->min_price and $request->max_price or courses.discount_price between $request->min_price and $request->max_price)");
                //  $q->orWhereBetween('courses.discount_price', [$request->min_price, $request->max_price]);
            })
            ->when($request->max_time, function ($q) use ($request) {
                $q->whereBetween('courses.credit_hours', [$request->min_time, $request->max_time]);
            })
            ->when($request->rating, function ($q) use ($request) {
                $q->join('review_ratings', 'review_ratings.course_id', '=', 'courses.id')
                    ->groupBy('review_ratings.course_id')
                    ->havingRaw('avg(avg_rating) >= ?', [$request->rating]);
            })
            ->active()
            ->paginate($perPage);

        $courses->getCollection()->transform(function ($b) use ($courses, $user) {
            $data = [
                'id' => $b->id,
                'title' => $b->title,
                'image' => url('/images/course/' . $b->preview_image),
                'instructor' => $b->user->fname . ' ' . $b->user->lname,
                'lessons' => $b->courseclass->count(),
                'in_wishlist' => $user ? ($b->inwishlist($user->id) ? true : false) : false,
                'rating' => round($b->review->avg('avg_rating'), 2),
                'reviews_by' => $b->review->count() ?? 0,
            ];
            return $data;
        });

        return response()->json($courses, 200);
    }


    public function recentcourse(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $course = Course::where('status', 1)->orderBy('id', 'DESC')->with('include')->with('whatlearns')->with('user')->get();

        $course = $course->map(function ($c) use ($course) {

            $reviews = ReviewRating::where('course_id', $c->id)->where('status', '1')->get();
            $count = ReviewRating::where('course_id', $c->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {

                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = ($count * 3) * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = ($count * 3) * 5;

            if ($count != 0) {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = ($ratings_var0 / 2) / 10;

                $total_rating = round($overallrating, 1);
            }

            $c['in_wishlist'] = Is_wishlist::in_wishlist($c->id);
            $c['total_rating_percent'] = round($course_total_rating, 2);
            $c['total_rating'] = $total_rating;

            return $c;
        });

        return response()->json(array('course' => $course), 200);
    }


    public function featuredcourse(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $featured = Course::where('status', 1)->where('featured', 1)->with('include')->with('whatlearns')->with('review')->with('user')->get();

        $featured = $featured->map(function ($c) use ($featured) {

            $reviews = ReviewRating::where('course_id', $c->id)->where('status', '1')->get();
            $count = ReviewRating::where('course_id', $c->id)->count();
            $learn = 0;
            $price = 0;
            $value = 0;
            $sub_total = 0;
            $sub_total = 0;
            $course_total_rating = 0;
            $total_rating = 0;

            if ($count > 0) {

                foreach ($reviews as $review) {
                    $learn = $review->learn * 5;
                    $price = $review->price * 5;
                    $value = $review->value * 5;
                    $sub_total = $sub_total + $learn + $price + $value;
                }

                $count = ($count * 3) * 5;
                $rat = $sub_total / $count;
                $ratings_var0 = ($rat * 100) / 5;

                $course_total_rating = $ratings_var0;
            }

            $count = ($count * 3) * 5;

            if ($count != "") {
                $rat = $sub_total / $count;

                $ratings_var = ($rat * 100) / 5;

                $overallrating = ($ratings_var0 / 2) / 10;

                $total_rating = round($overallrating, 1);
            }

            $c['in_wishlist'] = Is_wishlist::in_wishlist($c->id);
            $c['total_rating_percent'] = round($course_total_rating, 2);
            $c['total_rating'] = $total_rating;
            return $c;
        });

        return response()->json(array('featured' => $featured), 200);
    }


    public function bundle(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|max:100'
        ], [
            'perPage.max' => __("Pagination should not be more than 100"),
        ]);

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        $lang = $request->header('Accept-Language') ?? 'en';

        $category_id = $request->category_id;
        $seach_text = $request->search_text ?? null;
        $scnd_category_id = $request->scnd_category_id;
        $sub_category_id = $request->sub_category;
        $child_sub_category = $request->ch_sub_category;
        $perPage = $request->perPage ?? 10;

        if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category) && $user) {
            $category_id = $user->main_category;
            $scnd_category_id = $user->scnd_category_id;
            $sub_category_id = $user->sub_category;
            $child_sub_category = $user->ch_sub_category;
        }

        // if ((!$category_id || !$scnd_category_id || !$sub_category_id || !$child_sub_category)) {
        //     return response()->json(array("errors"=>["message"=>['Category Not selected']]),422);
        // }

        $bundles = BundleCourse::select('bundle_courses.*')
            ->join('courses_in_bundle', 'bundle_courses.id', '=', 'courses_in_bundle.bundle_id')
            ->join('courses', 'courses.id', '=', 'courses_in_bundle.course_id')
            ->when($request->max_price, function ($q) use ($request) {
                $q->whereRaw("(bundle_courses.price between $request->min_price and $request->max_price or bundle_courses.discount_price between $request->min_price and $request->max_price)");
                // $q->whereBetween('bundle_courses.price', [$request->min_price, $request->max_price]);
                // $q->orWhereBetween('bundle_courses.discount_price', [$request->min_price, $request->max_price]);
            })
            ->when($seach_text, function ($q) use ($seach_text, $lang) {
                $q->where(DB::raw("LOWER(bundle_courses.title->>'$.en')"), 'like', '%' . strtolower($seach_text) . '%')
                    ->orWhere(DB::raw("LOWER(bundle_courses.title->>'$.ar')"), 'like', '%' . strtolower($seach_text) . '%');
            })
            ->when($seach_text, function ($q) use ($seach_text) {
                $q->where('bundle_courses.title', 'like', '%' . $seach_text . '%');
            })
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('courses.category_id', $category_id);
            })
            ->when($scnd_category_id, function ($q) use ($scnd_category_id) {
                $q->where('courses.scnd_category_id', $scnd_category_id);
            })
            ->when($sub_category_id, function ($q) use ($sub_category_id) {
                $q->where('courses.subcategory_id', $sub_category_id);
            })
            ->when($child_sub_category, function ($q) use ($child_sub_category) {
                $q->whereJsonContains('courses.childcategory_id', strval($child_sub_category));
            })
            ->when($request->max_time, function ($q) use ($request) {
                $q->whereBetween('courses.credit_hours', [$request->min_time, $request->max_time]);
            })
            ->where('bundle_courses.status', '1')
            ->where('bundle_courses.end_date', '>=', date('Y-m-d'))
            ->groupBy('bundle_courses.id')
            ->paginate($perPage);

        $bundles->getCollection()->transform(function ($bundle) use ($bundles, $user) {

            $arr = [
                'id' => $bundle->id,
                'course_id' => $bundle->course_id,
                'title' => $bundle->title,
                'in_wishlist' => $user ? ($bundle->inwishlist($user->id) ? true : false) : false,
                'image' => url('images/bundle/' . $bundle->preview_image),
                'price' => $bundle->price,
                'discount_price' => $bundle->discount_price,
                'total_courses' => is_array($bundle->course_id) ? count($bundle->course_id) : 0,
                'status' => $bundle->status,
            ];
            return $arr;
        });

        return response()->json($bundles, 200);
    }


    public function bundledetail(Request $request, $id)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key'
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $bundle = BundleCourse::active()->find($id);

        if (!$bundle) {
            return response()->json(array("errors" => ["message" => [__("Package not exist OR may have been disabled")]]), 422);

        }

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;

        $order = null;
        if ($user) {
            $order = Order::where(['user_id' => $user->id, 'bundle_id' => $id])->activeOrder()->first();
        }

        $courses_in_bundle = [];
        // $courses_in_bundle = CoursesInBundle::where('bundle_id',$id)->whereHas('course', function($query){
        //     $query->active();
        // })->paginate(5);

        $courses_in_bundle = CoursesInBundle::where('bundle_id', $id)->paginate(5);

        $lg_user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        $courses_in_bundle->getCollection()->transform(function ($course) use ($lg_user) {
            $b = $course->course;
            $courses = [
                'id' => $b->id,
                'title' => $b->title,
                'image' => url('/images/course/' . $b->preview_image),
                'lessons' => $b->courseclass->count(),
                'instructor' => $b->instructor->fname . ' ' . $b->instructor->lname,
                'in_wishlist' => $lg_user ? ($b->inwishlist($lg_user->id) ? true : false) : false,
                'rating' => round($b->review->avg('avg_rating'), 2),
                'reviews_by' => $b->review->count() ?? 0,
            ];
            return $courses;
        });


        $data = [
            'id' => $bundle->id,
            'order_id' => $order ? $order->id : null,
            'is_cart' => $user ? ($user->cartType('bundle', $bundle->id)->exists() ? true : false) : false,
            'title' => $bundle->title,
            'short_detail' => $bundle->short_detail,
            'detail' => $bundle->detail,
            'in_wishlist' => $user ? ($bundle->inwishlist($user->id) ? true : false) : false,
            'image' => url('/images/bundle/' . $bundle->preview_image),
            'total_courses' => count($bundle->course_id),
            'price' => $bundle->price,
            'discount_price' => $bundle->discount_price,
            'discount_type' => $bundle->discount_type,
            'instalment_price' => $bundle->discount_price > 0 ? $bundle->_installments()->sum('amount') ?? 0 : null,
            'instalments' => $bundle->discount_price > 0 ? $bundle->_installments() : [],
            'created_by' => $bundle->user->fname . ' ' . $bundle->user->lname,
            // 'last_updated' => date('jS F Y', strtotime($bundle->updated_at)),
            'last_updated' => $bundle->updated_at,
            'courses' => $courses_in_bundle
        ];

        return response()->json($data, 200);
    }


    public function bundleCourses(Request $request, $id)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $bundle = BundleCourse::active()->find($id);

        if (!$bundle) {
            return response()->json(array("errors" => ["message" => [__("Package not exist OR may have been disabled")]]), 422);

        }

        $courses_in_bundle = [];
        $courses_in_bundle = CoursesInBundle::where('bundle_id', $id)->paginate(10);
        $lg_user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        $courses_in_bundle->getCollection()->transform(function ($course) use ($lg_user) {
            $b = $course->course;
            $courses = [
                'id' => $b->id,
                'title' => $b->title,
                'image' => url('/images/course/' . $b->preview_image),
                'lessons' => $b->courseclass->count(),
                'instructor' => $b->instructor->fname . ' ' . $b->instructor->lname,
                'in_wishlist' => $lg_user ? ($b->inwishlist($lg_user->id) ? true : false) : false,
                'rating' => round($b->review->avg('avg_rating'), 2),
                'reviews_by' => $b->review->count() ?? 0,
            ];
            return $courses;
        });
        return response()->json($courses_in_bundle, 200);
    }


    public function studentfaq(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);


        $faq = FaqStudent::where('status', 1)->get();
        return response()->json(array('faq' => $faq), 200);
    }


    public function instructorfaq(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $faq = FaqInstructor::where('status', 1)->get();
        return response()->json(array('faq' => $faq), 200);
    }


    public function blog(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $blog = Blog::where('status', 1)->get();

        $blog_result = array();

        foreach ($blog as $data) {

            $blog_result[] = array(
                'id' => $data->id,
                'user' => $data->user_id,
                'date' => $data->date,
                'image' => $data->image,
                'heading' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($data->heading))),
                'detail' => preg_replace("/\r\n|\r|\n/", '', strip_tags(html_entity_decode($data->detail))),
                'text' => $data->text,
                'approved' => $data->approved,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('blog' => $blog_result), 200);
    }


    public function blogdetail(Request $request)
    {
        $request->validate([
            'blog_id' => 'required',
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $blog = Blog::where('id', $request->blog_id)->where('status', 1)->with('user')->get();

        return response()->json(array('blog' => $blog), 200);
    }


    public function recentblog(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $blog = Blog::where('status', 1)->orderBy('id', 'DESC')->get();

        return response()->json(array('blog' => $blog), 200);
    }


    public function wishlistcourse(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $user = Auth::guard('api')->user();
        $wishlists = Wishlist::query()
            ->where('user_id', $user->id)
            ->whereHas('courses')
            ->whereNotNull('course_id')
            ->latest()
            ->paginate(10);

        $wishlists->getCollection()->transform(function ($w) {

            $course = Course::with(['chapter', 'review'])
                ->findOrFail($w->course_id);

            $data = [
                'id' => $course->wishlist->id,
                'user_id' => $w->user_id,
                'course_id' => $course->id,
                'title' => $course->title,
                'image' => url('/images/course/' . $course->preview_image),
                'instructor' => $course->instructor->fname . ' ' . $course->instructor->lname,
                'lessons' => $course->courseclass->count(),
                'reviews_by' => round($course->review->avg('avg_rating'), 2),
                'total_rating' => $course->review->count() ?? 0,
            ];
            return $data;
        });

        return response()->json($wishlists, 200);

    }


    public function wishlistbundle()
    {

        $user = Auth::guard('api')->user();
        $wishlists = Wishlist::query()
            ->where('user_id', $user->id)
            ->whereHas('bundle')
            ->whereNotNull('bundle_id')
            ->latest()
            ->paginate(10);

        $wishlists->getCollection()->transform(function ($w) {

            $bundle = BundleCourse::findOrFail($w->bundle_id);

            $data = [
                'id' => $w->id,
                'bundle_id' => $w->bundle_id,
                'title' => $bundle->title,
                'image' => url('images/bundle/' . $bundle->preview_image),
                'price' => $bundle->price,
                'discount_price' => $bundle->discount_price,
                'total_courses' => count($bundle->course_id),
                'status' => $bundle->status,
            ];
            return $data;
        });

        return response()->json($wishlists, 200);
    }


    public function wishlistmeeting()
    {

        $user = Auth::guard('api')->user();
        $bbl_meetings = Wishlist::query()
            ->where('user_id', $user->id)
            ->whereHas('meeting')
            ->whereNotNull('meeting_id')
            ->latest()
            ->paginate(10);

        $bbl_meetings->getCollection()->transform(function ($w) {

            $meeting = BBL::findOrFail($w->meeting_id);

            return [
                'id' => $w->id,
                'meeting_id' => $w->meeting_id,
                'owner_id' => $meeting->owner_id,
                'instructor_id' => $meeting->instructor_id,
                'meeting_title' => $meeting->meetingname,
                'bigblue_meetingid' => $meeting->meetingid,
                'instructor' => $meeting->user->fname . ' ' . $meeting->user->lname,
                // 'date' => date('d M', strtotime($meeting->start_time)),
                // 'time' => date('h:i A', strtotime($meeting->start_time)),
                'date_time' => $meeting->start_time,
                'image' => url('images/bg/' . $meeting->image),
                'discount_price' => $meeting->discount_price,
                'type' => 'live-streaming'
            ];
        });

        return response()->json($bbl_meetings, 200);
    }


    public function wishlistsession()
    {

        $user = Auth::guard('api')->user();
        $offline_sessions = Wishlist::query()
            ->where('user_id', $user->id)
            ->whereHas('session')
            ->whereNotNull('offline_session_id')
            ->latest()
            ->paginate(10);

        $offline_sessions->getCollection()->transform(function ($w) {

            $session = OfflineSession::findOrFail($w->offline_session_id);

            return [
                'id' => $w->id,
                'meeting_id' => $w->offline_session_id,
                'owner_id' => $session->owner_id,
                'instructor_id' => $session->instructor_id,
                'meeting_title' => $session->title,
                'instructor' => $session->user->fname . ' ' . $session->user->lname,
                // 'date' => date('d M', strtotime($meeting->start_time)),
                // 'time' => date('h:i A', strtotime($meeting->start_time)),
                'date_time' => $session->start_time,
                'image' => url('images/offlinesession/' . $session->image),
                'discount_price' => $session->discount_price,
            ];
        });

        return response()->json($offline_sessions, 200);
    }


    public function showwishlist()
    {

        $user = Auth::guard('api')->user();
        $wishlist = Wishlist::where('user_id', $user->id)->get();

        $courses = [];
        $bundles = [];
        $meetings = [];
        $data1 = [];
        $data2 = [];
        $data3 = [];

        foreach ($wishlist as $w) {
            if ($w->course_id) {
                $courses[] = Course::with('chapter')
                    ->with('review')
                    ->findOrFail($w->course_id);
            }
            if ($w->bundle_id) {
                $bundles[] = BundleCourse::findOrFail($w->bundle_id);
            }
            if ($w->meeting_id) {
                $meetings[] = BBL::findOrFail($w->meeting_id);
            }
        }

        if (count($courses) > 0) {

            foreach ($courses as $course) {
                $t = $course->review->count() ?? 0;
                $l = $course->review->avg('learn');
                $p = $course->review->avg('price');
                $v = $course->review->avg('value');
                $t_avg = $t > 0 ? (($l + $p + $v) / $t) : 0;
                $data1[] = [
                    'id' => $course->wishlist->id,
                    'user_id' => $w->user_id,
                    'course_id' => $course->id,
                    'title' => $course->title,
                    'image' => url('/images/course/' . $course->preview_image),
                    'instructor' => $course->instructor->fname . ' ' . $course->instructor->lname,
                    'lessons' => $course->courseclass->count(),
                    'reviews_by' => $t_avg,
                    'total_rating' => $t,
                ];
            }
        }

        if (count($bundles) > 0) {

            foreach ($bundles as $bundle) {

                $data2[] = [
                    'id' => $bundle->wishlist->id,
                    'bundle_id' => $w->bundle_id,
                    'title' => $bundle->title,
                    // 'detail' => strip_tags($bundle->detail),
                    // 'type' => $bundle->type,
                    // 'preview_image' => $bundle->preview_image,
                    'image' => url('images/bundle/' . $bundle->preview_image),
                    'price' => $bundle->price ? $bundle->price : 0,
                    'discount_price' => $bundle->discount_price ? $bundle->discount_price : 0,
                    // 'slug' => $bundle->slug,
                    'total_courses' => count($bundle->course_id),
                    'status' => $bundle->status,
                ];
            }
        }

        if (count($meetings) > 0) {

            foreach ($meetings as $m) {

                $data3[] = [
                    'id' => $m->wishlist->id,
                    'meeting_id' => $m->id,
                    'owner_id' => $m->owner_id,
                    'instructor_id' => $m->instructor_id,
                    'meeting_title' => $m->meetingname,
                    'bigblue_meetingid' => $m->meetingid,
                    'instructor' => $m->user->fname . ' ' . $m->user->lname,
                    // 'date' => date('d M', strtotime($m->start_time)),
                    // 'time' => date('h:i A', strtotime($m->start_time)),
                    'date_time' => $m->start_time,
                    'image' => url('images/bg/' . $m->image),
                    'price' => $m->price ? $m->price : 0
                ];
            }
        }

        return response()->json(array('courses' => $data1, 'bundles' => $data2, 'meetings' => $data3), 200);
    }


    public function addtowishlist(Request $request)
    {

        $auth = $user = Auth::guard('api')->user();
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
            'bundle_id' => [
                'nullable',
                'exists:bundle_courses,id',
                Rule::unique('orders')->where(function ($query) use ($auth) {
                    return $query->where('user_id', $auth->id)
                        ->where('status', '<>', '0')
                        ->whereNull('deleted_at');
                })
            ],
            'course_id' => [
                'nullable',
                'exists:courses,id',
                Rule::unique('orders')->where(function ($query) use ($auth) {
                    return $query->where('user_id', $auth->id)
                        ->where('status', '<>', '0')
                        ->whereNull('deleted_at');
                })
            ],
            'meeting_id' => [
                'nullable',
                'exists:bigbluemeetings,id',
                Rule::unique('orders')->where(function ($query) use ($auth) {
                    return $query->where('user_id', $auth->id)
                        ->where('status', '<>', '0')
                        ->whereNull('deleted_at');
                })
            ],
            'offline_session_id' => [
                'nullable',
                'exists:offline_sessions,id',
                Rule::unique('orders')->where(function ($query) use ($auth) {
                    return $query->where('user_id', $auth->id)
                        ->where('status', '<>', '0')
                        ->whereNull('deleted_at');
                })
            ],
        ], [
            "bundle_id.exists" => __("Package not found"),
            "bundle_id.unique" => __("You already enrolled in this package"),
            "course_id.exists" => __("Course not found"),
            "course_id.unique" => __("You already enrolled in this course"),
            "meeting_id.exists" => __("Live streeaming not found"),
            "meeting_id.unique" => __("You already enrolled in this live streaming"),
            "offline_session_id.exists" => __("In-person session not found"),
            "offline_session_id.unique" => __("You already enrolled in this In-person session"),
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);


        if ($request->course_id) {

            $wishlist = Wishlist::where('course_id', $request->course_id)->where('user_id', $auth->id)->first();
        } elseif ($request->bundle_id) {

            $wishlist = Wishlist::where('bundle_id', $request->bundle_id)->where('user_id', $auth->id)->first();
        } elseif ($request->meeting_id) {

            $wishlist = Wishlist::where('meeting_id', $request->meeting_id)->where('user_id', $auth->id)->first();
        } elseif ($request->offline_session_id) {

            $wishlist = Wishlist::where('offline_session_id', $request->offline_session_id)->where('user_id', $auth->id)->first();
        }


        if (!empty($wishlist)) {

            return response()->json(array("errors" => ["message" => [__("It's already in your saved list")]]), 422);

        } else {
            if ($request->course_id) {

                $wishlist = Wishlist::create([
                    'course_id' => $request->course_id,
                    'user_id' => $auth->id,
                ]);
                return response()->json(__('Course is added to your saved list'), 200);
            } elseif ($request->bundle_id) {

                $wishlist = Wishlist::create([
                    'bundle_id' => $request->bundle_id,
                    'user_id' => $auth->id,
                ]);
                return response()->json(__('Package is added to your saved list'), 200);
            } elseif ($request->meeting_id) {

                $wishlist = Wishlist::create([
                    'meeting_id' => $request->meeting_id,
                    'user_id' => $auth->id,
                ]);
                return response()->json(__('Meeting is added to your saved list'), 200);
            } elseif ($request->offline_session_id) {

                $wishlist = Wishlist::create([
                    'offline_session_id' => $request->offline_session_id,
                    'user_id' => $auth->id,
                ]);
                return response()->json(__('In-person session is added to your saved list'), 200);
            }

            return response()->json(array("errors" => ["message" => [__('Select something to Add in your saved list')]]), 422);
        }
    }


    public function removewishlist(Request $request)
    {
        $request->validate([
            'course_id' => ['nullable', Rule::exists('wishlists')->where('user_id', Auth::id())],
            'bundle_id' => ['nullable', Rule::exists('wishlists')->where('user_id', Auth::id())],
            'meeting_id' => ['nullable', Rule::exists('wishlists')->where('user_id', Auth::id())],
            'offline_session_id' => ['nullable', Rule::exists('wishlists')->where('user_id', Auth::id())],
        ], [
            "bundle_id.exists" => __("bundle not found"),
            "bundle_id.unique" => __("you already enrolled in this bundle"),
            "course_id.exists" => __("course not found"),
            "course_id.unique" => __("you already enrolled in this course"),
            "meeting_id.exists" => __("meeting not found"),
            "meeting_id.unique" => __("you already enrolled in this meeting"),
            "offline_session_id.exists" => __("In-person session not found"),
            "offline_session_id.unique" => __("you already enrolled in this In-person session"),
        ]);

        $auth = Auth::guard('api')->user();

        if ($request->course_id) {

            Wishlist::where('course_id', $request->course_id)->where('user_id', $auth->id)->delete();
            return response()->json(__('course removed from saved'), 200);

        } elseif ($request->bundle_id) {

            Wishlist::where('bundle_id', $request->bundle_id)->where('user_id', $auth->id)->delete();
            return response()->json(__('package removed from saved'), 200);

        } elseif ($request->meeting_id) {

            Wishlist::where('meeting_id', $request->meeting_id)->where('user_id', $auth->id)->delete();
            return response()->json(__('live streaming removed from saved'), 200);

        } elseif ($request->offline_session_id) {

            Wishlist::where('offline_session_id', $request->offline_session_id)->where('user_id', $auth->id)->delete();
            return response()->json(__('In-person session removed from saved'), 200);

        } else {
            return response()->json(array("errors" => ["message" => [__('error')]]), 401);
        }
    }


    public function addtocartCourse(Request $request)
    {
        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    return $query
                        ->where('price', '<>', '0')
                        ->where('end_date', '>=', date('Y-m-d'))
                        ->where('status', '1');
                })
            ],
            'payment_type' => 'nullable|in:installments,full',
            'installments' => 'required_if:payment_type,installments|array',
        ], [
            "course_id.required" => __("Course ID is required"),
            "course_id.exists" => __("Course not exist OR may have been disabled"),
            "payment_type" => __("Payment type is invalid"),
            "installments.required_if" => __("Installments not selected"),
            "installments.array" => __("Installments invalid"),
        ]);

        $auth = Auth::guard('api')->user();

        $course = Course::find($request->course_id);
        $cart = Cart::where('course_id', $course->id)->where('user_id', $auth->id)->first();

        if (isset($request->payment_type) && $request->payment_type == 'installments' &&
            isset($request->installments) && $course->installment && $course->installments
        ) {
            $due_inst = $course->installments->where('due_date', '<', date('Y-m-d'))->pluck('id')->toArray();
            $inst = $course->installments->pluck('id')->toArray();
            if (count($request->installments) < count($due_inst)) {
                return response()->json(array("errors" => ["message" => [__("Pay all due installments")]]), 422);
            }
            foreach ($request->installments as $in) {
                if (!in_array($in, $inst)) {
                    return response()->json(array("errors" => ["message" => [__("Selected Installment has been removed or invalid")]]), 422);
                }
            }
            $price_total = $course->installments->sum('amount');
            $pay_amount = $course->installments->whereIn('id', $request->installments)->sum('amount');

            $cart->update([
                'price' => $price_total,
                'offer_price' => $pay_amount,
                'installment' => 1,
                'total_installments' => $request->installments,
            ]);

            // return all carts
            return $this->showcart();

        } else if (isset($request->payment_type) && $request->payment_type == 'full') {

            $cart->update([
                'price' => $course->price,
                'offer_price' => $course->discount_price,
                'installment' => 0,
                'total_installments' => NULL,
            ]);

            // return all carts
            return $this->showcart();
        }

        // Get chapters in cart of this course
        $cartchapters = Cart::select('chapter_id')->where('user_id', $auth->id)->whereNotNull('chapter_id')->pluck('chapter_id')->toArray();

        // Delete chapters in cart that is a part of this course
        foreach ($cartchapters as $u) {
            if (in_array($u, $course->chapter()->pluck('id')->toArray())) {
                Cart::where('chapter_id', $u)->delete();
            }
        }

        $order = Order::where('user_id', $auth->id)->where('course_id', $course->id)->activeOrder()->first();

        if (isset($order)) {
            return response()->json(array("errors" => ["message" => [__("You already purchased this course")]]), 422);

        } else {

            $order2 = Order::where('user_id', $auth->id)->where('course_id', $course->id)->inActiveOrder()->first();
            if (isset($order2)) {
                return response()->json(array("errors" => ["message" => [__("You already purchased this course but Admin has disabled your enrollment")]]), 422);

            }

            if (!empty($cart)) {
                // return all carts
                return $this->showcart();
            } else {
                $cart = Cart::create([
                    'course_id' => $course->id,
                    'user_id' => $auth->id,
                    'type' => $course->type,
                    'category_id' => $course->category_id,
                    'price' => $course->price,
                    'offer_price' => $course->discount_price,
                    'offer_type' => $course->discount_type,
                    'coupon_id' => NULL,
                    'disamount' => 0,
                    'installment' => 0,
                    'total_installments' => NULL,
                ]);

                // course detail and overview data
                $coursecontroller = new CourseController;

                $resp = [
                    'coursechapters' => $coursecontroller->getAllchaptersWithLessons($request),
                    'overview' => $this->detailPage($request),
                ];

                return response()->json($resp, 200);
            }
        }
    }


    public function addtocartChapter(Request $request)
    {
        $request->validate([
            'chapter_id' => [
                'required',
                Rule::exists('course_chapters', 'id')->where(function ($query) {
                    return $query->where('discount_price', '<>', '0')
                        ->where('status', '1');
                })
            ],
        ], [
            "chapter_id.required" => __("Chapter ID is required"),
            "chapter_id.exists" => __("Chapter not exist OR may have been disabled"),
        ]);

        $auth = Auth::guard('api')->user();

        $chapter = CourseChapter::find($request->chapter_id);

        $order = Order::where('user_id', $auth->id)->where('chapter_id', $chapter->id)->activeOrder()->first();
        $cart = Cart::where('chapter_id', $chapter->id)->where('user_id', $auth->id)->first();


        if (isset($order)) {
            return response()->json(array("errors" => ["message" => [__("You already purchased this chapter")]]), 422);

        } else {
            $order2 = Order::where('user_id', $auth->id)->where('chapter_id', $chapter->id)->inActiveOrder()->first();
            if (isset($order2)) {
                return response()->json(array("errors" => ["message" => [__("You already purchased this chapter but Admin has disabled your enrollment")]]), 422);

            }

            if (!empty($cart)) {
                return response()->json(array("errors" => ["message" => [__("Chapter is already in cart")]]), 422);

            } else {
                $cart = Cart::create([
                    'chapter_id' => $chapter->id,
                    'user_id' => $auth->id,
                    'category_id' => $chapter->courses->category_id,
                    'price' => $chapter->price,
                    'offer_price' => $chapter->price,
                    'type' => 1,
                    'coupon_id' => NULL,
                    'disamount' => 0,
                    'installment' => 0,
                    'total_installments' => NULL,
                ]);

                $request->merge(['course_id' => $chapter->course_id]);

                $coursecontroller = new CourseController;

                $resp = [
                    'coursechapters' => $coursecontroller->getAllchaptersWithLessons($request),
                    'overview' => $this->detailPage($request),
                ];

                return response()->json($resp, 200);
            }
        }
    }


    public function addtocartBundle(Request $request)
    {

        $request->validate([
            'bundle_id' => [
                'required',
                Rule::exists('bundle_courses', 'id')->where(function ($query) {
                    return $query
                        ->where('price', '<>', '0')
                        ->where('end_date', '>=', date('Y-m-d'))
                        ->where('status', '1');
                })
            ],
            'payment_type' => 'nullable|in:installments,full',
            'installments' => 'required_if:payment_type,installments|array',
        ], [
            "bundle_id.required" => __("Package ID is required"),
            "bundle_id.exists" => __("Package not found"),
            "payment_type" => __("Payment type is invalid"),
            "installments.required_if" => __("Installments not selected"),
            "installments.array" => __("Installments invalid"),
        ]);

        $auth = Auth::guard('api')->user();

        $bundle = BundleCourse::find($request->bundle_id);

        $cart = Cart::where('bundle_id', $bundle->id)->where('user_id', $auth->id)->first();

        if (isset($request->payment_type) && $request->payment_type == 'installments' && isset($request->installments) && $bundle->installment && $bundle->installments) {
            $due_inst = $bundle->installments->where('due_date', '<', date('Y-m-d'))->pluck('id')->toArray();
            $inst = $bundle->installments->pluck('id')->toArray();
            if (count($request->installments) < count($due_inst)) {
                return response()->json(array("errors" => ["message" => [__("Pay all due installments")]]), 422);
            }
            foreach ($request->installments as $in) {
                if (!in_array($in, $inst)) {
                    return response()->json(array("errors" => ["message" => [__("Selected Installment has been removed or invalid")]]), 422);
                }
            }
            $price_total = $bundle->installments->sum('amount');
            $pay_amount = $bundle->installments->whereIn('id', $request->installments)->sum('amount');

            $cart->update([
                'price' => $price_total,
                'offer_price' => $pay_amount,
                'installment' => 1,
                'total_installments' => $request->installments,
            ]);

            // return all carts
            return $this->showcart();

        } else if (isset($request->payment_type) && $request->payment_type == 'full') {

            $cart->update([
                'price' => $bundle->price,
                'offer_price' => $bundle->discount_price,
                'installment' => 0,
                'total_installments' => NULL,
            ]);

            // return all carts
            return $this->showcart();
        }

        $order = Order::where('user_id', $auth->id)->where('bundle_id', $bundle->id)->activeOrder()->first();

        if (isset($order)) {
            return response()->json(array("errors" => ["message" => [__("You already purchased this package")]]), 422);

        } else {
            $order2 = Order::where('user_id', $auth->id)->where('bundle_id', $bundle->id)->inActiveOrder()->first();
            if (isset($order2)) {
                return response()->json(array("errors" => ["message" => [__("You already purchased this package but Admin has disabled your enrollment")]]), 422);

            }

            if (!empty($cart)) {
                // return all carts
                return $this->showcart();
            } else {

                $cart = Cart::create([
                    'bundle_id' => $bundle->id,
                    'user_id' => $auth->id,
                    'type' => $bundle->type,
                    'category_id' => $bundle->category_id,
                    'price' => $bundle->price,
                    'offer_price' => $bundle->discount_price,
                    'offer_type' => $bundle->discount_type,
                    'coupon_id' => NULL,
                    'disamount' => 0,
                    'installment' => 0,
                    'total_installments' => NULL,
                ]);

                return response()->json('Package is added to your cart', 200);
            }
        }
    }

    public function addtocartMeeting(Request $request)
    {

        $request->validate([
            'meeting_id' => [
                'required',
                Rule::exists('bigbluemeetings', 'id')->where(function ($query) {
                    return $query->where('price', '<>', '0')
                        ->where('expire_date', '>=', date('Y-m-d'));
                })
            ],

        ], [
            "meeting_id.required" => __("Live streaming ID is required"),
            "meeting_id.exists" => __("Live streaming not found"),
        ]);

        $meetings = BBL::where('id', $request->meeting_id)->whereColumn('order_count', '<', 'setMaxParticipants')->get();


        if ($meetings->isNotEmpty()) {

            $auth = Auth::guard('api')->user();
            $bbl_meeting = BBL::find($request->meeting_id);

            $order = Order::where('user_id', $auth->id)->where('meeting_id', $bbl_meeting->id)->activeOrder()->first();

            $cart = Cart::where('meeting_id', $bbl_meeting->id)->where('user_id', $auth->id)->first();

            if (isset($order)) {
                return response()->json(array("errors" => ["message" => [__("You already purchased this live streaming")]]), 422);

            } else {
                $order2 = Order::where('user_id', $auth->id)->where('meeting_id', $bbl_meeting->id)->inActiveOrder()->first();
                if (isset($order2)) {
                    return response()->json(array("errors" => ["message" => [__("You already purchased this live streaming but Admin has disabled your enrollment")]]), 422);

                }

                if (!empty($cart)) {
                    return response()->json(array("errors" => ["message" => [__("Live streaming is already in cart")]]), 422);

                } else {

                    $cart = Cart::create([
                        'meeting_id' => $bbl_meeting->id,
                        'category_id' => $bbl_meeting->main_category,
                        'user_id' => $auth->id,
                        'price' => $bbl_meeting->price,
                        'offer_price' => $bbl_meeting->discount_price,
                        'coupon_id' => NULL,
                        'disamount' => 0,
                        'installment' => 0,
                        'total_installments' => NULL,
                    ]);

                    return response()->json('Live streaming is added to your cart', 200);
                }
            }

        } else {
            return response()->json(['errors' => ['message' => ['Live streaming seats not available anymore']]], 422);
        }
    }


    public function addtocartOfflineSession(Request $request)
    {

        $request->validate([
            'offline_session_id' => [
                'required',
                Rule::exists('offline_sessions', 'id')->where(function ($query) {
                    return $query->where('discount_price', '<>', '0')
                        ->where('expire_date', '>=', date('Y-m-d'))
                        ->where('is_ended', '<>', '1');
                })
            ],
        ], [
            "offline_session_id.required" => __("In-person session ID is required"),
            "offline_session_id.exists" => __("In-person session not found"),
        ]);

        $sessions = OfflineSession::where('id', $request->offline_session_id)->whereColumn('order_count', '<', 'setMaxParticipants')->get();

        if ($sessions->isNotEmpty()) {

            $auth = Auth::guard('api')->user();
            $offline_session = OfflineSession::find($request->offline_session_id);

            $order = Order::where('user_id', $auth->id)->where('offline_session_id', $offline_session->id)->activeOrder()->first();

            $cart = Cart::where('offline_session_id', $offline_session->id)->where('user_id', $auth->id)->first();

            if (isset($order)) {
                return response()->json(array("errors" => ["message" => [__("You already purchased this In-person session")]]), 422);

            } else {
                $order2 = Order::where('user_id', $auth->id)->where('offline_session_id', $offline_session->id)->inActiveOrder()->first();
                if (isset($order2)) {
                    return response()->json(array("errors" => ["message" => [__("You already purchased this in-person session but Admin has disabled your enrollment")]]), 422);

                }

                if (!empty($cart)) {
                    return response()->json(array("errors" => ["message" => [__("In-person session is already in cart")]]), 422);

                } else {

                    $cart = Cart::create([
                        'offline_session_id' => $offline_session->id,
                        'category_id' => $offline_session->main_category,
                        'user_id' => $auth->id,
                        'price' => $offline_session->price,
                        'offer_price' => $offline_session->discount_price,
                        'coupon_id' => NULL,
                        'disamount' => 0,
                        'installment' => 0,
                        'total_installments' => NULL,
                    ]);

                    return response()->json('In-person session is added to your cart', 200);
                }
            }

        } else {
            return response()->json(['errors' => ['message' => ['In-person session seats not available anymore']]], 422);
        }
    }


    public function removecart(Request $request)
    {

        $request->validate([
            'id' => 'nullable|exists:carts,id',
            'course_id' => ['nullable', Rule::exists('carts')->where('user_id', auth()->id())],
            'chapter_id' => ['nullable', Rule::exists('carts')->where('user_id', auth()->id())],
            'bundle_id' => ['nullable', Rule::exists('carts')->where('user_id', auth()->id())],
            'meeting_id' => ['nullable', Rule::exists('carts')->where('user_id', auth()->id())],
            'offline_session_id' => ['nullable', Rule::exists('carts')->where('user_id', auth()->id())],
        ], [
            "id.exists" => __("Cart ID is invalid"),
            "course_id.exists" => __("Course not found"),
            "chapter_id.exists" => __("Chapter not found"),
            "bundle_id.exists" => __("Package not found"),
            "meeting_id.exists" => __("Live streaming not found"),
            "offline_session_id.exists" => __("In-person session not found"),
        ]);


        if ($request->id) {

            $cart = Cart::find($request->id);
            $cart->delete();
            CartCoupon::where('cart_id', $request->id)->delete();

            // return all carts
            return $this->showcart();
        }

        $auth = Auth::guard('api')->user();

        // This is being used in items detail screen to removed item from cart list
        if ($request->chapter_id) {

            $userCarts = Cart::where('chapter_id', $request->chapter_id)->where('user_id', $auth->id)->get();
            foreach ($userCarts as $cart) {
                CartCoupon::where('cart_id', $cart->id)->delete();
            }
            $userCarts->each->delete();

            $chapter = CourseChapter::find($request->chapter_id);

            // add course_id in request object
            $request->merge(['course_id' => $chapter->course_id]);

            // course detail and overview data
            $coursecontroller = new CourseController;

            $resp = [
                'coursechapters' => $coursecontroller->getAllchaptersWithLessons($request),
                'overview' => $this->detailPage($request),
            ];

            return response()->json($resp, 200);

        } elseif ($request->course_id) {

            $userCarts = Cart::where('course_id', $request->course_id)->where('user_id', $auth->id)->get();
            foreach ($userCarts as $cart) {
                CartCoupon::where('cart_id', $cart->id)->delete();
            }
            $userCarts->each->delete();

            // course detail and overview data
            $coursecontroller = new CourseController;

            $resp = [
                'coursechapters' => $coursecontroller->getAllchaptersWithLessons($request),
                'overview' => $this->detailPage($request),
            ];

            return response()->json($resp, 200);

        } elseif ($request->bundle_id) {

            $userCarts = Cart::where('bundle_id', $request->bundle_id)->where('user_id', $auth->id)->get();
            foreach ($userCarts as $cart) {
                CartCoupon::where('cart_id', $cart->id)->delete();
            }
            $userCarts->each->delete();

            return response()->json(__('Package removed from cart'), 200);

        } elseif ($request->meeting_id) {

            $userCarts = Cart::where('meeting_id', $request->meeting_id)->where('user_id', $auth->id)->get();
            foreach ($userCarts as $cart) {
                CartCoupon::where('cart_id', $cart->id)->delete();
            }
            $userCarts->each->delete();

            return response()->json(__('Live streaming removed from cart'), 200);

        } elseif ($request->offline_session_id) {

            $userCarts = Cart::where('offline_session_id', $request->offline_session_id)->where('user_id', $auth->id)->get();
            foreach ($userCarts as $cart) {
                CartCoupon::where('cart_id', $cart->id)->delete();
            }
            $userCarts->each->delete();

            return response()->json(__('In-person session removed from cart'), 200);

        } else {
            return response()->json(array('error'), 401);

        }
    }


    public function showcart($is_applied = null)
    {
        $data = [];
        $msg = [];
        $arr = [];
        $count = 0;

        $user = Auth::guard('api')->user();

        $msg = Cart::validatecartitem($user->id); // validate cart items

        $carts = Cart::where('user_id', $user->id)
            ->with([
                'cartCoupon' => function ($query) {
                    $query->with('coupon');
                },
                'cartCoupons' => function ($query) {
                    $query->with('coupon');
                },
                'course' => function ($query) {
                    $query->with('installments');
                },
                'bundle' => function ($query) {
                    $query->with('installments');
                },
                'meeting',
                'chapter',
                'offlinesession',
            ])
            ->get();

        $data = [];
        $total_amount = 0;
        $cpn_discount = 0;
        $cart_total = 0;

        foreach ($carts as $c) {

            $cart_item = $c->course_id ? Course::find($c->course_id) : ($c->bundle_id ? BundleCourse::find($c->bundle_id) : ($c->meeting_id ? BBL::find($c->meeting_id) : ($c->chapter_id ? CourseChapter::find($c->chapter_id) : ($c->offline_session_id ? OfflineSession::find($c->offline_session_id) : NULL))));
            $cart_type = $c->course_id ? 'course' : ($c->bundle_id ? 'package' : ($c->meeting_id ? 'meeting' : ($c->chapter_id ? 'chapter' : ($c->offline_session_id ? 'offline_session' : NULL))));

            $installments = $c->course_id ? ($c->course->installment ? $c->course->installments->take($cart_item->total_installments) : []) : ($c->bundle_id ? ($c->bundle->installment ? $c->bundle->installments->take($cart_item->total_installments) : []) : []);

            if (isset($installments)) {

                $arr = [];
                $count = 0;
                $installmentCount = $c->total_installments ? count($c->total_installments) : 0;

                foreach ($installments as $key => $installment) {
                    if ($installment->due_date < date('Y-m-d')) {
                        $expire = true;
                        $count++;
                    } else {
                        $expire = false;
                    }

                    $cartCoupon = ($key < $installmentCount) ? $c->cartCoupons->where('installment_id', $c->total_installments[$key])->first() : null;

                    $arr[] = [
                        'id' => $installment->id,
                        'coupon' => $cartCoupon ? $cartCoupon->coupon->code : NULL,
                        'coupon_id' => $cartCoupon ? $cartCoupon->coupon_id : NULL,
                        'cart_coupon_id' => $cartCoupon ? $cartCoupon->id : NULL,
                        'amount' => $installment->amount,
                        'due_date' => $installment->due_date,
                        'expire' => $expire,
                    ];
                }
            }

            $data[] = [
                'id' => $c->id,
                'type' => $cart_type,
                'type_id' => $cart_item->id,
                'title' => $cart_item->_title(),
                'price' => $c->offer_price,
                'discountType' => $c->offer_type,
                'originalPrice' => $c->price,
                'coupon' => $c->cartCoupon ? $c->cartCoupon->coupon->code : NULL,
                'coupon_id' => $c->cartCoupon ? $c->cartCoupon->coupon_id : NULL,
                'cart_coupon_id' => $c->cartCoupon ? $c->cartCoupon->id : NULL,
                'payment_type' => $c->installment == 1 ? 'installments' : 'full',
                'installments' => $count == $cart_item->total_installments ? [] : $arr,
                'total_installments' => $c->total_installments ?? [],
            ];
        }

        $total_amount = 0;
        $cpn_discount = 0;
        $cart_total = 0;

        foreach ($carts as $c) {
            //cart price after offer
            if ($c->installment == 1) {
                $total_amount = $total_amount + $c->offer_price;
            } else {

                if (is_null($c->offer_type) && $c->offer_price == 0) {
                    $total_amount += $c->price;
                } elseif (is_null($c->offer_type) && $c->offer_price) {
                    $total_amount += $c->offer_price;
                } else {
                    //fixed
                    if ($c->offer_type == 'fixed') {
                        $total_amount += ($c->price - $c->offer_price);
                    }
                    //%
                    elseif ($c->offer_type == 'percentage') {
                        $total_amount += ($c->price - (($c->offer_price / 100) * $c->price));
                    }
                }
            }

            //for coupon discount total
            if ($c->installment == 0 && $c->cartCoupon) {
                $cpn_discount = ($c->cartCoupon->disamount > $c->offer_price) ? ($cpn_discount + $c->offer_price) : ($cpn_discount + $c->cartCoupon->disamount);

            } elseif ($c->installment == 1) {
                foreach ($c->cartCoupons as $cartCoupon) {
                    if (in_array($cartCoupon->installment_id, $c->total_installments)) {
                        $cpn_discount = ($cartCoupon->disamount >= $c->offer_price) ? ($cpn_discount + $c->offer_price) : ($cpn_discount + $cartCoupon->disamount);
                    }
                }
            }
        }

        if ($cpn_discount != 0) {
            $cart_total = ($total_amount - $cpn_discount) > 0 ? $total_amount - $cpn_discount : 0;
        } else {

            $cart_total = $total_amount;
        }

        $payments = PaymentGateway::all();
        $visa_master = $payments->where('payment_method', 'VISA/MASTER')->pluck('charges')->first();
        $knet = $payments->where('payment_method', 'KNET')->pluck('charges')->first();

        if ($cart_total == 0) {
            $knet = 0;
            $visa_master = 0;
        }

        return response()->json([
            'cart' => $data,
            'price_total' => $total_amount,

            'knet' => $knet,
            'knet_total' => round(($cart_total + $knet), 3),
            'visa_master' => round((($visa_master / 100) * $cart_total), 3),
            'visa_master_total' => round($cart_total + (($visa_master / 100) * $cart_total), 3),

            'cpn_discount' => ($total_amount - $cart_total) > 0 ? round($total_amount - $cart_total, 3) : 0,
            'after_discount' => round($cart_total, 3),
            'is_applied' => $is_applied,
            'message' => $msg ?? NULL,
            'show_message' => $msg ? true : false,
        ], 200);
    }


    public function removeallcart()
    {

        $auth = Auth::guard('api')->user();

        $cart = Cart::where('user_id', $auth->id)->delete();

        if ($cart == 1) {
            return response()->json(array('Successfully removed from cart'), 200);
        } else {
            return response()->json(array('error'), 401);
        }
    }


    public function removebundlecart(Request $request)
    {
        $request->validate([
            'bundle_id' => 'required',
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $auth = Auth::guard('api')->user();

        $cart = Cart::where('bundle_id', $request->bundle_id)->where('user_id', $auth->id)->delete();

        if ($cart == 1) {
            return response()->json(array('1'), 200);
        } else {
            return response()->json(array('error'), 401);
        }
    }


    public function detailpage(Request $request)
    {

        if ($request->is_bundle === true || $request->is_bundle === 'true') {
            $request->validate([
                'course_id' => 'required'
            ], [
                "course_id.required" => __("Course not selected"),
            ]);

            $exist = BundleCourse::whereJsonContains('course_id', strval($request->course_id))->where('status', '1')->where('end_date', '>=', date('Y-m-d'))->exists();

            if (!$exist) {
                return response()->json(array("errors" => ["message" => [__("Course not exist OR may have been disabled")]]), 422);
            }

        } else {
            $request->validate([
                'course_id' => [
                    'required',
                    Rule::exists('courses', 'id')->where(function ($query) {
                        return $query->where('status', '1')
                            ->where('end_date', '>=', date('Y-m-d'))
                            ->whereNull('deleted_at');
                    })
                ],
            ], [
                "course_id.required" => __("Course not selected"),
                "course_id.exists" => __("Course not exist OR may have been disabled"),
            ]);
        }

        $course = Course::find($request->course_id);

        $orders = [];
        $f_order = [];
        $is_chapter_purchased = NULL;

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        if (Auth::guard('api')->check()) {

            $orders = Order::where('user_id', $user->id)->activeOrder()->get();

            foreach ($course->chapter as $chapter) {
                $chapter_order = $orders->where('chapter_id', $chapter->id);

                if ($chapter_order->isNotEmpty()) {
                    $is_chapter_purchased = true;
                }
            }

            $f_order = $orders->where('course_id', $request->course_id);
            if ($orders && count($f_order) < 1) {
                foreach ($orders as $o) {
                    $f = in_array($request->course_id, $o->bundle_course_id ?? []) ? $o : null;
                    if ($f) {
                        $f_order[] = $o;
                    }
                }
            }

            // Check any chapter of course is added to cart
            $usercart = Cart::select('chapter_id')->where('user_id', $user->id)->whereNotNull('chapter_id')->pluck('chapter_id')->toArray();
            foreach ($usercart as $chapter_id) {
                if (in_array($chapter_id, $course->chapter()->pluck('id')->toArray())) {
                    $is_chapter_carted = true;
                }
            }
        }
        $courseDetail = [
            'id' => $course->id,
            'order_id' => ($orders && count($f_order) > 0) ? collect($f_order)->first()->id : null,
            'wtsap_link' => ($orders && count($f_order) > 0) ? $course->wtsap_link : null,
            'title' => $course->title,
            'image' => url('/images/course/' . $course->preview_image),
            'detail' => $course->detail,
            'duration' => $course->courseclass ? $course->class_duration() ?? 0 : 0,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'in_wishlist' => $user ? ($course->inwishlist($user->id) ? true : false) : false,
            'is_chapter_carted' => $is_chapter_carted ?? false,
            'is_chapter_purchased' => $is_chapter_purchased ?? false,
            'is_cart' => $user ? ($user->cartType('course', $course->id)->exists() ? true : false) : false,
            'price' => $course->price,
            'discount_price' => $course->discount_price,
            'discount_type' => $course->discount_type,
            'instalment_price' => $course->discount_price > 0 ? $course->_installments()->sum('amount') ?? 0 : null,
            'instalments' => $course->discount_price > 0 ? $course->_installments() : [],
            'course_tags' => $course->course_tags
        ];

        $whatlearns = [];
        foreach ($course->whatlearns as $whatlearn) {
            $whatlearns[] = [
                'id' => $whatlearn->id,
                'course_id' => $course->id,
                'detail' => $whatlearn->detail,
            ];
        }

        $reviewszz = ReviewRating::where('course_id', $request->course_id)->where('status', '1')->orderBy('id', 'desc')->paginate(5);
        $reviews_by_user = $user ? (ReviewRating::where('course_id', $request->course_id)->where('user_id', $user->id)->first() ? true : false) : false;
        if ($reviewszz) {
            $reviewszz->getCollection()->transform(function ($review) {
                $data = [
                    'id' => $review->id,
                    'user_id' => $review->user_id,
                    'name' => $review->user->fname . ' ' . $review->user->lname,
                    'image' => url('images/user_img/' . $review->user->user_img),
                    'review' => $review->review,
                    'total_rating' => round($review->avg_rating, 2),
                    // 'created_at' => date('d-M-Y',strtotime($review->created_at)),
                    'created_at' => $review->created_at,
                ];
                return $data;
            });
        }

        $student_enrolled = Order::where('course_id', $request->course_id)->activeOrder()->count();

        $resp = [
            'course' => $courseDetail,
            'instructor' => [
                'id' => $course->teacher->id,
                'name' => $course->teacher->fname . ' ' . $course->teacher->lname,
                'image' => url('/images/user_img/' . $course->teacher->user_img),
                'short_info' => $course->teacher->short_info
            ],
            'whatlearns' => $whatlearns,
            'reviews' => $reviewszz ?? null,
            'reviews_added' => $reviews_by_user,
            'total_learn' => round($course->review->avg('learn'), 2) ?? 0,
            'total_price' => round($course->review->avg('price'), 2) ?? 0,
            'total_value' => round($course->review->avg('value'), 2) ?? 0,
            'duration' => $course->class_duration(),
            'total_rating' => $course->review->count() ?? 0,
            'avg_rating' => round($course->review->avg('avg_rating'), 2) ?? 0,
            'student_enrolled' => $student_enrolled ?? 0,
        ];

        return response()->json($resp, 200);
    }


    public function pages(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);
        return response()->json(['pages' => Page::get()], 200);
    }


    public function allnotification(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $user = Auth::guard('api')->user();
        $notifications = $user->unreadnotifications;
        if ($notifications) {
            return response()->json(array('notifications' => $notifications), 200);
        } else {
            return response()->json(array('notifications' => []), 200);
            return response()->json(array("errors" => ["message" => ['error']]), 401);
        }
    }


    public function notificationread(Request $request, $id)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $userunreadnotification = Auth::guard('api')->user()->unreadNotifications->findOrFail($id);

        if ($userunreadnotification) {
            $userunreadnotification->markAsRead();
            return response()->json(array('1'), 200);
        } else {
            return response()->json(array('error'), 401);
        }
    }


    public function readallnotification(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $notifications = auth()->User()->unreadNotifications()->count();

        if ($notifications > 0) {

            $user = auth()->User();

            foreach ($user->unreadNotifications as $unnotification) {
                $unnotification->markAsRead();
            }

            return response()->json(array('1'), 200);
        } else {
            return response()->json(array('Notification already marked as read !'), 401);
        }
    }


    public function allinstructor(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
            'category_id.exists' => __("Category Not selected"),
        ]);

        $category_id = $request->category_id;
        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;

        if (!$category_id && $user) {
            $category_id = $user->main_category;
        }

        if ((!$category_id)) {
            return response()->json(array("errors" => ["message" => [__("Category Not selected")]]), 422);
        }

        $users = User::where(['role' => 'instructor', 'status' => 1])
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('main_category', $category_id);
            })
            ->paginate(10);

        $users->getCollection()->transform(function ($user) {
            $userr = [
                'id' => $user->id,
                'fname' => $user->fname . ' ' . $user->lname,
                'image' => $user->user_img ? url('/images/user_img/' . $user->user_img) : null,
                'short_info' => $user->short_info,
            ];
            return $userr;
        });

        return response()->json($users, 200);
    }


    public function instructorprofile(Request $request)
    {
        $request->validate([
            'instructor_id' => ['required', Rule::exists('users', 'id')->where('status', '1')],
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
            'instructor_id.required' => __("Instructor Not Exists"),
            'instructor_id.exists' => __("Instructor not exist"),
        ]);

        $user = User::withTrashed()->find($request->instructor_id);
        $course_count = Course::where('user_id', $user->id)->active()->count();
        $enrolled_user = Order::where('instructor_id', $user->id)->activeOrder()->count();
        $courses = Course::where('user_id', $user->id)->active()->paginate(5);
        $userImage = $user->user_img ? url('/images/user_img/' . $user->user_img) : '';
        $user = [
            'id' => $user->id,
            'fname' => $user->fname,
            'lname' => $user->lname,
            'image' => $userImage,
            'short_info' => $user->short_info,
            'detail' => $user->detail,
            'total_courses' => $course_count,
            'enrolled_user' => $enrolled_user
        ];

        $lg_user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;

        $courses->getCollection()->transform(function ($b) use ($lg_user) {
            $course = [
                'id' => $b->id,
                'title' => $b->title,
                'instructor' => $b->user->fname . ' ' . $b->user->lname,
                'image' => url('/images/course/' . $b->preview_image),
                'lessons' => $b->courseclass->count(),
                'in_wishlist' => $lg_user ? ($b->inwishlist($lg_user->id) ? true : false) : false,
                'rating' => round($b->review->avg('avg_rating'), 2),
                'reviews_by' => $b->review->count() ?? 0,
            ];
            return $course;
        });

        // if ($user) {
        return response()->json(array('user' => $user, 'courses' => $courses), 200);
        // } else {
        //     return response()->json(array('error'), 401);
        // }
    }


    public function instructorCourses(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:courses,user_id',
        ], [
            'instructor_id.required' => __("Instructor Not Exists"),
            'instructor_id.exists' => __("Instructor Not Exists"),
        ]);

        $user = Auth::guard('api')->check() ? Auth::guard('api')->user() : null;
        $courses = Course::where('user_id', $request->instructor_id)->where('status', 1)->where('courses.end_date', '>=', date('Y-m-d'))->latest()->paginate(10);


        $courses->getCollection()->transform(function ($b) use ($user) {

            $course = [
                'id' => $b->id,
                'title' => $b->title,
                'instructor' => $b->user->fname . ' ' . $b->user->lname,
                'image' => url('/images/course/' . $b->preview_image),
                'lessons' => $b->courseclass->count(),
                'in_wishlist' => $user ? ($b->inwishlist($user->id) ? true : false) : false,
                'rating' => round($b->review->avg('avg_rating'), 2),
                'reviews_by' => $b->review->count() ?? 0,
            ];
            return $course;
        });

        // if ($user) {
        return response()->json($courses, 200);
        // } else {
        //     return response()->json(array('error'), 401);
        // }
    }


    public function review(Request $request)
    {
        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    return $query->where('status', '1')
                        ->where('end_date', '>=', date('Y-m-d'));
                })
            ],
            'secret' => 'required|exists:api_keys,secret_key',
            'sort_by' => 'required|In:date,rating',
            'sort_order' => 'required|In:ascending,descending',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
            'course_id.required' => __("course not selecte"),
            "course_id.exists" => __("Course not exist OR may have been disabled"),
            'sort_by.required' => __("Filetr not selected"),
            'sort_by.exists' => __("Filetr not selected"),
            'sort_order.required' => __("Filetr not selected"),
            'sort_order.exists' => __("Filetr not selected"),
        ]);

        $sort_by = $request->sort_by == 'rating' ? 'avg_rating' : 'created_at';
        $sort_order = $request->sort_order == 'ascending' ? 'asc' : 'desc';
        $review = ReviewRating::
            where('course_id', $request->course_id)
            ->where('status', '1')
            ->orderBy($sort_by, $sort_order)
            ->paginate(5);
        if ($review) {
            $review->getCollection()->transform(function ($review) {
                $data = [
                    'id' => $review->id,
                    'user_id' => $review->user_id,
                    'name' => $review->user->fname . ' ' . $review->user->lname,
                    'image' => $review->user->user_img ? url('images/user_img/' . $review->user->user_img) : NULL,
                    'review' => $review->review,
                    'total_rating' => $review->avg_rating,
                    'created_at' => date('d-M-Y', strtotime($review->created_at)),
                ];
                return $data;
            });
        }
        return response()->json(array('review' => $review), 200);

    }


    public function duration(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required',
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $chapter = CourseChapter::where('course_id', $request->chapter_id)->first();

        if ($chapter) {

            $duration = CourseClass::where('coursechapter_id', $chapter->id)->sum("duration");
        } else {
            return response()->json(['Invalid Chapter ID !'], 401);
        }

        if ($chapter) {

            return response()->json(array('duration' => $duration), 200);
        } else {
            return response()->json(array('error'), 401);
        }
    }


    public function apikeys(Request $request)
    {

        $key = DB::table('api_keys')->first();

        if (!$key) {
            return response()->json(['key' => null], 401);
        }

        return response()->json(array('key' => $key->secret_key), 200);
    }


    public function coursedetail(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $course = Course::where('status', 1)
            ->with([
                'include' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with([
                'whatlearns' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with([
                'related' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with('review')
            ->with([
                'language' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with('user')
            ->with([
                'order' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with([
                'chapter' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with([
                'courseclass' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->with('policy')->get();

        return response()->json(array('course' => $course), 200);
    }


    public function showcoupon(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $coupon = Coupon::get();

        return response()->json(array('coupon' => $coupon), 200);
    }


    public function becomeaninstructor(Request $request)
    {

        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'age' => 'required',
            'mobile' => 'required',
            'gender' => 'required',
            'detail' => 'required',
            'file' => 'required',
            'image' => 'required',
            'secret' => 'required|exists:api_keys,secret_key',
        ]);


        $auth = Auth::guard('api')->user();

        $users = Instructor::where('user_id', $auth->id)->get();

        if (!$users->isEmpty()) {

            return response()->json('Already Requested !', 401);
        } else {

            if ($file = $request->file('image')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('images/instructor', $name);
                $input['image'] = $name;
            }

            if ($file = $request->file('file')) {
                $name = time() . $file->getClientOriginalName();
                $file->move('files/instructor/', $name);
                $input['file'] = $name;
            }

            $input = $request->all();

            $instructor = Instructor::create([
                'user_id' => $auth->id,
                'fname' => isset($input['fname']) ? $input['fname'] : $auth->fname,
                'lname' => isset($input['lname']) ? $input['lname'] : $auth->lname,
                'email' => $input['email'],
                'mobile' => isset($input['mobile']) ? trim($input['mobile']) : $auth->mobile,
                'age' => isset($input['age']) ? $input['age'] : $auth->age,
                'image' => isset($input['image']) ? $input['image'] : $auth->image,
                'file' => $input['file'],
                'detail' => isset($input['detail']) ? $input['detail'] : $auth->detail,
                'gender' => isset($input['gender']) ? $input['gender'] : $auth->gender,
                'status' => '0',
            ]);

            return response()->json(array('instructor' => $instructor), 200);
        }
    }


    public function aboutus(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $about = About::all()->toArray();
        return response()->json(array('about' => $about), 200);
    }


    public function contactus(Request $request)
    {

        $request->validate([
            'fname' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'message' => 'required',
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $created_contact = Contact::create(
            [
                'fname' => $request->fname,
                'email' => $request->email,
                'mobile' => trim($request->mobile),
                'message' => $request->message,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );

        return response()->json(array('contact' => $created_contact), 200);
    }


    public function courseprogress(Request $request)
    {

        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    return $query->where('status', '1')
                        ->where('end_date', '>=', date('Y-m-d'));
                })
            ],
        ]);

        $auth = Auth::guard('api')->user();

        $course = Course::active()->where('id', $request->course_id)->first();

        $progress = CourseProgress::where('course_id', $course->id)->where('user_id', $auth->id)->activeProgress()->first();

        return response()->json(array('progress' => $progress), 200);
    }


    public function courseprogressupdate(Request $request)
    {

        $request->validate([
            'class_id' => [
                'required',
                Rule::exists('course_classes', 'id')->where(function ($query) {
                    return $query->where('status', '1')
                        ->whereNull('deleted_at');
                })
            ],
        ], [
            'class_id.required' => __("Class not selected"),
            'class_id.exists' => __("Class not exist OR may have been disabled"),
        ]);

        $class = CourseClass::find($request->class_id);

        $auth = Auth::guard('api')->user();

        $course = Course::find($class->course_id);
        $progress = CourseProgress::where('course_id', $course->id)->where('user_id', $auth->id)->activeProgress()->first();

        if (isset($progress)) {
            $course_return = $progress->mark_chapter_id;
            if (!in_array($request->class_id, $course_return)) {
                array_push($course_return, $request->class_id);
            }

            $read_count = 0;
            $chapters = CourseClass::select('id', 'status')->where('course_id', $course->id)->get();
            $total_count = count($chapters->where('status', '1'));

            foreach ($course_return as $read_lesson) {
                $lesson = CourseClass::where('status', '1')->find($read_lesson);
                if ($lesson) {
                    $read_count++;
                }
            }

            // $read_count = count($course_return);
            $progres = ($read_count / $total_count) * 100;

            CourseProgress::where('course_id', $course->id)->where('user_id', '=', $auth->id)
                ->update([
                    'progress' => $progres,
                    'mark_chapter_id' => $course_return,
                    'all_chapter_id' => $chapters->pluck('id'),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                ]);

            return response()->json('Updated sucessfully !', 200);

        } else {
            $chapter = CourseClass::select('id', 'status')->where('course_id', $course->id)->get();
            $total_count = count($chapter->where('status', '1'));
            $read_count = count([$request->class_id]);
            $progres = ($read_count / $total_count) * 100;
            $created_progress = CourseProgress::create([
                'course_id' => $course->id,
                'user_id' => $auth->id,
                'progress' => $progres,
                'mark_chapter_id' => [$request->class_id],
                'all_chapter_id' => $chapter->pluck('id'),
                'status' => '1'
            ]);

            return response()->json(array('created_progress' => $created_progress), 200);
        }
    }


    public function terms(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $terms_policy = Terms::first();

        return response()->json(array('terms' => $terms_policy->terms), 200);
    }


    public function policy(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $terms_policy = Terms::first();

        return response()->json(array('policy' => $terms_policy->policy), 200);
    }


    public function about_us(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $terms_policy = Terms::first();

        return response()->json(array('about_us' => $terms_policy->about_us), 200);
    }


    public function career(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $career = Career::get()->toArray();

        return response()->json(array('career' => $career), 200);
    }


    public function zoom(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $meeting = Meeting::get()->toArray();

        return response()->json(array('meeting' => $meeting), 200);
    }


    public function bigblue(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $bigblue = BBL::get()->toArray();

        return response()->json(array('bigblue' => $bigblue), 200);
    }


    public function coursereport(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
            'course_id' => 'required',
            'title' => 'required',
            'email' => 'required',
            'detail' => 'required',
        ]);

        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        $created_report = CourseReport::create(
            [
                'course_id' => $course->id,
                'user_id' => $auth->id,
                'title' => $course->title,
                'email' => $auth->email,
                'detail' => $request->detail,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );
        return response()->json(array('message' => 'Course reported!', 'status' => 'success'), 200);
    }


    public function coursecontent(Request $request, $id)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $result = Course::where('id', '=', $id)->where('status', 1)->first();

        if (!$result) {
            return response()->json(array("errors" => ["message" => ['404 | Course not found !']]), 404);
        }

        $order = Order::where('course_id', $result->id)->activeOrder()->get();

        $chapters = CourseChapter::where('course_id', $result->id)
            ->where('status', 1)
            ->with('courseclass')
            ->get();

        $classes = CourseClass::where('course_id', $result->id)->where('status', 1)->get();

        $overview[] = array(
            'course_title' => $result->title,
            'short_detail' => strip_tags($result->short_detail),
            'detail' => strip_tags($result->detail),
            'instructor' => $result->user->fname,
            'instructor_email' => $result->user->email,
            'instructor_detail' => strip_tags($result->user->detail),
            'user_enrolled' => count($order),
            'classes' => count($classes),
        );

        $quiz = array();

        if (isset($result->quiztopic)) {

            foreach ($result->quiztopic as $key => $topic) {

                $questions = [];

                if ($topic->type == null) {

                    foreach ($topic->quizquestion as $key => $data) {

                        if ($data->type == null) {

                            if ($data->answer == 'A') {

                                $correct_answer = $data->a;

                                $options = [
                                    $data->b,
                                    $data->c,
                                    $data->d,
                                ];
                            } elseif ($data->answer == 'B') {
                                $correct_answer = $data->b;

                                $options = [
                                    $data->a,
                                    $data->c,
                                    $data->d,
                                ];
                            } elseif ($data->answer == 'C') {
                                $correct_answer = $data->c;

                                $options = [
                                    $data->a,
                                    $data->b,
                                    $data->d,
                                ];
                            } elseif ($data->answer == 'D') {

                                $correct_answer = $data->d;

                                $options = [
                                    $data->a,
                                    $data->b,
                                    $data->c,
                                ];
                            }
                        }

                        $all_options = [
                            'A' => $data->a,
                            'B' => $data->b,
                            'C' => $data->c,
                            'D' => $data->d,
                        ];

                        $questions[] = [
                            'id' => $data->id,
                            'course' => $result->title,
                            'topic' => $topic->title,
                            'question' => $data->question,
                            'correct' => $correct_answer,
                            'status' => $data->status,
                            'incorrect_answers' => $options,
                            'all_answers' => $all_options,
                            'correct_answer' => $data->answer,
                        ];
                    }
                } elseif ($topic->type == 1) {

                    foreach ($topic->quizquestion as $key => $data) {

                        $questions[] = [
                            'id' => $data->id,
                            'course' => $result->title,
                            'topic' => $topic->title,
                            'question' => $data->question,
                            'status' => $data->status,
                            'correct' => null,
                            'correct' => null,
                            'status' => $data->status,
                            'incorrect_answers' => null,
                            'correct_answer' => null,
                        ];
                    }
                }

                $startDate = '0';

                if (Auth::guard('api')->check()) {

                    $order = Order::where('course_id', $id)->where('user_id', '=', Auth::guard('api')->user()->id)->activeOrder()->first();

                    $days = $topic->due_days;
                    $orderDate = optional($order)['created_at'];

                    $bundle = Order::where('user_id', Auth::guard('api')->user()->id)->where('bundle_id', '!=', null)->activeOrder()->get();

                    $course_id = array();

                    foreach ($bundle as $b) {
                        $bundle = BundleCourse::where('id', $b->bundle_id)->first();
                        array_push($course_id, $bundle->course_id);
                    }

                    $course_id = array_values(array_filter($course_id));
                    $course_id = array_flatten($course_id);

                    if ($orderDate != null) {
                        $startDate = date("Y-m-d", strtotime("$orderDate +$days days"));
                    } elseif (isset($course_id) && in_array($id, $course_id)) {
                        $startDate = date("Y-m-d", strtotime("$bundle->created_at +$days days"));
                    } else {
                        $startDate = '0';
                    }
                }

                $mytime = \Carbon\Carbon::now()->toDateString();

                $quiz[] = array(
                    'id' => $topic->id,
                    'course_id' => $result->id,
                    'course' => $result->title,
                    'title' => $topic->title,
                    'description' => $topic->description,
                    'per_question_mark' => $topic->per_q_mark,
                    'status' => $topic->status,
                    'quiz_again' => $topic->quiz_again,
                    'due_days' => $topic->due_days,
                    'type' => $topic->type,
                    'created_by' => $topic->created_at,
                    'updated_by' => $topic->updated_at,
                    'quiz_live_days' => $startDate,
                    'today_date' => $mytime,
                    'questions' => $questions,
                );
            }
        }

        $announcement = Announcement::where('course_id', $id)->where('status', 1)->get();

        $announcements = array();

        foreach ($announcement as $announc) {

            $announcements[] = array(
                'id' => $announc->id,
                'user' => $announc->user->fname,
                'course_id' => $announc->courses->title,
                'detail' => strip_tags($announc->announsment),
                'status' => $announc->status,
                'created_at' => $announc->created_at,
                'updated_at' => $announc->updated_at,
            );
        }

        $assign = array();

        if (Auth::guard('api')->check()) {

            $user = Auth::guard('api')->user();

            $assignments = Assignment::where('course_id', $id)->where('user_id', Auth::guard('api')->user()->id)->get();

            foreach ($assignments as $assignment) {

                $assign[] = array(
                    'id' => $assignment->id,
                    'user' => $assignment->user->fname,
                    'course_id' => $assignment->courses->title,
                    'instructor' => $assignment->instructor->fname,
                    'chapter_id' => $assignment->chapter['chapter_name'],
                    'title' => $assignment->title,
                    'assignment' => $assignment->assignment,
                    'assignment_path' => url('files/assignment/' . $assignment->assignment),
                    'type' => $assignment->type,
                    'detail' => strip_tags($assignment->detail),
                    'rating' => $assignment->rating,
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                );
            }
        }

        $appointments = Appointment::where('course_id', $id)->get();

        $appointment = array();

        foreach ($appointments as $appoint) {

            $appointment[] = array(
                'id' => $appoint->id,
                'user' => $appoint->user->fname,
                'course_id' => $appoint->courses->title,
                'instructor' => $appoint->instructor->fname,
                'title' => $appoint->title,
                'detail' => strip_tags($appoint->detail),
                'accept' => $appoint->accept,
                'reply' => $appoint->reply,
                'status' => $appoint->status,
                'created_at' => $appoint->created_at,
                'updated_at' => $appoint->updated_at,
            );
        }

        $questions = Question::where('course_id', $id)->get();

        $question = array();

        foreach ($questions as $ques) {

            $answer = [];
            foreach ($ques->answers as $key => $data) {

                $answer[] = [
                    'course' => $data->courses->title,
                    'user' => $data->user->fname,
                    'instructor' => $data->instructor->fname,
                    'image' => $ques->instructor->user_img,
                    'imagepath' => url('images/user_img/' . $ques->user->user_img),
                    'question' => $data->question->question,
                    'answer' => strip_tags($data->answer),
                    'status' => $data->status,
                ];
            }

            $question[] = array(
                'id' => $ques->id,
                'user' => $ques->user->fname,
                'instructor' => $ques->instructor->fname,
                'image' => $ques->instructor->user_img,
                'imagepath' => url('images/user_img/' . $ques->user->user_img),
                'course' => $ques->courses->title,
                'title' => strip_tags($ques->question),
                'status' => $ques->status,
                'created_at' => $ques->created_at,
                'updated_at' => $ques->updated_at,
                'answer' => $answer,
            );
        }

        $zoom_meeting = Meeting::where('course_id', '=', $id)->get();
        $bigblue_meeting = BBL::where('course_id', '=', $id)->get();
        $google_meet = Googlemeet::where('course_id', '=', $id)->get();
        $jitsi_meeting = JitsiMeeting::where('course_id', '=', $id)->get();

        $previouspapers = PreviousPaper::where('course_id', '=', $id)->get();

        $papers = array();

        foreach ($previouspapers as $data) {

            $papers[] = array(
                'id' => $data->id,
                'course' => $data->courses->title,
                'title' => $data->title,
                'file' => $data->file,
                'filepath' => url('files/papers/' . $data->file),
                'detail' => strip_tags($data->detail),
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            );
        }

        return response()->json(array('overview' => $overview, 'quiz' => $quiz, 'announcement' => $announcements, 'assignment' => $assign, 'questions' => $question, 'appointment' => $appointment, 'chapter' => $chapters, 'zoom_meeting' => $zoom_meeting, 'bigblue_meeting' => $bigblue_meeting, 'jitsi_meeting' => $jitsi_meeting, 'google_meet' => $google_meet, 'papers' => $papers), 200);
    }


    public function assignment(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
            'course_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required',
            'file' => 'required',
        ]);

        $auth = Auth::guard('api')->user();
        $course = Course::where('id', $request->course_id)->first();
        if ($file = $request->file('file')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('files/assignment', $name);
            $input['assignment'] = $name;
        }
        $assignment = Assignment::create(
            [
                'user_id' => $auth->id,
                'instructor_id' => $course->user_id,
                'course_id' => $course->id,
                'chapter_id' => $request->chapter_id,
                'title' => $request->title,
                'assignment' => $name,
                'type' => 0,
            ]
        );

        return response()->json(array('message' => 'Assignment submitted successfully', 'status' => "success"), 200);
    }


    public function appointment(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
            'course_id' => 'required',
            'title' => 'required',
        ]);

        $auth = Auth::guard('api')->user();

        $course = Course::where('id', $request->course_id)->first();

        $appointment = Appointment::create(
            [
                'user_id' => $auth->id,
                'instructor_id' => $course->user_id,
                'course_id' => $course->id,
                'title' => $request->title,
                'detail' => $request->detail,
                'accept' => '0',
                'start_time' => \Carbon\Carbon::now()->toDateTimeString(),
            ]
        );

        $users = User::where('id', $course->user_id)->first();

        if ($appointment) {
            if (env('MAIL_USERNAME') != null) {
                try {

                    /* sending email */
                    $x = 'You get Appointment Request';
                    $request = $appointment;
                    Mail::to($users->email)->send(new UserAppointment($x, $request));
                } catch (\Swift_TransportException $e) {
                    return back()->with('success', trans('flash.RequestMailError'));
                }
            }
        }

        return response()->json(array('appointment' => $appointment), 200);
    }


    public function appointmentdelete(Request $request, $id)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        Appointment::where('id', $id)->delete();

        return response()->json('Deleted Successfully !', 200);
    }


    // it returns quiz report as well
    public function quiz(Request $request, $id)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ], [
            'secret.required' => __("secret key is missing"),
            'secret.exists' => __("secret key is invalid"),
        ]);

        $auth = Auth::guard('api')->user();
        $quiz = QuizTopic::findOrFail($id);
        if ($quiz) {
            $questions = Quiz::where('topic_id', $id)->count();
            $last_attempt = QuizAnswer::where('topic_id', $id)->where('user_id', $auth->id)->orderBy('attempt', 'desc')->first();
            $grade = NULL;
            $mark = null;
            $fullyMarked = true;
            if ($last_attempt) {

                $answer = QuizAnswer::where('topic_id', $id)->where('user_id', $auth->id)->where('attempt', $last_attempt->attempt)->get();
                $mark = 0;

                foreach ($answer as $ans) {

                    if (is_null($ans->grade) && is_null($ans->type)) {
                        $mark += (strtolower($ans->answer) == strtolower($ans->user_answer)) ? 1 : 0;
                    } elseif (is_null($ans->grade) && !is_null($ans->type)) {
                        $fullyMarked = false;
                    } else {
                        $mark += $ans->grade;
                    }

                }
                $grade = round(($mark / $questions) * 100, 2);
            }

            $remark = Remark::where('topic_id', $id)->where('student_id', $auth->id)->first();


            $data = [
                'id' => $quiz->id,
                'course_id' => $quiz->course_id,
                'title' => $quiz->title,
                'passing_percent_age' => $quiz->p_percent . '%',
                'description' => $quiz->description,
                'questions' => $questions,
                'per_quiz_mark' => $quiz->per_q_mark,
                'total_marks' => ($quiz->per_q_mark * $questions),
                'timer' => $quiz->timer,
                'reattempt' => $quiz->quiz_again ? true : false,
                'grade' => $grade,
                'earned_marks' => $grade ? ($mark * $quiz->per_q_mark) : null,
                'fullyMarked' => $fullyMarked,
                'remark' => $remark
            ];


            return response()->json($data, 200);

            // else{
            //     return response()->json(['error'=>["msg"=>['not attempt already']]],400);
            // }
        } else {
            return response()->json(['error' => ["msg" => ['Quiz not exist']]], 400);
        }

    }


    public function quizstart($id)
    {

        $auth = Auth::guard('api')->user();

        $topic = QuizTopic::find($id);

        if ($topic) {
            $que = Quiz::where('topic_id', $topic->id)->get();
            $que_count = Quiz::where('topic_id', $topic->id)->count();
            $questions = [];
            foreach ($que as $q) {

                $questions[] = [
                    "id" => $q->id,
                    "question" => $q->question,
                    "question_video_link" => $q->question_video_link,
                    "question_img" => $q->question_img,
                    "a" => $q->a,
                    "b" => $q->b,
                    "c" => $q->c,
                    "d" => $q->d,
                    'type' => $q->type,
                    'audio' => $q->audio,
                    'is_image' => $q->is_image,
                ];
            }

            $data = [
                'id' => $topic->id,
                'course_id' => $topic->course_id,
                'topic_id' => $topic->id,
                'timer' => $topic->timer,
                'passing_percent_age' => $topic->p_percent . '%',
                'total_questions' => count($questions ?? []),
                'per_quiz_mark' => $topic->per_q_mark,
                'total_marks' => ($topic->per_q_mark * count($questions ?? [])),
                'title' => $topic->title,
                'is_complete' => $topic->userquizanswer($auth->id) ? true : false,
                'quiz_again' => $topic->quiz_again ? true : false,
                'questions' => $questions,
            ];

            return response()->json($data, 200);

        } else {
            return response()->json(array("errors" => ["message" => [__("Quiz not exist anynmore")]]), 400);
        }
    }


    public function quizsubmit(Request $request)
    {
        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    return $query->where('status', '1')
                        ->where('end_date', '>=', date('Y-m-d'));
                })
            ],
            'topic_id' => 'required|exists:quiz_topics,id',
            'question_id' => 'required|array',
            'answer' => 'required|array'
        ], [
            'course_id.required' => __("course not selected"),
            "course_id.exists" => __("course not found"),
            'topic_id.required' => __("Topic not selected"),
            "topic_id.exists" => __("Topic not found"),
            'question_id.required' => __("Questions not found for submit answer"),
            "question_id.array" => __("Questions must be in array"),
            'answer.required' => __("Answer can not be empty"),
            "answer.array" => __("Answer must be in array"),
        ]);

        $auth = Auth::guard('api')->user();
        $course = Course::find($request->course_id);
        $topics = QuizTopic::find($request->topic_id);
        $unique_question = array_unique($request->question_id);
        $quiz_already = QuizAnswer::where('user_id', $auth->id)->where('topic_id', $topics->id)->orderBy('id', 'desc')->first();
        $mark = 0;
        $correct = 0;
        $total = $topics->quizquestion->count() * $topics->per_q_mark;
        $result = [];
        if ($topics->type == null) {
            if ($quiz_already == null || $topics->quiz_again) {

                for ($i = 0; $i < count($request->answer); $i++) {

                    $q = Quiz::find($unique_question[$i]);

                    $grade = null;

                    if ($q->type == 'mcq' || $q->type == 'image' || $q->type == null) {
                        $grade = strtolower($request->answer[$i]) == strtolower($q->answer) ? 1 : 0;
                    }

                    $answers[] = [
                        'user_id' => Auth::guard('api')->user()->id,
                        'user_answer' => strtolower($request->answer[$i]),
                        'question_id' => $q->id,
                        'course_id' => $topics->course_id,
                        'topic_id' => $topics->id,
                        'attempt' => ($quiz_already && $quiz_already->attempt) ? ($quiz_already->attempt + 1) : 1,
                        'answer' => strtolower($q->answer),
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'type' => $q->type,
                        'grade' => $grade
                    ];

                    $mark += strtolower($request->answer[$i]) == strtolower($q->answer) ? 1 : 0;

                    $result[] = [
                        'question_id' => $q->id,
                        'correct' => strtolower($request->answer[$i]) == strtolower($q->answer) ? true : false,
                        'grade' => $grade
                    ];
                }
                $correct = $mark * $topics->per_q_mark;

                QuizAnswer::insert($answers);
            }
        }
        // elseif ($topics->type == 1) {
        //     if ($quiz_already == null) {
        //         for ($i = 0; $i < count($request->txt_answer); $i++) {
        //             $already_answer = QuizAnswer::
        //                     where('question_id', $unique_question[$i])
        //                     ->where('topic_id', $topics->id)
        //                     ->where('user_id', Auth::guard('api')->user()->id)
        //                     ->first();
        //             if (!isset($already_answer)) {
        //                 $q = Quiz::find($unique_question[$i]);
        //                 $answers[] = [
        //                     'user_id' => Auth::guard('api')->user()->id,
        //                     'question_id' => $q->id,
        //                     'course_id' => $topics->course_id,
        //                     'topic_id' => $topics->id,
        //                     'txt_answer' => $request->txt_answer[$i],
        //                     'type' => '1',
        //                     'txt_approved' => '0',
        //                     'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
        //                 ];
        //             }
        //         }
        //         QuizAnswer::insert($answers);
        //     }
        // }
        return response()->json(
            array(
                'message' => 'Quiz Submitted',
                'status' => 'success',
                'result' => count($result) ? $result : null,
                'total_marks' => $total,
                'passing_percent_age' => $topics->p_percent . '%',
                'quiz_again' => $topics->quiz_again ? true : false,
                'earned_marks' => count($result) ? $correct : null,
                'grade_in_percent' => count($result) ? round((($correct / $total) * 100), 2) . '%' : null
            ),
            200
        );
    }

    public function userreview(Request $request)
    {

        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($query) {
                    return $query->where('status', '1')
                        ->where('end_date', '>=', date('Y-m-d'));
                })
            ],
            'learn' => 'required|min:1|max:5',
            'price' => 'required|min:1|max:5',
            'value' => 'required|min:1|max:5',
            'review' => 'required|min:1|max:300',
        ], [
            "course_id.required" => __("course not found"),
            "course_id.exists" => __("Course not exist OR may heve been disabled"),
            "learn.required" => __("select all rating types minimum 1"),
            "learn.min" => __("minimum rating can be one"),
            "learn.max" => __("maximum rating can be five"),
            "price.required" => __("select all rating types minimum 1"),
            "price.min" => __("minimum rating can be one"),
            "price.max" => __("maximum rating can be five"),
            "value.required" => __("select all rating types minimum 1"),
            "value.min" => __("minimum rating can be one"),
            "value.max" => __("maximum rating can be five"),
            "review.required" => __("add your reviews as text please"),
            "review.min" => __("minimum review length can be 1 digit"),
            "review.max" => __("maximum review length can be 300"),
        ]);


        $auth = Auth::guard('api')->user();

        $f_order = [];
        $orders = Order::where('user_id', $auth->id)->activeOrder()->get();
        $f_order = $orders->where('course_id', $request->course_id);
        if ($orders && count($f_order) < 1) {
            foreach ($orders as $o) {
                $f = in_array($request->course_id, $o->bundle_course_id ?? []) ? $o : null;
                if ($f) {
                    $f_order[] = $o;
                }
            }
        }

        $course = Course::find($request->course_id);
        $review = ReviewRating::where('user_id', Auth::guard('api')->User()->id)->where('course_id', $course->id)->first();

        foreach ($course->chapter as $chapter) {
            $chapter_order = $orders->where('chapter_id', $chapter->id)->toArray();
            if ($chapter_order) {
                break;
            }
        }

        if (count($f_order) || $chapter_order) {
            if (!empty($review)) {
                return response()->json(array("errors" => ["message" => ['Already Reviewed !']]), 422);
            } else {

                $input = $request->all();

                $review = ReviewRating::create([
                    'user_id' => $auth->id,
                    'course_id' => $input['course_id'],
                    'learn' => $input['learn'],
                    'price' => $input['price'],
                    'value' => $input['value'],
                    'avg_rating' => ((($input['learn'] ?? 0) + ($input['value'] ?? 0) + ($input['price'] ?? 0)) / 3),
                    'review' => $input['review'],
                    'approved' => '1',
                    'status' => '1',
                ]);

                return response()->json($review, 200);
            }
        } else {

            return response()->json(array("errors" => ["message" => ['Please Purchase course !']]), 422);
        }
    }


    public function paginationcourse(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $paginator = Course::where('status', 1)->with('include')->with('whatlearns')->with('review')->paginate(5);

        $paginator->getCollection()->transform(function ($c) use ($paginator) {

            $c['in_wishlist'] = Is_wishlist::in_wishlist($c->id);
            return $c;
        });

        return response()->json(array('course' => $paginator), 200);
    }


    public function categoryPage(Request $request, $id, $name)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $category = Categories::where('status', '1')->where('id', $id)->first();

        if (!$category) {
            return response()->json(['Invalid Category !']);
        }

        $subcategory = $category->subcategory()->where('status', 1)->get();

        if ($request->type) {

            $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? '1' : '0')->paginate($request->limit ?? 10);
        } else if ($request->sortby) {

            if ($request->sortby == 'l-h') {

                $course = $category->courses()->where('status', '1')->where('type', '=', '1')->orderBy('price', 'DESC')->paginate($request->limit ?? 10);
            }

            if ($request->sortby == 'h-l') {

                $course = $category->courses()->where('status', '1')->where('type', '=', '1')->orderBy('price', 'ASC')->paginate($request->limit ?? 10);
            }

            if ($request->sortby == 'a-z') {

                if ($request->type) {
                    $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('title', 'ASC')->paginate($request->limit ?? 10);
                } else {

                    $course = $category->courses()->where('status', '1')->orderBy('title', 'ASC')->paginate($request->limit ?? 10);
                }
            }

            if ($request->sortby == 'z-a') {

                if ($request->type) {
                    $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('title', 'DESC')->paginate($request->limit ?? 10);
                } else {

                    $course = $category->courses()->where('status', '1')->orderBy('title', 'DESC')->paginate($request->limit ?? 10);
                }
            }

            if ($request->sortby == 'newest') {

                if ($request->type) {

                    $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('created_at', 'DESC')->paginate($request->limit ?? 10);
                } else {

                    $course = $category->courses()->where('status', '1')->orderBy('created_at', 'DESC')->paginate($request->limit ?? 10);
                }
            }

            if ($request->sortby == 'featured') {

                if ($request->type) {
                    $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->where('featured', '=', '1')->paginate($request->limit ?? 10);
                } else {

                    $course = $category->courses()->where('status', '1')->where('featured', '=', '1')->paginate($request->limit ?? 10);
                }
            } else if ($request->limit) {

                $course = $category->courses()->where('status', '1')->paginate($request->limit ?? 10);
            }
        } else {
            $course = Course::where('status', 1)->where('category_id', $category->id)->paginate($request->limit ?? 10);
        }

        $result = array(
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
            'subcategory' => $subcategory,
            'course' => $course,
        );
        return response()->json($result, 200);
    }


    public function subcategoryPage(Request $request, $id, $name)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $category = SubCategory::where('id', $id)->first();
        if (!$category) {
            return response()->json(['Invalid Category !']);
        }
        $subcategory = ChildCategory::where('status', 1)->where('subcategory_id', $category->id)->get();
        if ($request->type) {
            $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? '1' : '0')->paginate($request->limit ?? 10);
        } else if ($request->sortby) {
            if ($request->sortby == 'l-h') {
                $courses = $cats->courses()->where('status', '1')->where('type', '=', '1')->orderBy('price', 'DESC')->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'h-l') {
                $courses = $cats->courses()->where('status', '1')->where('type', '=', '1')->orderBy('price', 'ASC')->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'a-z') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('title', 'ASC')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->orderBy('title', 'ASC')->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'z-a') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('title', 'DESC')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->orderBy('title', 'DESC')->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'newest') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('created_at', 'DESC')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->orderBy('created_at', 'DESC')->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'featured') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->where('featured', '=', '1')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->where('featured', '=', '1')->paginate($request->limit ?? 10);
                }
            } else if ($request->limit) {
                $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);
            }
        } else {
            $course = Course::where('status', 1)->where('category_id', $category->id)->paginate($request->limit ?? 10);
        }
        $result = array(
            'id' => $category->id,
            'title' => $category->title,
            'icon' => $category->icon,
            'slug' => $category->slug,
            'status' => $category->status,
            'image' => Avatar::create($category->title),
            'position' => $category->position,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'childcategory' => $subcategory,
            'course' => $course,
        );
        return response()->json($result, 200);
    }


    public function childcategoryPage(Request $request, $id, $name)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $category = ChildCategory::where('id', $id)->first();
        if (!$category) {
            return response()->json(['Invalid Category !']);
        }
        if ($request->type) {
            $course = $category->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? '1' : '0')->paginate($request->limit ?? 10);
        } else if ($request->sortby) {
            if ($request->sortby == 'l-h') {
                $courses = $cats->courses()->where('status', '1')->where('type', '=', '1')->orderBy('price', 'DESC')->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'h-l') {
                $courses = $cats->courses()->where('status', '1')->where('type', '=', '1')->orderBy('price', 'ASC')->paginate($request->limit ?? 10);
            }
            if ($request->sortby == 'a-z') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('title', 'ASC')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->orderBy('title', 'ASC')->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'z-a') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('title', 'DESC')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->orderBy('title', 'DESC')->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'newest') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->orderBy('created_at', 'DESC')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->orderBy('created_at', 'DESC')->paginate($request->limit ?? 10);
                }
            }
            if ($request->sortby == 'featured') {
                if ($request->type) {
                    $courses = $cats->courses()->where('status', '1')->where('type', '=', $request->type == 'paid' ? 1 : 0)->where('featured', '=', '1')->paginate($request->limit ?? 10);
                } else {
                    $courses = $cats->courses()->where('status', '1')->where('featured', '=', '1')->paginate($request->limit ?? 10);
                }
            } else if ($request->limit) {
                $courses = $cats->courses()->where('status', '1')->paginate($request->limit ?? 10);
            }
        } else {
            $course = Course::where('status', 1)->where('category_id', $category->id)->paginate($request->limit ?? 10);
        }
        $result = array(
            'id' => $category->id,
            'title' => $category->title,
            'icon' => $category->icon,
            'slug' => $category->slug,
            'status' => $category->status,
            'featured' => $category->featured,
            'image' => Avatar::create($category->title),
            'position' => $category->position,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'course' => $course,
        );
        return response()->json($result, 200);
    }


    public function deleteAssignment(Request $request)
    {
        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
            'assignment_id' => 'required',
        ]);

        $user = Auth::guard('api')->user();

        Assignment::where('id', $request->assignment_id)->where('user_id', $user->id)->delete();

        return response()->json(array('watchlist' => $watch), 200);
    }


    public function requestCheck(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $user = Auth::guard('api')->user();

        $alreadyRequest = Instructor::where('user_id', Auth::guard('api')->user()->id)->first();

        if ($alreadyRequest != null) {

            return response()->json([
                "message" => "Already Requested",
            ]);
        }

        return response()->json([
            "message" => "Please Request to became an instructor",
        ]);
    }


    public function cancelRequest(Request $request)
    {

        $request->validate([
            'secret' => 'required|exists:api_keys,secret_key',
        ]);

        $user = Auth::guard('api')->user();

        if (Instructor::where('user_id', $user->id)->exists()) {
            $instructor = Instructor::where('user_id', $user->id)->first();
            $instructor->delete();

            return response()->json([
                "message" => "records deleted",
            ]);
        } else {
            return response()->json([
                "message" => "Instructor not found",
            ], 404);
        }
    }


    public function watchcourse($id)
    {
        if (Auth::guard('api')->check()) {

            $order = Order::where('status', '1')->where('user_id', Auth::guard('api')->User()->id)->where('course_id', $id)->where('status', '1')->first();

            $courses = Course::where('id', $id)->first();

            $bundle = Order::where('user_id', Auth::guard('api')->User()->id)->where('bundle_id', '!=', null)->where('status', '1')->get();

            $gsetting = Setting::first();

            //attandance start
            if (!empty($order)) {
                if ($gsetting->attandance_enable == 1) {

                    $date = Carbon::now();
                    //Get date
                    $date->toDateString();

                    $courseAttandance = Attandance::where('course_id', '=', $id)->where('user_id', Auth::guard('api')->User()->id)->where('date', '=', $date->toDateString())->first();

                    if (!$courseAttandance) {
                        $attanded = Attandance::create(
                            [
                                'user_id' => Auth::guard('api')->user()->id,
                                'course_id' => $id,
                                'instructor_id' => $courses->user_id,
                                'date' => $date->toDateString(),
                                'order_id' => $id,
                                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                            ]
                        );
                    }
                }
            } //attandance end

            $course = Course::findOrFail($id);

            $course_id = array();

            foreach ($bundle as $b) {
                $bundle = BundleCourse::where('id', $b->bundle_id)->first();
                array_push($course_id, $bundle->course_id);
            }

            $course_id = array_values(array_filter($course_id));

            $course_id = array_flatten($course_id);

            if (Auth::guard('api')->User()->role == "admin") {
                return view('watch', compact('courses'));
            } elseif (Auth::guard('api')->User()->id == $course->user_id) {
                return view('watch', compact('courses'));
            } else {
                if (!empty($order)) {

                    $coursewatch = WatchCourse::where('course_id', '=', $id)->where('user_id', Auth::guard('api')->User()->id)->first();

                    if ($gsetting->device_control == 1) {

                        if (!$coursewatch) {

                            $watching = WatchCourse::create(
                                [
                                    'user_id' => Auth::guard('api')->user()->id,
                                    'course_id' => $id,
                                    'start_time' => \Carbon\Carbon::now()->toDateTimeString(),
                                    'active' => '1',
                                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
                                ]
                            );

                            return view('watch', compact('courses'));
                        } else {

                            if ($coursewatch->active == 0) {

                                $coursewatch->active = 1;
                                $coursewatch->save();
                                return view('watch', compact('courses'));
                            } else {

                                return response()->json(array('message' => 'User Already Watching Course !!', 'status' => 'fail'), 402);
                            }
                        }
                    } else {
                        return view('watch', compact('courses'));
                    }
                } elseif (isset($course_id) && in_array($id, $course_id)) {
                    return view('watch', compact('courses'));
                } else {
                    return response()->json(array('message' => 'Unauthorization Action', 'status' => 'fail'), 402);
                }
            }
        }
        return response()->json(array('message' => 'Please Login to Continue', 'status' => 'fail'), 401);
    }


    public function reviewlike(Request $request, $id)
    {

        $user = Auth::user();

        $help = ReviewHelpful::where('review_id', $id)->where('user_id', $user->id)->first();

        if ($request->review_like == '1') {
            if (isset($help)) {

                ReviewHelpful::where('id', $help->id)
                    ->update([
                        'review_like' => '1',
                        'review_dislike' => '0',
                    ]);
            } else {

                $created_review = ReviewHelpful::create(
                    [
                        'course_id' => $request->course_id,
                        'user_id' => $user->id,
                        'review_id' => $id,
                        'helpful' => 'yes',
                        'review_like' => '1',
                    ]
                );

                ReviewHelpful::where('id', $created_review->id)
                    ->update([
                        'review_dislike' => '0',
                    ]);
            }
        } elseif ($request->review_dislike == '1') {

            if (isset($help)) {

                ReviewHelpful::where('id', $help->id)
                    ->update([
                        'review_dislike' => '1',
                        'review_like' => '0',
                    ]);
            } else {

                $created_review = ReviewHelpful::create(
                    [
                        'course_id' => $request->course_id,
                        'user_id' => $user->id,
                        'review_id' => $id,
                        'helpful' => 'yes',
                        'review_dislike' => '1',
                    ]
                );

                ReviewHelpful::where('id', $created_review->id)
                    ->update([
                        'review_like' => '0',
                    ]);
            }
        } elseif ($help->review_like == '1') {
            ReviewHelpful::where('id', $help->id)
                ->update([
                    'review_like' => '0',
                ]);
        } elseif ($help->review_dislike == '1') {
            ReviewHelpful::where('id', $help->id)
                ->update([
                    'review_dislike' => '0',
                ]);
        }

        return response()->json(array('message' => 'Updated Successfully', 'status' => 'success'), 200);
    }


    public function getcategoryCourse($catid)
    {

        $cat = Categories::whereHas('courses')
            ->whereHas('courses.user')
            ->where('status', '1')
            ->with(['courses.instructor'])
            ->find($catid);

        if (isset($cat)) {
            foreach ($cat->courses as $course) {

                $category_slider_courses[] = array(
                    'id' => $course->id,
                    'title' => $course->title,
                    'level_tags' => $course->level_tags,
                    'short_detail' => $course->short_detail,
                    'price' => $course->price,
                    'discount_price' => $course->discount_price,
                    'featured' => $course->featured,
                    'status' => $course->status,
                    'preview_image' => $course->preview_image,
                    'total_rating_percent' => course_rating($course->id)->getData()->total_rating_percent,
                    'total_rating' => course_rating($course->id)->getData()->total_rating,
                    'imagepath' => url('images/course/' . $course->preview_image),
                    'in_wishlist' => Is_wishlist::in_wishlist($course->id),
                    'instructor' => array(
                        'id' => $course->user->id,
                        'name' => $course->user->fname . ' ' . $course->user->lname,
                        'image' => url('/images/user_img/' . $course->user->user_img),
                    ),
                );
            }

            $category_slider1['course'] = $category_slider_courses;

            return response()->json([
                'course' => $category_slider_courses,
            ]);
        } else {
            return response()->json([
                'course' => null,
                'msg' => 'No courses or category found !',
            ]);
        }
    }

    public function overdue()
    {
        $userId = Auth::user()->id;
        if (!$userId) {
            return [];
        }
        $ordersIds = Order::whereHas('installments_list', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with([
                    'payment_plan' => function ($query) {
                        $query
                            ->where('due_date', '<=', now()->addDays(2))
                            ->where('status', null);
                    }
                ])->select('id')->get();
        $ids = [];
        if ($ordersIds) {
            foreach ($ordersIds as $orderId) {
                $ids[] = $orderId->id;
            }
        }
        $orders = Order::whereIn('id', $ids)->whereHas('installments_list', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('enroll_expire', '>=', date('Y-m-d'))
        ->where(function ($q) {
            $q->whereHas('courses', function ($q) {
                $q->active();
            })
                ->OrWhereHas('bundle', function ($q) {
                    $q->active();
                });
        })
            ->with([
                'courses',
                'bundle',
                'payment_plan' => function ($query) {
                    $query
                        ->where('status', null);
                    // ->where('due_date', '<=', now()->addDays(2))
                }
            ])
            ->get();
        $response = [];
        $userPayInstallments = Cache::get($userId) ?? [];
        for ($i = 0; $i < count($orders); $i++) {
            $item = null;
            if (isset($orders[$i]->courses) && !empty($orders[$i]->courses->title)) {
                $item = $orders[$i]->courses;
            } elseif (isset($orders[$i]->bundle) && !empty($orders[$i]->bundle->title)) {
                $item = $orders[$i]->bundle;
            } else {
                continue;
            }
            $installments = [];
            foreach ($orders[$i]->payment_plan as $paymentPlan) {
                $installments[] = [
                    'id' => $paymentPlan->id,
                    'is_selected' => in_array($paymentPlan->id, $userPayInstallments) ? true : false,
                    'amount' => $paymentPlan->amount,
                    'due_date' => $paymentPlan->due_date,
                    'status' => $paymentPlan->status,
                    'installment_no' => $paymentPlan->installment_no,
                ];
            }
            $response[] = [
                'typeId' => $item->id,
                'name' => $item->title,
                'type' => isset($orders[$i]->courses) ? 'course' : 'bundle',
                'image' => url($item->preview_image),
                'installments' => $installments,
            ];
        }
        return response()->json($response);
    }
}
