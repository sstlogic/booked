{foreach from=$Openings item=opening}
    <div class="opening"
         data-resourceid="{$opening->Resource()->Id}"
         data-startdate="{format_date date=$opening->Start() key=system_datetime}"
         data-enddate="{format_date date=$opening->End() key=system_datetime}">
        <div class="resourceName" data-resourceId="{$opening->Resource()->Id}" {if $opening->Resource()->HasColor()}style="background-color: {$opening->Resource()->Color};color:{$opening->Resource()->TextColor};"{/if}>
            {$opening->Resource()->Name|escape}
        </div>
        {assign var=key value=short_reservation_date}
        {if $opening->SameDate()}
            {assign var=key value=period_time}
        {/if}
        <div class="dates">
        {format_date date=$opening->Start() key=res_popup} -
        {format_date date=$opening->End() key=$key}
        </div>
    </div>
{/foreach}

{if count($Openings) == 0}
    <div class="alert alert-warning text-center">
        <span class="bi bi-emoji-frown"></span> {translate key=NoAvailableMatchingTimes}

        {if isset($WaitlistTime)}
            <div class="text-center mt-2" id="join-waitlist-div">
                <form ajaxAction="joinWaitlist" method="post" id="join-waitlist-form">
                    {foreach from=$WaitlistResources item=id}
                    <input type="hidden" {formname key=RESOURCE_ID multi=true} value="{$id}" />
                    {/foreach}
                    <input type="hidden" {formname key=BEGIN_DATE} value="{formatdate date=$WaitlistTime->GetBegin() key=system_datetime}" />
                    <input type="hidden" {formname key=END_DATE} value="{formatdate date=$WaitlistTime->GetEnd() key=system_datetime}" />
                    <button type="submit" class="btn btn-primary" id="join-waitlist-btn"> <i class="bi bi-bell"></i> {translate key=NotifyWhenAvailable}</button>
                    {indicator id="waitlist-indicator"}
                </form>
                <div class="no-show" id="join-waitlist-success">
                    {translate key=WaitlistRequestAdded}
                </div>
            </div>
        {/if}
    </div>

{/if}