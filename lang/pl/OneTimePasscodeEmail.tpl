<p>{$FirstName},</p>

<p>Bezpieczeństwo twojego konta jest dla nas istotne. Aby potwierdzić swoją tożsamość, proszę  skorzystać z otrzymanego jednorazowego kodu dostępu. To nie będzie miało wpływu na twoje hasło ani inne ustawienia.</p>

<h3 style="margin-top:25px;margin-bottom:25px;">{$OTP}</h3>

<p>Ważność tego kodu wygasa w teminie {$ExpirationDate->Format($dateFormat)}</p>

<p><a href="{$OtpUrl}">Zweryfikuj swoje konto</a></p>