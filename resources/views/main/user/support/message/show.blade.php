@extends('main.layouts.master')

@section('title') @lang(' Support') @endsection

@section('content')

    @component('main.layouts.components.breadcrumb')
        @slot('li_1') User @endslot
        @slot('li_2') Ticket @endslot
        @slot('title') Create  @endslot
    @endcomponent

{{-- Section goes here --}}
<div class="d-lg-flex">

    <div class="w-100 user-chat">
        <div class="card">
            <div class="p-4 border-bottom ">
                <div class="row">
                    <div class="col-md-9 col-9">
                        <h5 class="font-size-15 mb-1">{{$ticket->subject}}</h5>
                        <p class="text-muted mb-0"><i class="mdi mdi-circle text-success align-middle me-1"></i> {{$ticket->description}}</p>
                    </div>
                    <div class="col-md-3 col-3">
                        <ul class="list-inline user-chat-nav text-end mb-0">

                            <li class="list-inline-item">
                                <div class="dropdown">
                                    <button type="button" class="btn @if($ticket->status == 'OPEN') btn-success @else btn-dark @endif btn-rounded waves-effect waves-light">{{$ticket->status}}</button>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>


            <div>
                <div class="chat-conversation p-3">
                    @if($ticket->contents != NULL)
                        @foreach (json_decode($ticket->contents) as $contentTitle => $content)
                            <a class="dropdown-item" href="#">{{$contentTitle .' : '. $content}}</a>
                        @endforeach
                    @endif
                    <ul class="list-unstyled mb-0" data-simplebar style="max-height: 486px;">
                        @foreach ($ticketMessages as $key => $ticketMessage)
                            <li class="@if($ticketMessage->user_id == Auth::user()->id ) right @endif @if($lastId == $ticketMessage->id) last-chat @endif">
                                <div class="conversation-list">
                                    @php
                                        if($ticketMessage->user_id == Auth::user()->id){
                                            $sender = 'Me';
                                        }else{
                                            $sender = usersName($ticketMessage->user_id) .' from OpaSocial';
                                        }
                                    @endphp
                                    <div class="ctext-wrap">
                                        <div class="conversation-name">{{$sender}}</div>
                                        <p>
                                            {!!$ticketMessage->content!!}
                                        </p>
                                        <p class="chat-time mb-0"><i class="bx bx-time-five align-middle me-1"></i> {{setToTimezone("dS F Y h:m a", $ticketMessage->created_at, auth()->user()->timezone)}}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if($ticket->status == 'OPEN')
                    <div class="p-3 chat-input-section">
                        <form method="post" action="{{route('user.ticket.message', $ticket->id)}}">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="position-relative">
                                        <input type="text" name="content" class="form-control chat-input" placeholder="Enter Message..." required>
                                        {{-- <div class="chat-input-links" id="tooltip-container">
                                            <ul class="list-inline mb-0">
                                                <li class="list-inline-item"><a href="javascript: void(0);" title="Emoji"><i class="mdi mdi-emoticon-happy-outline"></i></a></li>
                                                <li class="list-inline-item"><a href="javascript: void(0);" title="Images"><i class="mdi mdi-file-image-outline"></i></a></li>
                                                <li class="list-inline-item"><a href="javascript: void(0);" title="Add Files"><i class="mdi mdi-file-document-outline"></i></a></li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-rounded chat-send w-md waves-effect waves-light"><span class="d-none d-sm-inline-block me-2">Send</span> <i class="mdi mdi-send"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
<!-- apexcharts -->
{{-- <script src="{{ asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}

<!-- dashboard init -->
<script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>

@endsection
