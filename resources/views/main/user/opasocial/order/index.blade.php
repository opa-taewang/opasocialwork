@extends('main.layouts.master')

@section('title') @lang(' Dashboard') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('title') Dashboard @endslot
    @endcomponent



<!-- end row -->

<!-- end modal -->



@endsection

@section('script')
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}


@endsection
