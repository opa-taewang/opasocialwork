@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ asset('/assets/libs/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('title') Affiliate @endslot
    @endcomponent


{{-- Start Table --}}
 <div class="row">
    <div class="col-xl-12">
        <div class="text-center mb-3">
                <p class="text-primary text-small">
                    🤑 Wanna get money ? Refer {{env('APP_NAME')}} and use our Affiliated system to get payouts! Refer your friends and lets make money! 👍
                </p>
            </div>
        {{-- Your Referral Links --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><i class="link-icon mdi mdi-link"></i>Your Referral Links</h4>
                <div class="table-responsive">
                    <table class="table table-striped-columns mb-0">
                        <thead>
                            <tr>
                                <th>Referral link</th>
                                <th>Commission Rate</th>
                                <th>Minimum payout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>{{$link}}</th>
                                <td>{{$commission[0]->commission_val}}%</td>
                                <td>{{convertCurrency($commission[0]->min_payout)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Affiliate statistics --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><i class="link-icon mdi mdi-currency-usd"></i>Affiliate Statistics</h4>
                <div class="table-responsive">
                    <table class="table table-striped-columns mb-0">
                        <thead>
                            <tr>
                                <th>Visits</th>
                                <th>Registration</th>
                                <th>Referral</th>
                                <th>Total Earnings</th>
                                <th>Paid Earnings</th>
                                <th>Available Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$visits}}</td>
                                <td>{{$registration}}</td>
                                <td>{{convertCurrency(10)}}</td>
                                <td>{{convertCurrency((Auth::user()->treffund))}}</td>
                                <td>{{convertCurrency(( Auth::user()->reffund))}}</td>
                                <td>{{convertCurrency((Auth::user()->treffund) - ( Auth::user()->reffund))}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Terms and Condition --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><span class="number-icon">1</span> Affiliate Terms And Conditions</h4>
                <p class="card-text">As an authorized affiliate (Affiliate) of OPA SCEPTRE LTD (RC - 1861688), you agree to abide by the terms and conditions contained in this Agreement (Agreement). Please read the entire Agreement carefully before registering and promoting {{env('APP_URL')}} as an Affiliate.</p>
                <p class="card-text">Your participation in the Program is solely to legally advertise our website to receive a commission on memberships and products purchased by individuals referred to {{env('APP_URL')}} by your own website or personal referrals.</p>
                <p class="card-text">By signing up for the {{env('APP_URL')}} Affiliate Program (Program), you indicate your acceptance of this Agreement and its terms and conditions.</p>
            </div>
        </div>

         {{-- Commissions --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><span class="number-icon">2</span> Commissions</h4>
                <p class="card-text">Commissions will be paid once a month. For an Affiliate to receive a commission, the referred account must remain active for a minimum of 37 days.</p>
                <p class="card-text">You cannot refer yourself, and you will not receive a commission on your own accounts. There is also a limit of one commission per referral. If someone clicks the link on your site and orders multiple accounts, then you will receive a commission on the first order only.</p>
                <p class="card-text">Payments will only be sent for transactions that have been successfully completed. Transactions that result in chargebacks or refunds will not be paid out.</p>
            </div>
        </div>

        {{-- Termination --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><span class="number-icon">3</span> Termination</h4>
                 <ul class="list-p">
                    <li><b>3a.</b> Inappropriate advertisements (false claims, misleading hyperlinks, etc.).</li>
                    <br>
                    <li><b>3b.</b> Spamming (mass email, mass newsgroup posting, etc.).</li>
                    <br>
                    <li><b>3c.</b>
                    Advertising on sites containing or promoting illegal activities
                    </li>
                    <br>
                    <li><b>3d.</b>
                    Failure to disclose the affiliate relationship for any promotion that qualifies as an endorsement under existing Federal Trade Commission guidelines and regulations, or any applicable state laws.
                    </li>
                    <br>
                    <li><b>3e.</b>
                    Violation of intellectual property rights. {{env('APP_URL')}} reserves the right to require license agreements from those who employ trademarks of {{env('APP_URL')}} in order to protect our intellectual property rights.
                    </li>
                    <br>
                    <li><b>3f.</b>
                    Offering rebates, coupons, or other form of promised kick-backs from your affiliate commission as an incentive. Adding bonuses or bundling other products with {{env('APP_URL')}}, however, is acceptable.
                    </li>
                    <br>
                    <li><b>3g.</b>
                    Self referrals, fraudulent transactions, suspected Affiliate fraud.
                    </li>
                </ul>
                <br>
                <p>In addition to the foregoing, {{env('APP_URL')}} reserves the right to terminate any Affiliate account at any time, for any violations of this Agreement or no reason.</p>
            </div>
        </div>

        {{-- Affliate Links --}}
         <div class="card mt-5">
            <div class="card-body">
            <h4 class="card-title"><span class="number-icon">4</span> Affiliate Links</h4>
            <p>You may use graphic and text links both on your website and within in your email messages. You may also advertise the {{env('APP_URL')}} site in online and offline classified ads, magazines, and newspapers.</p>
                <p>You may use the graphics and text provided by us, or you may create your own as long as they are deemed appropriate according to the conditions and not in violation as outlined in Condition 3.</p>
            </div>
        </div>


        {{-- Coupon and deal site --}}
        <div class="card mt-5">
            <div class="card-body">
                <h4 class="card-title"><span class="number-icon">5</span> Coupon and Deal Sites</h4>
                <p>{{env('APP_URL')}} occasionally offers coupon to select affiliates and to our newsletter subscribers. If you’re not pre-approved / assigned a branded coupon, then you’re not allowed to promote the coupon. Below are the terms that apply for any affiliate who is considering the promotion of our products in relation to a deal or coupon:</p><br>
                <ul class="list-p">
                    <li>
                        <b>5a.</b> Affiliates may not use misleading text on affiliate links, buttons or images to imply that anything besides currently authorized deals to the specific affiliate.
                    </li>
                    <br>
                    <li>
                    <b>5b.</b> Affiliates may not bid on {{env('APP_URL')}} Coupons, {{env('APP_URL')}} Discounts or other phrases implying coupons are available.
                    </li>
                    <br>
                    <li>
                        <b>5c.</b> Affiliates may not generate pop-ups, pop-unders, iframes, frames, or any other seen or unseen actions that set affiliate cookies unless the user has expressed a clear and explicit interest in activating a specific savings by clicking on a clearly marked link, button or image for that particular coupon or deal. Your link must send the visitor to the merchant site.
                    </li>
                    <br>
                    <li>
                        <b>5e.</b> User must be able to see coupon/deal/savings information and details before an affiliate cookie is set (i.e. “click here to see coupons and open a window to merchant site” is NOT allowed).
                    </li>
                    <br>
                    <li>
                        <b>5f.</b> Affiliate sites may not have “Click for (or to see) Deal/Coupon” or any variation, when there are no coupons or deals available, and the click opens the merchant site or sets a cookie. Affiliates with such text on the merchant landing page will be removed from the program immediately.
                    </li>
                </ul>
            </div>
        </div>

        {{-- Pay Per click --}}
        <div class="card mt-5">
            <div class="card-body">
                <h4 class="card-title"><span class="number-icon">6</span> Pay Per Click (PPC) Policy</h4>
                <p>PPC bidding is NOT allowed without prior written permission.</p>
            </div>
        </div>

        {{-- Liability --}}
        <div class="card mt-5">
            <div class="card-body">
            <h4 class="card-title"><span class="number-icon">7</span> Liability</h4>
                <p>{{env('APP_URL')}} will not be liable for indirect or accidental damages (loss of revenue, commissions) due to affiliate tracking failures, loss of database files, or any results of intents of harm to the Program and/or to our website(s).</p>
                <p>We do not make any expressed or implied warranties with respect to the Program and/or the memberships or products sold by {{env('APP_URL')}}. We make no claim that the operation of the Program and/or our website(s) will be error-free and we will not be liable for any interruptions or errors.</p>
            </div>
        </div>

        {{-- terms of agreement --}}
        <div class="card mt-5">
            <div class="card-body">
            <h4 class="card-title"><span class="number-icon">8</span> Term of the Agreement</h4>
                <p>The term of this Agreement begins upon your acceptance in the Program and will end when your Affiliate account is terminated.</p>
                <p>The terms and conditions of this agreement may be modified by us at any time. If any modification to the terms and conditions of this Agreement are unacceptable to you, your only choice is to terminate your Affiliate account. Your continuing participation in the Program will constitute your acceptance of any change.</p>
            </div>
        </div>

        {{-- Indeminification --}}
        <div class="card mt-5">
            <div class="card-body">
            <h4 class="card-title"><span class="number-icon">9</span> Indemnification</h4>
                <p>Affiliate shall indemnify and hold harmless {{env('APP_URL')}} and its affiliate and subsidiary companies, officers, directors, employees, licensees, successors and assigns, including those licensed or authorized by {{env('APP_URL')}} to transmit and distribute materials, from any and all liabilities, damages, fines, judgments, claims, costs, losses, and expenses (including reasonable legal fees and costs) arising out of or related to any and all claims sustained in connection with this Agreement due to the negligence, misrepresentation, failure to disclose, or intentional misconduct of Affiliate.</p>
            </div>
        </div>

        {{-- Governing Law, Jurisdiction, and Attorney Fees --}}
        <div class="card mt-5">
            <div class="card-body">
            <h4 class="card-title"><span class="number-icon">10</span> Governing Law, Jurisdiction, and Attorney Fees</h4>
                <p>This Agreement shall be governed by and construed in accordance with the laws of the Federal Republic of Nigeria. Any dispute arising under or related in any way to this Agreement shall be adjudicated exclusively in the state courts located in Ras Al Khaimah, UAE.</p>
                <p>In the event of litigation to enforce any provision of this Agreement, the prevailing party will be entitled to recover from the other party its costs and fees, including reasonable legal fees.</p>
            </div>
        </div>

        {{-- Electronic Signatures Effective --}}
        <div class="card mt-5">
            <div class="card-body">
            <h4 class="card-title"><span class="number-icon">11</span> Electronic Signatures Effective</h4>
            <p>The Agreement is an electronic contract that sets out the legally binding terms of your participation in the {{env('APP_URL')}} affiliate program. You indicate your acceptance of this Agreement and all of the terms and conditions contained or referenced in this Agreement by completing the ShareASale and/or {{env('APP_URL')}} application process. This action creates an electronic signature that has the same legal force and effect as a handwritten signature.</p>
            </div>
        </div>

    </div><!--end col-->
</div> <!-- end row -->
{{-- End Table --}}

@endsection

@section('script')

@endsection

