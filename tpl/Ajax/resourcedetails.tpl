{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div id="resourceDetailsPopup">
    {assign var=h4Style value=""}
    {if !empty($color)}
        {assign var=h4Style value=" style=\"background-color:{$color};color:{$textColor};padding:5px 3px;\""}
    {/if}
    <div class="resourceNameTitle">
        <h4 {$h4Style}>{$resourceName}</h4>
        <a href="#" class="visible-sm-inline-block hideResourceDetailsPopup">{translate key=Close}</a>
        <div class="clearfix"></div>
    </div>
    {assign var=class value='col-6'}

    <div class="row">
        {if $imageUrl neq ''}
            {assign var=class value='col-5'}
            <div class="resourceImage col-2">
                <div class="owl-carousel owl-theme">
                    <div class="item">
                        <a href="{resource_image image=$imageUrl}" target="_blank" rel="noreferrer">
                            <img src="{resource_image image=$imageUrl}" alt="{$resourceName}" class="image"/>
                        </a>

                    </div>
                    {foreach from=$images item=image}
                        <div class="item">
                            <a href="{resource_image image=$image}" target="_blank" rel="noreferrer">
                                <img src="{resource_image image=$image}" alt="{$resourceName}" class="image"/>
                            </a>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/if}
        <div class="description {$class}">
            <span class="bold">{translate key=Schedule}</span>
            {$scheduleName}
            <br/>
            <span class="bold">{translate key=Description}</span>
            {if $description neq ''}
                {markdown text=$description}
            {else}
                {translate key=NoDescriptionLabel}
            {/if}
            <br/>
            <span class="bold">{translate key=Notes}</span>
            {if $notes neq ''}
                {markdown text=$notes}
            {else}
                {translate key=NoNotesLabel}
            {/if}
            <br/>
            <span class="bold">{translate key=Contact}</span>
            {if $contactInformation neq ''}
                {nl2br($contactInformation|url2link)}
            {else}
                {translate key=NoContactLabel}
            {/if}
            <br/>
            <span class="bold">{translate key=Location}</span>
            {if $locationInformation neq ''}
                {nl2br($locationInformation|url2link)}
            {else}
                {translate key=NoLocationLabel}
            {/if}
            <br/>
            <span class="bold">{translate key=ResourceType}</span>
            {if $resourceType neq ''}
                {$resourceType}
            {else}
                {translate key=NoResourceTypeLabel}
            {/if}
            {if !empty($Attributes)}
                {foreach from=$Attributes item=attribute}
                    <div>
                        {control type="AttributeControl" attribute=$attribute readonly=true}
                    </div>
                {/foreach}
            {/if}
            {if $ResourceTypeAttributes && !empty($ResourceTypeAttributes)}
                {foreach from=$ResourceTypeAttributes item=attribute}
                    <div>
                        {control type="AttributeControl" attribute=$attribute readonly=true}
                    </div>
                {/foreach}
            {/if}
        </div>
        <div class="attributes {$class}">
            <div>
                {if $minimumDuration neq ''}
                    {translate key='ResourceMinLength' args=$minimumDuration}
                {else}
                    {translate key='ResourceMinLengthNone'}
                {/if}
            </div>
            <div>
                {if $maximumDuration neq ''}
                    {translate key='ResourceMaxLength' args=$maximumDuration}
                {else}
                    {translate key='ResourceMaxLengthNone'}
                {/if}
            </div>
            <div>
                {if $requiresApproval}
                    {translate key='ResourceRequiresApproval'}
                {else}
                    {translate key='ResourceRequiresApprovalNone'}
                {/if}
            </div>
            <div>
                {if $minimumNotice neq ''}
                    {translate key='ResourceMinNotice' args=$minimumNotice}
                {else}
                    {translate key='ResourceMinNoticeNone'}
                {/if}
            </div>
            <div>
                {if $maximumNotice neq ''}
                    {translate key='ResourceMaxNotice' args=$maximumNotice}
                {else}
                    {translate key='ResourceMaxNoticeNone'}
                {/if}
            </div>
            <div>
                {if $allowMultiday}
                    {translate key='ResourceAllowMultiDay'}
                {else}
                    {translate key='ResourceNotAllowMultiDay'}
                {/if}
            </div>
            <div>
                {if $maxParticipants != '' && $maxParticipants != 0}
                    {translate key='ResourceCapacity' args=$maxParticipants}
                {else}
                    {translate key='ResourceCapacityNone'}
                {/if}
            </div>

            {if $autoReleaseMinutes neq ''}
                <div>
                    {translate key='AutoReleaseNotification' args=$autoReleaseMinutes}
                </div>
            {/if}
            {if $isCheckInEnabled}
                <div>
                    {translate key='RequiresCheckInNotification'}
                </div>
            {/if}

            {if $creditsEnabled}
                <div>
                    {translate key=CreditUsagePerSlot args=$offPeakCredits}
                </div>
                <div>
                    {translate key=PeakCreditUsagePerSlot args=$peakCredits}
                </div>
            {/if}

            {if $requiredResources}
                <div>{translate key=MustBeBookedWith} <em>
                        {foreach from=$requiredResources item=id name=required_loop}
                            {$relationships[$id]}{if !$smarty.foreach.required_loop.last}, {/if}
                        {/foreach}
                    </em>
                </div>
            {/if}

            {if $excludedResources}
                <div>{translate key=CannotBeBookedWith} <em>
                        {foreach from=$excludedResources item=id name=excluded_loop}
                            {$relationships[$id]}{if !$smarty.foreach.excluded_loop.last}, {/if}
                        {/foreach}
                    </em>
                </div>
            {/if}

            {if $excludedTimeResources}
                <div>{translate key=CannotBeBookedAtSameTime} <em>
                        {foreach from=$excludedTimeResources item=id name=excluded_time_loop}
                            {$relationships[$id]}{if !$smarty.foreach.excluded_time_loop.last}, {/if}
                        {/foreach}
                    </em>
                </div>
            {/if}
        </div>
    </div>
</div>