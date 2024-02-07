<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\Api\VerificationController;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

// Route::get('/test/{userId}', 'Api\MainController@overdue');

// Route::get('/questionnaires/', [QuestionnaireController::class, 'index']);
// Route::post('/questionnaires', [QuestionnaireController::class, 'store']);
// Route::get('/questionnaires/{id}', [QuestionnaireController::class, 'show']);
// Route::put('/questionnaires/{id}', [QuestionnaireController::class, 'update']);
// Route::delete('/questionnaires/{id}', [QuestionnaireController::class, 'destroy']);
Route::post('/questionnaires/{id}/answer/public', [QuestionnaireController::class, 'answer']);
// Route::get('/questionnaires/user/all/public', [QuestionnaireController::class, 'getQuestionnairesForStudent']);
Route::get('questionnaires/{id}/edit', [QuestionnaireController::class, 'edit']);


// Route::get('attendee', function () {
//   $user = \App\User::find(321);
//   $code = $user->code;
//   $config = \App\Setting::findOrFail(1);
//   $data = ['code' => $code, 'logo' => $config->logo, 'company' => $config->project_title, 'from' => $config->wel_email];
//   Mail::to($user->email)->send(new \App\Mail\EmailVerficationOTP($user, $data));
// });

//Route::get('email/verify', 'Api\VerificationController@show')->name('verification.notice');
Route::post('email/verify/otp', 'Api\Auth\RegisterController@verifyotp');
Route::post('email/resend/otp', 'Api\Auth\RegisterController@resendotp');
Route::get('email/verify/{token}', 'Api\Auth\RegisterController@verifyemail');
//Route::get('email/verify/{id}/{hash}', 'Api\VerificationController@verifybyapi')->name('verification.verify');
//Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend');

/* HomeModule API */
Route::get('homemodules', 'Api\OtherApiController@homeModules');
Route::get('settings', 'Api\OtherApiController@setting');

// Route::get('email/verify/{id}/{hash}',  'Auth\VerificationController@verify')->name('verification.verifyemail');
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['basicAuth'])->prefix('webhooks')->group(function () {
  Route::post('/fpjs', 'Api\WebhookController@storefpjs');
});

Route::middleware(['ip_block', 'switch_languages_api'])->group(function () {
  Route::post('login', 'Api\Auth\LoginController@login');
  Route::post('fblogin', 'Api\Auth\LoginController@fblogin');
  Route::post('googlelogin', 'Api\Auth\LoginController@googlelogin');

  Route::get('social/login/{provider}', 'Api\Auth\LoginController@redirectToblizzard_sociallogin');
  Route::get('coupons', 'Api\CouponController@coupons');

  Route::post('social/login/{provider}/callback', 'Api\Auth\LoginController@blizzard_sociallogin');

  Route::post('register', 'Api\Auth\RegisterController@register')->name('api.register');
  Route::post('refresh', 'Api\Auth\LoginController@refresh');

  Route::post('forgotpassword', 'Api\Auth\LoginController@forgotApi');
  Route::post('verifycode', 'Api\Auth\LoginController@verifyApi');
  Route::post('resetpassword', 'Api\Auth\LoginController@resetApi');

  Route::get('institute/categories', 'Api\OtherApiController@getInstituteCategories');
  Route::get('major/categories', 'Api\OtherApiController@getMajorCategories');

  Route::post('home', 'Api\MainController@home');

  Route::post('instructors', 'Api\MainController@allinstructor');
  Route::post('course', 'Api\MainController@course');
  Route::post('bundle/courses', 'Api\MainController@bundle');
  Route::post('live/meetings', 'Api\OtherApiController@meetings');
  Route::post('in-person/sessions', 'Api\OtherApiController@inpersonsession');
  Route::get('meeting/detail/{id}', 'Api\OtherApiController@meetingdetail');
  Route::get('session/detail/{id}', 'Api\OtherApiController@sessiondetail');
  Route::get('course/paginate', 'Api\MainController@paginationcourse');

  Route::get('featuredcourse', 'Api\MainController@featuredcourse');
  Route::get('recent/course', 'Api\MainController@recentcourse');
  Route::get('discount/course', 'Api\MainController@discountcourses');

  Route::get('bundle/detail/{id}', 'Api\MainController@bundledetail');
  Route::get('bundle/courses/{id}', 'Api\MainController@bundleCourses');
  Route::get('user/faq', 'Api\MainController@studentfaq');
  Route::get('instructor/faq', 'Api\MainController@instructorfaq');

  Route::get('main', 'Api\MainController@main');

  Route::post('course/detail', 'Api\MainController@detailpage');
  Route::get('all/pages', 'Api\MainController@pages');
  Route::post('instructor/profile', 'Api\MainController@instructorprofile');
  Route::post('instructor/courses', 'Api\MainController@instructorCourses');
  Route::get('course/reviews', 'Api\MainController@review');
  Route::post('chapter/duration', 'Api\MainController@duration');

  Route::get('apikeys', 'Api\MainController@apikeys');
  Route::get('all/courses/detail', 'Api\MainController@coursedetail');
  Route::get('all/coupons', 'Api\MainController@showcoupon');

  Route::get('aboutus', 'Api\MainController@aboutus');

  Route::post('contactus', 'Api\MainController@contactus');

  Route::get('payment/apikeys', 'Api\PaymentController@apikeys');

  Route::get('blog', 'Api\MainController@blog');
  Route::post('blog/detail', 'Api\MainController@blogdetail');
  Route::get('recent/blog', 'Api\MainController@recentblog');

  Route::get('terms', 'Api\MainController@terms');
  Route::get('policy', 'Api\MainController@policy');
  Route::get('about-us', 'Api\MainController@about_us');
  Route::get('career', 'Api\MainController@career');
  Route::get('zoom', 'Api\MainController@zoom');
  Route::get('bigblue', 'Api\MainController@bigblue');
  Route::get('fetch/category/{id}/courses', 'Api\MainController@getcategoryCourse');

  Route::get('course/content/{id}', 'Api\MainController@coursecontent');
  Route::get('course/chapters', 'Api\CourseController@getAllchapter');
  Route::get('course/lessons', 'Api\CourseController@Lessons');
  Route::get('course/chapterswithlessons', 'Api\CourseController@getAllchaptersWithLessons');
  Route::get('myfatoorah/wallettopup', 'MyFatoorahController@walletTopUp')->name('myfatoorah.wallettopup');
  Route::get('pay-payinstalment-invoice', 'MyFatoorahController@InstalmentInvoicePaid')->name('payinstalment-inv');

  // KNET Payment API Response URLs
  Route::any('user/knet/payment/response', 'Api\KnetPaymentController@knetPaymentResponse')->name('knet.payment.response');
  Route::get('user/knet/payment/error', 'Api\KnetPaymentController@knetPaymentError')->name('knet.payment.error');

  // UPayment API Response URLs
  Route::prefix('user/upayment')->group(function () {
    Route::get('/success', 'Api\UPaymentController@success')->name('upayment.success');
    Route::get('/error', 'Api\UPaymentController@error')->name('upayment.error');
    Route::get('/payinstallment/success', 'Api\UPaymentController@payInstallmentSuccess')->name('upayment.payinstallment.success');
    Route::get('/payinstallment/error', 'Api\UPaymentController@payInstallmentError')->name('upayment.payinstallment.error');
    //   Route::get('/wallet/topup/success', 'Api\UPaymentController@walletTopUpSuccess')->name('upayment.wallet.topup.success'); // abandoned
    //   Route::get('/wallet/topup/error', 'Api\UPaymentController@walletTopUpError')->name('upayment.wallet.topup.error'); //abandoned
  });

  Route::prefix('upayment')->group(function () {
    Route::post('/cart/webhookurl', 'Api\UPaymentController@cartWebhookUrl')->name('cart.upayment.webhook');
    Route::post('/pay-installment/webhookurl', 'Api\UPaymentController@payInstallmentWebhookUrl')->name('installment.upayment.webhook');
    // Route::get('/topup/webhookurl', 'Api\UPaymentController@topUpWebhookUrl')->name('topup.upayment.webhook'); //abandoned
  });

  Route::group(['middleware' => ['auth:api']], function () {

    Route::post('remove/user/order', 'Api\UserApiController@removeUserFromOrder');

    Route::post('playerdeviceid', 'Api\UserApiController@storePlayerDeviceId');

    Route::post('logout', 'Api\Auth\LoginController@logoutApi');
    Route::post('change-pass', 'Api\UserApiController@ChangePass');
    Route::post('notifications', 'Api\UserApiController@Notifications');

    //wishlist
    Route::post('addtowishlist', 'Api\MainController@addtowishlist');
    Route::post('remove/wishlist', 'Api\MainController@removewishlist');

    // Route::post('show/wishlist', 'Api\MainController@showwishlist');
    Route::get('wishlist/courses', 'Api\MainController@wishlistcourse');
    Route::get('wishlist/bundles', 'Api\MainController@wishlistbundle');
    Route::get('wishlist/meetings', 'Api\MainController@wishlistmeeting');
    Route::get('wishlist/sessions', 'Api\MainController@wishlistsession');

    Route::get('my/categories', 'Api\UserApiController@getUserSelectedCategories');

    //userprofile
    Route::post('show/profile', 'Api\UserApiController@userprofile');
    Route::post('update/profile', 'Api\UserApiController@updateprofile');
    Route::delete('delete/profile', 'Api\UserApiController@DeleteUser');
    // Route::get('my/courses', 'Api\MainController@mycourses');
    Route::get('discover/courses', 'Api\CourseController@discovercourse');
    Route::get('in-progress/courses', 'Api\CourseController@inprogresscourse');
    Route::get('completed/courses', 'Api\CourseController@completedcourse');
    Route::post('my/calendar', 'Api\UserApiController@mycalendar');
    Route::post('user-categories', 'Api\UserApiController@updateUserCategories');

    Route::get('/overdue/{userId}', 'Api\MainController@overdue');

    //cart
    Route::post('addtocart/course', 'Api\MainController@addtocartCourse');
    Route::post('addtocart/chapter', 'Api\MainController@addtocartChapter');
    Route::post('addtocart/bundle', 'Api\MainController@addtocartBundle');
    Route::post('addtocart/meeting', 'Api\MainController@addtocartMeeting');
    Route::post('addtocart/offline-session', 'Api\MainController@addtocartOfflineSession');
    Route::post('remove/cart', 'Api\MainController@removecart');
    Route::get('show/cart', 'Api\MainController@showcart');
    Route::post('remove/all/cart', 'Api\MainController@removeallcart');
    // Route::post('addtocart/bundle', 'Api\MainController@addbundletocart');
    // Route::post('remove/bundle', 'Api\MainController@removebundlecart');

    //userprofile
    Route::get('notifications-on', 'Api\NotificationController@onOffNotification');
    Route::get('notifications', 'Api\NotificationController@allnotification');
    Route::get('readnotification/{id}', 'Api\NotificationController@notificationread');
    Route::post('readall/notification', 'Api\NotificationController@readallnotification');

    //paymentAPI
    Route::post('gen-installment-inv', 'MyFatoorahController@payInstallment');
    Route::post('pay/installment', 'Api\PaymentController@payInstallment');
    Route::post('pay/store', 'Api\PaymentController@paystore');
    Route::get('purchase/history', 'Api\PaymentController@purchasehistory');
    Route::post('pending/instalments', 'Api\PaymentController@pendingInstalments');
    Route::get('invoices', 'Api\PaymentController@Invoices');

    //KNET PaymentAPI
    Route::post('cart/knet/order', 'Api\KnetPaymentController@knetPaymentCreate');

    //UPaymentAPI
    Route::post('cart/upayment/order', 'Api\UPaymentController@create');
    Route::post('payinstallment/upayment/', 'Api\UPaymentController@payInstallment');
    // Route::post('wallet/upayment/card/topup', 'Api\UPaymentController@createWalletTopUp'); // abandoned

    Route::post('instructor/request', 'Api\MainController@becomeaninstructor');

    Route::post('course/progress', 'Api\MainController@courseprogress');
    Route::post('course/progress/update', 'Api\MainController@courseprogressupdate');

    Route::post('course/report', 'Api\MainController@coursereport');

    Route::post('apply/coupon/order', 'Api\CouponController@applyOrdercoupon'); // before used, abandoned
    Route::post('apply/coupon', 'Api\CouponController@applycoupon'); // before used, abandoned
    Route::post('remove/coupon', 'Api\CouponController@remove'); // before used, abandoned
    Route::post('cart/coupon', 'Api\CouponController@applyCartCoupon'); // being used
    Route::post('pending-installment/coupon', 'Api\CouponController@applyPendingInstallmentCoupon'); // being used

    Route::post('assignment/submit', 'Api\MainController@assignment');

    Route::post('appointment/request', 'Api\MainController@appointment');

    Route::get('course/lesson-content', 'Api\CourseController@lessonContent');
    Route::get('course/questions', 'Api\CourseController@CourseQuestions');
    Route::get('course/question/answer', 'Api\CourseController@answers');
    Route::post('question/submit', 'Api\CourseController@question');
    Route::delete('question/delete', 'Api\CourseController@DeleteQuestion');
    Route::post('answer/submit', 'Api\CourseController@answer');
    Route::delete('answer/delete', 'Api\CourseController@DeleteAnswer');
    Route::delete('review/delete', 'Api\CourseController@deleteReview');

    Route::post('appointment/delete/{id}', 'Api\MainController@appointmentdelete');

    Route::post('review/submit', 'Api\MainController@userreview');

    //Instructor API
    Route::get('instructor/dashboard', 'Api\InstructorApiController@dashboard');

    Route::get('instructor/course', 'Api\InstructorApiController@getAllcourse');
    Route::get('instructor/course/{id}', 'Api\InstructorApiController@getcourse');

    Route::post('instructor/update/profile', 'Api\InstructorApiController@instructorprofileupdate');
    Route::post('instructor/comparecourse', 'Api\InstructorApiController@getAllcomparecourse');

    Route::get('course/class', 'Api\InstructorApiController@getAllclass');
    Route::get('course/class/{id}', 'Api\InstructorApiController@getclass');
    Route::post('course/class', 'Api\InstructorApiController@createclass');
    Route::post('course/class/{id}', 'Api\InstructorApiController@updateclass');
    Route::delete('course/class/{id}', 'Api\InstructorApiController@deleteclass');

    /* Certicficate api */
    Route::get('certificate/download/{progress_id}', 'Api\OtherApiController@apipdfdownload');

    /* Certificate Module */
    Route::get('/certificate/{course_id}', 'Api\OtherApiController@getCertificate');

    /* Invoice api */
    Route::get('invoice/download/{order_id}', 'OrderController@apiinvoicepdfdownload');

    Route::post('free/enroll', 'Api\PaymentController@enroll');

    Route::get('user/bankdetails', 'Api\OtherApiController@userbankdetail');
    Route::post('add/bankdetails', 'Api\OtherApiController@addbankdetail');
    Route::post('update/bankdetails/{id}', 'Api\OtherApiController@updatebankdetail');

    /* Wallet API */
    Route::get('wallet/wallettransactions', 'Api\WalletController@getWalletTransactions');
    Route::post('wallet-topup-inv', 'MyFatoorahController@generateInvoiceForWallet')->name('wallettopup.inv');
    Route::post('wallet/topup', 'Api\WalletController@walletTopUp');
    Route::get('wallet/balance', 'Api\WalletController@getWalletBalance');
    Route::get('wallet/{type?}', 'Api\WalletController@getWallet');

    /* Affiliate */
    Route::get('affiliate/affiliatedetails', 'Api\OtherApiController@getAffiliate');

    /* Institute API */
    Route::get('institute/institutedetails', 'Api\OtherApiController@getInstitute');

    /* Resume API */
    Route::post('create/resumes', 'Api\OtherApiController@addResumeDetails');
    Route::post('update/resumes/{id}', 'Api\OtherApiController@updateResumeDetails');
    Route::get('view/resumes/{id}', 'Api\OtherApiController@viewResumeDetails');

    /* Job Post API */
    Route::post('create/postjob', 'Api\OtherApiController@createPostJob');

    Route::post('update/listjob/{id}', 'Api\OtherApiController@updateJobList');
    Route::get('listjob', 'Api\OtherApiController@JobList');
    Route::get('viewjob/{id}', 'Api\OtherApiController@Jobview');
    Route::get('viewjobcreatedbyuser/{id}', 'Api\OtherApiController@viewjobcreatedbyuser');
    Route::delete('jobdestroy/{id}', 'Api\OtherApiController@jobdestroy');
    Route::post('job/userstatus', 'Api\OtherApiController@userstatus')->name('job.userstatus');
    Route::post('view/applyjob/{id}', 'Api\OtherApiController@applyJobs');
    Route::delete('view/applyjobdestroy/{id}', 'Api\OtherApiController@applyjobdestroy');
    Route::get('view/applyjoblist/', 'Api\OtherApiController@applyjoblist');

    //filter
    Route::get('job/find', 'Api\OtherApiController@searchfind')->name('job.searchfind');
    Route::get('locationfilter', 'Api\OtherApiController@locationfilter')->name('job.filter');
    Route::get('allcompanylist', 'Api\OtherApiController@allcompanylist');
    Route::get('allcountrystatelist', 'Api\OtherApiController@allcountrystatelist');

    /* Homework Module */
    Route::post('/homework', 'Api\OtherApiController@getHomework');
    Route::post('/submithomework', 'Api\OtherApiController@submitHomework');
    Route::get('/gethomework/{id}', 'Api\OtherApiController@getSpecificHomework');
    Route::get('/getanswer/{id}', 'Api\OtherApiController@getAnswer');

    /* Forum and Discussion */
    Route::post('/addforumscategory', 'Api\OtherApiController@addforumscategory');
    Route::get('/listforumscategory', 'Api\OtherApiController@forumsList');
    Route::post('/addforums', 'Api\OtherApiController@addforums');

    /* Topic List */
    Route::get('/topiclist', 'Api\OtherApiController@topicList');
    Route::get('/specifictopicdetail/{id}', 'Api\OtherApiController@specifictopicdetail');

    /* Comment List */
    Route::post('/addcommentspecifictopic/{id}', 'Api\OtherApiController@addcommentspecifictopic');
    Route::get('/showcommentspecifictopic/{id}', 'Api\OtherApiController@showcommentspecifictopic');
    Route::post('/submitreplycomment/{id}', 'Api\OtherApiController@submitreplycomment');
    Route::get('/listofallreplycomments/{id}', 'Api\OtherApiController@listofallreplycomments');
    Route::post('/updatecommentreplay/{id}', 'Api\OtherApiController@updatecommentreplay');
    Route::delete('/deletecommentreply/{id}', 'Api\OtherApiController@deletecommentreply');

    /* WatchList API */
    Route::post('create/watchlist', 'Api\OtherApiController@addwatchlist');
    Route::get('view/watchlist', 'Api\OtherApiController@viewwatchlist');
    Route::post('delete/watchlist', 'Api\OtherApiController@deletewatchlist');
    Route::post('assignment/delete', 'Api\MainController@deleteAssignment');

    Route::get('instructor/request/check', 'Api\MainController@requestCheck');
    Route::post('cancel/instructor/request', 'Api\MainController@cancelRequest');

    Route::post('stripe/pay/store', 'Api\PaymentController@stripepay');

    //orders API
    Route::get('order', 'Api\InstructorApiController@getAllorder');

    Route::get('watch/course/{id}', 'Api\MainController@watchcourse');

    Route::post('review/helpful/{id}', 'Api\MainController@reviewlike');

    Route::get('course/assignment', 'Api\InstructorApiController@getAllassignment');

    Route::get('refundorder', 'Api\InstructorApiController@getAllrefund');

    Route::get('toinvolve/courses', 'Api\InstructorApiController@toinvolvecourses');
    Route::post('requesttoinvolve', 'Api\InstructorApiController@requesttoinvolve');
    Route::get('all/involve/request', 'Api\InstructorApiController@Allinvolvementrequest');
    Route::get('involved/courses', 'Api\InstructorApiController@involvedcourses');

    //Route::get('questions', 'Api\InstructorApiController@getAllquestions');
    Route::get('questions/{id?}', 'Api\InstructorApiController@getquestions');
    Route::post('questions', 'Api\InstructorApiController@createquestions');
    Route::post('questions/{id}', 'Api\InstructorApiController@updatequestions');

    Route::get('announcement', 'Api\InstructorApiController@Allannouncement');

    Route::get('answer', 'Api\InstructorApiController@getAllanswer');
    Route::get('answer/{id}', 'Api\InstructorApiController@getanswer');
    Route::post('answer/{id}', 'Api\InstructorApiController@updateanswer');
    Route::delete('answer/{id}', 'Api\InstructorApiController@deleteanswer');

    Route::get('vacationmode', 'Api\InstructorApiController@vacationmode');
    Route::post('vacationmodeupdate', 'Api\InstructorApiController@vacationmodeupdate');

    //get quiz route returns quiz report as well
    Route::get('quiz/{id}', 'Api\MainController@quiz');
    Route::get('quiz/start/{id}', 'Api\MainController@quizstart');
    Route::post('quiz/submit', 'Api\MainController@quizsubmit');
    //Route::get('quiz/reports/{id}', 'Api\MainController@getquizreports');

    Route::post('session/enrollment', 'Api\OtherApiController@sessionenrollment');
    Route::get('streaming/booking/list', 'Api\OtherApiController@streamingbookinglist');
    Route::get('session/booking/list', 'Api\OtherApiController@sessionbookinglist');

    Route::post('cart/order', 'Api\PaymentController@paycartorder');
    Route::post('cart/order2', 'MyFatoorahController@paycartorder');
    Route::post('order2', 'MyFatoorahController@createorder');
    Route::post('order', 'Api\InstructorApiController@createorder');
    Route::get('order/{id}', 'Api\InstructorApiController@getorder');
    Route::delete('order/{id}', 'Api\InstructorApiController@deleteorder');

    Route::middleware(['restrictFileAccess'])->group(function () {
      Route::get('courseclass/file/{id}/url', 'Api\CourseController@previewFileURL')->name('file.url');
    });
    Route::post('courseclass/file/permission', 'Api\CourseController@allowFileDownloadOrPrint');

    /*questionairs */
    Route::post('/questionnaires/{id}/answer', [QuestionnaireController::class, 'answer']);
    Route::get('/questionnaires/user/all', [QuestionnaireController::class, 'getQuestionnairesForStudent']);

  });

  Route::get('courseclass/file/preview', 'Api\CourseController@previewFile')->name('preview.file'); //->middleware('signed');

  Route::middleware(['allowFileDownloadOrPrint'])->group(function () {
    Route::get('courseclass/file/{id}/download', 'Api\CourseController@downloadFile');
    Route::get('courseclass/file/{id}/print', 'Api\CourseController@printFile');
  });

  Route::get('course/assignment/{id}', 'Api\InstructorApiController@getassignment');
  Route::post('course/assignment/{id}', 'Api\InstructorApiController@updateassignment');
  Route::delete('course/assignment/{id}', 'Api\InstructorApiController@deleteassignment');

  //course language API
  Route::get('courselanguage', 'Api\InstructorApiController@getAlllanguage');
  Route::get('courselanguage/{id}', 'Api\InstructorApiController@getlanguage');
  Route::post('courselanguage', 'Api\InstructorApiController@createlanguage');
  Route::post('courselanguage/{id}', 'Api\InstructorApiController@updatelanguage');
  Route::delete('courselanguage/{id}', 'Api\InstructorApiController@deletelanguage');

  //categories API
  Route::get('category', 'Api\InstructorApiController@getAllcategory');
  Route::get('category/{id}', 'Api\InstructorApiController@getcategory');
  Route::post('category', 'Api\InstructorApiController@createcategory');
  Route::post('category/{id}', 'Api\InstructorApiController@updatecategory');
  Route::delete('category/{id}', 'Api\InstructorApiController@deletecategory');

  //Type Categories API
  Route::get('typecategories/{id?}', 'Api\InstructorApiController@getAllTypeCategory');

  //subcategories API
  Route::get('subcategory/{id?}', 'Api\InstructorApiController@getAllsubcategory');
  Route::get('subcategory/{id}', 'Api\InstructorApiController@getsubcategory');
  Route::post('subcategory', 'Api\InstructorApiController@createsubcategory');
  Route::post('subcategory/{id}', 'Api\InstructorApiController@updatesubcategory');
  Route::delete('subcategory/{id}', 'Api\InstructorApiController@deletesubcategory');

  //childcategories API
  Route::get('childcategory/{id?}/{sub?}', 'Api\InstructorApiController@getAllchildcategory');
  Route::get('childcategory/{id}', 'Api\InstructorApiController@getchildcategory');
  Route::post('childcategory', 'Api\InstructorApiController@createchildcategory');
  Route::post('childcategory/{id}', 'Api\InstructorApiController@updatechildcategory');
  Route::delete('childcategory/{id}', 'Api\InstructorApiController@deletechildcategory');

  //Courses API
  Route::post('instructor/course', 'Api\InstructorApiController@createcourse');
  Route::post('instructor/course/{id}', 'Api\InstructorApiController@updatecourse');
  Route::delete('instructor/course/{id}', 'Api\InstructorApiController@deletecourse');

  Route::get('refundpolicy', 'Api\InstructorApiController@getAllrefundpolicy');

  //Refund orders API
  Route::get('refundorder/{id}', 'Api\InstructorApiController@getrefund');
  Route::post('refundorder/{id}', 'Api\InstructorApiController@updaterefund');
  Route::delete('refundorder/{id}', 'Api\InstructorApiController@deleterefund');

  //categories API
  Route::get('include', 'Api\InstructorApiController@getAllinclude');
  Route::get('include/{id}', 'Api\InstructorApiController@getinclude');
  Route::post('include', 'Api\InstructorApiController@createinclude');
  Route::post('include/{id}', 'Api\InstructorApiController@updateinclude');
  Route::delete('include/{id}', 'Api\InstructorApiController@deleteinclude');

  //categories API
  Route::get('whatlearn', 'Api\InstructorApiController@getAllwhatlearn');
  Route::get('whatlearn/{id}', 'Api\InstructorApiController@getwhatlearn');
  Route::post('whatlearn', 'Api\InstructorApiController@createwhatlearn');
  Route::post('whatlearn/{id}', 'Api\InstructorApiController@updatewhatlearn');
  Route::delete('whatlearn/{id}', 'Api\InstructorApiController@deletewhatlearn');

  Route::get('chapter', 'Api\InstructorApiController@getAllchapter');
  Route::get('chapter/{id}', 'Api\InstructorApiController@getchapter');
  Route::post('chapter', 'Api\InstructorApiController@createchapter');
  Route::post('chapter/{id}', 'Api\InstructorApiController@updatechapter');
  Route::delete('chapter/{id}', 'Api\InstructorApiController@deletechapter');

  Route::get('related/course', 'Api\InstructorApiController@getAllrelated');
  Route::get('related/course/{id}', 'Api\InstructorApiController@getrelated');
  Route::post('related/course', 'Api\InstructorApiController@createrelated');
  Route::post('related/course/{id}', 'Api\InstructorApiController@updaterelated');
  Route::delete('related/course/{id}', 'Api\InstructorApiController@deleterelated');

  Route::delete('questions/{id}', 'Api\InstructorApiController@deletequestions');

  Route::get('language', 'Api\OtherApiController@siteLanguage');
  Route::post('gift/user/check', 'Api\PaymentController@giftusercheck');
  Route::post('gift/checkout', 'Api\PaymentController@giftcheckout');
  Route::get('category/{id}/{name}', 'Api\MainController@categoryPage');
  Route::get('subcategory/{id}/{name}', 'Api\MainController@subcategoryPage');
  Route::get('childcategory/{id}/{name}', 'Api\MainController@childcategoryPage');
  Route::get('search', 'Api\OtherApiController@search');
  Route::get('factsetting', 'Api\MainController@factsetting');
  Route::get('videosetting', 'Api\MainController@videosetting');
  Route::get('bestselling', 'Api\MainController@bestselling');
  // Route::get('instructor', 'Api\MainController@Instructor');
  // Route::get('livemeeting', 'Api\MainController@livemeeting');

  Route::get('footer/widget', 'Api\OtherApiController@widget');
  Route::get('manual/payment', 'Api\OtherApiController@manual');
  Route::get('/check-for-update', 'OtaUpdateController@checkforupate');
  Route::post('live/attandance', 'Api\OtherApiController@attandance');
  Route::get('/currencies', 'Api\OtherApiController@currencies');
  Route::post('/currency/rates', 'Api\OtherApiController@currency_rates');

});
