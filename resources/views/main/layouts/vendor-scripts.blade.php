<!-- JAVASCRIPT -->
<script src="{{ asset('js/app.js')}}"></script>
{{-- <script src="{{ asset('assets/libs/jquery/jquery.min.js')}}"></script> --}}
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="{{ asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{ asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{ asset('assets/libs/node-waves/node-waves.min.js')}}"></script> --}}


@yield('script')

<!-- App js -->
{{-- <script src="{{ asset('js/app.js')}}"></script> --}}
{{-- <script src="{{ mix('/js/app.js') }}"></script> --}}

@yield('script-bottom')
