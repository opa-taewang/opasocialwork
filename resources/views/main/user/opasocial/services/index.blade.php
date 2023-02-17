@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('title') Orders @endslot
    @endcomponent


{{-- Start Table --}}
    <div class="row">
        <form method="get" action="{{route('user.order.search')}}">
            <div class="col-12 mb-3">
                <div class="input-group mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa-solid fa-magnifying-glass font-size-20"></i>
                        </span>
                    </div>
                    <input type="search" name="search" value="{{Request::input('search')}}" class="form-control">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="submit" id="orderSearch">Search</button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Mobile filter orders --}}
        <div class="col-12 mb-3">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-filter"></i> Filter Status <i class="mdi mdi-chevron-down"></i></button>
                <div class="dropdown-menu">
                    <a class="dropdown-item {{ request()->path() == 'orders' ? 'active' : '' }}" href="/orders">All</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/pending' ? 'active' : '' }}" href="{{ route('user.order.filter','pending') }}">Pending</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/inprogress' ? 'active' : '' }}" href="{{ route('user.order.filter','inprogress') }}">In progress</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/processing' ? 'active' : '' }}" href="{{ route('user.order.filter','processing') }}">Processing</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/completed' ? 'active' : '' }}" href="{{ route('user.order.filter','completed') }}">Completed</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/partial' ? 'active' : '' }}" href="{{ route('user.order.filter','partial') }}">Partial</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/refunded' ? 'active' : '' }}" href="{{ route('user.order.filter','refunded') }}">Refunded</a>
                    <a class="dropdown-item {{ request()->path() == 'orders/filter/cancelled' ? 'active' : '' }}" href="{{ route('user.order.filter','cancelled') }}">Canceled</a>
                                                </div>
</div>
        </div>
        {{-- Desktop filter show --}}
        <div class="col-12 mb-3 d-none d-md-block">
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item {{ request()->path() == 'orders' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.show') }}">ALL</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/pending' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','pending') }}">Pending</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/inprogress' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','inprogress') }}">InProgress</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/processing' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','processing') }}">Processing</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/completed' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','completed') }}">Completed</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/partial' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','partial') }}">Partial</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/refunded' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','refunded') }}">Refunded</a>
                </li>
                <li class="list-group-item {{ request()->path() == 'orders/filter/cancelled' ? 'list-group-item-primary' : '' }}">
                    <a href="{{ route('user.order.filter','cancelled') }}">Cancelled</a>
                </li>
            </ul>
        </div>


        <div class="col-xl-12">
            @foreach ($services as $service)
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">{{$service->name}}</h4>
                         @php
                            $data = getPackages($service);
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-striped-columns mb-0">

                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Service</th>
                                        <th>Rate per 1000</th>
                                        <th>Min / Max</th>
                                        <th>Refill</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($packages as $package)
                                    @if($package->service_id == $service->id)
                                        @php
                                            $price = isset($userPackagePrices[$package->id]) ? $userPackagePrices[$package->id] : $package->price_per_item;
                                            if (in_array($package->id, $package_ids)) {
                                                $price=number_formats($price-($price/100)*$group->price_percentage,2);
                                                                // $price=getConvertedRate($price);
                                            }
                                        $favouriteStatus = in_array($package->id, $favorite_pkgs) ? 1 : 0;
                                        @endphp
                                        <tr>
                                            <th scope="row">
                                                {{$package->id}}
                                                <br>
                                                {{-- v-b-tooltip.hover title="Add to cart"  --}}
                                                <favourite-service package-id="{{$package->id}}" status="{{$favouriteStatus}}"></favourite-service>
                                                {{-- <i onclick="addToFavorite({{$service->id}},{{$package->id}},this)" class="{{(in_array($package->id, $favorite_pkgs))?'fas':'far'}} fa-heart"></i> --}}
                                            </th>
                                            <td>{{$package->name}}</td>
                                            <td>{{convertCurrency($price * 1000)}}</td>
                                             <td>{{$package->minimum_quantity.' / '. $package->maximum_quantity}}</td>
                                            <td>
                                                <button type="button" class="btn {{$package->refillbtn ==1 ? 'btn-success' : 'btn-secondary'}} btn-sm">{{$package->refillbtn ==1 ? 'Refill' : 'No Refill'}}</button>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#description{{ $package->id }}">
                                                    <i class="mdi mdi-information"></i> Details
                                                </button>
                                                <div class="modal fade bs-example-modal-sm" id="description{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="mySmallModalLabel">{{$package->name}}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {!!$package->description!!}
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            </td>
                                        </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            @endforeach
        </div><!--end col-->
    </div> <!-- end row -->
{{-- End Table --}}

@include('main.user.ajax.order-details')
@endsection

@section('script')
<script>
    function aClass( classname, element ) {
        var cn = element.className;
        if( cn.indexOf( classname ) != -1 ) {
            return;
        }
        if( cn != '' ) {
            classname = ' '+classname;
        }
        element.className = cn+classname;
    }
    function rClass( classname, element ) {
        var cn = element.className;
        var rxp = new RegExp( "\\s?\\b"+classname+"\\b", "g" );
        cn = cn.replace( rxp, '' );
        element.className = cn;
    }
    function addToFavorite(sid,pid,element) {
        $("#p"+pid+" > i.fa-heart").css("visibility","hidden");
        $("#p"+pid).append('<i id="spin'+pid+'" class="fa fa-spinner fa-spin" style="font-size:20px;margin-right: 26px;"></i>');
        $.ajax({
            type: "POST",
            url: document.location.origin+"/addtofavorite",
            data: {
                sid:sid,
                pid:pid,
                _token: '{{csrf_token()}}'},
                success: function(data){
                    console.log(data)
                            if(data=="true"){
                                aClass("fas",element);
                            }
                            else{
                                rClass("fas",element);
                                aClass("far",element);
                            }
                            $("#p"+pid+" > i#spin"+pid).remove();
                            $("#p"+pid+" > i.fa-heart").css("visibility","visible");
                        },
                error: function (error) {
                            $("#p"+pid+" > i#spin"+pid).remove();
                            $("#p"+pid+" > i.fa-heart").css("visibility","visible");
                        }
            });
        }
</script>
@endsection
