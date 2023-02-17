@extends('main.layouts.master')

@section('title') @lang('Support') @endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('li_2') Ticket @endslot
        @slot('title') All  @endslot
    @endcomponent

{{-- Section goes here --}}
<div class="row">
    <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">General Info</h4>
                    <div class="card-title-desc">
                    <b>
                        <span class="text-info">Support Hours: </span>
                        <br />
                        <span class="">Business Hours (Mon - Sat) 10:00 AM to 10:00 PM WAT( West African Time)</span>
                        </br/>
                        <span class="text-info">Your order not started?</span>
                        <br />
                        <span class=""> First be sure you added it in right format!</span>
                        <br />
                        <span class="text-info">Reporting an order related problem ? </span>
                        <br />
                        <span class="">Be sure your order is at least 24 hours old as we check a order manually only if its 24 hours old even if the service is supposed to be instant!</span>
                        <br />
                        <span class="text-info">Your order is Partial?</span>
                        <br />
                        <span class="">If status Partial it means system can't give more likes/followers/views to current page and money automatically refunded for remains likes/followers/views. It can happen due to server problem or if your total order is more then servers total capacity . Please order in different server in that case or you can try placing it again 12-24 hours later.</span>
                    </b>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>SUBJECT (Ticket ID)</th>
                                <th>Description</th>
                                <th>STATUS</th>
                                <th>LAST UPDATED</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td>{{$ticket->subject}} (#{{$ticket->id}})</td>
                                    <td><a href="{{route('user.ticket.show', $ticket->id)}}">{{$ticket->description}}</a></td>
                                    <td>{{$ticket->status}}</td>
                                    <td class="text-muted">{{setToTimezone("dS M y h:m a", $ticket->updated_at, auth()->user()->timezone)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div> <!-- end col -->
</div>

@endsection

@section('script')
<!-- Required datatable js -->
    <script src="{{ asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ asset('/assets/js/pages/datatables.init.js') }}"></script>

    <script>
        $('#datatable').DataTable({
            // "ordering": false,
            "aaSorting": []
        });
    </script>
@endsection
