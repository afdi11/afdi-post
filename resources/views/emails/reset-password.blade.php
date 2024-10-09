<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <p>Klik link berikut untuk mereset password Anda:</p>
    <a href="{{ url('/api/?token=' . $token) }}">Reset Password</a>
    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
</body>
</html>
