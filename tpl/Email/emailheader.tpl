{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<?xml version="1.0" encoding="{$Charset}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$Charset}"/>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font: 13px Helvetica, "Lucida Grande", Verdana, Arial, sans-serif;
            background-color: #fefefe;
            color: #333333;
            height: 100%;
        }

        div.resource-image img {
            padding-bottom: 25px;
            max-height: 200px;
            max-width: 200px;
        }

        .logo {
            max-height: 50px;
        }

        .email-header {
            background-color: #E9E9E9;
            border-bottom: solid 1px #BCBCBC;
            height: 50px;
            padding: 3px 10px;
        }

        .email-header a, .email-header a:visited, .email-header a:hover {
            color: #424242;
            line-height: 50px;
            font-size: 1.5em;
            text-decoration: none;
        }

        .email-body {
            margin: 10px;
        }

        footer {
            border-top: solid 1px #BCBCBC;
            text-align: center;
            padding: 3px 0;
        }

        label.customAttribute.readonly {
            font-weight: bold;
        }

        .reservation-qr-code {
            margin-top: 10px;
        }

        .resource-section {
            margin-top:10px;
            margin-bottom:10px;
        }

        .reservation-email-section {
            margin-bottom:10px;
        }

        .reservation-email-header {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .reservation-links {
            margin-top: 20px;
        }

    </style>
</head>
<body>
<div class="email-header">
    <div style="float: left;">
        <img src="{$ScriptUrl}/img/{$LogoUrl}?{$Version}" alt="{$Title}" class="logo" />
    </div>
    <div style="float: right; margin-left: auto;">
        <a href="{$ScriptUrl}" title="Log In">{translate key=LogIn}</a>
    </div>
</div>
<div class="email-body">