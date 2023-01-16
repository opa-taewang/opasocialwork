<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | Opasocial - Number #1 Social Media Marketing Panel</title>
    <meta name="description" content="The number #1 social media growing channel in Africa">
    <meta name="keywords" content="smm, social, social media marketing in nigeria, nigeria, africa, smm panel, smm panel in africa, smm panel in nigeria ">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="OpaSocial" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    @include('main.layouts.head-css')
</head>

@section('body')
    <body data-sidebar="dark">
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('main.layouts.topbar')
        @include('main.layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('main.layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('main.layouts.right-sidebar')
    <!-- /Right-bar -->

    <!-- JAVASCRIPT -->
    @include('main.layouts.vendor-scripts')
</body>

</html>
