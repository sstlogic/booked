{if count($Requests) == 0}
    <div class="text-center">{translate key=NoResultsFound}</div>
{/if}
{if count($Requests) > 0}
    <table class="table table-striped">
        <thead>
        <tr>
        <td>Resource</td>
        <td>Time Requested</td>
        <td>Requested By</td>
        <td class="action">Cancel</td>
        </tr>
        </thead>
    <tbody>
    {foreach from=$Requests item=r}
        <tr>
            <td>{$r->ResourceName()}</td>
            <td>{formatdate date=$r->StartDate() timezone=$Timezone key=short_datetime} -
                {if $r->StartDate()->ToTimezone($Timezone)->DateEquals($r->EndDate()->ToTimezone($Timezone))}
                    {formatdate date=$r->EndDate() timezone=$Timezone key=period_time}
                    {else}
                    {formatdate date=$r->EndDate() timezone=$Timezone key=short_datetime}
                {/if}
            </td>
            <td>{$r->UserName()}</td>
            <td class="action"><button class="btn btn-link icon delete" data-waitlistid="{$r->Id()}"><i class="bi bi-trash"></i></button></td>
        </tr>
    {/foreach}
    </tbody>
    </table>
{/if}
