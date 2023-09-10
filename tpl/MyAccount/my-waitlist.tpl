{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl'}

<div id="page-my-waitlist">
    <div id="waitlist-box" class="default-box col-md-8 offset-md-2 col-sm-12">
        <h1>{translate key=WaitlistRequests} <span>({count($WaitlistRequests)})</span></h1>

        {foreach from=$WaitlistRequests item=r}
            <div class="d-flex justify-content-between">
                <div class="align-self-center">
                    {$ResourceNames[$r->ResourceId()]}
                </div>
                <div class="align-self-center">
                    {formatdate date=$r->StartDate() key=short_datetime timezone=$Timezone} -
                    {formatdate date=$r->EndDate() key=short_datetime timezone=$Timezone}
                </div>
                <div>
                    <button class="btn btn-link icon delete" data-waitlistid="{$r->Id()}">Cancel Request</button>
                </div>
            </div>
        {/foreach}
    </div>
</div>

<form id="delete-waitlist-form" ajaxAction="delete" method="post">
    <input type="hidden" {formname key=WAITLIST_REQUEST_ID} id="waitlistId" />
    {csrf_token}
</form>

{include file="javascript-includes.tpl"}
{jsfile src="ajax-helpers.js"}

<script>
    $(document).ready(function () {
        const form = $("#delete-waitlist-form");

        $('#waitlist-box').on('click', '.delete', e => {
            const btn = e.target;
            $(btn).attr('disabled', true);
            $('#waitlistId').val($(btn).data('waitlistid'));
            form.submit();
        });
        ConfigureAsyncForm(form);
    });
</script>

{include file='globalfooter.tpl'}