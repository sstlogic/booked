{if $TotalAvailable > 0}
    <div class="header">{translate key=Available}</div>
    {assign var=availability value=$Available}
    {if is_array($availability) && count($availability) > 0}
        {foreach from=$availability item=i}
            <div class="availability-item row">
                <div class="col-8">
                        <div>
                            <i resource-id="{$i->ResourceId()}" class="resourceNameSelector bi bi-info-circle"></i>
                            <div class="resourceName"
                                 style="background-color:{$i->GetColor()};color:{$i->GetTextColor()};">
                                <a href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}={$i->ResourceId()}"
                                   data-resourceId="{$i->ResourceId()}"
                                   class="resourceNameSelector"
                                   style="color:{$i->GetTextColor()}">{$i->ResourceName()}</a>
                            </div>
                            {if in_array($i->ResourceId(), $FavoriteIds)}
                                <span role="button" class="favorite-resource bi bi-star-fill"
                                      data-resource-id="{$i->ResourceId()}"
                                      title="{translate key=RemoveFavoriteResource}"></span>
                            {/if}
                        </div>
                        <div class="availability">
                            {if $i->NextTime() != null}
                                {translate key=AvailableUntil}
                                {format_date date=$i->NextTime() timezone=$Timezone key=dashboard}
                            {else}
                                <span>{translate key=Available}</span>
                            {/if}
                        </div>
                </div>

                <div class="reserve-button col-4">
                    {if $i->CanBook()}
                    <a class="btn btn-xs col-xs-12"
                       href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}={$i->ResourceId()}">{translate key=Reserve}</a>
                    {/if}
                </div>
            </div>
            {foreachelse}
            <div class="no-data">{translate key=None}</div>
        {/foreach}
    {/if}
{/if}

{if $TotalUnavailable > 0}
    <div class="header">{translate key=Unavailable}</div>
    {assign var=availability value=$Unavailable}
    {if is_array($availability) && count($availability) > 0}
        {foreach from=$availability item=i}
            <div class="availability-item row">
                <div class="col-xs-12 col-sm-5">
                    <i resource-id="{$i->ResourceId()}" class="resourceNameSelector bi bi-info-circle"></i>
                    <div class="resourceName"
                         style="background-color:{$i->GetColor()};color:{$i->GetTextColor()};">
                        <a href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}={$i->ResourceId()}"
                           resource-id="{$i->ResourceId()}"
                           class="resourceNameSelector"
                           style="color:{$i->GetTextColor()}">{$i->ResourceName()}</a>
                    </div>
                </div>
                <div class="availability col-xs-12 col-sm-4">
                    {translate key=AvailableBeginningAt} {format_date date=$i->ReservationEnds() timezone=$Timezone key=dashboard}
                </div>
                <div class="reserve-button col-xs-12 col-sm-3">
                    {if $i->CanBook()}
                    <a class="btn btn-xs col-xs-12"
                       href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}={$i->ResourceId()}&{QueryStringKeys::START_DATE}={format_date date=$i->ReservationEnds() timezone=$Timezone key=url_full}">{translate key=Reserve}</a>
                    {/if}
                </div>
            </div>
            <div class="clearfix"></div>
            {foreachelse}
            <div class="no-data">{translate key=None}</div>
        {/foreach}
    {/if}
{/if}

{if $TotalUnavailableAllDay > 0}
    <div class="header">{translate key=UnavailableAllDay}</div>
    {assign var=availability value=$UnavailableAllDay}
    {if is_array($availability) && count($availability) > 0}
        {foreach from=$availability item=i}
            <div class="availability-item row">
                <div class="col-xs-12 col-sm-5">
                    <i data-resourceId="{$i->ResourceId()}" class="resourceNameSelector bi bi-info-circle"></i>
                    <div class="resourceName"
                         style="background-color:{$i->GetColor()};color:{$i->GetTextColor()};">
                        <a href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}={$i->ResourceId()}"
                           data-resourceId="{$i->ResourceId()}"
                           class="resourceNameSelector"
                           style="color:{$i->GetTextColor()}">{$i->ResourceName()}</a>
                    </div>
                </div>
                <div class="availability col-xs-12 col-sm-4">
                    {translate key=AvailableAt} {format_date date=$i->ReservationEnds() timezone=$Timezone key=dashboard}
                </div>
                <div class="reserve-button col-xs-12 col-sm-3">
                    {if $i->CanBook()}
                    <a class="btn btn-xs col-xs-12"
                       href="{$Path}{UrlPaths::RESERVATION}?{QueryStringKeys::RESOURCE_ID}={$i->ResourceId()}&{QueryStringKeys::START_DATE}={format_date date=$i->ReservationEnds() timezone=$Timezone key=url_full}">{translate key=Reserve}</a>
                    {/if}
                </div>
            </div>
            <div class="clearfix"></div>
            {foreachelse}
            <div class="no-data">{translate key=None}</div>
        {/foreach}
    {/if}
{/if}