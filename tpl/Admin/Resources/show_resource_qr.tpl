<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="{$Charset}"/>
    <title>{if $TitleKey neq ''}{translate key=$TitleKey args=$TitleArgs}{else}{$Title}{/if}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex"/>
    <link rel="shortcut icon" href="{$Path}{$FaviconUrl}"/>
    <link rel="icon" href="{$Path}{$FaviconUrl}"/>
</head>

<body>
<h1>{$ResourceName}</h1>
<img src="{$QRImageUrl}" alt="{$ResourceName}"/>

<div style="margin-top:50px">
    <h3>What can I do with this code?</h3>
    <div>{$HelpText}</div>
</div>

<div>
    <button id="print">Print</button>
    <button id="download">Download</button>
</div>

<script>
    document.getElementById("print").onclick = () => {
        window.print();
    };

    document.getElementById("download").onclick = () => {
        var a = document.createElement('a');
        a.href = "{$QRImageUrl}";
        a.download = "";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };

</script>
</body>
</html>