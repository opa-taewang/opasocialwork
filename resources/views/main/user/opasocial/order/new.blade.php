@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('css')
<!-- owl.carousel css -->
<link rel="stylesheet" href="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.css') }}">
@endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('li_2') Order @endslot
        @slot('title') New @endslot
    @endcomponent


<div class="row">
    {{-- small card --}}
    <div class="col-xl-12">
        <div class="row">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">@lang('TOTAL ORDERS ON OPASOCIAL')</p>
                                <h4 class="mb-0">{{$orders}}</h4>
                            </div>

                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="bx bx-cart-alt font-size-24"></i>
                                        {{-- <i class="fas fa-shopping-cart font-size-24"></i> --}}
                                        {{-- <i class="fas fa-arrow-circle-right font-size-24"></i> --}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">@lang('OPASOCIAL POINTS')</p>
                                <h4 class="mb-0">{{Auth::user()->points}}</h4>
                            </div>

                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="bx bx-credit-card font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">@lang('START RESELLING TODAY')</p>
                                <h4 class="mb-0"><a href="{{route('user.make.money')}}">@lang('MAKE MONEY')</a></h4>
                            </div>

                            <div class="flex-shrink-0 align-self-center ">
                                <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-dollar font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">@lang('ACCOUNT STATUS')</p>
                                <h4 class="mb-0">474</h4>
                            </div>

                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-user-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     {{-- Small card for info ends --}}
</div>
<!-- end row -->

  {{-- Order Tab --}}

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#newOrder" role="tab">
                            <span class="d-block d-sm-none"><i class="fa-solid fa-cart-plus"></i></span>
                            <span class="d-none d-sm-block">New Order</span>
                        </a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/massorder" >
                            <span class="d-block d-sm-none"><i class="fa-solid fa-layer-group"></i></span>
                            <span class="d-none d-sm-block">Mass Order</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#favourite" role="tab">
                            <span class="d-block d-sm-none"><i class="fa-solid fa-heart"></i></span>
                            <span class="d-none d-sm-block">Favourite</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#subscription" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-hand-pointer"></i></i></span>
                            <span class="d-none d-sm-block">Auto Subscription</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#search" role="tab">
                            <span class="d-block"><i class="fas fa-search"></i></span>
                            {{-- <span class="d-none d-sm-block">Search By Id</span> --}}
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="newOrder" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                               @if (Session::has('alert'))
                                    @php
                                        $alert = Session::get('alert');
                                    @endphp
                                   <div class="alert alert-{{$alert->class}}" role="alert">
                                        <h5 class="alert-heading"><i class="fa-regular fa-face-{{$alert->class == 'danger' ? 'sad-tear' :'smile'}}"></i> {{$alert->title}}</h5>
                                        <p>{!! nl2br(e($alert->message)) !!}</p>
                                    </div>
                               @endif
                               {{-- class="form-control @error('subject') is-invalid @enderror" --}}
                                {{-- New order Vue  --}}
                                <new-order></new-order>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="favourite" role="tabpanel">
                        <p class="mb-0">
                            Food truck fixie locavore, accusamus mcsweeney
                        </p>
                    </div>
                    <div class="tab-pane" id="subscription" role="tabpanel">
                        <p class="mb-0">
                            Etsy mixtape wayfarers
                        </p>
                    </div>
                    <div class="tab-pane" id="search" role="tabpanel">
                        <p class="mb-0">
                            Trust fund seitan letterpress
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">

                <!-- Nav tabs -->
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#latestNews" role="tab">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">Latest News</span>
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#pleaseRead" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">Please Read</span>
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#realTimeUpdate" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">Real Time Updates</span>
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#contactUs" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                            <span class="d-none d-sm-block">Contact Us</span>
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane active" id="latestNews" role="tabpanel">

                        {{-- Mobile view --}}
                        <div class="card d-block d-sm-none">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Note from admin</h4>
                                <div class="hori-timeline">
                                    <div class="owl-carousel owl-theme  navs-carousel events" id="timeline-carousel">
                                         @foreach ($broadcasts as $broadcast)
                                        <div class="item  event-list @if($broadcasts_latest->id === $broadcast->id) active @endif">
                                            <div class="row col-lg-12">
                                                <div class="event-date">
                                                    <div class="text-primary mb-1">{{setToTimezone("D d M", $broadcast->StartTime, auth()->user()->timezone)}}</div>
                                                    <h5 class="mb-4">{{$broadcast->MsgTitle}}</h5>
                                                </div>
                                                <div class="event-down-icon">
                                                    <i class="bx bx-down-arrow-circle h1 text-primary down-arrow-icon"></i>
                                                </div>

                                                <div class="mt-3 px-3">
                                                    <p class="text-muted">{{htmlToPlainText($broadcast->MsgText)}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Desktop view --}}
                        <div class="card d-none d-sm-block">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Note from admin</h4>
                                <ul class="verti-timeline list-unstyled">
                                    @foreach ($broadcasts as $broadcast)
                                        <li class="event-list @if($broadcasts_latest->id === $broadcast->id) active @endif">
                                            <div class="event-timeline-dot">
                                                <i class=" font-size-18 @if($broadcasts_latest->id === $broadcast->id) bx bxs-right-arrow-circle bx-fade-right @else  bx bx-right-arrow-circle @endif"></i>
                                            </div>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <h5 class="font-size-14">{{setToTimezone("d M", $broadcast->StartTime, auth()->user()->timezone)}} <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                </div>
                                                <div class="mr-0 flex-grow-3">
                                                     <div>
                                                        <p class="fw-bold">{{$broadcast->MsgTitle}}</p>
                                                    </div>
                                                    <div>
                                                        {{htmlToPlainText($broadcast->MsgText)}}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fw-bold" id="pleaseRead" role="tabpanel">
                        <p class="">
                        Some clients place orders with us for huge accounts, for example they order 1,000 followers for an account with tens or hundreds or thousands of followers,
                        such accounts are bound to drop at some point due to the large numbers of previously bought followers, and would not be eligible for refill
                        or refund if the order dint complete or drops.</p>
                        <p>Once an order is entered it cannot be cancelled unless the link entered
                            is for a different service, for example a YouTube link in a Facebook service.
                        </p>
                        <p class="mb-0">
                            Do not place duplicate orders for same link on our site,
                            otherwise both orders will get completed but all likes/followers/views will not be given. We cannot do anything in such case.
                        </p>
                    </div>
                    <div class="tab-pane" id="realTimeUpdate" role="tabpanel">
                        <p class="mb-0">
                            Food truck fixie locavore, accusamus mcsweeney's marfa nulla
                            single-origin coffee squid. Exercitation +1 labore velit, blog
                            sartorial PBR leggings next level wes anderson artisan four loko
                            farm-to-table craft beer twee. Qui photo booth letterpress,
                            commodo enim craft beer mlkshk aliquip jean shorts ullamco ad
                            vinyl cillum PBR. Homo nostrud organic, assumenda labore
                            aesthetic magna 8-bit.
                        </p>
                    </div>
                    <div class="tab-pane" id="contactUs" role="tabpanel">
                        <p class="mb-0">
                            Etsy mixtape wayfarers, ethical wes anderson tofu before they
                            sold out mcsweeney's organic lomo retro fanny pack lo-fi
                            farm-to-table readymade. Messenger bag gentrify pitchfork
                            tattooed craft beer, iphone skateboard locavore carles etsy
                            salvia banksy hoodie helvetica. DIY synth PBR banksy irony.
                            Leggings gentrify squid 8-bit cred pitchfork. Williamsburg banh
                            mi whatever gluten-free.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- end modal -->



@endsection

@section('script')
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}

<script src="{{ asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>

<!-- timeline init js -->
<script src="{{ asset('/assets/js/pages/timeline.init.js') }}"></script>

@endsection
