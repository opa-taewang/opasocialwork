@if (Gate::allows('isAdmin', Auth::user()))
        @include('main.layouts.sidebar.admin')
    @elseif(Gate::allows('isModerator', Auth::user()))
        @include('main.layouts.sidebar.moderator')
    @else
        @include('main.layouts.sidebar.user')
    @endif
