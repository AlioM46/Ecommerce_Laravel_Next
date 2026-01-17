<p>Hi {{ $name }},</p>
<p>We noticed a login to your account.</p>
<p><b>IP:</b> {{ $ip }}</p>
@if($userAgent)
<p><b>Device:</b> {{ $userAgent }}</p>
@endif
<p>If this wasn’t you, please reset your password.</p>
