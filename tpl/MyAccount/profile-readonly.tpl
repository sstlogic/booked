{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' Validator=true}

<div class="page-profile">

    <div id="profile-box" class="default-box col-md-8 offset-md-2 col-sm-12">
        <h1>{translate key=Profile}</h1>

        <div class="row">
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="username">{translate key="Username"}</label>
                    <div>{$Username}</div>
                </div>
            </div>

            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="email">{translate key="Email"}</label>
                    <div>{$Email}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="fname">{translate key="FirstName"}</label>
                    <div>{$FirstName}</div>
                </div>
            </div>
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="lname">{translate key="LastName"}</label>
                    <div>{$LastName}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="homepage">{translate key="DefaultPage"}</label>
                    {foreach from=$HomepageValues item=h}
                        {if $h == $Homepage}
                            <div>{$HomepageOutput[$h]|escape}</div>
                        {/if}
                    {/foreach}
                </div>

            </div>
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="timezoneDropDown">{translate key="Timezone"}</label>
                    <div>{$Timezone}</div>
                    <div id="timezone-warning">{translate key=TimezoneMismatchError}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="phone">{translate key="Phone"}</label>
                    <div>{$Phone}</div>
                </div>
            </div>

            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="txtOrganization">{translate key="Organization"}</label>
                    <div>{$Organization}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md">
                <div class="mb-3">
                    <label class="form-label" for="txtPosition">{translate key="Position"}</label>
                    <div>{$Position}</div>
                </div>
            </div>

            <div class="col-md">
                {if count($Attributes) > 0}
                    {control type="AttributeControl" attribute=$Attributes[0] readonly=true}
                {/if}
            </div>

        </div>

        {if count($Attributes) > 1}
            {for $i=1 to count($Attributes)-1}
                {if $i%2==1}
                    <div class="row">
                {/if}
                <div class="col-md">
                    {control type="AttributeControl" attribute=$Attributes[$i] readonly=true}
                </div>
                {if $i%2==0 || $i==count($Attributes)-1}
                    </div>
                {/if}
            {/for}
        {/if}
    </div>

    {include file="javascript-includes.tpl"}

    <script>
        $(document).ready(function () {
            if ('{$Timezone}'.toLowerCase() !== Intl.DateTimeFormat().resolvedOptions().timeZone.toLowerCase()) {
                $('#timezone-warning').show();
            }

            $('#timezoneDropDown').on('change', e => {
                    if ($('#timezoneDropDown').val().toLowerCase() !== Intl.DateTimeFormat().resolvedOptions().timeZone.toLowerCase()) {
                        $('#timezone-warning').show();
                    } else {
                        $('#timezone-warning').hide();
                    }
                }
            );
        });
    </script>
</div>
{include file='globalfooter.tpl'}