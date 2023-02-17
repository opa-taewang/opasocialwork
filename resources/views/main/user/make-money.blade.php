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
            <h5 class="card-header bg-transparent border-bottom text-uppercase"><span class="number-icon">1</span> Earn Money With Making Video</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Create a video about how to use our panel and share it in any social media, then you can earn $2.</p>
                <p class="card-text">You can upload this video on any platform like youtube , facebook , instagram ,twitter, tiktok or any platform you like.</p>

                    <div class="card border border-primary">
                        {{-- <div class="card-header bg-transparent border-primary">
                            <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Primary outline Card</h5>
                        </div> --}}
                        <div class="card-body">
                            <h5 class="card-title mt-0">Earn 2$ Easily!</h5>
                            <span class="mb-2 text-muted">Video Rules :</span>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">One user can create one video.</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">Add Best Title</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">Add Best Description</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">Add Tags</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">Min video length should be 1:30 Minute</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">You should give our Website link in your description .</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">We will review the video.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"><span class="number-icon">2</span> Earn Money With Writing Blog Post</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Write a blog about our panel and then you can earn $2.</p>
                <div class="card border border-primary">
                        {{-- <div class="card-header bg-transparent border-primary">
                            <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Primary outline Card</h5>
                        </div> --}}
                        <div class="card-body">
                            <h5 class="card-title mt-0">Earn 2$ Easily!</h5>
                            <span class="mb-2 text-muted">Blog Rules :</span>
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">One user can create one video.</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">500+ Words</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">A link to Our Website with keyword SMM Panel</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">DA should be Minimum 30</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">Subdomains Not Allowed!</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="text-truncate font-size-14 m-0">We will review the blog post.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"><span class="number-icon">3</span> Affiliates</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">By referring users to our site, you can receive 5% of every single deposit they make into the panel.</p>
                <p class="card-text">Advertising us on social media sites, forums, even your friends are all great ways of getting valuable affiliates! Be creative and you can make hundreds with your affiliate link!</p>
                <div class="card border border-primary">
                        {{-- <div class="card-header bg-transparent border-primary">
                            <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Primary outline Card</h5>
                        </div> --}}
                        <div class="card-body">
                            <h5 class="card-title mt-0">Affilliate Link</h5>
                            <span class="mb-2 text-muted">You can find your affiliate link <a href="{{route('user.affiliate.show')}}">here.</a></span>
                        </div>
                </div>

                <div class="card border border-primary">
                        {{-- <div class="card-header bg-transparent border-primary">
                            <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Primary outline Card</h5>
                        </div> --}}
                        <div class="card-body">
                            <h5 class="card-title mt-0">Minimum Payout</h5>
                            <span class="mb-2 text-muted">The minimum affiliate payout is currently {{convertCurrency(8.00)}}, which will be applied to your OpaSocial credit Or You Can Ask For Withdraw through Paytm, Paypal.</span>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"><span class="number-icon">4</span> Your Own Panel</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">We offer a setup of your own panel for {{convertCurrency(getOption('child_panel_price',true))}}/monthly. We will advise you on advertising as well as helping you with all setup!</p>
                <p class="card-text">The panel will connect to OpaSocial's API. Any order placed on your panel will automatically be placed on OpaSocial via your account, so make sure your balance is topped up!</p>
                <p class="card-text">You will have full access to an admin panel where everything can be managed and set up. Including: Payment processors, services, prices, users, preset themes, orders and much more!</p>
                <p class="card-text">To order your own panel,  <a href="{{route('user.affiliate.show')}}">click here!</a></p>
                <div class="card border border-primary">
                        {{-- <div class="card-header bg-transparent border-primary">
                            <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Primary outline Card</h5>
                        </div> --}}
                        <div class="card-body">
                            <h5 class="card-title mt-0">Example & Support</h5>
                            <span class="mb-2 text-muted">For an example of a child panel feel free to contact us by opening a ticket</span>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <h5 class="card-header bg-transparent border-bottom text-uppercase"><span class="number-icon">5</span> Reselling</h5>
            <div class="card-body">
                {{-- <h4 class="card-title">Special title treatment</h4> --}}
                <p class="card-text">Since all of our service are automated, this allows you to be able to resell our services to others who are unaware of OpaSocial.</p>
                <p class="card-text">You can create your own threads, advertise on social media or reach out to local businesses. Again, being creative can go a long way by doing this!</p>
                <div class="card border border-primary">
                        {{-- <div class="card-header bg-transparent border-primary">
                            <h5 class="my-0 text-primary"><i class="mdi mdi-bullseye-arrow me-3"></i>Primary outline Card</h5>
                        </div> --}}
                    <div class="card-body">
                        <h5 class="card-title mt-0">Example & Support</h5>
                        <span class="mb-2 text-muted">We are selling IG followers for {{convertCurrency(0.5)}} / K, you can advertise those same followers for {{convertCurrency(1.50)}} / K. This can lead to huge margins as you will often sell more than one unit. Feel free to sell as many services as you like!</span>
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
