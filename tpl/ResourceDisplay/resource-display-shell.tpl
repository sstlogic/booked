{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl' HideNavBar=true}

<div id="page-resource-display-resource">
    <div id="placeholder"></div>
</div>

<div id="wait-box" class="wait-box">
    {indicator id="waitIndicator"}
</div>

{include file="javascript-includes.tpl" Autocomplete=true}
{jsfile src="resourceDisplay.js"}
{jsfile src="ajax-helpers.js"}
{jsfile src="autocomplete.js"}

<script>
    $(function () {
        var resourceDisplay = new ResourceDisplay();
        resourceDisplay.initDisplay(
            {
                url: '{$smarty.server.SCRIPT_NAME}?dr=resource&rid={$PublicResourceId}&dr=display',
                userAutocompleteUrl: "ajax/autocomplete.php?type={AutoCompleteType::User}&as=1",
                allowAutocomplete: {if $AllowAutocomplete}true{else}false{/if}
            }
        );
    });
</script>

{include file='globalfooter.tpl'}