<div class="d-flex align-items-md-center timepicker-range" id="{$Id}">
    <div class="form-group position-relative">
        <label for="{$StartInputId}" class="visually-hidden">{translate key=StartTime}</label>
        <input name="{$StartInputFormName}" type="text" id="{$StartInputId}"
                                         class="form-control dateinput inline-block timepicker-start"
                                         value="{$Start->ToTimezone($Timezone)->Format($TimeFormat)}"
                                         data-hour="{$Start->ToTimezone($Timezone)->Hour()}"
                                         data-minute="{$Start->ToTimezone($Timezone)->Minute()}"
                                         data-phour="{$Start->ToTimezone($Timezone)->Hour()}"
                                         data-pminute="{$Start->ToTimezone($Timezone)->Minute()}"
                                         title="{translate key=StartTime|escape}"/>
        <div class="timepicker-list timepicker-start-list">
            <ul>
                {foreach from=$times item=t}
                    <li data-hour="{$t->Hour()}" data-minute="{$t->Minute()}" data-val="{$t->Format($TimeFormat)}">{$t->Format($TimeFormat)}</li>
                {/foreach}
            </ul>
        </div>
    </div>
    <div class="ms-2 me-2">
        -
    </div>
    <div class="form-group position-relative">
        <label for="{$EndInputId}" class="visually-hidden">{translate key=EndTime}</label>
        <input name="{$EndInputFormName}" type="text" id="{$EndInputId}"
                                       class="form-control dateinput inline-block timepicker-end"
                                       value="{$End->ToTimezone($Timezone)->Format($TimeFormat)}"
                                       data-hour="{$End->ToTimezone($Timezone)->Hour()}"
                                       data-minute="{$End->ToTimezone($Timezone)->Minute()}"
                                       title="{translate key=EndTime|escape}"/>
        <div class="timepicker-list timepicker-end-list">
            <ul>
                {foreach from=$times item=t}
                    <li data-hour="{$t->Hour()}" data-minute="{$t->Minute()}" data-val="{$t->Format($TimeFormat)}">{$t->Format($TimeFormat)}</li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>