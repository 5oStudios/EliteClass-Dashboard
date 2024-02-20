<div class="leftbar sidebar-two" style="background-image: {{ url('images/navbar.png') }}">

    <img id="loading" src="{{ url('images\logo\pre_loader.gif') }}" alt="Loading..." />

    <!-- Start Sidebar -->
    <div class="sidebar">
        <!-- Start Navigationbar -->
        <div class="navigationbar">
            <div class="vertical-menu-detail">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade active show" id="v-pills-dashboard" role="tabpanel"
                        aria-labelledby="v-pills-dashboard">
                        <ul class="vertical-menu">
                            <div class="logobar">
                                <a href="{{ url('/') }}" class="logo logo-large">
                                    <img style="object-fit:scale-down;"
                                        src="{{ url('images/logo/' . $gsetting->footer_logo) }}" class="img-fluid"
                                        alt="logo">
                                </a>
                            </div>

                            <li class="{{ Nav::isRoute('admin.index') }}">
                                <a class="nav-link" href="{{ route('admin.index') }}">
                                    <i class="feather icon-pie-chart text-secondary"></i>
                                    <span>{{ __('adminstaticword.Dashboard') }}</span>
                                </a>
                            </li>

                            @can(['marketing-dashboard.manage'])
                                <li class="{{ Nav::isRoute('market.index') }}">
                                    <a class="nav-link" href="{{ route('market.index') }}">
                                        <i class="feather icon-pie-chart text-secondary"></i>
                                        <span>{{ __('Marketing Dashboard') }}</span>
                                    </a>
                                </li>
                            @endcan
                            <!-- dashboard end -->

                            <!-- Category start  -->
                            @canany(['categories.view', 'subcategories.view', 'childcategories.views'])
                                <li class="header">{{ __('Home') }}</li>

                                @if (auth()->user()->role == 'admin')
                                    <li class="{{ Nav::isResource('order.enrollments') }}"><a
                                            href="{{ route('order.enrollments') }}"><i
                                                class="feather icon-user-check text-secondary"></i><span>{{ __('All Enrollments') }}</span></a>
                                    </li>
                                @endif

                                @can(['categories.view'])
                                    <li class="{{ Nav::hasSegment('category') }}"><a href="{{ url('category') }}"><i
                                                class="feather icon-map text-secondary"></i><span>{{ __('adminstaticword.Category') }}</span></a>
                                    </li>
                                @endcan

                                @can(['subcategories.view'])
                                    <li class="{{ Nav::isResource('typecategory') }}"><a href="{{ url('typecategory') }}"><i
                                                class="feather icon-command text-secondary"></i><span>{{ __('adminstaticword.TypeCategory') }}</span></a>
                                    </li>
                                    <li class="{{ Nav::isResource('subcategory') }}"><a href="{{ url('subcategory') }}"><i
                                                class="feather icon-briefcase text-secondary"></i><span>{{ __('adminstaticword.SubCategory') }}</span></a>
                                    </li>
                                @endcan

                                @can(['childcategories.view'])
                                    <li class="{{ Nav::isResource('childcategory') }}"><a href="{{ url('childcategory') }}"><i
                                                class="feather icon-layers text-secondary"></i><span>{{ __('adminstaticword.ChildCategory') }}</span></a>
                                    </li>
                                @endcan
                            @endcanany
                            <!-- Category end  -->

                            @canany(['users.view', 'Alluser.view', 'Allinstructor.view'])
                                <li class="header header-one">{{ __('Users') }}</li>
                                <!-- user start  -->
                                <!-- user start  -->
                                <li
                                    class="{{ Nav::isRoute('user.index') }} {{ Nav::isRoute('user.add') }} {{ Nav::isRoute('user.edit') }}{{ Nav::isRoute('alluser.index') }} {{ Nav::isRoute('alluser.add') }} {{ Nav::isRoute('alluser.edit') }}{{ Nav::isRoute('allinstructor.index') }} {{ Nav::isRoute('allinstructor.add') }} {{ Nav::isRoute('allinstructor.edit') }} {{ Nav::isRoute('testuser.index') }} {{ Nav::urlDoesContain('testusers') }} {{ Nav::isResource('roles') }}">
                                    <a href="javaScript:void();">
                                        <i class="feather icon-users text-secondary"></i><span>{{ __('Users') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can('users.view')
                                            <li>
                                                <a class="{{ Nav::isResource('user') }}"
                                                    href="{{ route('user.index') }}">{{ __('All Users') }}</a>
                                            </li>
                                        @endcan
                                        @can('Alluser.view')
                                            <li>
                                                <a class="{{ Nav::isResource('alluser') }}"
                                                    href="{{ route('alluser.index') }}">{{ __('All Students') }}</a>
                                            </li>
                                        @endcan
                                        @can(['Allinstructor.view'])
                                            <li>
                                                <a class="{{ Nav::isResource('allinstructor') }}"
                                                    href="{{ route('allinstructor.index') }}">{{ __('All Instructors') }}</a>
                                                {{-- <a href="javaScript:void();">
                                                    <i class=""></i> <span>{{ __('Big Blue') }}</span><i
                                                        class="feather icon-chevron-right"></i>
                                                </a> --}}

                                                {{-- <a href="javaScript:void();">
                                                    <span>{{ __('Instructors') }}</span><i
                                                        class="feather icon-chevron-right"></i>
                                                </a>

                                                <ul class="vertical-submenu">
                                                    <li>
                                                        <a class="{{ Nav::isResource('allinstructor') }}"
                                                            href="{{ route('allinstructor.index') }}">{{ __('All Instructors') }}</a>
                                                    </li>
                                                    <li>
                                                        <a class="{{ Nav::isRoute('instructor.settings') }} {{ Nav::isRoute('admin.instructor') }} {{ Nav::isRoute('admin.completed') }}"
                                                            href="javaScript:void();">
                                                            <span>{{ __('adminstaticword.InstructorPayout') }}</span>
                                                        </a>
                                                    </li>
                                                    <ul class="vertical-submenu">

                                                        <li class="{{ Nav::isRoute('instructor.settings') }}"><a
                                                                href="{{ route('instructor.settings') }}">{{ __('adminstaticword.PayoutSettings') }}</a>
                                                        </li>
                                                        <li class="{{ Nav::isRoute('admin.instructor') }}"><a
                                                                href="{{ route('admin.instructor') }}">{{ __('adminstaticword.PendingPayout') }}</a>
                                                        </li>
                                                        <li class="{{ Nav::isRoute('admin.completed') }}"><a
                                                                href="{{ route('admin.completed') }}">{{ __('adminstaticword.CompletedPayout') }}</a>
                                                        </li>
                                                    </ul>
                                                </ul> --}}
                                            </li>
                                        @endcan

                                        @if (auth()->user()->hasRole('admin'))
                                            <li>
                                                <a class="{{ Nav::isRoute('testuser.index') }} {{ Nav::urlDoesContain('testusers') }}"
                                                    href="{{ route('testuser.index') }}">{{ __('Test Users') }}</a>
                                            </li>
                                        @endif

                                        <li>
                                            <a class="{{ Nav::isResource('roles') }}"
                                                href="{{ route('roles.index') }}">{{ __('Roles And Permission') }}</a>
                                        </li>

                                    </ul>
                                </li>
                            @endcanany
                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ABPP')  
                                 <li>
                                        <a href="{{ route('user.bulk_add') }}">
                                            <i class="feather icon-users text-secondary"></i><span>{{ __('bulkAdd') }} {{ __('Students') }}</span>
                                        </a>
                                 </li>  
                            @endif

                            @canany(['instructorrequest.view', 'instructor-pending-request.manage',
                                'instructor-plan-subscription.view'])
                                <li
                                    class="d-none {{ Nav::isResource('plan/subscribe/settings') }} {{ Nav::isResource('subscription/plan') }}  {{ Nav::isRoute('all.instructor') }} {{ Nav::isResource('requestinstructor') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-user text-secondary"></i><span>{{ __('adminstaticword.Instructors') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can('instructorrequest.view')
                                            <li class="{{ Nav::isRoute('all.instructor') }}"><a
                                                    href="{{ route('all.instructor') }}">{{ __('adminstaticword.All') }}
                                                    {{ __('adminstaticword.InstructorRequest') }}</a></li>
                                        @endcan
                                        @can('instructor-pending-request.manage')
                                            <li class="{{ Nav::isResource('requestinstructor') }}"><a
                                                    href="{{ url('requestinstructor') }}">{{ __('adminstaticword.Pending') }}
                                                    {{ __('Request') }}</a></li>
                                        @endcan
                                        @can('instructor-plan-subscription.view')
                                            <li class="{{ Nav::isResource('plan/subscribe/settings') }}"><a
                                                    href="{{ url('plan/subscribe/settings') }}">{{ __('adminstaticword.Instructor') }}
                                                    {{ __('adminstaticword.Subscription') }}</a></li>
                                        @endcan
                                        @if (env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1)
                                            <li class="{{ Nav::isResource('subscription/plan') }}"><a
                                                    href="{{ url('subscription/plan') }}">{{ __('adminstaticword.InstructorPlan') }}</a>
                                            </li>
                                        @endif
                                        <!-- MultipleInstructor start  -->
                                        <li
                                            class="{{ Nav::isRoute('allrequestinvolve') }} {{ Nav::isRoute('involve.request.index') }} {{ Nav::isRoute('involve.request') }}">
                                            <a href="javaScript:void();">
                                                <i class=""></i>
                                                <span>{{ __('adminstaticword.MultipleInstructor') }}</span>
                                            </a>
                                            <ul class="vertical-submenu">

                                                <li class="{{ Nav::isRoute('allrequestinvolve') }}"><a
                                                        href="{{ route('allrequestinvolve') }}">{{ __('adminstaticword.RequestToInvolve') }}</a>
                                                </li>
                                                <li class="{{ Nav::isRoute('involve.request.index') }}"><a
                                                        href="{{ route('involve.request.index') }}">{{ __('adminstaticword.InvolvementRequests') }}</a>
                                                </li>
                                                <li class="{{ Nav::isRoute('involve.request') }}"><a
                                                        href="{{ route('involve.request') }}">{{ __('adminstaticword.InvolvedInCourse') }}</a>
                                                </li>

                                            </ul>
                                        </li>
                                        <!-- MultipleInstructor end  -->
                                        <!-- InstructorPayout start  -->
                                        {{-- <li
                                            class="{{ Nav::isRoute('instructor.settings') }} {{ Nav::isRoute('admin.instructor') }} {{ Nav::isRoute('admin.completed') }}">
                                            <a href="javaScript:void();">
                                                <i class=""></i>
                                                <span>{{ __('adminstaticword.InstructorPayout') }}</span>
                                            </a>
                                            <ul class="vertical-submenu">

                                                <li class="{{ Nav::isRoute('instructor.settings') }}"><a
                                                        href="{{ route('instructor.settings') }}">{{ __('adminstaticword.PayoutSettings') }}</a>
                                                </li>
                                                <li class="{{ Nav::isRoute('admin.instructor') }}"><a
                                                        href="{{ route('admin.instructor') }}">{{ __('adminstaticword.PendingPayout') }}</a>
                                                </li>
                                                <li class="{{ Nav::isRoute('admin.completed') }}"><a
                                                        href="{{ route('admin.completed') }}">{{ __('adminstaticword.CompletedPayout') }}</a>
                                                </li>
                                            </ul>
                                        </li> --}}
                                        <!-- InstructorPayout end  -->
                                    </ul>
                                </li>
                            @endcanany
                            <!-- user end -->

                            @canany(['categories.view', 'courses.view', 'bundle-courses.view', 'course-languages.view',
                                'course-reviews.view', 'assignment.view', 'refund-policy.view', 'batch.view',
                                'quiz-review.view', 'private-course.view', 'reported-course.view',
                                'reported-question.view'])

                                <li class="header">{{ __('Education') }}</li>
                                <!-- ====================Course start======================== -->
                                @can(['courses.view'])
                                    <li class="{{ Nav::isResource('course') }}"><a href="{{ url('course') }}"><i
                                                class="feather icon-book text-secondary"></i><span>{{ __('adminstaticword.Courses') }}</span></a>
                                    </li>
                                @endcan
                                <!--=================== Course end====================================  -->

                                @can(['bundle-courses.view'])
                                    <li class="{{ Nav::isResource('bundle') }}"><a href="{{ url('bundle') }}"><i
                                                class="feather icon-package text-secondary"></i><span>{{ __('adminstaticword.BundleCourse') }}</span></a>
                                    </li>
                                @endcan

                                <!-- ====================Meetings start======================== -->
                                @canany(['meetings.big-blue.view', 'meetings.meeting-recordings.view'])
                                    @if (isset($gsetting) && $gsetting->bbl_enable == 1)
                                        <li
                                            class="{{ Nav::isRoute('meeting.create') }} {{ Nav::isRoute('zoom.show') }} {{ Nav::isRoute('zoom.edit') }} {{ Nav::isRoute('zoom.setting') }} {{ Nav::isRoute('zoom.index') }} {{ Nav::isRoute('meeting.show') }} {{ Nav::isRoute('bbl.setting') }} {{ Nav::isRoute('bbl.all.meeting') }} {{ Nav::isRoute('download.meeting') }} {{ Nav::isRoute('googlemeet.setting') }} {{ Nav::isRoute('googlemeet.index') }} {{ Nav::isRoute('googlemeet.allgooglemeeting') }} {{ Nav::isRoute('jitsi.dashboard') }} {{ Nav::isRoute('jitsi.create') }} {{ Nav::isResource('meeting-recordings') }}">
                                            <a href="javaScript:void();">
                                                <i class="feather icon-clock text-secondary"></i>
                                                <span>{{ __('adminstaticword.Meetings') }}</span><i
                                                    class="feather icon-chevron-right"></i>
                                            </a>
                                            <ul class="vertical-submenu">
                                                <!-- BigBlueMeetings start  -->
                                                <li
                                                    class="{{ Nav::isRoute('bbl.setting') }} {{ Nav::isRoute('bbl.all.meeting') }} {{ Nav::isRoute('download.meeting') }}">
                                                    <a href="javaScript:void();">
                                                        <span>{{ __('Big Blue') }}</span><i
                                                            class="feather icon-chevron-right"></i>
                                                    </a>
                                                    <ul class="vertical-submenu">

                                                        <li class="{{ Nav::isRoute('bbl.setting') }}"><a
                                                                href="{{ route('bbl.setting') }}">{{ __('Settings') }}</a>
                                                        </li>
                                                        <li class="{{ Nav::isRoute('bbl.all.meeting') }}"><a
                                                                href="{{ route('bbl.all.meeting') }}">{{ __('adminstaticword.ListMeetings') }}</a>
                                                        </li>
                                                        <li class="{{ Nav::isRoute('download.meeting') }}"><a
                                                                href="{{ route('download.meeting') }}">{{ __('Recorded') }}</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- BigBlueMeetings end  -->
                                    @endif
                                    @can(['in-person-session.view'])
                                        <li class="{{ Nav::isRoute('offline.sessions.index') }}">
                                            <a href="{{ route('offline.sessions.index') }}">
                                                <i class="feather icon-clock text-secondary"></i>
                                                <span>{{ __('In-Person Sessions') }}</span><i
                                                    class="feather icon-chevron-right"></i>
                                            </a>
                                        </li>
                                    @endcan
                                @endcanany


                                {{-- <li
                                    class="d-none {{ Nav::isResource('category') }} {{ Nav::isResource('subcategory') }} {{ Nav::isResource('childcategory') }} {{ Nav::isResource('course') }} {{ Nav::isResource('bundle') }} {{ Nav::isResource('courselang') }} {{ Nav::isResource('coursereview') }} {{ Nav::isRoute('assignment.view') }} {{ Nav::isResource('refundpolicy') }} {{ Nav::isResource('batch') }} {{ Nav::isRoute('quiz.review') }} {{ Nav::isResource('private-course') }} {{ Nav::isResource('admin/report/view') }} {{ Nav::isResource('user/question/report') }}">
                                    <ul class="vertical-submenu">


                                        @can(['course-languages.view'])
                                            <li class="{{ Nav::isResource('courselang') }}"><a
                                                    href="{{ url('courselang') }}"><span>{{ __('adminstaticword.CourseLanguage') }}</span></a>
                                            </li>
                                        @endcan
                                        @can(['course-reviews.view'])
                                            <li class="{{ Nav::isResource('coursereview') }}"><a
                                                    href="{{ url('coursereview') }}"><span>{{ __('adminstaticword.CourseReview') }}</span></a>
                                            </li>
                                        @endcan
                                        @can(['assignment.view'])
                                            @if ($gsetting->assignment_enable == 1)
                                                <li class="{{ Nav::isRoute('assignment.view') }}"><a
                                                        href="{{ route('assignment.view') }}"><span>{{ __('adminstaticword.Assignment') }}</span></a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can(['refund-policy.view'])
                                            <li class="{{ Nav::isResource('refundpolicy') }}"><a
                                                    href="{{ url('refundpolicy') }}"><span>{{ __('adminstaticword.RefundPolicy') }}</span></a>
                                            </li>
                                        @endcan
                                        @can(['batch.view'])
                                            <li class="{{ Nav::isResource('batch') }}"><a
                                                    href="{{ url('batch') }}"><span>{{ __('adminstaticword.Batch') }}</span></a>
                                            </li>
                                        @endcan
                                        @can(['quiz-review.view'])
                                            <li class="{{ Nav::isRoute('quiz.review') }}"><a
                                                    href="{{ route('quiz.review') }}"><span>{{ __('adminstaticword.QuizReview') }}</span></a>
                                            </li>
                                        @endcan
                                        @can(['private-course.view'])
                                            <li class="{{ Nav::isResource('private-course') }}"><a
                                                    href="{{ url('private-course') }}"><span>{{ __('adminstaticword.PrivateCourse') }}</span></a>
                                            </li>
                                        @endcan
                                        @can(['reported-course.view'])
                                            <li class="{{ Nav::isResource('admin/report/view') }}">
                                                <a href="{{ url('admin/report/view') }}">{{ __('adminstaticword.Reported') }}
                                                    {{ __('Course') }}
                                                </a>
                                            </li>
                                        @endcan

                                        @can(['reported-question.view'])
                                            <li class="{{ Nav::isResource('user/question/report') }}">
                                                <a href="{{ url('user/question/report') }}">{{ __('adminstaticword.Reported') }}
                                                    {{ __('Question') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li> --}}
                            @endcanany

                            {{-- @can(['institute.view'])
                                <li>
                                    <a href="{{ url('institute') }}"><i
                                            class="feather icon-grid text-secondary"></i><span>{{ __('Institute') }}</span></a>
                                </li>
                            @endcan --}}

                            {{-- @can('certificate.manage')
                                @if (Module::has('Certificate') && Module::find('Certificate')->isEnabled())
                                    @include('certificate::admin.sidebar_menu')
                                @endif

                                <li class="{{ Nav::isRoute('certificate.index') }}"><a
                                        href="{{ route('certificate.index') }}">
                                        <i
                                            class="feather icon-help-circle text-secondary"></i><span>{{ __('Certificate Verify') }}</span>
                                    </a>
                                </li>
                            @endcan --}}

                            <!--===================meeting end====================================  -->
                            <!-- ====================instructor start======================== -->

                            <!--===================instructor end====================================  -->
                            <li class="header">{{ __('Marketing') }}</li>
                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'ABPP')  
                                <li class="{{ Nav::isResource('coupon') }}"><a href="{{ url('coupon') }}"><i
                                            class="feather icon-award text-secondary"></i><span>{{ __('adminstaticword.Coupon') }}</span></a>
                                </li>
                            @endif

                            {{-- @can(['followers.manage'])
                                <li class="{{ Nav::isRoute('follower.view') }}"><a
                                        href="{{ route('follower.view') }}"><i
                                            class="feather icon-help-circle text-secondary"></i><span>{{ __('Followers') }}</span></a>
                                </li>
                            @endcan --}}

                            @canany(['affiliate.manage'])
                                <li class="{{ Nav::isRoute('save.affiliates') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-dollar-sign text-secondary"></i><span>{{ __('adminstaticword.Affiliate') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can(['affiliate.manage'])
                                            <li class="{{ Nav::isRoute('save.affiliates') }}">
                                                <a
                                                    href="{{ route('save.affiliates') }}">{{ __('adminstaticword.Affiliate') }}</a>
                                            </li>
                                        @endcan
                                        {{-- @canany(['wallet-setting.manage', 'wallet-transactions.manage'])
                                            <li class="{{ Nav::isRoute('wallet.settings') }}">
                                                <a href="javaScript:void();">

                                                    <span>{{ __('adminstaticword.Wallet') }}</span>

                                                </a>
                                                <ul class="vertical-submenu">
                                                    @can(['wallet-setting.manage'])
                                                        <li class="{{ Nav::isRoute('wallet.settings') }}"><a
                                                                href="{{ route('wallet.settings') }}">{{ __('adminstaticword.Wallet') }}
                                                                {{ __('adminstaticword.Setting') }}</a>
                                                        </li>
                                                        <li class="{{ Nav::isRoute('wallet.user') }}"><a
                                                                href="{{ route('wallet.user') }}">{{ __('adminstaticword.Users') }}
                                                                {{ __('adminstaticword.Wallet') }}</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </li>
                                        @endcanany --}}
                                    </ul>
                                </li>
                            @endcanany
                            <!-- Transactions -->
                            @can(['wallet-transactions.manage'])
                                <li class="{{ Nav::isRoute('transactions') }}">
                                    <a href="{{ route('transactions') }}">
                                        <i
                                            class="feather icon-trending-up text-secondary"></i>{{ __('adminstaticword.Transactions') }}</a>
                                </li>
                            @endcan
                            <!-- PushNotification -->
                            @can(['push-notification.manage'])
                                <li class="{{ Nav::isRoute('onesignal.settings') }}"><a
                                        href="{{ route('onesignal.settings') }}"><i
                                            class="feather icon-navigation text-secondary"></i><span>{{ __('adminstaticword.PushNotification') }}</span></a>
                                </li>
                            @endcan

                            {{-- @can(['flash-deals.view'])
                                <li class="{{ Nav::isResource('admin/flash-sales') }}"><a
                                        href="{{ url('admin/flash-sales') }}"><i
                                            class="feather icon-clock text-secondary"></i>
                                        <span>{{ __('Flash Deals') }}</span></a>
                                </li>
                            @endcan --}}

                            <!-- attandance -->
                            @can(['attendance.manage'])
                                @if (isset($gsetting) && $gsetting->attandance_enable == 1)
                                    <li class="{{ Nav::isResource('attandance') }}"><a
                                            href="{{ url('attandance') }}"><i
                                                class="feather icon-user text-secondary"></i><span>{{ __('adminstaticword.Attandance') }}</span></a>
                                    </li>
                                @endif
                            @endcan

                            <!-- coupon -->
                            <!-- order -->
                            {{-- @can(['orders.manage'])
                                <li class="header">{{ __('Financial') }}</li>

                                <li class="{{ Nav::isResource('order') }}"><a href="{{ url('order') }}"><i
                                            class="feather icon-shopping-cart text-secondary"></i><span>{{ __('adminstaticword.Order') }}</span></a>
                                </li>
                            @endcan --}}

                            @can(['orders.manage'])
                                <li class="header">{{ __('Financial') }}</li>

                                <li
                                    class="{{ Nav::isResource('category') }} {{ Nav::isResource('subcategory') }} {{ Nav::isResource('childcategory') }} {{ Nav::isResource('course') }} {{ Nav::isResource('bundle') }} {{ Nav::isResource('courselang') }} {{ Nav::isResource('coursereview') }} {{ Nav::isRoute('assignment.view') }} {{ Nav::isResource('refundpolicy') }} {{ Nav::isResource('batch') }} {{ Nav::isRoute('quiz.review') }} {{ Nav::isResource('private-course') }} {{ Nav::isResource('admin/report/view') }} {{ Nav::isResource('user/question/report') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-book text-secondary"></i><span>{{ __('Invoices') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        <!-- Category start  -->

                                        <li class="{{ Nav::isResource('full-payments') }}"><a
                                                href="{{ url('full-payments') }}">
                                                <span>{{ __('adminstaticword.FullPayment') }}</span></a>
                                        </li>


                                        <li class="{{ Nav::isResource('installments') }}"><a
                                                href="{{ url('installments') }}">{{ __('adminstaticword.PaymentInInstallments') }}</a>
                                        </li>

                                        <li class="{{ Nav::isResource('invoices') }}"><a
                                                href="{{ route('invoices') }}">{{ __('adminstaticword.Invoices') }}</a>
                                        </li>
                                    </ul>

                                </li>
                            @endcan

                            {{-- @can(['blogs.view'])
                                <li class="header">{{ __('Content') }}</li>

                                <li class="{{ Nav::isResource('blog') }}">
                                    <a href="{{ url('blog') }}"><i class="feather icon-message-square"></i>
                                        <span>{{ __('Blogs') }}</span>
                                    </a>
                                </li>
                            @endcan --}}
                            <!-- pages start -->
                            {{-- @can(['pages.view'])
                                <li class="{{ Nav::isResource('page') }}"><a href="{{ url('page') }}"><i
                                            class="feather icon-file-text"></i><span>{{ __('Pages') }}</span></a>
                                </li>
                            @endcan --}}
                            <!-- pages end -->

                            <!-- report start  -->
                            @canany(['report.progress-report.manage', 'report.quiz-report.manage',
                                'report.revenue-admin-report.manage', 'report.revenue-instructor-report.manage'])
                                <li class="header">{{ __('Reports') }}</li>
                                <li
                                    class="{{ Nav::isResource('user/course/report') }} {{ Nav::isResource('user/question/report') }}{{ url('show/progress/report') }} {{ Nav::isResource('show/quiz/report') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-file-text text-secondary"></i><span>{{ __('adminstaticword.Report') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        <li class="{{ Nav::isResource('show/quiz/report') }}">
                                            <a href="{{ url('show/quiz/report') }}">{{ __('Quiz') }}
                                                {{ __('Report') }} </a>
                                        </li>
                                        {{-- <li class="{{ Nav::isResource('show/progress/report') }}">
                                            <a href="{{ url('show/progress/report') }}">{{ __('Progress') }}
                                                {{ __('Report') }}</a>
                                        </li> --}}

                                        <!-- revenue report start  -->
                                        {{-- <li
                                            class="{{ Nav::isRoute('admin.revenue.report') }} {{ Nav::isRoute('instructor.revenue.report') }}{{ Nav::isResource('device-logs') }}">
                                            <a href="javaScript:void();"><span>{{ __('adminstaticword.Revenue') }}
                                                    {{ __('adminstaticword.Report') }}</span><i
                                                    class="feather icon-chevron-right"></i>
                                            </a>
                                            <ul class="vertical-submenu">

                                                <li class="{{ Nav::isRoute('admin.revenue.report') }}">
                                                    <a
                                                        href="{{ route('admin.revenue.report') }}">{{ __('adminstaticword.AdminRevenue') }}</a>
                                                </li>

                                                <li class="{{ Nav::isRoute('instructor.revenue.report') }}">
                                                    <a
                                                        href="{{ route('instructor.revenue.report') }}">{{ __('adminstaticword.InstructorRevenue') }}</a>
                                                </li>

                                            </ul>
                                        </li> --}}

                                        {{-- <li class="{{ Nav::isResource('admin/report/view') }}">
                                            <a href="{{ route('order.report') }}">
                                                {{ __('Financial reports') }} </a>
                                        </li>

                                        <li class="{{ Nav::isResource('device-logs') }}">
                                            <a href="{{ url('device-logs') }}">{{ __('Device History') }} </a>
                                        </li> --}}
                                    </ul>
                                </li>
                            @endcanany
                            <!-- report end -->
                            <!-- forum -->

                            <!-- faq start  -->
                            {{-- @canany(['faq.faq-student.view', 'faq.faq-instructor.view'])
                                <li class="{{ Nav::isResource('faq') }} {{ Nav::isResource('faqinstructor') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-help-circle text-secondary"></i><span>{{ __('adminstaticword.Faq') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">

                                        <li class="{{ Nav::isResource('faq') }}">
                                            <a href="{{ url('faq') }}">{{ __('adminstaticword.FaqStudent') }}</a>
                                        </li>

                                        <li class="{{ Nav::isResource('faqinstructor') }}">
                                            <a
                                                href="{{ url('faqinstructor') }}">{{ __('adminstaticword.FaqInstructor') }}</a>
                                        </li>

                                    </ul>
                                </li>
                            @endcanany --}}
                            {{-- @can(['career.manage'])
                                <li class="{{ Nav::isRoute('careers.page') }}">
                                    <a href="{{ route('careers.page') }}"><i
                                            class="feather icon-sidebar text-secondary"></i>{{ __('adminstaticword.Career') }}</a>
                                </li>
                            @endcan --}}
                            <!-- faq end -->

                            <!-- location start -->
                            {{-- @canany(['locations.country.view', 'locations.state.view', 'locations.city.view'])
                                <li
                                    class="{{ Nav::isResource('admin/country') }} {{ Nav::isResource('admin/state') }} {{ Nav::isResource('admin/city') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="fa fa-map-marker text-secondary"></i><span>{{ __('Locations') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can(['locations.country.view'])
                                            <li class="{{ Nav::isResource('admin/country') }}"><a
                                                    href="{{ url('admin/country') }}">{{ __('adminstaticword.Country') }}</a>
                                            </li>
                                        @endcan
                                        @can(['locations.state.view'])
                                            <li class="{{ Nav::isResource('admin/state') }}"><a
                                                    href="{{ url('admin/state') }}">{{ __('adminstaticword.State') }}</a>
                                            </li>
                                        @endcan
                                        @can(['locations.city.view'])
                                            <li class="{{ Nav::isResource('admin/city') }}"><a
                                                    href="{{ url('admin/city') }}">{{ __('adminstaticword.City') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany --}}

                            <!-- contact us start -->
                            {{-- @can('contact-us.manage')
                                <li class="{{ Nav::isResource('usermessage') }}"><a href="{{ url('usermessage') }}"><i
                                            class="feather icon-phone-call text-secondary"></i><span>{{ __('adminstaticword.ContactUs') }}</span>
                                    </a>
                                </li>
                            @endcan --}}

                            {{-- @can('job.manage')
                                @if (Module::has('Resume') && Module::find('Resume')->isEnabled())
                                    @include('resume::front.job.admin.icon')
                                @endif
                            @endcan --}}
                            <!-- contact us end -->
                            <!-- location end -->

                            <li class="header">{{ __('Setting') }}</li>
                            {{-- @can(['get-api-key.manage'])
                                <li class="{{ Nav::isRoute('get.api.key') }}">
                                    <a href="{{ route('get.api.key') }}"><i
                                            class="feather icon-share text-secondary"></i><span>{{ __('adminstaticword.GetAPIKeys') }}</span></a>
                                </li>
                            @endcan --}}

                            @can(['currency.view'])
                                <li class="{{ Nav::isRoute('currency.index') }}"><a
                                        href="{{ route('currency.index') }}"><i
                                            class="feather icon-dollar-sign text-secondary"></i><span>{{ __('adminstaticword.Currency') }}</span></a>
                                </li>
                            @endcan

                            {{-- @can(['themes.manage'])
                                <li class="{{ Nav::isRoute('themesettings.index') }}">
                                    <a href="{{ route('themesettings.index') }}">
                                        <i class="feather icon-airplay text-secondary"></i>
                                        <span>{{ __('adminstaticword.Themes') }}</span>
                                    </a>
                                </li>
                            @endcan --}}

                            {{-- @can(['homepage-setting.manage'])
                                <li class="{{ Nav::isRoute('homepage.setting') }}">
                                    <a href="{{ route('homepage.setting') }}"><i
                                            class="feather icon-settings text-secondary"></i><span>{{ __('Homepage Setting') }}</span></a>
                                </li>
                            @endcan --}}
                            <!-- front setting start  -->

                            @canany(['front-settings.testimonial.view', 'front-settings.advertisement.view',
                                'front-settings.sliders.view', 'front-settings.fact-slider.view', 'category-sliders.manage',
                                'get-started.manage', 'front-settings.trusted-sliders.view', 'widget.manage',
                                'front-settings.seo-directory.view', 'coming-soon.manage', 'terms-condition.manage',
                                'privacy-policy.manage', 'invoice-design.manage', 'login-signup.manage',
                                'video-setting.manage', 'breadcum-setting.manage', 'front-settings.fact-slider.view',
                                'join-an-instructor.manage '])
                                <li
                                    class="{{ Nav::isResource('testimonial') }} {{ Nav::isResource('advertisement') }} {{ Nav::isResource('slider') }} {{ Nav::isResource('facts') }} {{ Nav::isRoute('category.slider') }} {{ Nav::isResource('getstarted') }} {{ Nav::isResource('trusted') }} {{ Nav::isRoute('widget.setting') }} {{ Nav::isRoute('terms') }} {{ Nav::isResource('directory') }} {{ Nav::isRoute('videosetting') }} {{ Nav::isRoute('breadcum') }} {{ Nav::isRoute('fact') }} {{ Nav::isRoute('joininstructor') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-monitor text-secondary"></i><span>{{ __('adminstaticword.FrontSetting') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        {{-- @can(['front-settings.testimonial.view'])
                                            <li class="{{ Nav::isResource('testimonial') }}"><a
                                                    href="{{ url('testimonial') }}"><span>{{ __('adminstaticword.Testimonial') }}</span></a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['front-settings.advertisement.view'])
                                            <li class="{{ Nav::isResource('advertisement') }}"><a
                                                    href="{{ url('advertisement') }}"><span>{{ __('adminstaticword.Advertisement') }}</span></a>
                                            </li>
                                        @endcan --}}

                                        @can(['front-settings.sliders.view'])
                                            <li class="{{ Nav::isResource('slider') }}"><a
                                                    href="{{ url('slider') }}"><span>{{ __('adminstaticword.Slider') }}</span></a>
                                            </li>
                                        @endcan

                                        {{-- @can(['front-settings.fact-slider.view'])
                                            <li class="{{ Nav::isResource('facts') }}"><a
                                                    href="{{ url('facts') }}"><span>{{ __('Fact Slider') }}</span></a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['category-sliders.manage'])
                                            <li class="{{ Nav::isRoute('category.slider') }}"><a
                                                    href="{{ route('category.slider') }}"><span>{{ __('adminstaticword.CategorySlider') }}</span></a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['get-started.manage'])
                                            <li class="{{ Nav::isResource('getstarted') }}"><a
                                                    href="{{ url('getstarted') }}">{{ __('adminstaticword.GetStarted') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['front-settings.trusted-sliders.view'])
                                            <li class="{{ Nav::isResource('trusted') }}"><a
                                                    href="{{ url('trusted') }}"><span>{{ __('adminstaticword.TrustedSlider') }}</span></a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['widget.manage'])
                                            <li class="{{ Nav::isRoute('widget.setting') }}"><a
                                                    href="{{ route('widget.setting') }}">{{ __('Widget') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['front-settings.seo-directory.view'])
                                            <li class="{{ Nav::isResource('directory') }}"><a
                                                    href="{{ url('directory') }}"><span>{{ __('adminstaticword.Seo') }}
                                                        {{ __('adminstaticword.Directory') }}</span></a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['coming-soon.manage'])
                                            <li class="{{ Nav::isRoute('comingsoon.page') }}">
                                                <a
                                                    href="{{ route('comingsoon.page') }}">{{ __('adminstaticword.ComingSoon') }}</a>
                                            </li>
                                        @endcan --}}

                                        @can(['terms-condition.manage'])
                                            <li class="{{ Nav::isRoute('termscondition') }}">
                                                <a href="{{ route('termscondition') }}">{{ __('adminstaticword.Terms&Condition') }}
                                                </a>
                                            </li>
                                            <li class="{{ Nav::isRoute('about_us') }}">
                                                <a href="{{ route('about_us') }}">{{ __('adminstaticword.AboutUs') }}</a>
                                            </li>
                                        @endcan

                                        @can(['privacy-policy.manage'])
                                            <li class="{{ Nav::isRoute('policy') }}">
                                                <a
                                                    href="{{ route('policy') }}">{{ __('adminstaticword.PrivacyPolicy') }}</a>
                                            </li>
                                        @endcan

                                        {{-- @can(['invoice-design.manage'])
                                            <li class="{{ Nav::isRoute('invoice/settings') }}">
                                                <a
                                                    href="{{ url('invoice/settings') }}">{{ __('Invoice Design') }}{{ __('') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['login-signup.manage'])
                                            <li class="{{ Nav::isRoute('login') }}">
                                                <a
                                                    href="{{ url('settings/login') }}">{{ __('Login/Signup') }}{{ __('') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['video-setting.manage'])
                                            <li class="{{ Nav::isRoute('videosetting') }}">
                                                <a
                                                    href="{{ route('videosetting') }}">{{ __('Videosetting') }}{{ __('') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['breadcum-setting.manage'])
                                            <li class="{{ Nav::isRoute('breadcum') }}">
                                                <a
                                                    href="{{ url('breadcum/setting') }}">{{ __('Breadcumsetting') }}{{ __('') }}</a>
                                            </li>
                                        @endcan --}}

                                        @can(['front-settings.fact-slider.view'])
                                            <li class="{{ Nav::isRoute('fact') }}">
                                                <a
                                                    href="{{ url('fact') }}">{{ __('Factsetting') }}{{ __('') }}</a>
                                            </li>
                                        @endcan

                                        @can(['join-an-instructor.manage'])
                                            <li class="{{ Nav::isRoute('joininstructor') }}">
                                                <a
                                                    href="{{ url('join/setting') }}">{{ __('Join an Instructor') }}{{ __('') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            <!-- front setting end -->

                            <!-- site setting start  -->
                            @canany(['settings.manage', 'pwa.manage', 'adsense-setting.manage', 'twilio-setting.manage',
                                'site-map-setting.manage', 'site-settings.language.view', 'email-design.manage'])
                                <li
                                    class="{{ Nav::isRoute('gen.set') }} {{ Nav::isRoute('careers.page') }}  {{ Nav::isRoute('termscondition') }} {{ Nav::isRoute('policy') }}  {{ Nav::isRoute('show.pwa') }} {{ Nav::isRoute('adsense') }} {{ Nav::isRoute('ipblock.view') }}   {{ Nav::isRoute('twilio.settings') }} {{ Nav::isRoute('show.sitemap') }} {{ Nav::isRoute('show.lang') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-settings text-secondary"></i><span>{{ __('adminstaticword.SiteSetting') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">

                                        @can(['settings.manage'])
                                            <li class="{{ Nav::isRoute('gen.set') }}">
                                                <a href="{{ route('gen.set') }}">{{ __('adminstaticword.Setting') }}</a>
                                            </li>
                                        @endcan

                                        {{-- @can(['pwa.manage'])
                                            <li class="{{ Nav::isRoute('show.pwa') }}">
                                                <a href="{{ route('show.pwa') }}">{{ __('PWA') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['adsense-setting.manage'])
                                            <li class="{{ Nav::isRoute('adsense') }}">
                                                <a href="{{ url('/admin/adsensesetting') }}">{{ __('Adsense') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @if (isset($gsetting) && $gsetting->ipblock_enable == 1)
                                            <li class="{{ Nav::isRoute('ipblock.view') }}">
                                                <a
                                                    href="{{ url('admin/ipblock') }}">{{ __('adminstaticword.IPBlockSettings') }}</a>
                                            </li>
                                        @endif --}}

                                        {{-- @can(['twilio-setting.manage'])
                                            <li class="{{ Nav::isRoute('twilio.settings') }}">
                                                <a href="{{ route('twilio.settings') }}">{{ __('Twilio') }}</a>
                                            </li>
                                        @endcan --}}

                                        {{-- @can(['site-map-setting.manage'])
                                            <li class="{{ Nav::isRoute('show.sitemap') }}">
                                                <a
                                                    href="{{ route('show.sitemap') }}">{{ __('adminstaticword.SiteMap') }}</a>
                                            </li>
                                        @endcan --}}
                                        @can(['site-settings.language.view'])
                                            <li class="{{ Nav::isRoute('show.lang') }}">
                                                <a href="{{ route('show.lang') }}">{{ __('adminstaticword.Language') }}</a>
                                            </li>
                                        @endcan
                                        {{-- @can(['email-design.manage'])
                                            <li class="{{ Nav::isRoute('maileclipse/mailables') }}">
                                                <a
                                                    href="{{ url('maileclipse/mailables') }}">{{ __('Email Design') }}{{ __('') }}</a>
                                            </li>
                                        @endcan --}}
                                    </ul>
                                </li>
                            @endcanany
                            <!-- site setting end -->

                            <!-- payment setting start -->
                            @canany(['payment-setting-credentials.manage', 'payment-setting-MPESA-setting.manage',
                                'payment-setting-bank-details.manage', 'payment-setting.manual-payment.view,
                                payment-charges.manage'])
                                <li
                                    class=" {{ Nav::isRoute('api.setApiView') }}{{ Nav::isRoute('bank.transfer') }}{{ Nav::isResource('manualpayment') }} ">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-dollar-sign text-secondary"></i><span>{{ __('Payment Setting') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can(['payment-setting-credentials.manage'])
                                            <li class="{{ Nav::isRoute('api.setApiView') }}">
                                                <a href="{{ route('api.setApiView') }}">{{ __('Credentials') }}</a>
                                            </li>
                                        @endcan

                                        @can(['payment-charges.manage'])
                                            <li class="{{ Nav::isRoute('payment-charges.index') }}">
                                                <a
                                                    href="{{ route('payment-charges.index') }}">{{ __('adminstaticword.PaymentCharges') }}</a>
                                            </li>
                                        @endcan

                                        {{-- @if (Module::has('MPesa') && Module::find('MPesa')->isEnabled())
                                            @include('mpesa::admin.sidebar')
                                        @endif
                                        @can(['payment-setting-bank-details.manage'])
                                            <li class="{{ Nav::isRoute('bank.transfer') }}">
                                                <a
                                                    href="{{ route('bank.transfer') }}">{{ __('adminstaticword.BankDetails') }}</a>
                                            </li>
                                        @endcan
                                        @can(['payment-setting.manual-payment.view'])
                                            <li class="{{ Nav::isResource('manualpayment') }}">
                                                <a href="{{ url('manualpayment') }}">{{ __('Manual Payment') }}</a>
                                            </li>
                                        @endcan --}}
                                    </ul>
                                </li>
                            @endcanany
                            <!-- payment setting start end -->

                            <!-- player setting start -->
                            {{-- @canany(['player-settings.manage', 'player-settings.advertise.view'])
                                <li
                                    class="{{ Nav::isRoute('player.set') }} {{ Nav::isRoute('ads') }} {{ Nav::isRoute('ad.setting') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-play-circle text-secondary"></i><span>{{ __('adminstaticword.PlayerSettings') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can(['player-settings.manage'])
                                            <li class="{{ Nav::isRoute('player.set') }}"><a
                                                    href="{{ route('player.set') }}">{{ __('adminstaticword.PlayerCustomization') }}</a>
                                            </li>
                                        @endcan

                                        <li class="{{ Nav::isRoute('ads') }}"><a href="{{ url('admin/ads') }}"
                                                title="Create ad">{{ __('adminstaticword.Advertise') }}</a></li>
                                        @php $ads = App\Ads::all(); @endphp
                                        @can(['player-settings.advertise.view'])
                                            @if ($ads->count() > 0)
                                                <li class="{{ Nav::isRoute('ad.setting') }}"><a
                                                        href="{{ url('admin/ads/setting') }}"
                                                        title="Ad Settings">{{ __('adminstaticword.AdvertiseSettings') }}</a>
                                                </li>
                                            @endif
                                        @endcan

                                    </ul>
                                </li>
                            @endcanany --}}
                            <!-- player setting start end -->

                            @if (isset($gsetting) && $gsetting->activity_enable == '1')
                                <li class="{{ Nav::isRoute('activity.index') }}"><a
                                        href="{{ route('activity.index') }}">
                                        <i
                                            class="feather icon-help-circle text-secondary"></i><span>{{ __('adminstaticword.ActivityLog') }}</span>
                                    </a></li>
                            @endif

                            @if (Auth::User()->role == 'admin')
                                <li class="{{ Nav::isRoute('blocked.users') }}"><a
                                        href="{{ route('blocked.users') }}">
                                        <i
                                            class="feather icon-user text-secondary"></i><span>{{ __('Blocked Users') }}</span>
                                    </a></li>
                            @endif

                            <!-- help & support start  -->
                            {{-- @can(['addon.view'])
                                <li class="header">{{ __('Support') }}</li>

                                <li class="{{ Nav::isResource('admin-addon') }}">
                                    <a href="{{ url('admin/addon') }}"> <i
                                            class="feather icon-move  text-secondary"></i><span>{{ __('adminstaticword.Addon') }}</span>
                                        {{ __('adminstaticword.Manager') }}</a>
                                </li>
                            @endcan

                            @can(['update-process.manage'])
                                <li class="{{ Nav::isRoute('update.process') }}">
                                    <a href="{{ route('update.process') }}"><i
                                            class="feather icon-share text-secondary"></i><span>{{ __('adminstaticword.UpdateProcess') }}</span></a>
                                </li>
                            @endcan

                            @canany(['help-support-import-demo.manage', 'help-support-database-backup.manage', 'help-support-remove-public.manage', 'help-support-clear-cache.manage'])
                                <li class="{{ Nav::isRoute('import.view') }} {{ Nav::isRoute('database.backup') }} ">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-help-circle text-secondary"></i><span>{{ __('adminstaticword.Help&Support') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">
                                        @can(['help-support-import-demo.manage'])
                                            <li class="{{ Nav::isRoute('import.view') }}">
                                                <a
                                                    href="{{ route('import.view') }}">{{ __('adminstaticword.ImportDemo') }}</a>
                                            </li>
                                        @endcan
                                        @can(['help-support-database-backup.manage'])
                                            <li class="{{ Nav::isRoute('database.backup') }}">
                                                <a
                                                    href="{{ route('database.backup') }}">{{ __('adminstaticword.DatabaseBackup') }}</a>
                                            </li>
                                        @endcan
                                        @can(['help-support-remove-public.manage'])
                                            <li class="{{ Nav::isRoute('remove.public') }}">
                                                <a
                                                    href="{{ route('remove.public') }}">{{ __('adminstaticword.RemovePublic') }}</a>
                                            </li>
                                        @endcan
                                        @can(['help-support-clear-cache.manage'])
                                            <li class="{{ Nav::isRoute('clear-cache') }}">
                                                <a
                                                    href="{{ url('clear-cache') }}">{{ __('adminstaticword.ClearCache') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany --}}
                            <!-- help & support end -->

                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Navigationbar -->
    </div>
    <!-- End Sidebar -->
</div>
