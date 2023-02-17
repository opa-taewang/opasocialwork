@extends('main.layouts.master')

@section('title') @lang(' Get Support') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('li_2') Ticket @endslot
        @slot('title') New  @endslot
    @endcomponent

{{-- Section goes here --}}
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Vertical Nav Tabs</h4>
                <p class="card-title-desc">Example of Vertical nav tabs</p>

                <div class="row">
                    <div class="col-md-3">
                        <div class="nav flex-column nav-pills" id="support-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link mb-2 active" id="order-tab" data-bs-toggle="pill" href="#order" role="tab" aria-controls="order" aria-selected="true">Order</a>
                            <a class="nav-link mb-2" id="payment-tab" data-bs-toggle="pill" href="#payment" role="tab" aria-controls="payment" aria-selected="false">Payment</a>
                            <a class="nav-link mb-2" id="child-panel-tab" data-bs-toggle="pill" href="#child-panel" role="tab" aria-controls="child-panel" aria-selected="false">Child Panel</a>
                            <a class="nav-link" id="api-tab" data-bs-toggle="pill" href="#api" role="tab" aria-controls="api" aria-selected="false">API</a>
                            <a class="nav-link mb-2" id="bug-tab" data-bs-toggle="pill" href="#bug" role="tab" aria-controls="bbug" aria-selected="true">Bug</a>
                            <a class="nav-link mb-2" id="request-tab" data-bs-toggle="pill" href="#request" role="tab" aria-controls="request" aria-selected="false">Request</a>
                            <a class="nav-link mb-2" id="point-tab" data-bs-toggle="pill" href="#point" role="tab" aria-controls="point" aria-selected="false">Point</a>
                            <a class="nav-link mb-2" id="number-tab" data-bs-toggle="pill" href="#number" role="tab" aria-controls="number" aria-selected="false">Number</a>
                            <a class="nav-link" id="other-tab" data-bs-toggle="pill" href="#other" role="tab" aria-controls="other" aria-selected="false">Other</a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content text-muted mt-4 mt-md-0" id="support-tabContent">
                            <div class="tab-pane fade show active" id="order" role="tabpanel" aria-labelledby="order-tab">
                                <support-order></support-order>
                            </div>
                            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                                <support-payment></support-payment>
                            </div>
                            <div class="tab-pane fade" id="child-panel" role="tabpanel" aria-labelledby="child-panel-tab">
                                <support-child-panel></support-child-panel>
                            </div>
                            <div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">
                                <support-api></support-api>
                            </div>
                            <div class="tab-pane fade" id="bug" role="tabpanel" aria-labelledby="bug-tab">
                                <support-bug></support-bug>
                            </div>
                            <div class="tab-pane fade" id="request" role="tabpanel" aria-labelledby="request-tab">
                                <support-request></support-request>
                            </div>
                            <div class="tab-pane fade" id="point" role="tabpanel" aria-labelledby="point-tab">
                                <support-point></support-point>
                            </div>
                            <div class="tab-pane fade" id="number" role="tabpanel" aria-labelledby="number-tab">
                                <support-number></support-number>
                            </div>
                            <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="other-tab">
                                <support-other></support-other>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card -->
    </div>

</div>

@endsection

@section('script')
<!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
