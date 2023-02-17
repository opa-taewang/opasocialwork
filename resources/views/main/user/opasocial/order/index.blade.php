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
        <div class="col-12 mb-3 d-block d-md-none">
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


        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table id="orders" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th data-priority="1">Service</th>
                                        <th data-priority="1">Link</th>
                                        <th data-priority="1">Status</th>
                                        <th data-priority="1">Charge</th>
                                        <th data-priority="1">Quantity</th>
                                        <th data-priority="1">Remains</th>
                                        <th data-priority="1">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach ($orders as $order)
                                        <tr>
                                            <th>
                                                {{$order->id}}
                                                @if (getRefill($order))
                                                    <br><a href="/orders/' . $order->id . '/refill" class="btn btn-sm btn-success ">Refill</a>
                                                @endif
                                            </th>
                                            <td>{{$order->package->id.' - '.truncate($order->package->name, 30)}}</td>
                                            <td>
                                                <a class="copyBtn" data-clipboard-text="{{$order->link}}" style="cursor: pointer" title="Click here to copy the full link"><i class="mdi mdi-content-copy"></i></a>
                                                {{truncate($order->link, 60)}}
                                            </td>
                                            <td>{{$order->status}}</td>
                                            <td>{{convertCurrency($order->price)}}</td>
                                            <td>{{$order->quantity}} (Count: {{$order->start_counter}})</td>
                                            {{-- data-original-title="This order started at 3 and the remain is 0" --}}
                                            <td>{{$order->remains}}</td>
                                            <td>{{setToTimezone("D d M", $order->created_at, auth()->user()->timezone)}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                         <div class="d-flex justify-content-center">
                            {!! $orders->links() !!}
                        </div>
                    </div>


                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
{{-- End Table --}}

@include('main.user.ajax.order-details')
@endsection

@section('script')
 <!-- Responsive Table js -->
    <script src="{{ asset('/assets/libs/rwd-table/rwd-table.min.js') }}"></script>

    <!-- Init js -->
    <script src="{{ asset('/assets/js/pages/table-responsive.init.js') }}"></script>
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}


@endsection
