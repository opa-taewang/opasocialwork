@extends('main/layouts.master')

@section('title') @lang('Dashboards') @endsection

@section('content')

@component('main.layouts.components.breadcrumb')
@slot('li_1') Dashboards @endslot
@slot('title') Dashboard @endslot
@endcomponent



@endsection
@section('script')
<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- dashboard init -->
<script src="{{ URL::asset('assets/js/pages/dashboard.init.js') }}"></script>
@endsection