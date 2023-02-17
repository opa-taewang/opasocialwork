@extends('main.layouts.master')

@section('title') @lang(' FAQs') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        {{-- @slot('li_2') MAKE MONEY @endslot --}}
        @slot('title') FAQs @endslot
    @endcomponent


<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">FAQ's (Frequently Asked Questions)</h4>
                <div class="accordion accordion-flush mt-4" id="childPanelInfo">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                How To Add Funds In Panel?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Simply Go This Page <a href="http://opasocial.com/payment/add-funds" title="" target="">Add Funds</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                How To Order Service?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Simply Go to This Page.<a href="http://opasocial.com/order/new"> Order Services</a> (Order The Service that You Want. Be It Facebook, Instagram, Youtube &amp; Manymore)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                How Much time it will take to complete an order ?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Every Service Have Its Description Box Where The Exact Time Is Mentioned.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                               How To Fill The Field Link Depending On The Type Of Service ?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>
                                        Instagram Likes Fast: <a href="http://instagram.com/p/xxx/" rel="nofollow" target="_blank">http://instagram.com/p/xxx/</a><br>
                                        Instagram Likes Fake HQ: <a href="http://instagram.com/p/xxx/" rel="nofollow" target="_blank">http://instagram.com/p/xxx/</a> or <a href="http://instagram.com/username/p/xxx/" rel="nofollow" target="_blank">http://instagram.com/username/p/xxx/</a><br>
                                        Instagram Comments: <a href="http://instagram.com/p/xxx/" rel="nofollow" target="_blank">http://instagram.com/p/xxx/</a><br>
                                        Real Instagram Followers: username<br>
                                        Real Instagram Likes: <a href="http://instagram.com/p/xxx/" rel="nofollow" target="_blank">http://instagram.com/p/xxx/</a><br>
                                        Real Instagram Followers RUS: <a href="http://instagram.com/username" rel="nofollow" target="_blank">http://instagram.com/username</a><br>
                                        Real Instagram Likes RUS: <a href="http://instagram.com/p/xxx/" rel="nofollow" target="_blank">http://instagram.com/p/xxx/</a>Twitter Followers: <a href="http://twitter.com/username" rel="nofollow" target="_blank">http: //twitter.com/username</a><br>
                                        Twitter Retweets / Favorite/Likes: <a href="https://twitter.com/username/status/344828701747339264" rel="nofollow" target="_blank">https://twitter.com/username/status/344828701747339264</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                The Average Time Of Completion On Orders ?
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>If you have domain, you can simply change your domain name server and point it to
                                        All Orders Will Complete Within 24 hours Of The Order. Instagram &amp; Twitter Orders May Take Longer To
                                        Complete (1-3 days) Will I Be Refunded For Drop Followers Or Likes? No, Likes/followers May Possibly
                                        Drop, This Is Because Of The Updates In Twitter/Instagram/Facebook/Youtube. This Would Only Be Around
                                        2-3 Followers Per Update. (Max). No Refunds For The Payments.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading6">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                Can I Cancel An Order That I Gave ?
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>
                                        No, Orders are Permanent, Cannot Be Canceled By Admin. All Users Must Be To The Public Before The Order
                                        Is Placed Until The Order Is Finished. If The User Is On Private, Please Note That a Refund Is Not
                                        Issued as It Will Still Hit Our Servers No Matter What!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading7">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                               When can you activate my Paypal??
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>
                                        You can use Paypal whenever you want by following the instructions in the Add Funds page (manual use) To
                                        have it automatically enabled, you have to have added at least 200$ in your account, and then you can
                                        send us a ticket with your email to enable it for you!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading8">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                Do you accept bank transfer?
                            </button>
                        </h2>
                        <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Yes, you can make bank transfer by using manual method or flutterwave</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading9">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                               Do you accept cryptocurrency ?
                            </button>
                        </h2>
                        <div id="collapse9" class="accordion-collapse collapse" aria-labelledby="heading9" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Yes, you can use coinbase payment or fill a support form to request for our USDT(TRC 20), BTC or LTC wallet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading10">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                What other country currency do you support.
                            </button>
                        </h2>
                        <div id="collapse10" class="accordion-collapse collapse" aria-labelledby="heading10" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>We support paymetnt in currency like Ghana Cedis, Ugandan Shilling, Zambian Kwacha, Rwandan Franc, West African CFA Franc, Tanzanian Shilling, Kenya Shilling and Malawi Kwacha</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading11">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse11" aria-expanded="false" aria-controls="collapse11">
                                How to get youtube comment link??
                            </button>
                        </h2>
                        <div id="collapse11" class="accordion-collapse collapse" aria-labelledby="heading11" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                     <p>
                                        Find the timestamp that is located next to your username above your comment (for example: "3 days ago")
                                        and hover over it then right click and "Copy Link Address".
                                        The link will be something like this: <a href="https://www.youtube.com/watch?v=12345&amp;lc=a1b21etc"
                                        style="background-color: rgb(255, 255, 255); font-size: 14px;">https://www.youtube.com/watch?v=12345&amp;lc=a1b21etc</a>
                                        instead of just <a
                                        href="https://www.youtube.com/watch?v=12345">https://www.youtube.com/watch?v=12345</a>
                                        To be sure that you got the correct link, paste it in your browser's address bar and you will see that
                                        the comment is now the first one below the video and it says "Highlighted comment".
                                     </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading12">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse12" aria-expanded="false" aria-controls="collapse12">
                                Which youtube view service can be used with monetizable video? ?
                            </button>
                        </h2>
                        <div id="collapse12" class="accordion-collapse collapse" aria-labelledby="heading12" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                      <p>The one that has "Monetized" in its service' name. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading13">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse13" aria-expanded="false" aria-controls="collapse13">
                                What is "Instagram mentions", how do you use it?
                            </button>
                        </h2>
                        <div id="collapse13" class="accordion-collapse collapse" aria-labelledby="heading13" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Instagram Mention is when you mention someone on Instagram.<br>
                                        Example @abcde  this means you have mentioned abcde under this post and abcde will receive a notification
                                        to check the post.<br>
                                        Basically the Instagram Mentions [User Followers], you put the link to your post and the username of the
                                        person that you want us to mention HIS FOLLOWERS!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading14">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse14" aria-expanded="false" aria-controls="collapse14">
                                What is Partial status?
                            </button>
                        </h2>
                        <div id="collapse14" class="accordion-collapse collapse" aria-labelledby="heading14" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>
                                         Partial Status is when we partially refund the remains of an order. Sometimes for some reasons, we are
                                        unable to deliver a full order, so we refund you the remaining undelivered amount.<br> <br>
                                        Example: You bought an order with quantity 10,000 and charges 10$<br> let's say we delivered 9,000 and
                                        the remaining 1,000 we couldn't deliver, then we will "Partial" the order and refund you the remaining
                                        1,000 (1$ in this example).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading15">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse15" aria-expanded="false" aria-controls="collapse15">
                                I want a panel like yours / I want to resell your services how?
                            </button>
                        </h2>
                        <div id="collapse15" class="accordion-collapse collapse" aria-labelledby="heading15" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>We're working on getting a rental panel for our clients!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading16">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse16" aria-expanded="false" aria-controls="collapse16">
                                What is "Instagram impressions"?
                            </button>
                        </h2>
                        <div id="collapse16" class="accordion-collapse collapse" aria-labelledby="heading16" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Impression means reach (also how many users have seen your post) it is mostly used with brands, they will ask you to show them statistic of the impressions your posts have.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading17">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse17" aria-expanded="false" aria-controls="collapse17">
                                For Live Video, The link must be added before the user goes live or after?
                            </button>
                        </h2>
                        <div id="collapse17" class="accordion-collapse collapse" aria-labelledby="heading17" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>After he goes live, or just 5 seconds before he goes!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading18">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse18" aria-expanded="false" aria-controls="collapse18">
                                What is "Instagram Saves", and what do they do?
                            </button>
                        </h2>
                        <div id="collapse18" class="accordion-collapse collapse" aria-labelledby="heading18" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>Instagram Saves is when a user saves a post to his history on Instagram (by pressing the save button near the like button). A lot of saves for a post increase its impression.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading19">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse19" aria-expanded="false" aria-controls="collapse19">
                                How do I use mass order?
                            </button>
                        </h2>
                        <div id="collapse19" class="accordion-collapse collapse" aria-labelledby="heading19" data-bs-parent="#childPanelInfo">
                            <div class="accordion-body">
                                <div class="text-muted">
                                    <p>You put the package ID followed by | followed by the link followed by | followed by quantity on each
                                        line To get the package ID of a package please check here: </span> <a
                                        href="http://opasocial.com/services">https://opasocial.com/services</a><br>
                                        Let’s say you want to use the Mass Order to add Instagram Followers to your 3 accounts: abcd, asdf, qwer
                                        From the Package List @ </span> <a href="http://opasocial.com/services"
                                        style="background-color: rgb(255, 255, 255); font-family: "
                                        target="_blank">https://opasocial.com/services</a> <span style="color: rgb(0, 0, 0); font-family: "
                                        trebuchet="">, the service ID for this service “Instagram Followers [15K] [REAL] ” is 102<br>
                                        Let’s say you want to add 1000 followers for each account, the output will be like this:<br>
                                        ID|Link|Quantity<br>
                                        or in this example:<br><br>
                                        102|abcd|1000 <br>102|asdf|1000 <br>102|qwer|1000<br>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- end accordion -->
                <div style="text-align: center;">
          <span><a href="{{route('user.ticket.create')}}">For more information, you can open a ticket here </a></span>
        </div>
            </div><!-- end card body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->


@endsection

@section('script')
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}

<!-- dashboard init -->
{{-- <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script> --}}
@endsection
