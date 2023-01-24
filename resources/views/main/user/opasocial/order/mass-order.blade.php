@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('title') Dashboard @endslot
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
                        <a class="nav-link" href="{{route('user.order.new')}}" >
                            <span class="d-block d-sm-none"><i class="fa-solid fa-cart-plus"></i></span>
                            <span class="d-none d-sm-block">New Order</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#massOrder" role="tab">
                            <span class="d-block d-sm-none"><i class="fa-solid fa-layer-group"></i></span>
                            <span class="d-none d-sm-block">Mass Order</span>
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane active" id="massOrder" role="tabpanel">
                        <p class="mb-0">
                            Raw denim you probably haven't heard of them jean shorts Austin.
                            Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache
                            cliche tempor, williamsburg carles vegan helvetica. Reprehenderit
                            butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi,
                            qui irure terry richardson ex squid. Aliquip placeat salvia cillum
                            iphone. Seitan aliquip quis cardigan american apparel, butcher
                            voluptate nisi qui.
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
                        <a class="nav-link active" data-bs-toggle="tab" href="#home-1" role="tab">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">Home</span>
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#profile-1" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#messages-1" role="tab">
                            <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                            <span class="d-none d-sm-block">Messages</span>
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#settings-1" role="tab">
                            <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                            <span class="d-none d-sm-block">Settings</span>
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane active" id="home-1" role="tabpanel">
                        <p class="mb-0">
                            Raw denim you probably haven't heard of them jean shorts Austin.
                            Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache
                            cliche tempor, williamsburg carles vegan helvetica. Reprehenderit
                            butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi,
                            qui irure terry richardson ex squid. Aliquip placeat salvia cillum
                            iphone. Seitan aliquip quis cardigan american apparel, butcher
                            voluptate nisi qui.
                        </p>
                    </div>
                    <div class="tab-pane" id="profile-1" role="tabpanel">
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
                    <div class="tab-pane" id="messages-1" role="tabpanel">
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
                    <div class="tab-pane" id="settings-1" role="tabpanel">
                        <p class="mb-0">
                            Trust fund seitan letterpress, keytar raw denim keffiyeh etsy
                            art party before they sold out master cleanse gluten-free squid
                            scenester freegan cosby sweater. Fanny pack portland seitan DIY,
                            art party locavore wolf cliche high life echo park Austin. Cred
                            vinyl keffiyeh DIY salvia PBR, banh mi before they sold out
                            farm-to-table.
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


@endsection
