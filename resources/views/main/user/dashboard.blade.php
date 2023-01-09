@extends('main/layouts.master')

@section('title') @lang('translation.Starter_Page') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') Utility @endslot
        @slot('title') Starter Page @endslot
    @endcomponent

@endsection
