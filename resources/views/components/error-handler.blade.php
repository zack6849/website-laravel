@php
    $types = [
        '-error' => ['class' => 'alert-danger', 'icon' => 'fa fa-times-circle'],
        '-warning' => ['class' => 'alert-warning', 'icon' => 'fa fa-exclamation-triangle'],
        '-success' => ['class' => 'alert-success', 'icon' => 'fa fa-check-circle'],
        '-info' => ['class' => 'alert-info', 'icon' => 'fa fa-info-circle'],
        '' => ['class' => 'alert-info', 'icon' => 'fa fa-info-circle']
    ];
@endphp
@if(Session::has('status'))
    <div class="alert alert-info m-0 px-3">
        <i class="fas fa-info-circle"></i>&nbsp;&nbsp;{!! Session::get('status') !!}
    </div>
@endif
@if (Session::has('resent'))
    <div class="alert alert-success m-0 px-3" role="alert">
        {{ __('A fresh verification link has been sent to your email address.') }}
    </div>
@endif
@foreach($types as $type_suffix => $type_config)
    @if(Session::has('message'.$type_suffix))
        <div class="alert {{$type_config['class']}} m-0 px-3">
            <i class="{{$type_config['icon']}}"></i>&nbsp;&nbsp;{!! Session::get('message'.$type_suffix) !!}
        </div>
    @endif
@endforeach
@if(auth()->user() != null && auth()->user()->unreadNotifications->count() > 0)
    @foreach(auth()->user()->unreadNotifications as $notification)
        <div class="alert {{$types['-'.$notification->data['type']]['class']}} m-0 px-3">
            <i class="{{$types['-'.$notification->data['type']]['icon']}}"></i>&nbsp;&nbsp; {{$notification->data['message']}}
        </div>
        @php
            $notification->markAsRead();
        @endphp
    @endforeach
@endif
