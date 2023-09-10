<p>{$FirstName},</p>

<p>Käyttäjätilisi turvallisuus on meille tärkeää. Todistaaksesi henkilöllisyytesi, anna tämä kertakäyttösalasana kun sovellus sitä pyytää. Tämä ei vaikuta nykyiseen salasanaasi tai muihin tilisi asetuksiin.</p>

<h3 style="margin-top:25px;margin-bottom:25px;">{$OTP}</h3>

<p>Tämän koodin voimassaolo päättyy {$ExpirationDate->Format($dateFormat)}</p>

<p><a href="{$OtpUrl}">Vahvista käyttäjätilisi</a></p>