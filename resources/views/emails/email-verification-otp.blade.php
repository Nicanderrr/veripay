<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Veripay Verification Code</title>
</head>
<body style="margin:0;background:#f6f6f6;font-family:Arial,sans-serif;color:#1f2933;">
    <div style="max-width:620px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border-top:5px solid #ff6863;padding:28px;">
            <h1 style="margin:0;font-size:26px;">Verify your email</h1>
            <p style="margin:10px 0 0;color:#68717d;">Hi {{ $user->name }}, enter this code to finish creating your Veripay account.</p>
            <div style="margin:26px 0;padding:18px;text-align:center;background:#fff1f0;border:1px solid #ffd0ce;font-size:34px;font-weight:bold;letter-spacing:8px;color:#1f2933;">
                {{ $code }}
            </div>
            <p style="margin:0;color:#68717d;">This code expires in 10 minutes. If you did not create this account, you can ignore this email.</p>
        </div>
    </div>
</body>
</html>
