@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        {{-- @slot('li_2') MAKE MONEY @endslot --}}
        @slot('title') MAKE MONEY @endslot
    @endcomponent


<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"> API 2.0</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">

                        <thead>
                            <tr>
                                <th width="70%">HTTP Method</th>
                                <th>POST</th>
                            </tr>
                            <tr>
                                <th>API URL</th>
                                <th><div class="bg-secondary text-light">{{url('/api/v2')}}</div></th>
                            </tr>
                            <tr>
                                <th>Response Format</th>
                                <th>JSON</th>
                            </tr>
                            <tr>
                                <th>Example Code</th>
                                <th>
                                    <a target="_blank" href="{{url('/example.txt')}}" type="button" class="btn  btn-dark btn-sm btn-rounded waves-effect waves-light">Example of PHP Code</a>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"> Method: balance</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Note: All API funds/prices will be in USD.</p>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">

                        <thead>
                            <tr>
                                <th width="40%">PARAMETERS</th>
                                <th>DESCRIPTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">api_token</td>
                                <td>action</td>
                            </tr>
                            <tr>
                                <td scope="row">action</td>
                                <td>Method name</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Response Example --}}
                <p><strong>Success Response:</strong></p>
                <div class="bg-dark ">
                    <pre class="text-light p-1 fs-6">

  {
    "balance":"100.78",
    "currency": "USD"
  }
                    </pre>
                </div>

            </div>
        </div>
    </div>

     <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"> Method: packages</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Note: All API funds/prices will be in USD.</p>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">

                        <thead>
                            <tr>
                                <th width="40%">PARAMETERS</th>
                                <th>DESCRIPTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">api_token</td>
                                <td>action</td>
                            </tr>
                            <tr>
                                <td scope="row">action</td>
                                <td>Method name</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Response Example --}}
                <p><strong>Success Response:</strong></p>
                <div class="bg-dark ">
                    <pre class="text-light prewa p-1 fs-6">

    [
        {
            "id": 101,
            "service_id": 1,
            "name": "Real & Active Followers - Best Server",
            "rate": "250.00",
            "min": "100",
            "max": "100000",
            "service": "Instagram Followers",
            "type": "default",
            "desc": "0-3 Hours Start | Full Link | 20 Days Refill\r\nLink Format:\r\nhttps://www.instagram.com/official_social_sale/"
        },
        {
            "id": 107,
            "service_id": 1,
            "name": "Indian Mixed Followers",
            "rate": "150.00",
            "min": "200",
            "max": "10000",
            "service": "Instagram Followers",
            "type": "default",
            "desc": "0-12 Hours Start | Full Link |\r\nLink Format:\r\nhttps://www.instagram.com/official_social_sale/"
        },
        {
            "id": 30067,
            "service_id": 1,
            "name": "Instagram Followers - Worldwide",
            "rate": "103.00",
            "min": "100",
            "max": "10000",
            "service": "Instagram Followers",
            "type": "default",
            "desc": "10k/day\r\nMax - 55k\r\n8 Hours Start"
        }
    ]
                    </pre>
                </div>

            </div>
        </div>
    </div>

     <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"> Method: add</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Note: All API funds/prices will be in USD.</p>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">

                        <thead>
                            <tr>
                                <th width="40%">PARAMETERS</th>
                                <th>DESCRIPTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                    <td>api_token</td>
                                    <td>Your API token</td>
                                </tr>
                                <tr>
                                    <td>action</td>
                                    <td>Method Name</td>
                                </tr>
                                <tr>
                                    <td>package</td>
                                    <td>ID of package</td>
                                </tr>
                                <tr>
                                    <td>link</td>
                                    <td>Link to page</td>
                                </tr>
                                <tr>
                                    <td>quantity</td>
                                    <td>Needed quantity</td>
                                </tr>
                                <tr>
                                    <td>custom_data</td>
                                    <td>optional, needed for custom comments, mentions and other relaed packages only.<br/> each separated by '\n', '\n\r'</td>
                                </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Response Example --}}
                <p><strong>Success Response:</strong></p>
                <div class="bg-dark ">
                    <pre class="text-light p-1 fs-6">

  {
    "order":"23501"
  }
                    </pre>
                </div>

            </div>
        </div>
    </div>

     <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"> Method: status</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Note: All API funds/prices will be in USD.</p>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">

                        <thead>
                            <tr>
                                <th width="40%">PARAMETERS</th>
                                <th>DESCRIPTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>api_token</td>
                                <td>Your API token</td>
                            </tr>
                            <tr>
                                <td>action</td>
                                <td>Method Name</td>
                            </tr>
                            <tr>
                                <td>order</td>
                                <td>Order ID</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Response Example --}}
                <p><strong>Success Response:</strong></p>
                <div class="bg-dark ">
                    <pre class="text-light p-1 fs-6">

  {
    "status": "Completed",
    "start_counter": "600",
    "remains": "600"
  }
                    </pre>
                </div>

            </div>
        </div>
    </div>

</div>
                        <!-- end row -->

<!-- end row -->

<!-- end modal -->



@endsection

@section('script')
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}

<!-- dashboard init -->
<script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
@endsection
