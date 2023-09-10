<p>{$FirstName},</p>

<p>The security of your account is important to us. In order to verify your identity, please enter the one time passcode when requested. This will not affect your current password or any other settings.</p>

<h3 style="margin-top:25px;margin-bottom:25px;">{$OTP}</h3>

<p>This code expires at {$ExpirationDate->Format($dateFormat)}</p>

<p><a href="{$OtpUrl}">Verify your account</a></p>