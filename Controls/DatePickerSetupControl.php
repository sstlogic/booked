<?php
/**
 * Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Controls/Control.php');

class DatePickerSetupControl extends Control
{
    public function __construct(SmartyPage $smarty)
    {
        parent::__construct($smarty);
    }

    public function PageLoad()
    {
        $this->SetDefault('NumberOfMonths', 1);
        $this->SetDefault('ShowButtonPanel', 'false');
        $controlId = $this->Get("ControlId");
        $controlId = str_replace(']', '\\\\]', str_replace('[', '\\\\[', $controlId));
        $altId = $this->Get('AltId');

        $elementsToTrigger = '#' . $controlId;
        if (!empty($altId)) {
            $altId = str_replace(']', '\\\\]', str_replace('[', '\\\\[', $altId));
            $elementsToTrigger .= ",#$altId";
        }

        $this->Set('ControlId', $controlId);
        $this->Set('AltId', $altId);

        $this->SetDefault('OnSelect', sprintf("function() { $('%s').trigger('change'); }", $elementsToTrigger));
        $this->SetDefault('FirstDay', Configuration::Instance()->GetKey(ConfigKeys::FIRST_DAY_OF_WEEK, new IntConverter()));

        $hasTimepicker = $this->Get('HasTimepicker');
        $this->Set('HasTimepicker', $hasTimepicker);
        $resources = Resources::GetInstance();
        $this->Set('DateFormat',  $hasTimepicker ? $resources->GetDateFormat("react_datetime") : $resources->GetDateFormat("react_date"));
        $this->Set('AltFormat', $resources->GetDateFormat($hasTimepicker ? 'js_general_datetime' : 'js_general_date'));
        $this->Set('DayNamesMin', $this->GetJsDayNames('two'));
        $this->Set('DayNamesShort', $this->GetJsDayNames('abbr'));
        $this->Set('DayNames', $this->GetJsDayNames('full'));
        $this->Set('MonthNames', $this->GetJsMonthNames('full'));
        $this->Set('MonthNamesShort', $this->GetJsMonthNames('abbr'));
        $this->Set('ShowWeekNumbers', Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_WEEK_NUMBERS, new BooleanConverter()));
        $this->SetDefault('MinDate', null);
        $this->SetDefault('MaxDate', null);
        $this->Set('CurrentLanguageJs', $resources->CurrentLanguageJs());

        $inline = $this->Get('Inline');
        $this->Set('Inline', isset($inline) ? $inline : false);

        $label = $this->Get('Label');
        $this->Set('Label', isset($label) ? $label : false);

        $inputClass = $this->Get('InputClass');
        $this->Set('InputClass', isset($inputClass) ? $inputClass : '');

        $wrapperClass = $this->Get('WrapperClass');
        $this->Set('WrapperClass', isset($wrapperClass) ? $wrapperClass : '');

        $required = $this->Get('Required');
        $this->Set('Required', isset($required) ? $required : false);

        $placeholder = $this->Get('Placeholder');
        $this->Set('Placeholder', isset($placeholder) ? $placeholder : '');

        $defaultDate = $this->Get('DefaultDate');
        if (is_string($defaultDate)) {
            $this->Set('DefaultDate', Date::Parse($defaultDate));
        }

        $this->Display('Controls/DatePickerSetup.tpl');
    }

    private function SetDefault($key, $value)
    {
        $item = $this->Get($key);
        if ($item == null) {
            $this->Set($key, $value);
        }
    }

    private function GetJsDayNames($dayKey)
    {
        return $this->GetJsArrayValues(Resources::GetInstance()->GetDays($dayKey));
    }

    private function GetJsMonthNames($monthKey)
    {
        return $this->GetJsArrayValues(Resources::GetInstance()->GetMonths($monthKey));
    }

    private function GetJsArrayValues($values)
    {
        return "['" . implode("','", $values) . "']";
    }
}