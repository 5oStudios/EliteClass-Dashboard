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

                            @can(['marketing-dashboard.manage'])
                                <li class="{{ Nav::isRoute('market.index') }}">
                                    <a class="nav-link" href="{{ route('market.index') }}">
                                        <i class="feather icon-pie-chart text-secondary"></i>
                                        <span>{{ __('Marketing Dashboard') }}</span>
                                    </a>
                                </li>
                            @endcan

                            <!-- Category start  -->
                            @canany(['categories.view', 'subcategories.view', 'childcategories.view'])
                                <li class="header">{{ __('Home') }}</li>

                                @can(['categories.view'])
                                    <li class="{{ Nav::isResource('category') }}"><a href="{{ url('category') }}"><i
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

                            @canany(['users.view', 'Alluser.view', 'Allinstructor.view', 'role.view'])
                                <li class="header header-one">{{ __('Users') }}</li>
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
                                            </li>
                                        @endcan
                                        @can(['role.view'])
                                            <li>
                                                <a class="{{ Nav::isResource('roles') }}"
                                                    href="{{ route('roles.index') }}">{{ __('Roles And Permission') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany


                            <!-- user end -->

                            @canany(['courses.view', 'bundle-courses.view', 'meetings.big-blue.view',
                                'meetings.meeting-recordings.view', 'in-person-session.view'])

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
                                            class="{{ Nav::isRoute('meeting.create') }} {{ Nav::isRoute('meeting.show') }} {{ Nav::isRoute('bbl.setting') }} {{ Nav::isRoute('bbl.all.meeting') }} {{ Nav::isResource('meeting-recordings') }}">
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

                                                        @can(['meetings.big-blue.settings'])
                                                            <li class="{{ Nav::isRoute('bbl.setting') }}"><a
                                                                    href="{{ route('bbl.setting') }}">{{ __('Settings') }}</a>
                                                            </li>
                                                        @endcan
                                                        @can(['meetings.big-blue.view'])
                                                            <li class="{{ Nav::isRoute('bbl.all.meeting') }}"><a
                                                                    href="{{ route('bbl.all.meeting') }}">{{ __('adminstaticword.ListMeetings') }}</a>
                                                            </li>
                                                        @endcan
                                                        @can(['meetings.meeting-recordings.view'])
                                                            <li class="{{ Nav::isRoute('download.meeting') }}"><a
                                                                    href="{{ route('download.meeting') }}">{{ __('Recorded') }}</a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </li>
                                                <!-- BigBlueMeetings end  -->
                                            </ul>
                                        </li>
                                    @endif
                                @endcanany
                                @can(['in-person-session.view'])
                                    <li class="{{ Nav::isRoute('offline.sessions.index') }}">
                                        <a href="{{ route('offline.sessions.index') }}">
                                            <i class="feather icon-clock text-secondary"></i>
                                            <span>{{ __('In-Person Sessions') }}</span>
                                        </a>
                                    </li>
                                @endcan
                            @endcanany
                            <!--===================meeting end====================================  -->

                            @can(['coupons.view'])
                                <li class="header">{{ __('Marketing') }}</li>
                                <li class="{{ Nav::isResource('coupon') }}"><a href="{{ url('coupon') }}"><i
                                            class="feather icon-award text-secondary"></i><span>{{ __('adminstaticword.Coupon') }}</span></a>
                                </li>
                            @endcan

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

                            @can(['orders.manage'])
                                <li class="header">{{ __('Financial') }}</li>

                                <li
                                    class="{{ Nav::isResource('full-payments') }} {{ Nav::isResource('installments') }} {{ Nav::isResource('invoices') }}">
                                    <a href="javaScript:void();">
                                        <i class="feather icon-book text-secondary"></i><span>{{ __('Invoices') }}</span><i
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

                            <!-- report start  -->
                            @canany(['report.quiz-report.manage'])
                                <li class="header">{{ __('Reports') }}</li>
                                <li class="{{ Nav::isResource('show/quiz/report') }}">
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
                                    </ul>
                                </li>
                            @endcanany
                            <!-- report end -->

                            <li class="header">{{ __('Setting') }}</li>

                            @can(['currency.view'])
                                <li class="{{ Nav::isRoute('currency.index') }}"><a
                                        href="{{ route('currency.index') }}"><i
                                            class="feather icon-dollar-sign text-secondary"></i><span>{{ __('adminstaticword.Currency') }}</span></a>
                                </li>
                            @endcan

                            @canany(['front-settings.sliders.view', 'terms-condition.manage', 'privacy-policy.manage'])
                                <li class="{{ Nav::isResource('slider') }} {{ Nav::isRoute('terms') }}">
                                    <a href="javaScript:void();">
                                        <i
                                            class="feather icon-monitor text-secondary"></i><span>{{ __('adminstaticword.FrontSetting') }}</span><i
                                            class="feather icon-chevron-right"></i>
                                    </a>
                                    <ul class="vertical-submenu">

                                        @can(['front-settings.sliders.view'])
                                            <li class="{{ Nav::isResource('slider') }}"><a
                                                    href="{{ url('slider') }}"><span>{{ __('adminstaticword.Slider') }}</span></a>
                                            </li>
                                        @endcan

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
                                    </ul>
                                </li>
                            @endcanany
                            <!-- front setting end -->

                            <!-- site setting start  -->
                            @canany(['settings.manage', 'site-settings.language.view'])
                                <li class="{{ Nav::isRoute('gen.set') }} {{ Nav::isRoute('show.lang') }}">
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

                                        @can(['site-settings.language.view'])
                                            <li class="{{ Nav::isRoute('show.lang') }}">
                                                <a href="{{ route('show.lang') }}">{{ __('adminstaticword.Language') }}</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                            <!-- site setting end -->

                            <!-- payment setting start -->
                            @canany(['payment-setting-credentials.manage', 'payment-charges.manage'])
                                <li
                                    class="{{ Nav::isRoute('api.setApiView') }} {{ Nav::isRoute('payment-charges.index') }}">
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
                                    </ul>
                                </li>
                            @endcanany
                            <!-- payment setting start end -->

                            @can(['blocked-users.manage'])
                                <li class="{{ Nav::isRoute('blocked.users') }}">
                                    <a href="{{ route('blocked.users') }}">
                                        <i
                                            class="feather icon-user text-secondary"></i><span>{{ __('Blocked Users') }}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Navigationbar -->
    </div>
    <!-- End Sidebar -->
</div>
