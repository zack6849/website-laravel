@php
    $types = [
        '-error' => ['class' => 'notification-message-error', 'icon' => 'fa fa-times-circle'],
        '-warning' => ['class' => 'notification-message-warning', 'icon' => 'fa fa-exclamation-triangle'],
        '-success' => ['class' => 'notification-message-success', 'icon' => 'fa fa-check-circle'],
        '-info' => ['class' => 'notification-message-info', 'icon' => 'fa fa-info-circle'],
        '' => ['class' => 'notification-message-info', 'icon' => 'fa fa-info-circle'],
    ];

    $messages = [];

    if (Session::has('status')) {
        $messages[] = [
            'class' => 'notification-message-success',
            'icon' => 'fa fa-check-circle',
            'message' => Session::get('status'),
        ];
    }

    if (Session::has('error')) {
        $messages[] = [
            'class' => 'notification-message-error',
            'icon' => 'fa fa-times-circle',
            'message' => Session::get('error'),
        ];
    }

    if (Session::has('resent')) {
        $messages[] = [
            'class' => 'notification-message-success',
            'icon' => 'fa fa-check-circle',
            'message' => __('A fresh verification link has been sent to your email address.'),
        ];
    }

    foreach ($types as $type_suffix => $type_config) {
        if (Session::has('message' . $type_suffix)) {
            $messages[] = [
                'class' => $type_config['class'],
                'icon' => $type_config['icon'],
                'message' => Session::get('message' . $type_suffix),
            ];
        }
    }
@endphp
@if(auth()->user() != null && auth()->user()->unreadNotifications->count() > 0)
    @foreach(auth()->user()->unreadNotifications as $notification)
        @php
            $notificationType = '-' . ($notification->data['type'] ?? '');
            $notificationConfig = $types[$notificationType] ?? $types[''];
            $messages[] = [
                'class' => $notificationConfig['class'],
                'icon' => $notificationConfig['icon'],
                'message' => $notification->data['message'] ?? '',
            ];

            $notification->markAsRead();
        @endphp
    @endforeach
@endif

@if(count($messages) > 0)
    <div class="notification-stack">
        @foreach($messages as $message)
            <div
                class="notification-message {{ $message['class'] }}"
                role="alert"
                data-notification-message
                data-notification-dismiss-after="10000"
            >
                <button
                    type="button"
                    class="notification-dismiss"
                    aria-label="Dismiss notification"
                    data-notification-dismiss
                >
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <i class="notification-icon {{ $message['icon'] }}" aria-hidden="true"></i>
                <div>{{ $message['message'] }}</div>
            </div>
        @endforeach
    </div>
@endif
