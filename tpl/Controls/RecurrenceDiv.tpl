{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
<div id="{$prefix}repeatDiv" class="repeat-div">
    <div class="">
        <div class="col-12">
            <label for="{$prefix}repeatOptions">{translate key="RepeatPrompt"}</label>
            <select id="{$prefix}repeatOptions" {formname key=repeat_options}
                    class="form-select form-select-sm repeat-drop inline-block">
                {foreach from=$RepeatOptions key=k item=v}
                    <option value="{$k}">{translate key=$v['key']}</option>
                {/foreach}
            </select>
        </div>

        <div class="row mt-2 mb-2">
            <div class="col-12 col-md-4">
                <div id="{$prefix}repeatEveryDiv" class="recur-toggle no-show days weeks months years">
                    <label for="{$prefix}repeatInterval">{translate key="RepeatEveryPrompt"}</label>
                    <select id="{$prefix}repeatInterval" {formname key=repeat_every}
                            class="form-select form-select-sm repeat-interval-drop inline-block">
                        {html_options values=$RepeatEveryOptions output=$RepeatEveryOptions}
                    </select>
                    <span class="days">{translate key=$RepeatOptions['daily']['everyKey']}</span>
                    <span class="weeks">{translate key=$RepeatOptions['weekly']['everyKey']}</span>
                    <span class="months">{translate key=$RepeatOptions['monthly']['everyKey']}</span>
                    <span class="years">{translate key=$RepeatOptions['yearly']['everyKey']}</span>
                </div>
            </div>

            <div class="col-12 col-md-8">
                <div id="{$prefix}repeatOnWeeklyDiv" class="recur-toggle weeks no-show">
                    <label>On</label>
                    <div role="group">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay0" {formname key=repeat_sunday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay0" class="btn btn-outline-secondary btn-sm">
                                {translate key="DaySundayAbbr"}
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay1" {formname key=repeat_monday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay1" class="btn btn-outline-secondary btn-sm">
                                {translate key="DayMondayAbbr"}
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay2" {formname key=repeat_tuesday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay2" class="btn btn-outline-secondary btn-sm">
                                {translate key="DayTuesdayAbbr"}
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay3" {formname key=repeat_wednesday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay3" class="btn btn-outline-secondary btn-sm">
                                {translate key="DayWednesdayAbbr"}
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay4" {formname key=repeat_thursday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay4" class="btn btn-outline-secondary btn-sm">
                                {translate key="DayThursdayAbbr"}
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay5" {formname key=repeat_friday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay5" class="btn btn-outline-secondary btn-sm">
                                {translate key="DayFridayAbbr"}
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="{$prefix}repeatDay6" {formname key=repeat_saturday}
                                   class="btn-check"/>
                            <label for="{$prefix}repeatDay6" class="btn btn-outline-secondary btn-sm">
                                {translate key="DaySaturdayAbbr"}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="{$prefix}repeatOnMonthlyDiv" class="recur-toggle months no-show">
                <label>On</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input type="radio" {formname key=REPEAT_MONTHLY_TYPE}
                               value="{RepeatMonthlyType::DayOfMonth}"
                               id="{$prefix}repeatMonthDay" checked="checked" class="btn-check"/>
                        <label for="{$prefix}repeatMonthDay" class="btn btn-outline-secondary btn-sm">
                            {translate key="repeatDayOfMonth"}
                        </label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input type="radio" {formname key=REPEAT_MONTHLY_TYPE}
                               value="{RepeatMonthlyType::DayOfWeek}"
                               id="{$prefix}repeatMonthWeek" class="btn-check"/>
                        <label for="{$prefix}repeatMonthWeek" class="btn btn-outline-secondary btn-sm">
                            {translate key="repeatDayOfWeek"}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div id="{$prefix}repeatUntilDiv" class="col-xs-12 no-show recur-toggle">
            <div id="{$prefix}EndRepeat"></div>
            <input type="hidden" id="{$prefix}formattedEndRepeat" {formname key=end_repeat_date}
                   value="{formatdate date=$RepeatTerminationDate key=system}"/>
        </div>

        <div id="{$prefix}customDatesDiv" class="col-xs-12 no-show specific-dates">
            <div id="{$prefix}RepeatDate" class="d-inline-block"></div>
            <input type="hidden" id="{$prefix}formattedRepeatDate"/>
            <button type="button" class="btn btn-link d-inline-block text-decoration-none" id="{$prefix}AddDate">
                {translate key=AddDate}
                <span class="bi bi-plus-square"></span>
            </button>

            <div class="repeat-date-list"></div>
        </div>
    </div>
</div>