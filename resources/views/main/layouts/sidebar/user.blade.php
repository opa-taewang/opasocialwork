<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{route('user.dashboard')}}" class="waves-effect {{ request()->path() == 'dashboard' ? 'active' : '' }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">@lang('Dashboards')</span>
                    </a>
                </li>

                <li class="menu-title" key="t-menu">@lang('OPASOCIAL')</li>

                <li>
                    <a href="{{route('user.order.new')}}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-new-order">@lang('New Order')</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-history">@lang('History')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="{{route('user.order.show')}}" class="waves-effect {{ request()->path() == 'orders' ? 'active' : '' }}">@lang('Order History')</a></li>
                        <li><a href="{{route('user.dripfeed.show')}}" class="{{ request()->path() == 'dripfeeds' ? 'active' : '' }}">@lang('Drip feed')</a></li>
                        <li><a href="{{route('user.subscription.show')}}" class="{{ request()->path() == 'subscriptions' ? 'active' : '' }}">@lang('Subscription')</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{route('user.services.show')}}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-new-order">@lang('Services')</span>
                    </a>
                </li>

{{--
    Opaverify side bar starts here
    --}}
                <li class="menu-title" key="t-apps">@lang('OPAVERIFY(Number)')</li>

               <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-new-verification">@lang('New Verification')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('user.order.show')}}" class="waves-effect {{ request()->path() == 'dashboard' ? 'active' : '' }}">@lang('Temporary Number')</a></li>
                        <li><a href="{{route('user.dripfeed.show')}}" key="t-full-calendar">@lang('Rental')</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-verification-history">@lang('Verificatin History')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('user.order.show')}}"class="waves-effect {{ request()->path() == 'dashboard' ? 'active' : '' }}">@lang('Temporary History')</a></li>
                        <li><a href="{{route('user.dripfeed.show')}}" key="t-full-calendar">@lang('Rental History')</a></li>
                    </ul>
                </li>

{{-- OPA VERIFY SIDE BAR ENDS HERE
                     --}}

                <li class="menu-title" key="t-apps">@lang('GENERAL')</li>

                <li>
                    <a href="{{route('user.add-funds')}}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-add-funds">@lang('Add Funds')</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-resellers">@lang('Resellers')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('user.make.money')}}" class="waves-effect {{ request()->path() == 'makemoney' ? 'active' : '' }}"key="t-tui-calendar">@lang('Make Money')</a></li>
                        <li><a href="{{route('user.affiliate.show')}}" class="waves-effect {{ request()->path() == 'affiliates' ? 'active' : '' }}"key="t-full-calendar">@lang('Affliates')</a></li>
                        <li><a href="{{route('user.childpanel.show')}}" class="waves-effect {{ request()->path() == 'child-panel' ? 'active' : '' }}"key="t-full-calendar">@lang('Child Panel')</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-calendar"></i>
                        <span key="t-support">@lang('Support')</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('user.ticket.create')}}" class="waves-effect {{ request()->path() == 'ticket.create' ? 'active' : '' }}"key="t-tui-calendar">@lang('Create Ticket')</a></li>
                        <li><a href="{{route('user.ticket.index')}}" class="waves-effect {{ request()->path() == 'ticket' ? 'active' : '' }}"key="t-full-calendar">@lang('Tickets History')</a></li>
                    </ul>
                </li>

                {{-- <li>
                    <a href="{{route('user.faqs')}}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-faqs">@lang('Blog')</span>
                    </a>
                </li> --}}

                <li>
                    <a href="{{route('user.faqs')}}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-faqs">@lang('FAQs')</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('user.api')}}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-new-order">@lang('API')</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
