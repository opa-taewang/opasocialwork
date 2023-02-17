@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        {{-- @slot('li_2') MAKE MONEY @endslot --}}
        @slot('title') Child PANEL @endslot
    @endcomponent


<div class="row">
    <div class="card col-xl-3 col-lg-">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                <i class="mdi mdi-web text-primary mr-0 mr-sm-4 fa-5x"></i>
                <div class="container text-center text-sm-left">
                    <div class="fluid-container">
                        <h3 class="mb-0">Step 1</h3>
                        <small class="text-gray" style="font-size:13px">Enter Your Domain</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card col-xl-3 col-lg-">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                <i class="mdi mdi-currency-usd text-primary mr-0 mr-sm-4 fa-5x"></i>
                <div class="container text-center text-sm-left">
                    <div class="fluid-container">
                        <h3 class="mb-0">Step 2</h3>
                        <small class="text-gray" style="font-size:13px">Select Your Currency</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card col-xl-3 col-lg-">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                <i class="mdi mdi-face text-primary mr-0 mr-sm-4 fa-5x"></i>
                <div class="container text-center text-sm-left">
                    <div class="fluid-container">
                        <h3 class="mb-0">Step 3</h3>
                        <small class="text-gray" style="font-size:13px">Enter your username</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card col-xl-3 col-lg-">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
                <i class="mdi mdi-lock text-primary mr-0 mr-sm-4 fa-5x"></i>
                <div class="container text-center text-sm-left">
                    <div class="fluid-container">
                        <h3 class="mb-0">Step 3</h3>
                        <small class="text-gray" style="font-size:13px">Enter Your Password</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End ROw --}}

 <div class="text-center mb-3">
    <p class="text-primary text-small">
        👉 Hey {{Auth::user()->username}}, If you want to login your child panel admin, then you should login to your domain/admin, For an example if you are Domain URL is https://abcd.com, then you can login here:<b> https://abcd.com/admin. </b>
    </p>

    <p class="text-primary text-small">
        👉 Hey {{Auth::user()->username}}, If you want to change your Childpanel to coding mode ( You can change your panel Language, you design your panel own way, add logos ), please create a ticket about this. Check out the demo here: <a href="https://childpaneldemo.com/admin/">https://childpaneldemo.com/admin/</a> <b>" Username : childpaneldemo " Password: paneldemo@121 "</b>
    </p>
</div>
{{-- End of text description --}}
@if($checkPanelOrder == 0)
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{route('user.childpanel.create')}}">
                    @csrf
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="for-domain" class="form-label">Domain</label>
                                <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror" id="for-domain" value="{{old('domain')}}">
                                @error('domain')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <div>Please visit your registrar's dashboard to change nameservers to:</div>
                                <ul style="padding-left: 20px">
                                    <li>ns1.perfectdns.com</li>
                                    <li>ns2.perfectdns.com</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="for-currency" class="form-label @error('currency') is-invalid @enderror">Currency</label>
                                <select name="currency" id="for-currency" class="form-select">
                                    @foreach ($currencies as $currency)
                                        <option value="{{$currency->id}}">{{$currency->currency}} ({{$currency->code}})</option>
                                    @endforeach
                                </select>
                                @error('currency')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <div>" If you choose other currencies rather than USD, then you will not find lots of payment gateway "</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="for-admin-username" class="form-label">Admin Username</label>
                                <input type="text" class="form-control @error('admin_username') is-invalid @enderror" name="admin_username" value="{{old('admin_username')}}" id="for-admin-username">
                                @error('admin_username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="for-admin-password" class="form-label">Admin password</label>
                                <input type="text" class="form-control @error('admin_password') is-invalid @enderror" name="admin_password" value="{{old('admin_password')}}" id="for-admin-password">
                                @error('admin_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-6">
                            <div class="mb-3">
                                <label for="for-confirm-admin-password" class="form-label">Confirm Admin password</label>
                                <input type="text" class="form-control @error('confirm_admin_password') is-invalid @enderror" name="confirm_admin_password" value="{{old('confirm_admin_password')}}" id="for-confirm-admin-password">
                                @error('confirm_admin_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="for-admin-username" class="form-label">Price Per Month</label>
                                <input type="text" class="form-control" name="price" value="{{convertCurrency(getOption('child_panel_price',true))}}" id="for-admin-username" disabled>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                    </div>
                </form>

                <div class="accordion mt-4" id="childPanelInfo">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                What is child panel?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>A child panel is a panel with a limited selection of features that is linked to one of your regular panels such as <a href="/">{{env('APP_URL')}}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                How much cost for a child panel?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>It will cost you {{convertCurrency(getOption('child_panel_price',true))}} per month. Please note, you paying for panel, not for services, you have to pay for the services you will purchase from bulkfollows</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                How long it will take to activate the child panel ?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>It will take 3-6 hrs to active your child panel if you changed your name server perfectly.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                Do you need hosting for child panel ?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>No, for child panel you just need a domain and that's all.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                I have domain, what i can do next ?
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>If you have domain, you can simply change your domain name server and point it to
                                        <br>
                                        <b>ns1.perfectdns.com</b>
                                        <br>
                                        <b>ns2.perfectdns.com</b>
                                        <br>
                                        After you successfully changed the name server, you can submit a order for child panel
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading6">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                How can i change name server for my domain?
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Its actually depend on your domain provider, if you go to your domain settings and choose custom DNS and enter the name server given by {{env('APP_URL')}}.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading7">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                How to i connect child panel with {{env('APP_NAME')}}?
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>You can simply go to https://yourdomain.com/admin/settings/providers and you will find out option to connect your panel with {{env('APP_NAME')}}. You can a key to connect your panel with {{env('APP_NAME')}}. This key you will find out on settings of your {{env('APP_NAME')}} account.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading8">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                How can i get refund for child panel if i am not interested to continue?
                            </button>
                        </h2>
                        <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Unfortunately refund not possible after we activate your child panel. But you can terminate your child panel any time by creating a <a href="{{route('user.ticket.create')}}">ticket</a> to us.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading9">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                               I want to change my child panel domain address, what i need to do ?
                            </button>
                        </h2>
                        <div id="collapse9" class="accordion-collapse collapse" aria-labelledby="heading9" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>You can simply, change your new domain name server and send to us your new domain address. We will replace your new domain with old domain. But, this change only possible for 1 time. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading10">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                How can i activate affiliate on my child panel.
                            </button>
                        </h2>
                        <div id="collapse10" class="accordion-collapse collapse" aria-labelledby="heading10" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Unfortunately, there is no affiliate feature on child panel</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading11">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                How can i add payment gateway on our child panel?
                            </button>
                        </h2>
                        <div id="collapse11" class="accordion-collapse collapse" aria-labelledby="heading11" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                     <p>You can visit https://yourdomain.com/admin/settings/payments - Add method - Choose Payment Method </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading12">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse12" aria-expanded="false" aria-controls="collapse12">
                                How can i collect money from our customer ?
                            </button>
                        </h2>
                        <div id="collapse12" class="accordion-collapse collapse" aria-labelledby="heading12" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                      <p>You customer will pay to your own payment gateway account, they are not paying to us. So, you don't have to worry about payment. Setup your own payment gateway, and collect payment from your customers </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading13">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse13" aria-expanded="false" aria-controls="collapse13">
                                If i connect our child panel with {{env('APP_NAME')}}, is there any way customer will find out about {{env('APP_NAME')}}?
                            </button>
                        </h2>
                        <div id="collapse13" class="accordion-collapse collapse" aria-labelledby="heading13" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>No, your customer will never know about {{env('APP_URL')}}. They will place order on your website and your order will automatically place to {{env('APP_URL')}} under your user account. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading14">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse14" aria-expanded="false" aria-controls="collapse14">
                                If i choose different currency for my child panel, which payment gateway i will be able to add on our child panel?
                            </button>
                        </h2>
                        <div id="collapse14" class="accordion-collapse collapse" aria-labelledby="heading14" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>If you want to get all the payment gateway, then you must choose USD for your child panel, if you choose different currency, you will get only 1-3 payment gateway for the currency you will choose. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading15">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse15" aria-expanded="false" aria-controls="collapse15">
                                Is it possible to create another admin account for our child panel?
                            </button>
                        </h2>
                        <div id="collapse15" class="accordion-collapse collapse" aria-labelledby="heading15" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Yes, you can create a ticket and send us the username, password. We will create another account for your child panel. Even we can limited the access of the new account ( Such as, new account will not get access of your customer details or payment page or other pages access), you need to request us which access you want to give for new account, is it full access or only orders page/ticket page/payment page, etc. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end accordion -->
            </div><!-- end card body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->

@endif

@endsection

@section('script')
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}

<!-- dashboard init -->
{{-- <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script> --}}
@endsection
