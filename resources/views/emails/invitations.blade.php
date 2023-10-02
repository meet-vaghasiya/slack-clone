<html>

<body>
    You are invited to join workspace {{ $workspace->name }}.
    Kindly click <a href="{{ config('app.frontend_url') }}/accept-invitation/{{ $invitation->token }}">Click here</a>
</body>

</html>